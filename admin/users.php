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

// Sprawdź, czy użytkownik ma uprawnienia administratora
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

// Połączenie z bazą danych
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

// Obsługa usuwania użytkownika
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    
    // Nie pozwól na usunięcie własnego konta
    if ($userId !== $_SESSION['user']['id']) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            
            // Przekieruj, aby uniknąć ponownego usunięcia przy odświeżeniu strony
            header("Location: users.php?deleted=1");
            exit;
        } catch (Exception $e) {
            $error = "Błąd podczas usuwania użytkownika: " . $e->getMessage();
        }
    } else {
        $error = "Nie możesz usunąć własnego konta.";
    }
}

// Obsługa dodawania/edycji użytkownika
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Pobranie i walidacja danych
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $language = $_POST['language'] ?? 'en';
    
    // Podstawowa walidacja
    if (empty($username) || empty($email)) {
        $error = "Nazwa użytkownika i email są wymagane.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Podaj prawidłowy adres email.";
    } else {
        try {
            if ($_POST['action'] === 'add') {
                // Dodawanie nowego użytkownika
                if (empty($password)) {
                    $error = "Hasło jest wymagane dla nowego użytkownika.";
                } else {
                    // Sprawdź, czy nazwa użytkownika lub email już istnieją
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    
                    if ($stmt->rowCount() > 0) {
                        $existingUser = $stmt->fetch();
                        if ($existingUser['username'] === $username) {
                            $error = "Nazwa użytkownika jest już zajęta.";
                        } else {
                            $error = "Adres email jest już używany.";
                        }
                    } else {
                        // Dodaj nowego użytkownika
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, language) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $hashedPassword, $role, $language]);
                        
                        $success = "Użytkownik został pomyślnie dodany.";
                    }
                }
            } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
                // Edycja istniejącego użytkownika
                $userId = (int)$_POST['id'];
                
                // Sprawdź, czy nazwa użytkownika lub email już istnieją (z wyłączeniem edytowanego użytkownika)
                $stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND id != ?");
                $stmt->execute([$username, $email, $userId]);
                
                if ($stmt->rowCount() > 0) {
                    $existingUser = $stmt->fetch();
                    if ($existingUser['username'] === $username) {
                        $error = "Nazwa użytkownika jest już zajęta.";
                    } else {
                        $error = "Adres email jest już używany.";
                    }
                } else {
                    // Aktualizacja użytkownika
                    if (!empty($password)) {
                        // Aktualizacja z nowym hasłem
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ?, language = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $hashedPassword, $role, $language, $userId]);
                    } else {
                        // Aktualizacja bez zmiany hasła
                        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, language = ? WHERE id = ?");
                        $stmt->execute([$username, $email, $role, $language, $userId]);
                    }
                    
                    $success = "Użytkownik został pomyślnie zaktualizowany.";
                }
            }
        } catch (Exception $e) {
            $error = "Błąd podczas zapisywania użytkownika: " . $e->getMessage();
        }
    }
}

// Pobieranie użytkownika do edycji
$editUser = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $userId = (int)$_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $editUser = $stmt->fetch();
    } catch (Exception $e) {
        $error = "Błąd podczas pobierania danych użytkownika: " . $e->getMessage();
    }
}

// Pobieranie listy użytkowników
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    $error = "Błąd podczas pobierania listy użytkowników: " . $e->getMessage();
    $users = [];
}

// Tytuł strony
$pageTitle = "Panel Administracyjny - Zarządzanie Użytkownikami";
include_once 'includes/admin_header.php';
?>

<div class="flex h-screen bg-gray-100">
    <!-- Sidebar -->
    <?php include_once 'includes/admin_sidebar.php'; ?>

    <!-- Main content -->
    <div class="flex-1 overflow-auto">
        <main class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-3xl font-semibold text-gray-800">Zarządzanie Użytkownikami</h1>
            </div>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['deleted']) && $_GET['deleted'] === '1'): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    Użytkownik został pomyślnie usunięty.
                </div>
            <?php endif; ?>

            <!-- Form for adding/editing users -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">
                    <?php echo isset($editUser) ? 'Edytuj Użytkownika' : 'Dodaj Nowego Użytkownika'; ?>
                </h2>
                
                <form method="POST" action="users.php">
                    <input type="hidden" name="action" value="<?php echo isset($editUser) ? 'edit' : 'add'; ?>">
                    <?php if (isset($editUser)): ?>
                        <input type="hidden" name="id" value="<?php echo $editUser['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Nazwa użytkownika</label>
                            <input type="text" name="username" id="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editUser) ? htmlspecialchars($editUser['username']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" value="<?php echo isset($editUser) ? htmlspecialchars($editUser['email']) : ''; ?>">
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Hasło <?php echo isset($editUser) ? '(pozostaw puste, aby nie zmieniać)' : ''; ?>
                            </label>
                            <input type="password" name="password" id="password" <?php echo isset($editUser) ? '' : 'required'; ?> class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        
                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">Rola</label>
                            <select name="role" id="role" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="user" <?php echo (isset($editUser) && $editUser['role'] === 'user') ? 'selected' : ''; ?>>Użytkownik</option>
                                <option value="editor" <?php echo (isset($editUser) && $editUser['role'] === 'editor') ? 'selected' : ''; ?>>Redaktor</option>
                                <option value="admin" <?php echo (isset($editUser) && $editUser['role'] === 'admin') ? 'selected' : ''; ?>>Administrator</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="language" class="block text-sm font-medium text-gray-700">Język</label>
                            <select name="language" id="language" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                <option value="en" <?php echo (isset($editUser) && $editUser['language'] === 'en') ? 'selected' : ''; ?>>English</option>
                                <option value="pl" <?php echo (isset($editUser) && $editUser['language'] === 'pl') ? 'selected' : ''; ?>>Polski</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <?php if (isset($editUser)): ?>
                            <a href="users.php" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md mr-2 hover:bg-gray-300 transition">Anuluj</a>
                        <?php endif; ?>
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                            <?php echo isset($editUser) ? 'Zapisz zmiany' : 'Dodaj użytkownika'; ?>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Users list -->
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Lista Użytkowników</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nazwa użytkownika</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rola</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Język</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data utworzenia</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $user['id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if ($user['role'] === 'admin') echo 'bg-red-100 text-red-800';
                                            elseif ($user['role'] === 'editor') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo htmlspecialchars($user['role']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['language'] ?? 'en'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($user['created_at']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edytuj</a>
                                        <?php if ($user['id'] !== $_SESSION['user']['id']): ?>
                                            <a href="users.php?action=delete&id=<?php echo $user['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">Usuń</a>
                                        <?php else: ?>
                                            <span class="text-gray-400">Usuń</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">Nie znaleziono użytkowników.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>