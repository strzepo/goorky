<?php
// Ustawienie tytułu i opisu strony
$pageTitle = $lang['bmi_page_title'] ?? 'BMI Calculator - Calculate Your Body Mass Index | Goorky.com';
$pageDescription = $lang['bmi_page_description'] ?? 'Free online BMI calculator - quickly calculate your Body Mass Index and check if your weight is within the healthy range.';

// Inicjalizacja zmiennych
$weight = '';
$height = '';
$bmi = 0;
$bmiCategory = '';
$categoryColor = '';
$hasResult = false;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculate_bmi'])) {
    // Pobranie i walidacja danych
    $weight = sanitizeNumber($_POST['weight'] ?? '');
    $height = sanitizeNumber($_POST['height'] ?? '');
    
    // Konwersja wzrostu z cm na metry
    $heightInMeters = $height / 100;
    
    // Obliczenie BMI
    if ($weight > 0 && $heightInMeters > 0) {
        $bmi = $weight / ($heightInMeters * $heightInMeters);
        list($bmiCategory, $categoryColor) = getBMICategory($bmi);
        $hasResult = true;
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['bmi_heading'] ?? 'BMI Calculator'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['bmi_intro_text'] ?? 'BMI (Body Mass Index) is an indicator that helps assess whether your weight is appropriate for your height. Calculate your BMI now!'; ?></p>
        
        <form method="POST" action="/bmi" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="weight" class="block text-gray-700 font-medium mb-2"><?php echo $lang['weight_kg'] ?? 'Weight (kg)'; ?></label>
                    <input type="number" name="weight" id="weight" min="20" max="300" step="0.1" value="<?php echo htmlspecialchars($weight); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="<?php echo $lang['weight_placeholder'] ?? 'e.g. 70.5'; ?>">
                </div>
                <div>
                    <label for="height" class="block text-gray-700 font-medium mb-2"><?php echo $lang['height_cm'] ?? 'Height (cm)'; ?></label>
                    <input type="number" name="height" id="height" min="50" max="250" step="0.1" value="<?php echo htmlspecialchars($height); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="<?php echo $lang['height_placeholder'] ?? 'e.g. 175'; ?>">
                </div>
            </div>
            <div class="text-center">
                <button type="submit" name="calculate_bmi" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['calculate_bmi'] ?? 'Calculate BMI'; ?></button>
            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['your_bmi_result'] ?? 'Your BMI Result'; ?></h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="text-5xl font-bold <?php echo $categoryColor; ?>"><?php echo number_format($bmi, 2); ?></div>
            <div class="text-xl mt-2 <?php echo $categoryColor; ?>"><?php echo htmlspecialchars($bmiCategory); ?></div>
        </div>
        
        <div class="bg-gray-100 p-4 rounded-lg">
            <div class="relative h-8 mb-4">
                <div class="absolute inset-0 flex">
                    <div class="flex-1 bg-blue-700 rounded-l-full"></div>
                    <div class="flex-1 bg-green-500"></div>
                    <div class="flex-1 bg-yellow-500"></div>
                    <div class="flex-1 bg-orange-500"></div>
                    <div class="flex-1 bg-red-600 rounded-r-full"></div>
                </div>
                
                <!-- Wskaźnik BMI -->
                <?php
                $position = 0;
                if ($bmi <= 16) {
                    $position = 0;
                } elseif ($bmi >= 40) {
                    $position = 100;
                } else {
                    // Mapowanie BMI od 16 do 40 na pozycję od 0% do 100%
                    $position = (($bmi - 16) / (40 - 16)) * 100;
                }
                ?>
                <div class="absolute h-10 w-4 bg-gray-800 rounded-full" style="left: <?php echo $position; ?>%; top: -5px; transform: translateX(-50%);"></div>
            </div>
            
            <div class="flex justify-between text-xs text-gray-600">
                <span>16</span>
                <span>18.5</span>
                <span>25</span>
                <span>30</span>
                <span>40</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600 mt-1">
                <span><?php echo $lang['bmi_severe_underweight'] ?? 'Severe Thinness'; ?></span>
                <span><?php echo $lang['bmi_underweight'] ?? 'Underweight'; ?></span>
                <span><?php echo $lang['bmi_normal'] ?? 'Normal'; ?></span>
                <span><?php echo $lang['bmi_overweight'] ?? 'Overweight'; ?></span>
                <span><?php echo $lang['bmi_obesity'] ?? 'Obesity'; ?></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o BMI -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['bmi_interpretation'] ?? 'BMI Result Interpretation'; ?></h2>
        
        <div class="overflow-hidden overflow-x-auto mb-4">
            <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['bmi_value'] ?? 'BMI Value'; ?></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['bmi_category'] ?? 'Category'; ?></th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo $lang['bmi_interpretation'] ?? 'Interpretation'; ?></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $lang['bmi_below'] ?? 'below'; ?> 16</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700"><?php echo $lang['bmi_severe_thinness'] ?? 'Severe Thinness'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_severe_thinness_desc'] ?? 'Severe malnutrition is present. Immediate medical consultation is required.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">16 - 16.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600"><?php echo $lang['bmi_moderate_thinness'] ?? 'Moderate Thinness'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_moderate_thinness_desc'] ?? 'Malnutrition is present. Medical consultation is recommended.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">17 - 18.49</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600"><?php echo $lang['bmi_underweight'] ?? 'Underweight'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_underweight_desc'] ?? 'Body weight is below normal. Consider increasing calorie intake.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">18.5 - 24.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600"><?php echo $lang['bmi_normal_weight'] ?? 'Normal'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_normal_weight_desc'] ?? 'Normal body weight. Maintain a healthy lifestyle.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">25 - 29.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600"><?php echo $lang['bmi_overweight'] ?? 'Overweight'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_overweight_desc'] ?? 'Body weight above normal. Recommended dietary changes and more physical activity.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">30 - 34.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500"><?php echo $lang['bmi_obesity_class_1'] ?? 'Obesity Class I'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_obesity_class_1_desc'] ?? 'Obesity is present. Consult a doctor and dietitian.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">35 - 39.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600"><?php echo $lang['bmi_obesity_class_2'] ?? 'Obesity Class II'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_obesity_class_2_desc'] ?? 'Severe obesity is present. Medical consultation is necessary.'; ?></td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">40 <?php echo $lang['bmi_and_above'] ?? 'and above'; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700"><?php echo $lang['bmi_obesity_class_3'] ?? 'Obesity Class III'; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700"><?php echo $lang['bmi_obesity_class_3_desc'] ?? 'Extreme obesity is present. Urgent medical intervention is required.'; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Informacje o BMI -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_bmi'] ?? 'About BMI'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['bmi_description_1'] ?? 'BMI (Body Mass Index) was developed by Belgian mathematician Adolphe Quetelet in the 19th century. It is commonly used to assess healthy body weight.'; ?></p>
            
            <p><?php echo $lang['bmi_formula_intro'] ?? 'BMI is calculated by dividing weight (in kilograms) by height squared (in meters):'; ?></p>
            
            <div class="bg-gray-100 p-4 rounded-lg text-center">
                <strong><?php echo $lang['bmi_formula'] ?? 'BMI = weight (kg) / height² (m²)'; ?></strong>
            </div>
            
            <p><?php echo $lang['bmi_limitations_intro'] ?? 'However, it is important to remember that BMI has limitations:'; ?></p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['bmi_limitation_1'] ?? 'Does not account for body composition (muscle vs fat)'; ?></li>
                <li><?php echo $lang['bmi_limitation_2'] ?? 'May give misleading results for athletes, pregnant women, the elderly, and children'; ?></li>
                <li><?php echo $lang['bmi_limitation_3'] ?? 'Does not consider fat distribution in the body'; ?></li>
            </ul>
            
            <p><?php echo $lang['bmi_conclusion'] ?? 'BMI is a good starting point for assessing body weight, but in case of doubt always consult a doctor or dietitian.'; ?></p>
        </div>
    </div>
</div>