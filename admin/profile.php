<?php
// Włącz wyświetlanie błędów
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Rozpocznij sesję
session_start();

// Sprawdź, czy użytkownik jest zalogowany
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Połączenie z bazą danych
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

$user = $_SESSION['user'];
$error = null;
$success = null;

// Obsługa aktualizacji profilu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_profile') {
        // Pobranie i walidacja danych
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $language = $_POST['language'] ?? 'en';
        
        // Podstawowa walidacja
        if (empty($username) || empty($email)) {
            $error = $lang['register_empty_fields'];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = $lang['register_invalid_email'];
        } else {
            try {
                // Sprawdź, czy nazwa użytkownika lub email są już zajęte (z wyłączeniem bieżącego użytkownika)
                $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $stmt->execute([$username, $email, $user['id']]);
                
                if ($stmt->rowCount() > 0) {
                    $existingUser = $stmt->fetch();
                    if ($existingUser['username'] === $username && $username !== $user['username']) {
                        $error = $lang['register_username_taken'];
                    } elseif ($existingUser['email'] === $email && $email !== $user['email']) {
                        $error = $lang['register_email_taken'];
                    }
                } else {
                    // Aktualizuj dane profilu
                    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, language = ? WHERE id = ?");
                    $stmt->execute([$username, $email, $language, $user['id']]);
                    
                    // Aktualizuj dane sesji
                    $_SESSION['user']['username'] = $username;
                    $_SESSION['user']['email'] = $email;
                    $_SESSION['user']['language'] = $language;
                    $_SESSION['language'] = $language;
                    
                    // Odśwież dane użytkownika
                    $user = $_SESSION['user'];
                    
                    $success = $lang['profile_info_updated'];
                }
            } catch (Exception $e) {
                $error = "Błąd podczas aktualizacji profilu: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'change_password') {
        // Pobranie i walidacja danych
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Podstawowa walidacja
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = $lang['register_empty_fields'];
        } elseif ($newPassword !== $confirmPassword) {
            $error = $lang['register_password_mismatch'];
        } elseif (strlen($newPassword) < 6) {
            $error = $lang['register_password_too_short'];
        } else {
            try {
                // Sprawdź bieżące hasło
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user['id']]);
                $userData = $stmt->fetch();
                
                if (password_verify($currentPassword, $userData['password'])) {
                    // Aktualizuj hasło
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashedPassword, $user['id']]);
                    
                    $success = $lang['profile_password_updated'];
                } else {
                    $error = $lang['profile_incorrect_password'];
                }
            } catch (Exception $e) {
                $error = "Błąd podczas zmiany hasła: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'update_avatar') {
        // Obsługa przesyłania awatara
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 2 * 1024 * 1024; // 2MB
            
            // Sprawdź typ pliku
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($fileType, $allowedTypes)) {
                $error = "Dozwolone są tylko pliki obrazów (JPEG, PNG, GIF).";
            } elseif ($_FILES['avatar']['size'] > $maxFileSize) {
                $error = "Plik jest zbyt duży. Maksymalny rozmiar to 2MB.";
            } else {
                // Sprawdź, czy istnieje katalog na awatary
                $uploadDir = __DIR__ . '/../uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                // Generuj unikalną nazwę pliku
                $filename = $user['id'] . '_' . time() . '.' . pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $uploadFile = $uploadDir . $filename;
                
                // Przenieś przesłany plik
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadFile)) {
                    // Aktualizuj ścieżkę awatara w bazie danych
                    $avatarPath = '/uploads/avatars/' . $filename;
                    
                    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $stmt->execute([$avatarPath, $user['id']]);
                    
                    // Aktualizuj dane sesji
                    $_SESSION['user']['avatar'] = $avatarPath;
                    $user = $_SESSION['user'];
                    
                    $success = "Awatar został pomyślnie zaktualizowany.";
                } else {
                    $error = "Nie udało się przesłać pliku.";
                }
            }
        } else {
            $error = "Nie wybrano pliku lub wystąpił błąd podczas przesyłania.";
        }
    }
}

// Pobierz najnowsze aktywności użytkownika
try {
    $stmt = $pdo->prepare("SELECT * FROM activity_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$user['id']]);
    $activities = $stmt->fetchAll();
} catch (Exception $e) {
    $activities = [];
}

// Tytuł strony
$pageTitle = $lang['profile_page_title'];
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800"><?php echo $lang['profile_heading']; ?></h1>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kolumna awatara i podsumowania -->
                <div class="md:col-span-1">
                    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                        <div class="flex flex-col items-center">
                            <!-- Awatar -->
                            <div class="mb-4">
                                <div class="h-32 w-32 rounded-full overflow-hidden bg-gray-200">
                                    <?php if (isset($user['avatar']) && !empty($user['avatar'])): ?>
                                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Awatar" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <svg class="h-full w-full text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Nazwa użytkownika i rola -->
                            <h2 class="text-xl font-semibold text-gray-900"><?php echo htmlspecialchars($user['username']); ?></h2>
                            <p class="text-sm text-gray-500"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></p>
                            
                            <!-- Formularz przesyłania awatara -->
                            <form method="POST" action="profile.php" enctype="multipart/form-data" class="w-full mt-4">
                                <input type="hidden" name="action" value="update_avatar">
                                <div class="flex flex-col">
                                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">Zmień awatar</label>
                                    <input type="file" name="avatar" id="avatar" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <button type="submit" class="mt-2 w-full inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Prześlij awatar
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Informacje o koncie -->
                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Informacje o koncie</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Email:</dt>
                                    <dd class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Rola:</dt>
                                    <dd class="text-sm text-gray-900"><?php echo ucfirst(htmlspecialchars($user['role'])); ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Język:</dt>
                                    <dd class="text-sm text-gray-900"><?php echo $user['language'] === 'pl' ? 'Polski' : 'English'; ?></dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm font-medium text-gray-500">Data utworzenia:</dt>
                                    <dd class="text-sm text-gray-900"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
                
                <!-- Kolumna formularzy -->
                <div class="md:col-span-2">
                    <!-- Profil -->
                    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4"><?php echo $lang['profile_update_info']; ?></h2>
                        
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700"><?php echo $lang['username']; ?></label>
                                    <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700"><?php echo $lang['email']; ?></label>
                                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="language" class="block text-sm font-medium text-gray-700"><?php echo $lang['language_current']; ?></label>
                                    <select name="language" id="language" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="en" <?php echo ($_SESSION['language'] === 'en') ? 'selected' : ''; ?>>English</option>
                                        <option value="pl" <?php echo ($_SESSION['language'] === 'pl') ? 'selected' : ''; ?>>Polski</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex justify-end mt-6">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <?php echo $lang['save']; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Zmiana hasła -->
                    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4"><?php echo $lang['profile_change_password']; ?></h2>
                        
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="current_password" class="block text-sm font-medium text-gray-700"><?php echo $lang['profile_current_password']; ?></label>
                                    <input type="password" name="current_password" id="current_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="new_password" class="block text-sm font-medium text-gray-700"><?php echo $lang['profile_new_password']; ?></label>
                                    <input type="password" name="new_password" id="new_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700"><?php echo $lang['profile_confirm_new_password']; ?></label>
                                    <input type="password" name="confirm_password" id="confirm_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                            
                            <div class="flex justify-end mt-6">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <?php echo $lang['change']; ?>
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Ostatnie aktywności -->
                    <div class="bg-white shadow-md rounded-lg p-6">
                        <h2 class="text-xl font-semibold mb-4"><?php echo $lang['dashboard_recent_activity']; ?></h2>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['action']; ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['date']; ?></th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (count($activities) > 0): ?>
                                        <?php foreach ($activities as $activity): ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo htmlspecialchars($activity['action']); ?>
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo date('d/m/Y H:i:s', strtotime($activity['created_at'])); ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($activity['ip_address']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                                Brak aktywności do wyświetlenia.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>