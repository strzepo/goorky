<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Konwerter Jednostek - Przeliczaj długość, wagę i temperaturę | ToolsOnline';
$pageDescription = 'Darmowy konwerter jednostek online - łatwo przeliczaj długość (m, cm, km, cale, stopy), wagę (kg, g, funty) i temperaturę (°C, °F, K) w obie strony.';

// Inicjalizacja zmiennych
$value = '';
$from = '';
$to = '';
$type = 'length';
$result = '';
$hasResult = false;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['convert_units'])) {
    // Pobranie i walidacja danych
    $value = sanitizeNumber($_POST['value'] ?? '');
    $from = sanitizeInput($_POST['from'] ?? '');
    $to = sanitizeInput($_POST['to'] ?? '');
    $type = sanitizeInput($_POST['type'] ?? 'length');
    
    // Upewnienie się, że typ konwersji jest prawidłowy
    if (!in_array($type, ['length', 'weight', 'temperature'])) {
        $type = 'length';
    }
    
    // Upewnienie się, że jednostki są prawidłowe dla danego typu
    $validUnits = [
        'length' => ['mm', 'cm', 'm', 'km', 'in', 'ft', 'yd', 'mi'],
        'weight' => ['mg', 'g', 'kg', 'oz', 'lb', 'st'],
        'temperature' => ['C', 'F', 'K']
    ];
    
    if (!in_array($from, $validUnits[$type]) || !in_array($to, $validUnits[$type])) {
        // Ustaw domyślne jednostki dla danego typu
        $from = $validUnits[$type][0];
        $to = $validUnits[$type][1];
    }
    
    // Wykonaj konwersję
    if ($value !== '' && is_numeric($value)) {
        $result = convertUnits($value, $from, $to, $type);
        $hasResult = true;
    }
}

// Funkcja zwracająca pełną nazwę jednostki
function getUnitFullName($unit) {
    $unitNames = [
        // Długość
        'mm' => 'milimetr',
        'cm' => 'centymetr',
        'm' => 'metr',
        'km' => 'kilometr',
        'in' => 'cal',
        'ft' => 'stopa',
        'yd' => 'jard',
        'mi' => 'mila',
        
        // Waga
        'mg' => 'miligram',
        'g' => 'gram',
        'kg' => 'kilogram',
        'oz' => 'uncja',
        'lb' => 'funt',
        'st' => 'kamień',
        
        // Temperatura
        'C' => 'stopień Celsjusza',
        'F' => 'stopień Fahrenheita',
        'K' => 'kelwin'
    ];
    
    return $unitNames[$unit] ?? $unit;
}

// Funkcja zwracająca symbol jednostki
function getUnitSymbol($unit) {
    $unitSymbols = [
        // Długość
        'mm' => 'mm',
        'cm' => 'cm',
        'm' => 'm',
        'km' => 'km',
        'in' => 'in',
        'ft' => 'ft',
        'yd' => 'yd',
        'mi' => 'mi',
        
        // Waga
        'mg' => 'mg',
        'g' => 'g',
        'kg' => 'kg',
        'oz' => 'oz',
        'lb' => 'lb',
        'st' => 'st',
        
        // Temperatura
        'C' => '°C',
        'F' => '°F',
        'K' => 'K'
    ];
    
    return $unitSymbols[$unit] ?? $unit;
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Konwerter Jednostek</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Szybko i łatwo konwertuj między różnymi jednostkami miary. Wybierz typ konwersji, jednostkę źródłową, jednostkę docelową i wprowadź wartość.</p>
        
        <form method="POST" action="/units" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="type" class="block text-gray-700 font-medium mb-2">Typ konwersji</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($type === 'length') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                            <input type="radio" name="type" value="length" <?php echo ($type === 'length') ? 'checked' : ''; ?> class="hidden" onchange="this.form.submit()">
                            <div class="text-center">
                                <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="block mt-2 font-medium">Długość</span>
                            </div>
                        </label>
                        
                        <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($type === 'weight') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                            <input type="radio" name="type" value="weight" <?php echo ($type === 'weight') ? 'checked' : ''; ?> class="hidden" onchange="this.form.submit()">
                            <div class="text-center">
                                <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                                <span class="block mt-2 font-medium">Waga</span>
                            </div>
                        </label>
                        
                        <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($type === 'temperature') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                            <input type="radio" name="type" value="temperature" <?php echo ($type === 'temperature') ? 'checked' : ''; ?> class="hidden" onchange="this.form.submit()">
                            <div class="text-center">
                                <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="block mt-2 font-medium">Temperatura</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="value" class="block text-gray-700 font-medium mb-2">Wartość</label>
                    <input type="number" name="value" id="value" step="any" value="<?php echo htmlspecialchars($value); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Wprowadź wartość">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="from" class="block text-gray-700 font-medium mb-2">Z</label>
                        <select name="from" id="from" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if ($type === 'length'): ?>
                                <option value="mm" <?php echo ($from === 'mm') ? 'selected' : ''; ?>>Milimetr (mm)</option>
                                <option value="cm" <?php echo ($from === 'cm') ? 'selected' : ''; ?>>Centymetr (cm)</option>
                                <option value="m" <?php echo ($from === 'm') ? 'selected' : ''; ?>>Metr (m)</option>
                                <option value="km" <?php echo ($from === 'km') ? 'selected' : ''; ?>>Kilometr (km)</option>
                                <option value="in" <?php echo ($from === 'in') ? 'selected' : ''; ?>>Cal (in)</option>
                                <option value="ft" <?php echo ($from === 'ft') ? 'selected' : ''; ?>>Stopa (ft)</option>
                                <option value="yd" <?php echo ($from === 'yd') ? 'selected' : ''; ?>>Jard (yd)</option>
                                <option value="mi" <?php echo ($from === 'mi') ? 'selected' : ''; ?>>Mila (mi)</option>
                            <?php elseif ($type === 'weight'): ?>
                                <option value="mg" <?php echo ($from === 'mg') ? 'selected' : ''; ?>>Miligram (mg)</option>
                                <option value="g" <?php echo ($from === 'g') ? 'selected' : ''; ?>>Gram (g)</option>
                                <option value="kg" <?php echo ($from === 'kg') ? 'selected' : ''; ?>>Kilogram (kg)</option>
                                <option value="oz" <?php echo ($from === 'oz') ? 'selected' : ''; ?>>Uncja (oz)</option>
                                <option value="lb" <?php echo ($from === 'lb') ? 'selected' : ''; ?>>Funt (lb)</option>
                                <option value="st" <?php echo ($from === 'st') ? 'selected' : ''; ?>>Kamień (st)</option>
                            <?php elseif ($type === 'temperature'): ?>
                                <option value="C" <?php echo ($from === 'C') ? 'selected' : ''; ?>>Celsjusz (°C)</option>
                                <option value="F" <?php echo ($from === 'F') ? 'selected' : ''; ?>>Fahrenheit (°F)</option>
                                <option value="K" <?php echo ($from === 'K') ? 'selected' : ''; ?>>Kelwin (K)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="to" class="block text-gray-700 font-medium mb-2">Na</label>
                        <select name="to" id="to" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if ($type === 'length'): ?>
                                <option value="mm" <?php echo ($to === 'mm') ? 'selected' : ''; ?>>Milimetr (mm)</option>
                                <option value="cm" <?php echo ($to === 'cm') ? 'selected' : ''; ?>>Centymetr (cm)</option>
                                <option value="m" <?php echo ($to === 'm') ? 'selected' : ''; ?>>Metr (m)</option>
                                <option value="km" <?php echo ($to === 'km') ? 'selected' : ''; ?>>Kilometr (km)</option>
                                <option value="in" <?php echo ($to === 'in') ? 'selected' : ''; ?>>Cal (in)</option>
                                <option value="ft" <?php echo ($to === 'ft') ? 'selected' : ''; ?>>Stopa (ft)</option>
                                <option value="yd" <?php echo ($to === 'yd') ? 'selected' : ''; ?>>Jard (yd)</option>
                                <option value="mi" <?php echo ($to === 'mi') ? 'selected' : ''; ?>>Mila (mi)</option>
                            <?php elseif ($type === 'weight'): ?>
                                <option value="mg" <?php echo ($to === 'mg') ? 'selected' : ''; ?>>Miligram (mg)</option>
                                <option value="g" <?php echo ($to === 'g') ? 'selected' : ''; ?>>Gram (g)</option>
                                <option value="kg" <?php echo ($to === 'kg') ? 'selected' : ''; ?>>Kilogram (kg)</option>
                                <option value="oz" <?php echo ($to === 'oz') ? 'selected' : ''; ?>>Uncja (oz)</option>
                                <option value="lb" <?php echo ($to === 'lb') ? 'selected' : ''; ?>>Funt (lb)</option>
                                <option value="st" <?php echo ($to === 'st') ? 'selected' : ''; ?>>Kamień (st)</option>
                            <?php elseif ($type === 'temperature'): ?>
                                <option value="C" <?php echo ($to === 'C') ? 'selected' : ''; ?>>Celsjusz (°C)</option>
                                <option value="F" <?php echo ($to === 'F') ? 'selected' : ''; ?>>Fahrenheit (°F)</option>
                                <option value="K" <?php echo ($to === 'K') ? 'selected' : ''; ?>>Kelwin (K)</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" name="convert_units" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">Konwertuj</button>
            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Wynik konwersji</h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="text-3xl font-bold text-blue-600">
                <?php echo htmlspecialchars($value) . ' ' . getUnitSymbol($from) . ' = ' . number_format($result, 6) . ' ' . getUnitSymbol($to); ?>
            </div>
            <div class="text-lg mt-2 text-gray-600">
                <?php echo htmlspecialchars($value) . ' ' . getUnitFullName($from) . ' to ' . number_format($result, 6) . ' ' . getUnitFullName($to); ?>
            </div>
        </div>
        
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2">Szczegóły konwersji:</h3>
            <p>1 <?php echo getUnitFullName($from); ?> = <?php 
                $conversionFactor = convertUnits(1, $from, $to, $type);
                echo number_format($conversionFactor, 6) . ' ' . getUnitFullName($to); 
            ?></p>
            <p>1 <?php echo getUnitFullName($to); ?> = <?php 
                $reverseConversionFactor = convertUnits(1, $to, $from, $type);
                echo number_format($reverseConversionFactor, 6) . ' ' . getUnitFullName($from); 
            ?></p>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o jednostkach -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Tabele konwersji</h2>
        
        <div class="space-y-6">
            <?php if ($type === 'length'): ?>
                <h3 class="text-xl font-semibold">Długość</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jednostka</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Milimetr (mm)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Centymetr (cm)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metr (m)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilometr (km)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cal (in)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stopa (ft)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jard (yd)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mila (mi)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 mm</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.03937</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.003281</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001094</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.0000006214</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 cm</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">10</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.01</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.00001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.3937</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.03281</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.01094</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000006214</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 m</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1000</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">100</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">39.37</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">3.281</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1.094</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.0006214</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($type === 'weight'): ?>
                <h3 class="text-xl font-semibold">Waga</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jednostka</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Miligram (mg)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gram (g)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kilogram (kg)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uncja (oz)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Funt (lb)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kamień (st)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 mg</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000035</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.0000022</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.00000016</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 g</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1000</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.03527</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.002205</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000157</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 kg</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1000000</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1000</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">35.274</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">2.205</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.157</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php elseif ($type === 'temperature'): ?>
                <h3 class="text-xl font-semibold">Temperatura</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konwersja</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wzór</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Przykład</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Celsjusz na Fahrenheit</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">°F = (°C × 9/5) + 32</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">20°C = 68°F</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Fahrenheit na Celsjusz</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">°C = (°F - 32) × 5/9</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">68°F = 20°C</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Celsjusz na Kelwin</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">K = °C + 273.15</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">20°C = 293.15K</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Kelwin na Celsjusz</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">°C = K - 273.15</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">293.15K = 20°C</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Fahrenheit na Kelwin</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">K = (°F - 32) × 5/9 + 273.15</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">68°F = 293.15K</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Kelwin na Fahrenheit</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">°F = (K - 273.15) × 9/5 + 32</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">293.15K = 68°F</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Informacje o jednostkach -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Informacje o jednostkach</h2>
        
        <div class="space-y-6">
            <?php if ($type === 'length'): ?>
                <h3 class="text-xl font-semibold">Jednostki długości</h3>
                <p>Jednostki długości są używane do pomiaru odległości między dwoma punktami. Istnieją różne systemy jednostek, w tym metryczny i imperialny.</p>
                
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>System metryczny:</strong> Milimetr (mm), centymetr (cm), metr (m), kilometr (km)</li>
                    <li><strong>System imperialny:</strong> Cal (in), stopa (ft), jard (yd), mila (mi)</li>
                </ul>
                
                <p>Podstawową jednostką w systemie metrycznym jest metr (m), podczas gdy w systemie imperialnym używa się różnych jednostek podstawowych w zależności od kontekstu.</p>
            <?php elseif ($type === 'weight'): ?>
                <h3 class="text-xl font-semibold">Jednostki wagi</h3>
                <p>Jednostki wagi (masy) są używane do określenia ilości materii w obiekcie. Podobnie jak w przypadku długości, istnieją dwa główne systemy: metryczny i imperialny.</p>
                
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>System metryczny:</strong> Miligram (mg), gram (g), kilogram (kg)</li>
                    <li><strong>System imperialny:</strong> Uncja (oz), funt (lb), kamień (st)</li>
                </ul>
                
                <p>Podstawową jednostką w systemie metrycznym jest kilogram (kg), podczas gdy w systemie imperialnym najczęściej używa się funta (lb).</p>
            <?php elseif ($type === 'temperature'): ?>
                <h3 class="text-xl font-semibold">Jednostki temperatury</h3>
                <p>Temperatura jest miarą energii kinetycznej cząsteczek. Istnieją trzy główne skale temperatury:</p>
                
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong>Skala Celsjusza (°C):</strong> Używana w większości krajów świata. Punkt zamarzania wody to 0°C, a punkt wrzenia to 100°C.</li>
                    <li><strong>Skala Fahrenheita (°F):</strong> Używana głównie w USA. Punkt zamarzania wody to 32°F, a punkt wrzenia to 212°F.</li>
                    <li><strong>Skala Kelvina (K):</strong> Używana w nauce. Jest to skala absolutna, gdzie 0K to zero absolutne (-273.15°C). Nie używa się znaku stopnia.</li>
                </ul>
                
                <p>Skala Kelvina jest skalą absolutną, co oznacza, że 0K to najniższa możliwa temperatura teoretyczna (zero absolutne), przy której cząsteczki nie mają energii kinetycznej.</p>
            <?php endif; ?>
        </div>
    </div>
</div>