<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

$error = null;
$success = null;

// Check if already logged in
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

// Handle registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $user = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    $passConfirm = $_POST['password_confirm'] ?? '';
    
    // Validation
    if (empty($user) || empty($email) || empty($pass) || empty($passConfirm)) {
        $error = $lang['register_empty_fields'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = $lang['register_invalid_email'];
    } elseif ($pass !== $passConfirm) {
        $error = $lang['register_password_mismatch'];
    } elseif (strlen($pass) < 6) {
        $error = $lang['register_password_too_short'];
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$user, $email]);
        $existingUser = $stmt->fetch();
        
        if ($existingUser) {
            if ($existingUser['username'] === $user) {
                $error = $lang['register_username_taken'];
            } else {
                $error = $lang['register_email_taken'];
            }
        } else {
            // Insert new user
            try {
                $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, language, created_at) VALUES (?, ?, ?, 'user', ?, NOW())");
                $stmt->execute([$user, $email, $hashedPassword, $_SESSION['language']]);
                
                $success = $lang['register_success'];
            } catch (PDOException $e) {
                $error = "Registration error: " . $e->getMessage();
            }
        }
    }
}

// Include admin header
$pageTitle = $lang['register_page_title'];
include_once 'includes/admin_header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                <?php echo $lang['register_heading']; ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                <?php echo $lang['register_subheading']; ?>
            </p>
        </div>

        <?php if ($error): ?>
            <div class="rounded-md bg-red-50 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            <?php echo htmlspecialchars($error); ?>
                        </h3>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="rounded-md bg-green-50 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800">
                            <?php echo htmlspecialchars($success); ?>
                        </h3>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="register.php" method="POST">
            <input type="hidden" name="action" value="register">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only"><?php echo $lang['username']; ?></label>
                    <input id="username" name="username" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="<?php echo $lang['username']; ?>">
                </div>
                <div>
                    <label for="email" class="sr-only"><?php echo $lang['email']; ?></label>
                    <input id="email" name="email" type="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="<?php echo $lang['email']; ?>">
                </div>
                <div>
                    <label for="password" class="sr-only"><?php echo $lang['password']; ?></label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="<?php echo $lang['password']; ?>">
                </div>
                <div>
                    <label for="password_confirm" class="sr-only"><?php echo $lang['confirm_password']; ?></label>
                    <input id="password_confirm" name="password_confirm" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="<?php echo $lang['confirm_password']; ?>">
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M8 9a3 3 0 100-6 3 3 0 000 6zM8 11a6 6 0 016 6H2a6 6 0 016-6z" />
                            <path d="M16 7a1 1 0 10-2 0v1h-1a1 1 0 100 2h1v1a1 1 0 102 0v-1h1a1 1 0 100-2h-1V7z" />
                        </svg>
                    </span>
                    <?php echo $lang['register_button']; ?>
                </button>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm">
                    <a href="login.php" class="font-medium text-blue-600 hover:text-blue-500">
                        <?php echo $lang['register_login_prompt']; ?>
                    </a>
                </div>
                <div class="text-sm">
                    <a href="../index.php" class="font-medium text-blue-600 hover:text-blue-500">
                        <?php echo $lang['login_back_to_site']; ?>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>