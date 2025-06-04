<?php
// Page title and description
$pageTitle = $lang['units_page_title'] ?? 'Unit Converter - Convert Length, Weight, and Temperature | Goorky.com';
$pageDescription = $lang['units_page_description'] ?? 'Free online unit converter - easily convert length (m, cm, km, inches, feet), weight (kg, g, pounds), and temperature (°C, °F, K) in both directions.';

// Inicjalizacja zmiennych
$value = '';
$from = '';
$to = '';
$type = 'length';
$result = '';
$hasResult = false;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdź czy zmieniono typ konwersji
    if (isset($_POST['type']) && !isset($_POST['action_type'])) {
        $type = sanitizeInput($_POST['type'] ?? 'length');
    }
    
    // Sprawdź czy wykonano konwersję
    if (isset($_POST['action_type']) && $_POST['action_type'] === 'convert_units') {
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
}

// Function returning full unit name (English fallback)
function getUnitFullName($unit) {
    global $lang;
    $unitNames = [
        // Length
        'mm' => $lang['unit_mm'] ?? 'millimeter',
        'cm' => $lang['unit_cm'] ?? 'centimeter',
        'm' => $lang['unit_m'] ?? 'meter',
        'km' => $lang['unit_km'] ?? 'kilometer',
        'in' => $lang['unit_in'] ?? 'inch',
        'ft' => $lang['unit_ft'] ?? 'foot',
        'yd' => $lang['unit_yd'] ?? 'yard',
        'mi' => $lang['unit_mi'] ?? 'mile',
        // Weight
        'mg' => $lang['unit_mg'] ?? 'milligram',
        'g' => $lang['unit_g'] ?? 'gram',
        'kg' => $lang['unit_kg'] ?? 'kilogram',
        'oz' => $lang['unit_oz'] ?? 'ounce',
        'lb' => $lang['unit_lb'] ?? 'pound',
        'st' => $lang['unit_st'] ?? 'stone',
        // Temperature
        'C' => $lang['unit_C'] ?? 'degree Celsius',
        'F' => $lang['unit_F'] ?? 'degree Fahrenheit',
        'K' => $lang['unit_K'] ?? 'kelvin'
    ];
    return $unitNames[$unit] ?? $unit;
}

// Funkcja zwracająca symbol jednostki
function getUnitSymbol($unit) {
    global $lang;
    
    $unitSymbols = [
        // Długość
        'mm' => $lang['symbol_mm'] ?? 'mm',
        'cm' => $lang['symbol_cm'] ?? 'cm',
        'm' => $lang['symbol_m'] ?? 'm',
        'km' => $lang['symbol_km'] ?? 'km',
        'in' => $lang['symbol_in'] ?? 'in',
        'ft' => $lang['symbol_ft'] ?? 'ft',
        'yd' => $lang['symbol_yd'] ?? 'yd',
        'mi' => $lang['symbol_mi'] ?? 'mi',
        
        // Waga
        'mg' => $lang['symbol_mg'] ?? 'mg',
        'g' => $lang['symbol_g'] ?? 'g',
        'kg' => $lang['symbol_kg'] ?? 'kg',
        'oz' => $lang['symbol_oz'] ?? 'oz',
        'lb' => $lang['symbol_lb'] ?? 'lb',
        'st' => $lang['symbol_st'] ?? 'st',
        
        // Temperatura
        'C' => $lang['symbol_C'] ?? '°C',
        'F' => $lang['symbol_F'] ?? '°F',
        'K' => $lang['symbol_K'] ?? 'K'
    ];
    
    return $unitSymbols[$unit] ?? $unit;
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['units_converter'] ?? 'Unit Converter'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['units_intro'] ?? 'Quickly and easily convert between various measurement units... Choose the conversion type, source unit, target unit, and enter a value.'; ?></p>
        
        <form method="POST" action="/units" class="space-y-6" id="unitForm">
            <input type="hidden" name="action_type" value="convert_units">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="type" class="block text-gray-700 font-medium mb-2"><?php echo $lang['conversion_type'] ?? 'Conversion type'; ?></label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($type === 'length') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                            <input type="radio" name="type" value="length" <?php echo ($type === 'length') ? 'checked' : ''; ?> class="hidden">
                            <div class="text-center">
                                <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                <span class="block mt-2 font-medium"><?php echo $lang['length'] ?? 'Length'; ?></span>
                            </div>
                        </label>
                        
                        <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($type === 'weight') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                            <input type="radio" name="type" value="weight" <?php echo ($type === 'weight') ? 'checked' : ''; ?> class="hidden">
                            <div class="text-center">
                                <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                                <span class="block mt-2 font-medium"><?php echo $lang['weight'] ?? 'Weight'; ?></span>
                            </div>
                        </label>
                        
                        <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($type === 'temperature') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                            <input type="radio" name="type" value="temperature" <?php echo ($type === 'temperature') ? 'checked' : ''; ?> class="hidden">
                            <div class="text-center">
                                <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                <span class="block mt-2 font-medium"><?php echo $lang['temperature'] ?? 'Temperature'; ?></span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="value" class="block text-gray-700 font-medium mb-2"><?php echo $lang['value'] ?? 'Value'; ?></label>
                    <input type="number" name="value" id="value" step="any" value="<?php echo htmlspecialchars($value); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="<?php echo $lang['enter_value'] ?? 'Enter value'; ?>">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="from" class="block text-gray-700 font-medium mb-2"><?php echo $lang['from'] ?? 'From'; ?></label>
                        <select name="from" id="from" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if ($type === 'length'): ?>
                                <option value="mm" <?php echo ($from === 'mm') ? 'selected' : ''; ?>><?php echo $lang['millimeter'] ?? 'Millimeter (mm)'; ?></option>
                                <option value="cm" <?php echo ($from === 'cm') ? 'selected' : ''; ?>><?php echo $lang['centimeter'] ?? 'Centimeter (cm)'; ?></option>
                                <option value="m" <?php echo ($from === 'm') ? 'selected' : ''; ?>><?php echo $lang['meter'] ?? 'Meter (m)'; ?></option>
                                <option value="km" <?php echo ($from === 'km') ? 'selected' : ''; ?>><?php echo $lang['kilometer'] ?? 'Kilometer (km)'; ?></option>
                                <option value="in" <?php echo ($from === 'in') ? 'selected' : ''; ?>><?php echo $lang['inch'] ?? 'Inch (in)'; ?></option>
                                <option value="ft" <?php echo ($from === 'ft') ? 'selected' : ''; ?>><?php echo $lang['foot'] ?? 'Foot (ft)'; ?></option>
                                <option value="yd" <?php echo ($from === 'yd') ? 'selected' : ''; ?>><?php echo $lang['yard'] ?? 'Yard (yd)'; ?></option>
                                <option value="mi" <?php echo ($from === 'mi') ? 'selected' : ''; ?>><?php echo $lang['mile'] ?? 'Mile (mi)'; ?></option>
                            <?php elseif ($type === 'weight'): ?>
                                <option value="mg" <?php echo ($from === 'mg') ? 'selected' : ''; ?>><?php echo $lang['milligram'] ?? 'Milligram (mg)'; ?></option>
                                <option value="g" <?php echo ($from === 'g') ? 'selected' : ''; ?>><?php echo $lang['gram'] ?? 'Gram (g)'; ?></option>
                                <option value="kg" <?php echo ($from === 'kg') ? 'selected' : ''; ?>><?php echo $lang['kilogram'] ?? 'Kilogram (kg)'; ?></option>
                                <option value="oz" <?php echo ($from === 'oz') ? 'selected' : ''; ?>><?php echo $lang['ounce'] ?? 'Ounce (oz)'; ?></option>
                                <option value="lb" <?php echo ($from === 'lb') ? 'selected' : ''; ?>><?php echo $lang['pound'] ?? 'Pound (lb)'; ?></option>
                                <option value="st" <?php echo ($from === 'st') ? 'selected' : ''; ?>><?php echo $lang['stone'] ?? 'Stone (st)'; ?></option>
                            <?php elseif ($type === 'temperature'): ?>
                                <option value="C" <?php echo ($from === 'C') ? 'selected' : ''; ?>><?php echo $lang['celsius'] ?? 'Celsius (°C)'; ?></option>
                                <option value="F" <?php echo ($from === 'F') ? 'selected' : ''; ?>><?php echo $lang['fahrenheit'] ?? 'Fahrenheit (°F)'; ?></option>
                                <option value="K" <?php echo ($from === 'K') ? 'selected' : ''; ?>><?php echo $lang['kelvin'] ?? 'Kelvin (K)'; ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="to" class="block text-gray-700 font-medium mb-2"><?php echo $lang['to'] ?? 'To'; ?></label>
                        <select name="to" id="to" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if ($type === 'length'): ?>
                                <option value="mm" <?php echo ($to === 'mm') ? 'selected' : ''; ?>><?php echo $lang['millimeter'] ?? 'Millimeter (mm)'; ?></option>
                                <option value="cm" <?php echo ($to === 'cm') ? 'selected' : ''; ?>><?php echo $lang['centimeter'] ?? 'Centimeter (cm)'; ?></option>
                                <option value="m" <?php echo ($to === 'm') ? 'selected' : ''; ?>><?php echo $lang['meter'] ?? 'Meter (m)'; ?></option>
                                <option value="km" <?php echo ($to === 'km') ? 'selected' : ''; ?>><?php echo $lang['kilometer'] ?? 'Kilometer (km)'; ?></option>
                                <option value="in" <?php echo ($to === 'in') ? 'selected' : ''; ?>><?php echo $lang['inch'] ?? 'Inch (in)'; ?></option>
                                <option value="ft" <?php echo ($to === 'ft') ? 'selected' : ''; ?>><?php echo $lang['foot'] ?? 'Foot (ft)'; ?></option>
                                <option value="yd" <?php echo ($to === 'yd') ? 'selected' : ''; ?>><?php echo $lang['yard'] ?? 'Yard (yd)'; ?></option>
                                <option value="mi" <?php echo ($to === 'mi') ? 'selected' : ''; ?>><?php echo $lang['mile'] ?? 'Mile (mi)'; ?></option>
                            <?php elseif ($type === 'weight'): ?>
                                <option value="mg" <?php echo ($to === 'mg') ? 'selected' : ''; ?>><?php echo $lang['milligram'] ?? 'Milligram (mg)'; ?></option>
                                <option value="g" <?php echo ($to === 'g') ? 'selected' : ''; ?>><?php echo $lang['gram'] ?? 'Gram (g)'; ?></option>
                                <option value="kg" <?php echo ($to === 'kg') ? 'selected' : ''; ?>><?php echo $lang['kilogram'] ?? 'Kilogram (kg)'; ?></option>
                                <option value="oz" <?php echo ($to === 'oz') ? 'selected' : ''; ?>><?php echo $lang['ounce'] ?? 'Ounce (oz)'; ?></option>
                                <option value="lb" <?php echo ($to === 'lb') ? 'selected' : ''; ?>><?php echo $lang['pound'] ?? 'Pound (lb)'; ?></option>
                                <option value="st" <?php echo ($to === 'st') ? 'selected' : ''; ?>><?php echo $lang['stone'] ?? 'Stone (st)'; ?></option>
                            <?php elseif ($type === 'temperature'): ?>
                                <option value="C" <?php echo ($to === 'C') ? 'selected' : ''; ?>><?php echo $lang['celsius'] ?? 'Celsius (°C)'; ?></option>
                                <option value="F" <?php echo ($to === 'F') ? 'selected' : ''; ?>><?php echo $lang['fahrenheit'] ?? 'Fahrenheit (°F)'; ?></option>
                                <option value="K" <?php echo ($to === 'K') ? 'selected' : ''; ?>><?php echo $lang['kelvin'] ?? 'Kelvin (K)'; ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="button" class="trigger-popup bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['convert'] ?? 'Convert'; ?></button>
            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['conversion_result'] ?? 'Conversion Result'; ?></h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="text-3xl font-bold text-blue-600">
                <?php echo htmlspecialchars($value) . ' ' . getUnitSymbol($from) . ' = ' . number_format($result, 6) . ' ' . getUnitSymbol($to); ?>
            </div>
            <div class="text-lg mt-2 text-gray-600">
                <?php echo htmlspecialchars($value) . ' ' . getUnitFullName($from) . ' ' . ($lang['equals'] ?? 'equals') . ' ' . number_format($result, 6) . ' ' . getUnitFullName($to); ?>
            </div>
        </div>
        
        <div class="bg-gray-100 p-4 rounded-lg">
            <h3 class="font-semibold mb-2"><?php echo $lang['conversion_details'] ?? 'Conversion Details:'; ?></h3>
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
    
    <!-- Pozostała część kodu pozostaje bez zmian... -->
    
</div>

<script>
function submitTypeChange() {
    // Wyczyść wartość i wyślij formularz dla zmiany typu
    document.getElementById('value').value = '';
    
    // Utwórz ukryty formularz tylko do zmiany typu
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/units';
    
    // Dodaj wybrany typ
    const typeInput = document.createElement('input');
    typeInput.type = 'hidden';
    typeInput.name = 'type';
    typeInput.value = document.querySelector('input[name="type"]:checked').value;
    
    form.appendChild(typeInput);
    document.body.appendChild(form);
    form.submit();
}

// Funkcja do wykonania konwersji (wywoływana po zakończeniu popup-a)
function executeConversion() {
    const form = document.getElementById('unitForm');
    
    // Dodaj ukryte pole convert_units
    let convertInput = form.querySelector('input[name="convert_units"]');
    if (!convertInput) {
        convertInput = document.createElement('input');
        convertInput.type = 'hidden';
        convertInput.name = 'convert_units';
        convertInput.value = '1';
        form.appendChild(convertInput);
    }
    
    // Wyślij formularz
    form.submit();
}

// Event listener dla przycisków typu konwersji
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            submitTypeChange();
        });
    });
    
    // NIE dodawaj event listenera do trigger-popup
    // Pozwól istniejącemu systemowi popup obsłużyć to
    // System popup powinien wywołać executeConversion() po odliczeniu
});

// Udostępnij funkcję globalnie, żeby system popup mógł ją wywołać
window.executeConversion = executeConversion;
</script>