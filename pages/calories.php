<?php
// Ustawienie tytułu i opisu strony
$pageTitle = $lang['calories_page_title'] ?? 'Calorie Calculator - Estimate Daily Needs | Goorky.com';
$pageDescription = $lang['calories_page_description'] ?? 'Free online calorie calculator - estimate your daily caloric needs (BMR)...';

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
    global $lang;
    
    $descriptions = [
        'sedentary' => $lang['activity_sedentary'] ?? 'Sedentary lifestyle, little or no physical activity',
        'light' => $lang['activity_light'] ?? 'Light activity (light exercise/sport 1–3 days/week)',
        'moderate' => $lang['activity_moderate'] ?? 'Moderate activity (moderate exercise/sport 3–5 days/week)',
        'active' => $lang['activity_active'] ?? 'Active (intense exercise/sport 6–7 days/week)',
        'very_active' => $lang['activity_very_active'] ?? 'Very active (very intense exercise/sport and physical job)'
    ];
    
    return $descriptions[$activity] ?? '';
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['calories_heading'] ?? 'Calorie Calculator'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['calories_intro'] ?? 'Estimate your daily calorie needs based on age, gender, weight, height, and activity level. The result will help in diet planning and weight management.'; ?></p>
        
        <form method="POST" action="/calories" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="gender" class="block text-gray-700 font-medium mb-2"><?php echo $lang['gender'] ?? 'Gender'; ?></label>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="male" <?php echo ($gender === 'male') ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700"><?php echo $lang['male'] ?? 'Male'; ?></span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="female" <?php echo ($gender === 'female') ? 'checked' : ''; ?> class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-gray-700"><?php echo $lang['female'] ?? 'Female'; ?></span>
                        </label>
                    </div>
                </div>
                
                <div>
                    <label for="age" class="block text-gray-700 font-medium mb-2"><?php echo $lang['age_years'] ?? 'Age (years)'; ?></label>
                    <input type="number" name="age" id="age" min="15" max="100" value="<?php echo htmlspecialchars($age); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="<?php echo $lang['age_placeholder'] ?? 'e.g. 30'; ?>">
                </div>
                
                <div>
                    <label for="weight" class="block text-gray-700 font-medium mb-2"><?php echo $lang['weight_kg'] ?? 'Weight (kg)'; ?></label>
                    <input type="number" name="weight" id="weight" min="30" max="300" step="0.1" value="<?php echo htmlspecialchars($weight); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="<?php echo $lang['weight_placeholder'] ?? 'e.g. 70.5'; ?>">
                </div>
                
                <div>
                    <label for="height" class="block text-gray-700 font-medium mb-2"><?php echo $lang['height_cm'] ?? 'Height (cm)'; ?></label>
                    <input type="number" name="height" id="height" min="100" max="250" step="0.1" value="<?php echo htmlspecialchars($height); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="<?php echo $lang['height_placeholder'] ?? 'e.g. 175'; ?>">
                </div>
                
                <div class="md:col-span-2">
                    <label for="activity" class="block text-gray-700 font-medium mb-2"><?php echo $lang['activity_level'] ?? 'Physical Activity Level'; ?></label>
                    <select name="activity" id="activity" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="sedentary" <?php echo ($activity === 'sedentary') ? 'selected' : ''; ?>><?php echo $lang['activity_sedentary_option'] ?? 'Sedentary (little or no exercise)'; ?></option>
                        <option value="light" <?php echo ($activity === 'light') ? 'selected' : ''; ?>><?php echo $lang['activity_light_option'] ?? 'Light activity (1–3 times/week)'; ?></option>
                        <option value="moderate" <?php echo ($activity === 'moderate') ? 'selected' : ''; ?>><?php echo $lang['activity_moderate_option'] ?? 'Moderate activity (3–5 times/week)'; ?></option>
                        <option value="active" <?php echo ($activity === 'active') ? 'selected' : ''; ?>><?php echo $lang['activity_active_option'] ?? 'Active (6–7 times/week)'; ?></option>
                        <option value="very_active" <?php echo ($activity === 'very_active') ? 'selected' : ''; ?>><?php echo $lang['activity_very_active_option'] ?? 'Very active (hard exercise 2x/day)'; ?></option>
                    </select>
                </div>
            </div>
            
            <div class="text-center">
                <button type="button" name="calculate_calories" class="trigger-popup bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['calculate_calories'] ?? 'Calculate calories'; ?></button>
            </div>  
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['your_calorie_needs'] ?? 'Your Daily Calorie Needs'; ?></h2>
        
        <div class="flex flex-col items-center mb-6">
            <div class="text-5xl font-bold text-blue-600"><?php echo number_format($calories, 0); ?></div>
            <div class="text-xl mt-2"><?php echo $lang['calories_per_day'] ?? 'calories per day'; ?></div>
        </div>
        
        <div class="bg-gray-100 p-6 rounded-lg mb-4">
            <h3 class="font-semibold mb-2"><?php echo $lang['your_data'] ?? 'Your Data:'; ?></h3>
            <ul class="space-y-2">
                <li><strong><?php echo $lang['gender'] ?? 'Gender:'; ?></strong> <?php echo ($gender === 'male') ? ($lang['male'] ?? 'Male') : ($lang['female'] ?? 'Female'); ?></li>
                <li><strong><?php echo $lang['age'] ?? 'Age:'; ?></strong> <?php echo htmlspecialchars($age); ?> <?php echo $lang['years'] ?? 'years'; ?></li>
                <li><strong><?php echo $lang['weight'] ?? 'Weight:'; ?></strong> <?php echo htmlspecialchars($weight); ?> <?php echo $lang['kg'] ?? 'kg'; ?></li>
                <li><strong><?php echo $lang['height'] ?? 'Height:'; ?></strong> <?php echo htmlspecialchars($height); ?> <?php echo $lang['cm'] ?? 'cm'; ?></li>
                <li><strong><?php echo $lang['activity_level'] ?? 'Activity Level:'; ?></strong> <?php echo getActivityDescription($activity); ?></li>
            </ul>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold mb-2"><?php echo $lang['weight_loss'] ?? 'Weight Loss'; ?></h3>
                <div class="text-xl font-bold text-blue-600"><?php echo number_format($calories * 0.8, 0); ?></div>
                <p class="text-sm text-gray-600 mt-1"><?php echo $lang['calories_per_day'] ?? 'calories per day'; ?></p>
                <p class="text-xs text-gray-500 mt-2"><?php echo $lang['reduction_20'] ?? '20% Reduction'; ?></p>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold mb-2"><?php echo $lang['weight_maintenance'] ?? 'Weight Maintenance'; ?></h3>
                <div class="text-xl font-bold text-green-600"><?php echo number_format($calories, 0); ?></div>
                <p class="text-sm text-gray-600 mt-1"><?php echo $lang['calories_per_day'] ?? 'calories per day'; ?></p>
                <p class="text-xs text-gray-500 mt-2"><?php echo $lang['balanced_diet'] ?? 'Balanced Diet'; ?></p>
            </div>
            
            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                <h3 class="font-semibold mb-2"><?php echo $lang['weight_gain'] ?? 'Weight Gain'; ?></h3>
                <div class="text-xl font-bold text-yellow-600"><?php echo number_format($calories * 1.2, 0); ?></div>
                <p class="text-sm text-gray-600 mt-1"><?php echo $lang['calories_per_day'] ?? 'calories per day'; ?></p>
                <p class="text-xs text-gray-500 mt-2"><?php echo $lang['increase_20'] ?? '20% Increase'; ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

        <!-- Social Media Buttons -->
        <?php include BASE_PATH . '/includes/social.php'; ?>
    
    <!-- Informacje o zapotrzebowaniu kalorycznym -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['how_to_interpret'] ?? 'How to Interpret Results?'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['caloric_needs_explanation'] ?? 'The calculator result shows your estimated daily caloric needs... to maintain your current weight.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['weight_control_tips'] ?? 'Weight Management Tips:'; ?></h3>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong><?php echo $lang['weight_loss_title'] ?? 'Weight Loss:'; ?></strong> <?php echo $lang['weight_loss_desc'] ?? 'To lose about 0.5 kg per week, consume 500 calories less per day than your daily needs.'; ?></li>
                <li><strong><?php echo $lang['weight_maintenance_title'] ?? 'Weight Maintenance:'; ?></strong> <?php echo $lang['weight_maintenance_desc'] ?? 'Consume the number of calories equal to your daily needs.'; ?></li>
                <li><strong><?php echo $lang['weight_gain_title'] ?? 'Weight Gain:'; ?></strong> <?php echo $lang['weight_gain_desc'] ?? 'To gain about 0.5 kg per week, consume 500 calories more per day than your daily needs.'; ?></li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['calculator_note'] ?? 'The calculator provides estimated values. Actual needs may vary based on metabolism, health, environment. Consult a doctor or dietitian before starting a diet.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Informacje o metodzie obliczania -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_calculation_method'] ?? 'About the Calculation Method'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['calculation_method_intro'] ?? 'This calculator uses the Mifflin-St Jeor formula to estimate Basal Metabolic Rate (BMR), then applies an activity factor to calculate total daily needs.'; ?></p>
            
            <h3 class="text-lg font-semibold mt-4"><?php echo $lang['mifflin_formula'] ?? 'Mifflin-St Jeor Formula:'; ?></h3>
            
            <div class="bg-gray-100 p-4 rounded-lg">
                <p><strong><?php echo $lang['for_men'] ?? 'For Men:'; ?></strong> <?php echo $lang['men_formula'] ?? 'BMR = (10 × weight in kg) + (6.25 × height in cm) - (5 × age in years) + 5'; ?></p>
                <p><strong><?php echo $lang['for_women'] ?? 'For Women:'; ?></strong> <?php echo $lang['women_formula'] ?? 'BMR = (10 × weight in kg) + (6.25 × height in cm) - (5 × age in years) - 161'; ?></p>
            </div>
            
            <h3 class="text-lg font-semibold mt-4"><?php echo $lang['activity_factors'] ?? 'Physical Activity Multipliers:'; ?></h3>
            
            <ul class="list-disc pl-6 space-y-2">
                <li><strong><?php echo $lang['activity_sedentary_title'] ?? 'Sedentary:'; ?></strong> <?php echo $lang['activity_sedentary_factor'] ?? 'BMR × 1.2 (little to no exercise)'; ?></li>
                <li><strong><?php echo $lang['activity_light_title'] ?? 'Light Activity:'; ?></strong> <?php echo $lang['activity_light_factor'] ?? 'BMR × 1.375 (light exercise 1–3 times/week)'; ?></li>
                <li><strong><?php echo $lang['activity_moderate_title'] ?? 'Moderate Activity:'; ?></strong> <?php echo $lang['activity_moderate_factor'] ?? 'BMR × 1.55 (moderate exercise 3–5 times/week)'; ?></li>
                <li><strong><?php echo $lang['activity_active_title'] ?? 'Active:'; ?></strong> <?php echo $lang['activity_active_factor'] ?? 'BMR × 1.725 (intense exercise 6–7 times/week)'; ?></li>
                <li><strong><?php echo $lang['activity_very_active_title'] ?? 'Very Active:'; ?></strong> <?php echo $lang['activity_very_active_factor'] ?? 'BMR × 1.9 (very intense exercise, physical job)'; ?></li>
            </ul>
            
            <p><?php echo $lang['formula_accuracy'] ?? 'This formula is considered one of the most accurate for estimating BMR without lab testing.'; ?></p>
        </div>
    </div>
</div>