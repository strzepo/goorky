<?php
// Page title and description
$pageTitle = $lang['units_page_title'] ?? 'Unit Converter - Convert Length, Weight, and Temperature | Goorky.com';
$pageDescription = $lang['units_page_description'] ?? 'Free online unit converter - easily convert length (m, cm, km, inches, feet), weight (kg, g, pounds), and temperature (°C, °F, K) in both directions.';

// Inicjalizacja zmiennych - MUSI BYĆ NA POCZĄTKU
$value = '';
$from = '';
$to = '';
$type = 'length'; // Domyślnie zawsze length
$result = '';
$hasResult = false;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sprawdź czy zmieniono typ konwersji
    if (isset($_POST['type']) && !isset($_POST['action_type']) && !isset($_POST['convert_units'])) {
        $type = sanitizeInput($_POST['type'] ?? 'length');
        
        // Upewnienie się, że typ konwersji jest prawidłowy
        if (!in_array($type, ['length', 'weight', 'temperature'])) {
            $type = 'length';
        }
    }
    
    // Sprawdź czy wykonano konwersję
    if (isset($_POST['convert_units']) || (isset($_POST['action_type']) && $_POST['action_type'] === 'convert_units')) {
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
            <input type="hidden" name="convert_units" value="">
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
    
    <!-- Call to Action Section -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-lg shadow-md p-6 mb-8">
        <div class="text-center">
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <span class="inline-block mr-2">💙</span>
                    <?php echo $lang['cta_heading'] ?? 'Enjoyed using our Free Tools?'; ?>
                </h3>
                <p class="text-gray-600">
                    <?php echo $lang['cta_description'] ?? 'Help others discover this free tool! Share it with your friends or support our work with a small donation.'; ?>
                </p>
            </div>
            
            <!-- Social Media Share Buttons -->
            <div class="flex justify-center gap-2 mb-4 flex-wrap">
                <a href="https://x.com/intent/tweet?text=<?php echo urlencode(($lang['cta_tweet_text'] ?? 'Check out this awesome Unit Converter! Convert length, weight, and temperature units instantly') . ' - '); ?>https://goorky.com/units" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors text-sm">
                    <i class="fab fa-x-twitter mr-2"></i>
                    <?php echo $lang['share_on_x'] ?? 'X'; ?>
                </a>
                
                <a href="https://www.facebook.com/sharer/sharer.php?u=https://goorky.com/units" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fab fa-facebook mr-2"></i>
                    <?php echo $lang['share_on_facebook'] ?? 'Facebook'; ?>
                </a>
                
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=https://goorky.com/units&title=<?php echo urlencode($lang['cta_linkedin_title'] ?? 'Free Unit Converter Tool'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors text-sm">
                    <i class="fab fa-linkedin mr-2"></i>
                    <?php echo $lang['share_on_linkedin'] ?? 'LinkedIn'; ?>
                </a>
                
                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(($lang['cta_whatsapp_text'] ?? 'Check out this free Unit Converter:') . ' https://goorky.com/units'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                    <i class="fab fa-whatsapp mr-2"></i>
                    <?php echo $lang['share_on_whatsapp'] ?? 'WhatsApp'; ?>
                </a>
                
                <a href="https://t.me/share/url?url=https://goorky.com/units&text=<?php echo urlencode($lang['cta_telegram_text'] ?? 'Check out this free Unit Converter tool!'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors text-sm">
                    <i class="fab fa-telegram mr-2"></i>
                    <?php echo $lang['share_on_telegram'] ?? 'Telegram'; ?>
                </a>
                
                <a href="https://pinterest.com/pin/create/button/?url=https://goorky.com/units&description=<?php echo urlencode($lang['cta_pinterest_description'] ?? 'Free Unit Converter - Convert length, weight, and temperature units instantly!'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                    <i class="fab fa-pinterest mr-2"></i>
                    <?php echo $lang['share_on_pinterest'] ?? 'Pinterest'; ?>
                </a>
                
                <a href="https://www.reddit.com/submit?url=https://goorky.com/units&title=<?php echo urlencode($lang['cta_reddit_title'] ?? 'Free Unit Converter Tool - Convert between different units instantly'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">
                    <i class="fab fa-reddit mr-2"></i>
                    <?php echo $lang['share_on_reddit'] ?? 'Reddit'; ?>
                </a>
                
                <a href="mailto:?subject=<?php echo urlencode($lang['cta_email_subject'] ?? 'Check out this Free Unit Converter'); ?>&body=<?php echo urlencode(($lang['cta_email_body'] ?? 'I found this awesome free unit converter tool that you might find useful:') . ' https://goorky.com/units'); ?>" 
                   class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                    <i class="fas fa-envelope mr-2"></i>
                    <?php echo $lang['share_via_email'] ?? 'Email'; ?>
                </a>
            </div>
            
            <!-- Separator -->
            <div class="flex items-center justify-center my-4">
                <span class="border-b border-gray-300 flex-grow max-w-xs"></span>
                <span class="px-4 text-gray-500 text-sm font-medium uppercase tracking-wide">
                    <?php echo $lang['or'] ?? 'or'; ?>
                </span>
                <span class="border-b border-gray-300 flex-grow max-w-xs"></span>
            </div>
            
            <!-- Buy Coffee Button -->
            <div class="mb-2">
                <a href="https://buycoffee.to/lukson" 
                   target="_blank" 
                   class="inline-flex items-center px-6 py-3 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition-colors shadow-md hover:shadow-lg">
                    <span class="text-lg mr-2">☕</span>
                    <?php echo $lang['buy_me_coffee'] ?? 'Buy me a coffee'; ?>
                </a>
            </div>
            
            <p class="text-sm text-gray-500">
                <?php echo $lang['cta_support_text'] ?? 'Your support helps us maintain and improve our free tools while keeping ads minimal!'; ?>
            </p>
        </div>
    </div>
    
    <!-- Informacje o jednostkach -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['conversion_tables'] ?? 'Conversion Tables'; ?></h2>
        
        <div class="space-y-6">
            <?php if ($type === 'length'): ?>
                <h3 class="text-xl font-semibold"><?php echo $lang['length'] ?? 'Length'; ?></h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['unit'] ?? 'Unit'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['millimeter'] ?? 'Millimeter (mm)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['centimeter'] ?? 'Centimeter (cm)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['meter'] ?? 'Meter (m)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['kilometer'] ?? 'Kilometer (km)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['inch'] ?? 'Inch (in)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['foot'] ?? 'Foot (ft)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['yard'] ?? 'Yard (yd)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['mile'] ?? 'Mile (mi)'; ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 <?php echo $lang['symbol_mm'] ?? 'mm'; ?></td>
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
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 <?php echo $lang['symbol_cm'] ?? 'cm'; ?></td>
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
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 <?php echo $lang['symbol_m'] ?? 'm'; ?></td>
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
                <h3 class="text-xl font-semibold"><?php echo $lang['weight'] ?? 'Weight'; ?></h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['unit'] ?? 'Unit'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['milligram'] ?? 'Milligram (mg)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['gram'] ?? 'Gram (g)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['kilogram'] ?? 'Kilogram (kg)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['ounce'] ?? 'Ounce (oz)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['pound'] ?? 'Pound (lb)'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['stone'] ?? 'Stone (st)'; ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 <?php echo $lang['symbol_mg'] ?? 'mg'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000035</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.0000022</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.00000016</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 <?php echo $lang['symbol_g'] ?? 'g'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1000</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">1</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.001</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.03527</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.002205</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">0.000157</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">1 <?php echo $lang['symbol_kg'] ?? 'kg'; ?></td>
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
                <h3 class="text-xl font-semibold"><?php echo $lang['temperature'] ?? 'Temperature'; ?></h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['conversion'] ?? 'Conversion'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['formula'] ?? 'Formula'; ?></th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['example'] ?? 'Example'; ?></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $lang['celsius_to_fahrenheit'] ?? 'Celsius to Fahrenheit'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['celsius_to_fahrenheit_formula'] ?? '°F = (°C × 9/5) + 32'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['celsius_to_fahrenheit_example'] ?? '20°C = 68°F'; ?></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $lang['fahrenheit_to_celsius'] ?? 'Fahrenheit to Celsius'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['fahrenheit_to_celsius_formula'] ?? '°C = (°F - 32) × 5/9'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['fahrenheit_to_celsius_example'] ?? '68°F = 20°C'; ?></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $lang['celsius_to_kelvin'] ?? 'Celsius to Kelvin'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['celsius_to_kelvin_formula'] ?? 'K = °C + 273.15'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['celsius_to_kelvin_example'] ?? '20°C = 293.15K'; ?></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $lang['kelvin_to_celsius'] ?? 'Kelvin to Celsius'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['kelvin_to_celsius_formula'] ?? '°C = K - 273.15'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['kelvin_to_celsius_example'] ?? '293.15K = 20°C'; ?></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $lang['fahrenheit_to_kelvin'] ?? 'Fahrenheit to Kelvin'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['fahrenheit_to_kelvin_formula'] ?? 'K = (°F - 32) × 5/9 + 273.15'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['fahrenheit_to_kelvin_example'] ?? '68°F = 293.15K'; ?></td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $lang['kelvin_to_fahrenheit'] ?? 'Kelvin to Fahrenheit'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['kelvin_to_fahrenheit_formula'] ?? '°F = (K - 273.15) × 9/5 + 32'; ?></td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700"><?php echo $lang['kelvin_to_fahrenheit_example'] ?? '293.15K = 68°F'; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Informacje o jednostkach -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_units'] ?? 'About Units'; ?></h2>
        
        <div class="space-y-6">
            <?php if ($type === 'length'): ?>
                <h3 class="text-xl font-semibold"><?php echo $lang['length_units'] ?? 'Length Units'; ?></h3>
                <p><?php echo $lang['length_units_desc'] ?? 'Length units are used to measure the distance between two points. There are different unit systems, including metric and imperial.'; ?></p>
                
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong><?php echo $lang['metric_system'] ?? 'Metric system:'; ?></strong> <?php echo $lang['metric_system_units'] ?? 'Millimeter (mm), centimeter (cm), meter (m), kilometer (km)'; ?></li>
                    <li><strong><?php echo $lang['imperial_system'] ?? 'Imperial system:'; ?></strong> <?php echo $lang['imperial_system_units'] ?? 'Inch (in), foot (ft), yard (yd), mile (mi)'; ?></li>
                </ul>
                
                <p><?php echo $lang['length_basic_unit'] ?? 'The basic unit in the metric system is the meter (m), while in the imperial system, different basic units are used depending on the context.'; ?></p>
            <?php elseif ($type === 'weight'): ?>
                <h3 class="text-xl font-semibold"><?php echo $lang['weight_units'] ?? 'Weight Units'; ?></h3>
                <p><?php echo $lang['weight_units_desc'] ?? 'Weight (mass) units are used to determine the amount of matter in an object. Like length, there are two main systems: metric and imperial.'; ?></p>
                
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong><?php echo $lang['metric_system'] ?? 'Metric system:'; ?></strong> <?php echo $lang['metric_system_weight_units'] ?? 'Milligram (mg), gram (g), kilogram (kg)'; ?></li>
                    <li><strong><?php echo $lang['imperial_system'] ?? 'Imperial system:'; ?></strong> <?php echo $lang['imperial_system_weight_units'] ?? 'Ounce (oz), pound (lb), stone (st)'; ?></li>
                </ul>
                
                <p><?php echo $lang['weight_basic_unit'] ?? 'The basic unit in the metric system is the kilogram (kg), while in the imperial system the pound (lb) is most commonly used.'; ?></p>
            <?php elseif ($type === 'temperature'): ?>
                <h3 class="text-xl font-semibold"><?php echo $lang['temperature_units'] ?? 'Temperature Units'; ?></h3>
                <p><?php echo $lang['temperature_units_desc'] ?? 'Temperature is a measure of the kinetic energy of molecules. There are three main temperature scales:'; ?></p>
                
                <ul class="list-disc pl-6 space-y-2">
                    <li><strong><?php echo $lang['celsius_scale'] ?? 'Celsius scale (°C):'; ?></strong> <?php echo $lang['celsius_scale_desc'] ?? 'Used in most countries worldwide. The freezing point of water is 0°C, and the boiling point is 100°C.'; ?></li>
                    <li><strong><?php echo $lang['fahrenheit_scale'] ?? 'Fahrenheit scale (°F):'; ?></strong> <?php echo $lang['fahrenheit_scale_desc'] ?? 'Used mainly in the USA. The freezing point of water is 32°F, and the boiling point is 212°F.'; ?></li>
                    <li><strong><?php echo $lang['kelvin_scale'] ?? 'Kelvin scale (K):'; ?></strong> <?php echo $lang['kelvin_scale_desc'] ?? 'Used in science. This is an absolute scale, where 0K is absolute zero (-273.15°C). The degree symbol is not used.'; ?></li>
                </ul>
                
                <p><?php echo $lang['kelvin_absolute_scale'] ?? 'The Kelvin scale is an absolute scale, meaning 0K is the lowest possible theoretical temperature (absolute zero), at which molecules have no kinetic energy.'; ?></p>
            <?php endif; ?>
        </div>
    </div>
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

// Event listener dla przycisków typu konwersji
document.addEventListener('DOMContentLoaded', function() {
    const typeRadios = document.querySelectorAll('input[name="type"]');
    typeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            submitTypeChange();
        });
    });
});
</script>