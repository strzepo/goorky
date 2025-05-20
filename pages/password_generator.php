<?php
// Ustawienie tytułu i opisu strony
$pageTitle = $lang['password_page_title'] ?? 'Password Generator - Create secure, random passwords | Goorky.com';
$pageDescription = $lang['password_page_description'] ?? 'Free online password generator - create secure, random passwords with chosen length and complexity. Increase the security of your online accounts.';

// Inicjalizacja zmiennych
$length = 12;
$useSpecial = true;
$useNumbers = true;
$useUpper = true;
$useLower = true;
$password = '';
$hasResult = false;
$passwordStrength = '';
$strengthColor = '';

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_password'])) {
    // Pobranie i walidacja danych
    $length = intval($_POST['length'] ?? 12);
    $useSpecial = isset($_POST['use_special']);
    $useNumbers = isset($_POST['use_numbers']);
    $useUpper = isset($_POST['use_upper']);
    $useLower = isset($_POST['use_lower']);
    
    // Walidacja długości hasła
    if ($length < 4) {
        $length = 4;
    } elseif ($length > 64) {
        $length = 64;
    }
    
    // Upewnienie się, że przynajmniej jedna opcja jest wybrana
    if (!$useSpecial && !$useNumbers && !$useUpper && !$useLower) {
        $useLower = true; // Domyślnie używaj małych liter
    }
    
    // Generowanie hasła
    $password = generatePassword($length, $useSpecial, $useNumbers, $useUpper, $useLower);
    $hasResult = true;
    
    // Ocena siły hasła
    $strength = 0;
    
    // Długość hasła
    if ($length >= 8) {
        $strength += 1;
    }
    if ($length >= 12) {
        $strength += 1;
    }
    if ($length >= 16) {
        $strength += 1;
    }
    
    // Złożoność
    if ($useSpecial) {
        $strength += 1;
    }
    if ($useNumbers) {
        $strength += 1;
    }
    if ($useUpper && $useLower) {
        $strength += 1;
    }
    
    // Kategorie siły hasła
    if ($strength <= 2) {
        $passwordStrength = $lang['password_weak'] ?? 'Słabe';
        $strengthColor = 'text-red-600';
    } elseif ($strength <= 4) {
        $passwordStrength = $lang['password_medium'] ?? 'Średnie';
        $strengthColor = 'text-yellow-600';
    } else {
        $passwordStrength = $lang['password_strong'] ?? 'Silne';
        $strengthColor = 'text-green-600';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['password_generator'] ?? 'Password Generator'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['password_intro'] ?? 'Create a secure, random password tailored to your preferences. Choose the length and character types to include.'; ?></p>
        
        <form method="POST" action="/password-generator" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="length" class="block text-gray-700 font-medium mb-2"><?php echo $lang['password_length'] ?? 'Password length'; ?></label>
                    <div class="flex items-center">
                        <input type="range" name="length" id="length" min="4" max="64" value="<?php echo htmlspecialchars($length); ?>" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('lengthValue').textContent = this.value">
                        <span id="lengthValue" class="ml-4 w-10 text-center font-medium"><?php echo htmlspecialchars($length); ?></span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2"><?php echo $lang['options'] ?? 'Options'; ?></label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_upper" <?php echo $useUpper ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2"><?php echo $lang['uppercase_letters'] ?? 'Uppercase letters (A-Z)'; ?></span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_lower" <?php echo $useLower ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2"><?php echo $lang['lowercase_letters'] ?? 'Lowercase letters (a-z)'; ?></span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_numbers" <?php echo $useNumbers ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2"><?php echo $lang['numbers'] ?? 'Numbers (0-9)'; ?></span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_special" <?php echo $useSpecial ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2"><?php echo $lang['special_chars'] ?? 'Special characters (!@#$%^&*()_-+=<>?)'; ?></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                 <button type="button" name="generate_password" class="trigger-popup bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['generate_password'] ?? 'Generate password'; ?></button>

            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['generated_password'] ?? 'Generated password'; ?></h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="w-full bg-gray-100 p-4 rounded-lg text-center relative mb-4">
                <code id="password" class="text-2xl font-mono break-all"><?php echo htmlspecialchars($password); ?></code>
                <button onclick="copyPassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition" title="<?php echo $lang['copy_to_clipboard'] ?? 'Copy to clipboard'; ?>">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                    </svg>
                </button>
            </div>
            
            <div class="text-lg <?php echo $strengthColor; ?>">
                <?php echo $lang['password_strength'] ?? 'Password strength:'; ?> <strong><?php echo $passwordStrength; ?></strong>
            </div>
        </div>
        
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2"><?php echo $lang['password_details'] ?? 'Password details:'; ?></h3>
            <ul class="space-y-1">
                <li><strong><?php echo $lang['length'] ?? 'Length:'; ?></strong> <?php echo strlen($password); ?> <?php echo $lang['characters'] ?? 'characters'; ?></li>
                <li><strong><?php echo $lang['contains_uppercase'] ?? 'Contains uppercase letters:'; ?></strong> <?php echo preg_match('/[A-Z]/', $password) ? ($lang['yes'] ?? 'Yes') : ($lang['no'] ?? 'No'); ?></li>
                <li><strong><?php echo $lang['contains_lowercase'] ?? 'Contains lowercase letters:'; ?></strong> <?php echo preg_match('/[a-z]/', $password) ? ($lang['yes'] ?? 'Yes') : ($lang['no'] ?? 'No'); ?></li>
                <li><strong><?php echo $lang['contains_numbers'] ?? 'Contains numbers:'; ?></strong> <?php echo preg_match('/[0-9]/', $password) ? ($lang['yes'] ?? 'Yes') : ($lang['no'] ?? 'No'); ?></li>
                <li><strong><?php echo $lang['contains_special'] ?? 'Contains special characters:'; ?></strong> <?php echo preg_match('/[^a-zA-Z0-9]/', $password) ? ($lang['yes'] ?? 'Yes') : ($lang['no'] ?? 'No'); ?></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o hasłach -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['how_to_create_strong_password'] ?? 'How to create a strong password?'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['strong_password_intro'] ?? 'A strong password is key to securing your online accounts. Here are some tips for creating safe passwords:'; ?></p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong><?php echo $lang['use_long_passwords'] ?? 'Use long passwords'; ?></strong> - <?php echo $lang['long_passwords_desc'] ?? 'The password should be at least 12 characters long. The longer, the better.'; ?></li>
                <li><strong><?php echo $lang['use_variety'] ?? 'Character variety'; ?></strong> - <?php echo $lang['variety_desc'] ?? 'Use a mix of uppercase and lowercase letters, numbers, and special characters.'; ?></li>
                <li><strong><?php echo $lang['avoid_patterns'] ?? 'Avoid predictable patterns'; ?></strong> - <?php echo $lang['patterns_desc'] ?? 'Don\'t use sequences like "123456" or "qwerty".'; ?></li>
                <li><strong><?php echo $lang['avoid_personal_info'] ?? 'Avoid personal information'; ?></strong> - <?php echo $lang['personal_info_desc'] ?? 'Avoid using names, birthdates, or phone numbers that are easy to guess.'; ?></li>
                <li><strong><?php echo $lang['use_different_passwords'] ?? 'Use different passwords'; ?></strong> - <?php echo $lang['different_passwords_desc'] ?? 'Each account should have a unique password.'; ?></li>
                <li><strong><?php echo $lang['change_regularly'] ?? 'Change passwords regularly'; ?></strong> - <?php echo $lang['change_regularly_desc'] ?? 'especially for important accounts.'; ?></li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['password_storage_warning'] ?? 'Do not store passwords in unsecured places. Consider using a password manager to securely store them.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Dodatkowe informacje -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['why_secure_passwords_matter'] ?? 'Why are secure passwords important?'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['secure_passwords_intro'] ?? 'Strong passwords are the first line of defense against unauthorized access to your online accounts. Here\'s why they\'re important:'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['protection_against_attacks'] ?? 'Protection against attacks'; ?></h3>
            <p><?php echo $lang['hackers_methods'] ?? 'Hackers use different methods to crack passwords:'; ?></p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong><?php echo $lang['dictionary_attack'] ?? 'Dictionary attack'; ?></strong> - <?php echo $lang['dictionary_attack_desc'] ?? 'trying popular words and phrases'; ?></li>
                <li><strong><?php echo $lang['brute_force'] ?? 'Brute force attack'; ?></strong> - <?php echo $lang['brute_force_desc'] ?? 'systematically trying all possible combinations'; ?></li>
                <li><strong><?php echo $lang['phishing'] ?? 'Phishing'; ?></strong> - <?php echo $lang['phishing_desc'] ?? 'posing as trusted sources to steal passwords'; ?></li>
            </ul>
            
            <p><?php echo $lang['stronger_password_better'] ?? 'The stronger the password, the harder it is to crack using these methods.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['time_to_crack'] ?? 'Time required to crack a password'; ?></h3>
            <p><?php echo $lang['password_complexity_impact'] ?? 'Password length and complexity directly affect the time needed to crack it:'; ?></p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['crack_time_1'] ?? '6-character password, only lowercase letters: a few seconds'; ?></li>
                <li><?php echo $lang['crack_time_2'] ?? '8-character password, lowercase and uppercase: a few hours'; ?></li>
                <li><?php echo $lang['crack_time_3'] ?? '10-character password, lowercase, uppercase and numbers: a few days'; ?></li>
                <li><?php echo $lang['crack_time_4'] ?? '12-character password, lowercase, uppercase, numbers and symbols: several years'; ?></li>
                <li><?php echo $lang['crack_time_5'] ?? '16-character password, lowercase, uppercase, numbers and symbols: millions of years'; ?></li>
            </ul>
        </div>
    </div>
</div>

<script>
    function copyPassword() {
        const passwordElement = document.getElementById('password');
        const passwordText = passwordElement.textContent;
        
        // Tworzenie tymczasowego elementu textarea
        const textArea = document.createElement('textarea');
        textArea.value = passwordText;
        document.body.appendChild(textArea);
        
        // Zaznaczenie i skopiowanie tekstu
        textArea.select();
        document.execCommand('copy');
        
        // Usunięcie tymczasowego elementu
        document.body.removeChild(textArea);
        
        // Informacja o skopiowaniu
        alert('<?php echo $lang['password_copied'] ?? 'Password has been copied to clipboard!'; ?>');
    }
</script>