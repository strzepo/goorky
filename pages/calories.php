<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Kalkulator Kalorii - Oblicz dzienne zapotrzebowanie | ToolsOnline';
$pageDescription = 'Darmowy kalkulator kalorii online - oblicz swoje dzienne zapotrzebowanie kaloryczne (BMR) na podstawie wieku, płci, wagi, wzrostu i poziomu aktywności.';

// Inicjalizacja zmiennych
$weight = '';
$height = '';
$age = '';
$gender = 'male';
$activity = 'moderate';
$calories = 0;
$hasResult = false;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculate_calories'])) {
    // Pobranie i walidacja danych
    $weight = sanitizeNumber($_POST['weight'] ?? '');
    $height = sanitizeNumber($_POST['height'] ?? '');
    $age = sanitizeNumber($_POST['age'] ?? '');
    $gender = sanitizeInput($_POST['gender'] ?? 'male');
    $activity = sanitizeInput($_POST['activity'] ?? 'moderate');
    
    // Walidacja płci i poziomu aktywności
    if (!in_array($gender, ['male', 'female'])) {
        $gender = 'male';
    }
    
    if (!in_array($activity, ['sedentary', 'light', 'moderate', 'active', 'very_active'])) {
        $activity = 'moderate';
    }
    
    // Obliczenie zapotrzebowania kalorycznego
    if ($weight > 0 && $height > 0 && $age > 0) {
        $calories = calculateCalories($weight, $height, $age, $gender, $activity);
        $hasResult = true;
    }
}

// Funkcja zwracająca opis poziomu aktywności
function getActivityDescription($activity) {
    $descriptions = [
        'sedentary' => 'Siedzący tryb życia, brak lub minimalna aktywność fizyczna',
        'light' => 'Lekka aktywność (lekkie ćwiczenia/sport 1-3 dni w tygodniu)',
        'moderate' => 'Umiarkowana aktywność (umiarkowane ćwiczenia/sport 3-5 dni w tygodniu)',
        'active' => 'Duża aktywność (intensywne ćwiczenia/sport 6-7 dni w tygodniu)',
        'very_active' => 'Bardzo duża aktywność (bardzo intensywne ćwiczenia/sport oraz praca fizyczna)'
    ];
    
    return $descriptions[$activity] ?? '';
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Kalkulator Kalorii</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Oblicz swoje dzienne zapotrzebowanie kaloryczne na podstawie wieku, płci, wagi, wzrostu i poziomu aktywności fizycznej. Wynik pomoże Ci w planowaniu diety i kontroli wagi.</p>
        
        <form method="POST" action="/calories" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="gender" class="block text-gray-700 font-medium mb-2">Płeć</label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="male" <?php echo ($gender === 'male') ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700">Mężczyzna</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="female" <?php echo ($gender === 'female') ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700">Kobieta</span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="age" class="block text-gray-700 font-medium mb-2">Wiek (lata)</label>
                    <input type="number" name="age" id="age" min="15" max="100" value="<?php echo htmlspecialchars($age); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="np. 30">
                </div>
                
                <div>
                    <label for="weight" class="block text-gray-700 font-medium mb-2">Waga (kg)</label>
                    <input type="number" name="weight" id="weight" min="30" max="300" step="0.1" value="<?php echo htmlspecialchars($weight); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="np. 70.5">
                </div>
                
                <div>
                    <label for="height" class="block text-gray-700 font-medium mb-2">Wzrost (cm)</label>
                    <input type="number" name="height" id="height" min="100" max="250" step="0.1" value="<?php echo htmlspecialchars($height); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="np. 175">
                </div>
                
                <div class="md:col-span-2">
                    <label for="activity" class="block text-gray-700 font-medium mb-2">Poziom aktywności fizycznej</label>
                    <select name="activity" id="activity" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="sedentary" <?php echo ($activity === 'sedentary') ? 'selected' : ''; ?>>Siedzący tryb życia (brak aktywności)</option>
                        <option value="light" <?php echo ($activity === 'light') ? 'selected' : ''; ?>>Lekka aktywność (1-3 razy w tygodniu)</option>
                        <option value="moderate" <?php echo ($activity === 'moderate') ? 'selected' : ''; ?>>Umiarkowana aktywność (3-5 razy w tygodniu)</option>
                        <option value="active" <?php echo ($activity === 'active') ? 'selected' : ''; ?>>Duża aktywność (6-7 razy w tygodniu)</option>
                        <option value="very_active" <?php echo ($activity === 'very_active') ? 'selected' : ''; ?>>Bardzo duża aktywność (2 razy dziennie)</option>
                    </select>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" name="calculate_calories" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">Oblicz kalorie</button>
            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Twoje dzienne zapotrzebowanie kaloryczne</h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="text-5xl font-bold text-blue-600"><?php echo number_format($calories, 0); ?></div>
            <div class="text-xl mt-2">kalorii dziennie</div>
        </div>
        
        <div class="bg-gray-100 p-6 rounded-lg mb-4">
            <h3 class="font-semibold mb-2">Twoje dane:</h3>
            <ul class="space-y-2">
                <li><strong>Płeć:</strong> <?php echo ($gender === 'male') ? 'Mężczyzna' : 'Kobieta'; ?></li>
                <li><strong>Wiek:</strong> <?php echo htmlspecialchars($age); ?> lat</li>
                <li><strong>Waga:</strong> <?php echo htmlspecialchars($weight); ?> kg</li>
                <li><strong>Wzrost:</strong> <?php echo htmlspecialchars($height); ?> cm</li>
                <li><strong>Poziom aktywności:</strong> <?php echo getActivityDescription($activity); ?></li>
            </ul>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold mb-2">Redukcja masy ciała</h3>
                <div class="text-xl font-bold text-blue-600"><?php echo number_format($calories * 0.8, 0); ?></div>
                <p class="text-sm text-gray-600 mt-1">kalorii dziennie</p>
                <p class="text-xs text-gray-500 mt-2">Redukcja o 20%</p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold mb-2">Utrzymanie masy ciała</h3>
                <div class="text-xl font-bold text-green-600"><?php echo number_format($calories, 0); ?></div>
                <p class="text-sm text-gray-600 mt-1">kalorii dziennie</p>
                <p class="text-xs text-gray-500 mt-2">Zbilansowana dieta</p>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold mb-2">Zwiększenie masy ciała</h3>
                <div class="text-xl font-bold text-yellow-600"><?php echo number_format($calories * 1.2, 0); ?></div>
                <p class="text-sm text-gray-600 mt-1">kalorii dziennie</p>
                <p class="text-xs text-gray-500 mt-2">Zwiększenie o 20%</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o zapotrzebowaniu kalorycznym -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Jak interpretować wyniki?</h2>
        
        <div class="space-y-4">
            <p>Wynik kalkulatora pokazuje Twoje szacunkowe dzienne zapotrzebowanie kaloryczne, czyli liczbę kalorii, jaką powinieneś spożywać każdego dnia, aby utrzymać obecną masę ciała.</p>
            
            <h3 class="text-xl font-semibold mt-4">Wskazówki dotyczące kontroli wagi:</h3>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Utrata wagi:</strong> Aby tracić około 0,5 kg tygodniowo, spożywaj o 500 kalorii mniej dziennie niż Twoje dzienne zapotrzebowanie.</li>
                <li><strong>Utrzymanie wagi:</strong> Spożywaj liczbę kalorii równą Twojemu dziennemu zapotrzebowaniu.</li>
                <li><strong>Przybranie na wadze:</strong> Aby przybierać około 0,5 kg tygodniowo, spożywaj o 500 kalorii więcej dziennie niż Twoje dzienne zapotrzebowanie.</li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Kalkulator podaje jedynie szacunkowe wartości. Rzeczywiste zapotrzebowanie kaloryczne może różnić się w zależności od indywidualnych czynników, takich jak metabolizm, stan zdrowia, warunki środowiskowe itp. Przed rozpoczęciem diety redukcyjnej lub zwiększającej masę ciała, zaleca się konsultację z lekarzem lub dietetykiem.</p>
            </div>
        </div>
    </div>
    
    <!-- Informacje o metodzie obliczania -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">O metodzie obliczania</h2>
        
        <div class="space-y-4">
            <p>Kalkulator wykorzystuje wzór Mifflin-St Jeor do obliczenia podstawowej przemiany materii (BMR - Basal Metabolic Rate), a następnie uwzględnia współczynnik aktywności fizycznej, aby uzyskać całkowite dzienne zapotrzebowanie kaloryczne.</p>
            
            <h3 class="text-lg font-semibold mt-4">Wzór Mifflin-St Jeor:</h3>
            
            <div class="bg-gray-100 p-4 rounded-lg">
                <p><strong>Dla mężczyzn:</strong> BMR = (10 × waga w kg) + (6,25 × wzrost w cm) - (5 × wiek w latach) + 5</p>
                <p><strong>Dla kobiet:</strong> BMR = (10 × waga w kg) + (6,25 × wzrost w cm) - (5 × wiek w latach) - 161</p>
            </div>
            
            <h3 class="text-lg font-semibold mt-4">Współczynniki aktywności fizycznej:</h3>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>Siedzący tryb życia:</strong> BMR × 1,2 (brak aktywności lub minimalna)</li>
                <li><strong>Lekka aktywność:</strong> BMR × 1,375 (lekkie ćwiczenia 1-3 razy w tygodniu)</li>
                <li><strong>Umiarkowana aktywność:</strong> BMR × 1,55 (umiarkowane ćwiczenia 3-5 razy w tygodniu)</li>
                <li><strong>Duża aktywność:</strong> BMR × 1,725 (intensywne ćwiczenia 6-7 razy w tygodniu)</li>
                <li><strong>Bardzo duża aktywność:</strong> BMR × 1,9 (bardzo intensywne ćwiczenia, praca fizyczna)</li>
            </ul>
            
            <p>Ten wzór jest uważany za jeden z najdokładniejszych do obliczania podstawowej przemiany materii bez profesjonalnych badań laboratoryjnych.</p>
        </div>
    </div>
</div>