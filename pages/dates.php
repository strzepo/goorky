<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Kalkulator Dat - Oblicz różnicę między datami | ToolsOnline';
$pageDescription = 'Darmowy kalkulator dat online - oblicz różnicę między dwiema datami w dniach, tygodniach, miesiącach i latach. Dodawaj lub odejmuj dni od daty.';

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
    <h1 class="text-3xl font-bold mb-6">Kalkulator Dat</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Oblicz różnicę między dwiema datami lub dodaj/odejmij określoną liczbę dni od danej daty.</p>
        
        <div x-data="{ operation: '<?php echo $operation; ?>' }">
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Wybierz operację</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($operation === 'difference') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                        <input type="radio" name="operation" value="difference" x-model="operation" class="hidden">
                        <div class="text-center">
                            <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="block mt-2 font-medium">Różnica między datami</span>
                        </div>
                    </label>
                    
                    <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($operation === 'add') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                        <input type="radio" name="operation" value="add" x-model="operation" class="hidden">
                        <div class="text-center">
                            <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="block mt-2 font-medium">Dodaj dni do daty</span>
                        </div>
                    </label>
                    
                    <label class="inline-flex items-center justify-center p-4 bg-gray-100 rounded-lg cursor-pointer hover:bg-gray-200 <?php echo ($operation === 'subtract') ? 'bg-blue-100 border-2 border-blue-400' : ''; ?>">
                        <input type="radio" name="operation" value="subtract" x-model="operation" class="hidden">
                        <div class="text-center">
                            <svg class="h-8 w-8 mx-auto text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                            <span class="block mt-2 font-medium">Odejmij dni od daty</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <form method="POST" action="/dates" class="space-y-6">
                <input type="hidden" name="operation" x-bind:value="operation">
                
                <div x-show="operation === 'difference'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date1" class="block text-gray-700 font-medium mb-2">Pierwsza data</label>
                            <input type="date" name="date1" id="date1" value="<?php echo htmlspecialchars($date1); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="date2" class="block text-gray-700 font-medium mb-2">Druga data</label>
                            <input type="date" name="date2" id="date2" value="<?php echo htmlspecialchars($date2); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                
                <div x-show="operation === 'add' || operation === 'subtract'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="date1" class="block text-gray-700 font-medium mb-2">Data</label>
                            <input type="date" name="date1" id="date1" value="<?php echo htmlspecialchars($date1); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <div>
                            <label for="days" class="block text-gray-700 font-medium mb-2">Liczba dni</label>
                            <input type="number" name="days" id="days" min="1" value="<?php echo htmlspecialchars($days); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" name="calculate_dates" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">Oblicz</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Wynik</h2>
        
        <?php if ($operation === 'difference'): ?>
            <div class="flex flex-col items-center mb-6">
                <div class="text-4xl font-bold text-blue-600"><?php echo $diffDays; ?></div>
                <div class="text-xl">dni</div>
            </div>
            
            <div class="bg-gray-100 p-6 rounded-lg mb-6">
                <h3 class="font-semibold mb-4">Szczegółowe informacje:</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffDays; ?></div>
                        <div>dni</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffWeeks; ?></div>
                        <div>tygodni</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffMonths; ?></div>
                        <div>miesięcy</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold"><?php echo $diffYears; ?></div>
                        <div>lat</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-lg">Od <strong><?php echo formatFriendlyDate($date1); ?></strong> do <strong><?php echo formatFriendlyDate($date2); ?></strong> jest <strong><?php echo $diffDays; ?></strong> dni.</p>
            </div>
            
        <?php else: ?>
            <div class="flex flex-col items-center mb-6">
                <div class="text-3xl font-bold text-blue-600">
                    <?php 
                    if ($operation === 'add') {
                        echo "Po dodaniu {$days} dni do {$date1}";
                    } else {
                        echo "Po odjęciu {$days} dni od {$date1}";
                    }
                    ?>
                </div>
                <div class="text-4xl font-bold mt-4"><?php echo formatFriendlyDate($resultDate); ?></div>
            </div>
            
                            <div class="bg-gray-100 p-6 rounded-lg">
                <p class="text-lg text-center">Wynikowa data: <strong><?php echo formatFriendlyDate($resultDate); ?></strong></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o kalkulatorze dat -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">O kalkulatorze dat</h2>
        
        <div class="space-y-4">
            <p>Kalkulator dat to przydatne narzędzie, które umożliwia wykonywanie różnych operacji na datach:</p>
            
            <h3 class="text-xl font-semibold mt-4">Obliczanie różnicy między datami</h3>
            <p>Możesz obliczyć dokładną różnicę między dwiema wybranymi datami. Kalkulator pokazuje wynik w dniach, tygodniach, miesiącach i latach.</p>
            
            <h3 class="text-xl font-semibold mt-4">Dodawanie dni do daty</h3>
            <p>Dzięki tej funkcji możesz sprawdzić, jaka data wypada po dodaniu określonej liczby dni do wybranej daty. Jest to przydatne przy planowaniu terminów, obliczaniu dat zapadalności itp.</p>
            
            <h3 class="text-xl font-semibold mt-4">Odejmowanie dni od daty</h3>
            <p>Ta funkcja pozwala na obliczenie, jaka data wypadała określoną liczbę dni przed wybraną datą. Może być przydatna do obliczania dat historycznych lub terminów wstecznych.</p>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Kalkulator uwzględnia lata przestępne przy obliczaniu różnicy między datami oraz przy dodawaniu/odejmowaniu dni.</p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4">Zastosowania kalkulatora dat</h3>
            <ul class="list-disc pl-6 space-y-2">
                <li>Planowanie projektów i obliczanie terminów</li>
                <li>Obliczanie wieku lub czasu trwania</li>
                <li>Planowanie wydarzeń i spotkań</li>
                <li>Określanie dat płatności lub terminów</li>
                <li>Obliczanie dat ważności dokumentów</li>
                <li>Planowanie podróży i rezerwacji</li>
            </ul>
        </div>
    </div>
</div>