<?php
// Ustawienie tytułu i opisu strony
$pageTitle = $lang['dates_page_title'] ?? 'Date Calculator - Calculate the difference between dates | Goorky.com';
$pageDescription = $lang['dates_page_description'] ?? 'Free online date calculator - calculate the difference between two dates in days, weeks, months, and years. Add or subtract days from a date.';

// Inicjalizacja zmiennych
$date1 = date('Y-m-d');
$date2 = date('Y-m-d');
$days = 0;
$operation = 'difference';
$hasResult = false;
$resultDate = '';
$diffDays = 0;
$diffWeeks = 0;
$diffMonths = 0;
$diffYears = 0;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['calculate_dates'])) {
    $operation = sanitizeInput($_POST['operation'] ?? 'difference');
    
    if ($operation === 'difference') {
        // Obliczanie różnicy między dwiema datami
        $date1 = sanitizeInput($_POST['date1'] ?? date('Y-m-d'));
        $date2 = sanitizeInput($_POST['date2'] ?? date('Y-m-d'));
        
        // Walidacja dat
        if (strtotime($date1) && strtotime($date2)) {
            $diffDays = calculateDateDifference($date1, $date2);
            $diffWeeks = floor($diffDays / 7);
            
            // Obliczanie różnicy w miesiącach i latach
            $datetime1 = new DateTime($date1);
            $datetime2 = new DateTime($date2);
            $interval = $datetime1->diff($datetime2);
            
            $diffMonths = $interval->y * 12 + $interval->m;
            $diffYears = $interval->y;
            
            $hasResult = true;
        }
    } else {
        // Dodawanie lub odejmowanie dni od daty
        $date1 = sanitizeInput($_POST['date1'] ?? date('Y-m-d'));
        $days = intval($_POST['days'] ?? 0);
        
        // Walidacja daty
        if (strtotime($date1)) {
            $datetime = new DateTime($date1);
            
            if ($operation === 'add') {
                $datetime->modify("+{$days} days");
            } else {
                $datetime->modify("-{$days} days");
            }
            
            $resultDate = $datetime->format('Y-m-d');
            $hasResult = true;
        }
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['dates_heading'] ?? 'Date Calculator'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['dates_intro'] ?? 'Calculate the difference between two dates or add/subtract a specific number of days from a date.'; ?></p>
        
        <div x-data="{ operation: '<?php echo $operation; ?>' }">
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2"><?php echo $lang['choose_operation'] ?? 'Select operation'; ?></label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($operation === 'difference') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                        <input type="radio" name="operation" value="difference" x-model="operation" class="hidden">
                        <div class="text-center">
                            <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="block mt-2 font-medium"><?php echo $lang['date_diff'] ?? 'Difference between dates'; ?></span>
                        </div>
                    </label>
                    
                    <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($operation === 'add') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                        <input type="radio" name="operation" value="add" x-model="operation" class="hidden">
                        <div class="text-center">
                            <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="block mt-2 font-medium"><?php echo $lang['date_add'] ?? 'Add days to date'; ?></span>
                        </div>
                    </label>
                    
                    <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($operation === 'subtract') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                        <input type="radio" name="operation" value="subtract" x-model="operation" class="hidden">
                        <div class="text-center">
                            <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                            <span class="block mt-2 font-medium"><?php echo $lang['date_subtract'] ?? 'Subtract days from date'; ?></span>
                        </div>
                    </label>
                </div>
            </div>
            
            <form method="POST" action="/dates" class="space-y-6">
                <input type="hidden" name="operation" x-bind:value="operation">
                
                <div x-show="operation === 'difference'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date1" class="block text-gray-700 font-medium mb-2"><?php echo $lang['first_date'] ?? 'First date'; ?></label>
                            <input type="date" name="date1" id="date1" value="<?php echo htmlspecialchars($date1); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="date2" class="block text-gray-700 font-medium mb-2"><?php echo $lang['second_date'] ?? 'Second date'; ?></label>
                            <input type="date" name="date2" id="date2" value="<?php echo htmlspecialchars($date2); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                
                <div x-show="operation === 'add' || operation === 'subtract'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date1" class="block text-gray-700 font-medium mb-2"><?php echo $lang['date'] ?? 'Date'; ?></label>
                            <input type="date" name="date1" id="date1" value="<?php echo htmlspecialchars($date1); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="days" class="block text-gray-700 font-medium mb-2"><?php echo $lang['number_of_days'] ?? 'Number of days'; ?></label>
                            <input type="number" name="days" id="days" min="1" value="<?php echo htmlspecialchars($days); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" name="calculate_dates" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['calculate'] ?? 'Calculate'; ?></button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['result'] ?? 'Result'; ?></h2>
        
        <?php if ($operation === 'difference'): ?>
            <div class="flex flex-col items-center mb-6">
                <div class="text-4xl font-bold text-blue-600"><?php echo $diffDays; ?></div>
                <div class="text-xl"><?php echo $lang['days'] ?? 'days'; ?></div>
            </div>
            
            <div class="bg-gray-100 p-6 rounded-lg mb-6">
                <h3 class="font-semibold mb-4"><?php echo $lang['detailed_info'] ?? 'Detailed information:'; ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffDays; ?></div>
                        <div><?php echo $lang['days'] ?? 'days'; ?></div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffWeeks; ?></div>
                        <div><?php echo $lang['weeks'] ?? 'weeks'; ?></div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffMonths; ?></div>
                        <div><?php echo $lang['months'] ?? 'months'; ?></div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffYears; ?></div>
                        <div><?php echo $lang['years'] ?? 'years'; ?></div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-lg"><?php echo $lang['from'] ?? 'From'; ?> <strong><?php echo formatFriendlyDate($date1); ?></strong> <?php echo $lang['to'] ?? 'to'; ?> <strong><?php echo formatFriendlyDate($date2); ?></strong> <?php echo $lang['is'] ?? 'is'; ?> <strong><?php echo $diffDays; ?></strong> <?php echo $lang['days'] ?? 'days'; ?>.</p>
            </div>
            
        <?php else: ?>
            <div class="flex flex-col items-center mb-6">
                <div class="text-3xl font-bold text-blue-600">
                    <?php 
                    if ($operation === 'add') {
                        echo $lang['after_adding'] ?? "After adding {$days} days to {$date1}";
                    } else {
                        echo $lang['after_subtracting'] ?? "After subtracting {$days} days from {$date1}";
                    }
                    ?>
                </div>
                <div class="text-4xl font-bold mt-4"><?php echo formatFriendlyDate($resultDate); ?></div>
            </div>
            
            <div class="bg-gray-100 p-6 rounded-lg">
                <p class="text-lg text-center"><?php echo $lang['resulting_date'] ?? 'Resulting date:'; ?> <strong><?php echo formatFriendlyDate($resultDate); ?></strong></p>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o kalkulatorze dat -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_date_calculator'] ?? 'About the date calculator'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['date_calculator_desc'] ?? 'The date calculator is a useful tool that allows you to perform various date operations:'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['calculating_date_difference'] ?? 'Calculating date differences'; ?></h3>
            <p><?php echo $lang['date_difference_desc'] ?? 'You can calculate the exact difference between two selected dates. The calculator shows the result in days, weeks, months, and years.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['adding_days_to_date'] ?? 'Adding days to a date'; ?></h3>
            <p><?php echo $lang['adding_days_desc'] ?? 'With this function, you can check what date falls after adding a specific number of days to the selected date. Useful for planning deadlines, calculating due dates, etc.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['subtracting_days_from_date'] ?? 'Subtracting days from a date'; ?></h3>
            <p><?php echo $lang['subtracting_days_desc'] ?? 'This function allows you to determine what date was a certain number of days before the selected date. Useful for calculating historical dates or backdated deadlines.'; ?></p>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['date_calculator_note'] ?? 'The calculator accounts for leap years when calculating date differences and when adding/subtracting days.'; ?></p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['date_calculator_uses'] ?? 'Date calculator applications'; ?></h3>
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['use_project_planning'] ?? 'Project planning and deadline calculation'; ?></li>
                <li><?php echo $lang['use_age_calculation'] ?? 'Calculating age or duration'; ?></li>
                <li><?php echo $lang['use_event_planning'] ?? 'Event and meeting planning'; ?></li>
                <li><?php echo $lang['use_payment_dates'] ?? 'Determining payment or due dates'; ?></li>
                <li><?php echo $lang['use_expiry_dates'] ?? 'Calculating document expiry dates'; ?></li>
                <li><?php echo $lang['use_travel_planning'] ?? 'Travel and booking planning'; ?></li>
            </ul>
        </div>
    </div>
</div>