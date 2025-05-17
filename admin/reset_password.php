<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/includes/language.php';

// Already logged in users shouldn't be here
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = null;
$success = null;
$token = $_GET['token'] ?? '';
$validToken = false;
$userId = null;

// Validate token
if (!empty($token)) {
    $now = date('Y-m-d H:i:s');
    $stmt = $pdo->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > ? LIMIT 1");
    $stmt->execute([$token, $now]);
    $result = $stmt->fetch();
    
    if ($result) {
        $validToken = true;
        $userId = $result['user_id'];
    } else {
        $error = $lang['reset_token_invalid'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password') {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $formToken = $_POST['token'] ?? '';
    
    // Validation
    if (empty($password) || empty($passwordConfirm)) {
        $error = $lang['register_empty_fields'];
    } elseif ($password !== $passwordConfirm) {
        $error = $lang['register_password_mismatch'];
    } elseif (strlen($password) < 6) {
        $error = $lang['register_password_too_short'];
    } else {
        // Verify token again
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > ? LIMIT 1");
        $stmt->execute([$formToken, $now]);
        $result = $stmt->fetch();
        
        if ($result) {
            $userId = $result['user_id'];
            
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);
            
            // Remove used token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$formToken]);
            
            $success = $lang['reset_password_updated'];
        } else {
            $error = $lang['reset_token_invalid'];
        }
    }
}

// Include admin header
$pageTitle = $lang['reset_page_title'];
include_once 'includes/admin_header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                <?php echo $lang['reset_heading']; ?>
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                <?php echo $validToken ? $lang['profile_new_password'] : ''; ?>
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
    
    <div class="text-center mt-6">
        <a href="login.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <?php echo $lang['login']; ?>
        </a>
    </div>
<?php endif; ?>

<?php if ($validToken && !$success): ?>
    <form class="mt-8 space-y-6" action="reset_password.php" method="POST">
        <input type="hidden" name="action" value="reset_password">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700"><?php echo $lang['profile_new_password']; ?></label>
            <div class="mt-1">
                <input id="password" name="password" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>
        
        <div>
            <label for="password_confirm" class="block text-sm font-medium text-gray-700"><?php echo $lang['confirm_password']; ?></label>
            <div class="mt-1">
                <input id="password_confirm" name="password_confirm" type="password" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-500 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            </div>
        </div>

        <div>
            <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                    </svg>
                </span>
                <?php echo $lang['reset_button']; ?>
            </button>
        </div>
    </form>
<?php elseif (!$validToken && !$success): ?>
    <div class="text-center mt-6">
        <p class="text-gray-600 mb-4"><?php echo $lang['reset_token_invalid']; ?></p>
        <a href="forgot_password.php" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <?php echo $lang['reset_button']; ?>
        </a>
    </div>
<?php endif; ?>
    </div>
</div>

<?php
include_once 'includes/admin_footer.php';
?>