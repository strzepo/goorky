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

// Handle login form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    if (empty($user) || empty($pass)) {
        $error = $lang['login_empty_fields'];
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $data = $stmt->fetch();

        if ($data && password_verify($pass, $data['password'])) {
            $_SESSION['user'] = $data;
            
            // Store user's preferred language if set
            if (isset($data['language']) && !empty($data['language'])) {
                $_SESSION['language'] = $data['language'];
            }
            
            // Log the login activity
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, ip_address) VALUES (?, 'login', ?)");
            $stmt->execute([$data['id'], $_SERVER['REMOTE_ADDR']]);
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = $lang['login_invalid_credentials'];
        }
    }
}

// Include admin header
$pageTitle = $lang['login_page_title'];
include_once 'includes/admin_header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                <?php echo $lang['login_heading']; ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                <?php echo $lang['login_subheading']; ?>
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

        <form class="mt-8 space-y-6" action="login.php" method="POST">
            <input type="hidden" name="action" value="login">
            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="username" class="sr-only"><?php echo $lang['username']; ?></label>
                    <input id="username" name="username" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="<?php echo $lang['username']; ?>">
                </div>
                <div>
                    <label for="password" class="sr-only"><?php echo $lang['password']; ?></label>
                    <input id="password" name="password" type="password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm" placeholder="<?php echo $lang['password']; ?>">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm">
                    <a href="forgot_password.php" class="font-medium text-blue-600 hover:text-blue-500">
                        <?php echo $lang['forgot_password']; ?>
                    </a>
                </div>

                <div class="flex items-center">
                    <?php echo languageSwitcher(); ?>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    <?php echo $lang['login_button']; ?>
                </button>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm">
                    <a href="register.php" class="font-medium text-blue-600 hover:text-blue-500">
                        <?php echo $lang['login_register_prompt']; ?>
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