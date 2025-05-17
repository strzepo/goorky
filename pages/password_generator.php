<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Generator Haseł - Twórz bezpieczne, losowe hasła | ToolsOnline';
$pageDescription = 'Darmowy generator haseł online - twórz bezpieczne, losowe hasła o wybranej długości i złożoności. Zwiększ bezpieczeństwo swoich kont internetowych.';

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
        $passwordStrength = 'Słabe';
        $strengthColor = 'text-red-600';
    } elseif ($strength <= 4) {
        $passwordStrength = 'Średnie';
        $strengthColor = 'text-yellow-600';
    } else {
        $passwordStrength = 'Silne';
        $strengthColor = 'text-green-600';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Generator Haseł</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Stwórz bezpieczne, losowe hasło dostosowane do Twoich wymagań. Wybierz długość hasła i rodzaje znaków, które mają być użyte.</p>
        
        <form method="POST" action="/password-generator" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="length" class="block text-gray-700 font-medium mb-2">Długość hasła</label>
                    <div class="flex items-center">
                        <input type="range" name="length" id="length" min="4" max="64" value="<?php echo htmlspecialchars($length); ?>" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer" oninput="document.getElementById('lengthValue').textContent = this.value">
                        <span id="lengthValue" class="ml-4 w-10 text-center font-medium"><?php echo htmlspecialchars($length); ?></span>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-700 font-medium mb-2">Opcje</label>
                    <div class="space-y-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_upper" <?php echo $useUpper ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2">Wielkie litery (A-Z)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_lower" <?php echo $useLower ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2">Małe litery (a-z)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_numbers" <?php echo $useNumbers ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2">Cyfry (0-9)</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="use_special" <?php echo $useSpecial ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2">Znaki specjalne (!@#$%^&*()_-+=<>?)</span>
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" name="generate_password" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">Generuj hasło</button>
            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Wygenerowane hasło</h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="w-full bg-gray-100 p-4 rounded-lg text-center relative mb-4">
                <code id="password" class="text-2xl font-mono break-all"><?php echo htmlspecialchars($password); ?></code>
                <button onclick="copyPassword()" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition" title="Kopiuj do schowka">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path>
                    </svg>
                </button>
            </div>
            
            <div class="text-lg <?php echo $strengthColor; ?>">
                Siła hasła: <strong><?php echo $passwordStrength; ?></strong>
            </div>
        </div>
        
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Szczegóły hasła:</h3>
            <ul class="space-y-1">
                <li><strong>Długość:</strong> <?php echo strlen($password); ?> znaków</li>
                <li><strong>Zawiera wielkie litery:</strong> <?php echo preg_match('/[A-Z]/', $password) ? 'Tak' : 'Nie'; ?></li>
                <li><strong>Zawiera małe litery:</strong> <?php echo preg_match('/[a-z]/', $password) ? 'Tak' : 'Nie'; ?></li>
                <li><strong>Zawiera cyfry:</strong> <?php echo preg_match('/[0-9]/', $password) ? 'Tak' : 'Nie'; ?></li>
                <li><strong>Zawiera znaki specjalne:</strong> <?php echo preg_match('/[^a-zA-Z0-9]/', $password) ? 'Tak' : 'Nie'; ?></li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o hasłach -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Jak stworzyć silne hasło?</h2>
        
        <div class="space-y-4">
            <p>Silne hasło jest kluczowym elementem zabezpieczenia Twoich kont online. Oto kilka wskazówek dotyczących tworzenia bezpiecznych haseł:</p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Używaj długich haseł</strong> - hasło powinno mieć co najmniej 12 znaków. Im dłuższe, tym lepsze.</li>
                <li><strong>Różnorodność znaków</strong> - używaj kombinacji wielkich i małych liter, cyfr oraz znaków specjalnych.</li>
                <li><strong>Unikaj przewidywalnych wzorców</strong> - nie używaj sekwencji typu "123456" czy "qwerty".</li>
                <li><strong>Unikaj osobistych informacji</strong> - nie używaj imion, dat urodzenia, numerów telefonów, które są łatwe do odgadnięcia.</li>
                <li><strong>Używaj różnych haseł</strong> - każde konto powinno mieć unikalne hasło.</li>
                <li><strong>Regularnie zmieniaj hasła</strong> - zwłaszcza te do ważnych kont.</li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Nie zapisuj haseł w niezabezpieczonych miejscach. Rozważ użycie menedżera haseł, który bezpiecznie przechowa wszystkie Twoje hasła.</p>
            </div>
        </div>
    </div>
    
    <!-- Dodatkowe informacje -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Dlaczego bezpieczne hasła są ważne?</h2>
        
        <div class="space-y-4">
            <p>Bezpieczne hasła stanowią pierwszą linię obrony przed nieautoryzowanym dostępem do Twoich kont online. Oto dlaczego są tak ważne:</p>
            
            <h3 class="text-xl font-semibold mt-4">Ochrona przed atakami</h3>
            <p>Hakerzy używają różnych metod, aby złamać hasła:</p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Atak słownikowy</strong> - wypróbowywanie popularnych słów i fraz</li>
                <li><strong>Atak typu brute force</strong> - systematyczne wypróbowywanie wszystkich możliwych kombinacji znaków</li>
                <li><strong>Phishing</strong> - podszywanie się pod zaufane źródła, aby wyłudzić hasła</li>
            </ul>
            
            <p>Im silniejsze hasło, tym trudniej je złamać za pomocą tych metod.</p>
            
            <h3 class="text-xl font-semibold mt-4">Czas potrzebny do złamania hasła</h3>
            <p>Długość i złożoność hasła bezpośrednio wpływają na czas potrzebny do jego złamania:</p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li>Hasło 6 znaków, tylko małe litery: kilka sekund</li>
                <li>Hasło 8 znaków, małe i wielkie litery: kilka godzin</li>
                <li>Hasło 10 znaków, małe, wielkie litery i cyfry: kilka dni</li>
                <li>Hasło 12 znaków, małe, wielkie litery, cyfry i znaki specjalne: kilka lat</li>
                <li>Hasło 16 znaków, małe, wielkie litery, cyfry i znaki specjalne: miliony lat</li>
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
        alert('Hasło zostało skopiowane do schowka!');
    }
</script>