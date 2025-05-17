<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Kalkulator BMI - Oblicz swój wskaźnik masy ciała | ToolsOnline';
$pageDescription = 'Darmowy kalkulator BMI online - szybko oblicz swój wskaźnik masy ciała i sprawdź, czy Twoja waga jest prawidłowa. Prosty i dokładny.';

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

// // Funkcja zwracająca kategorię BMI i kolor na podstawie wartości BMI
// function getBMICategory($bmi) {
//     if ($bmi < 16) {
//         return ['Wygłodzenie', 'text-red-700'];
//     } elseif ($bmi < 17) {
//         return ['Wychudzenie', 'text-red-600'];
//     } elseif ($bmi < 18.5) {
//         return ['Niedowaga', 'text-yellow-600'];
//     } elseif ($bmi < 25) {
//         return ['Prawidłowa waga', 'text-green-600'];
//     } elseif ($bmi < 30) {
//         return ['Nadwaga', 'text-yellow-600'];
//     } elseif ($bmi < 35) {
//         return ['Otyłość I stopnia', 'text-red-500'];
//     } elseif ($bmi < 40) {
//         return ['Otyłość II stopnia', 'text-red-600'];
//     } else {
//         return ['Otyłość III stopnia', 'text-red-700'];
//     }
// }
// ?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Kalkulator BMI</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">BMI (Body Mass Index) to wskaźnik masy ciała, który pomaga ocenić, czy waga jest prawidłowa w stosunku do wzrostu. Oblicz swoje BMI już teraz!</p>
        
        <form method="POST" action="/bmi" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="weight" class="block text-gray-700 font-medium mb-2">Waga (kg)</label>
                    <input type="number" name="weight" id="weight" min="20" max="300" step="0.1" value="<?php echo htmlspecialchars($weight); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="np. 70.5">
                </div>
                <div>
                    <label for="height" class="block text-gray-700 font-medium mb-2">Wzrost (cm)</label>
                    <input type="number" name="height" id="height" min="50" max="250" step="0.1" value="<?php echo htmlspecialchars($height); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="np. 175">
                </div>
            </div>
            <div class="text-center">
                <button type="submit" name="calculate_bmi" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">Oblicz BMI</button>
            </div>
        </form>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Twój wynik BMI</h2>
        
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
                <span>Wygłodzenie</span>
                <span>Niedowaga</span>
                <span>Prawidłowa</span>
                <span>Nadwaga</span>
                <span>Otyłość</span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o BMI -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Interpretacja wyniku BMI</h2>
        
        <div class="overflow-hidden overflow-x-auto mb-4">
            <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wartość BMI</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategoria</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interpretacja</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">poniżej 16</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700">Wygłodzenie</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Występuje poważne niedożywienie. Wymagana natychmiastowa konsultacja z lekarzem.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">16 - 16.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">Wychudzenie</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Występuje niedożywienie. Zalecana konsultacja z lekarzem.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">17 - 18.49</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">Niedowaga</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Masa ciała poniżej normy. Zalecane zwiększenie kaloryczności diety.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">18.5 - 24.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">Prawidłowa waga</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Prawidłowa masa ciała. Utrzymuj zdrowy styl życia.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">25 - 29.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-yellow-600">Nadwaga</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Masa ciała powyżej normy. Zalecana zmiana nawyków żywieniowych i zwiększenie aktywności fizycznej.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">30 - 34.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500">Otyłość I stopnia</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Występuje otyłość. Zalecana konsultacja z lekarzem i dietetykiem.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">35 - 39.99</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">Otyłość II stopnia</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Występuje poważna otyłość. Konieczna konsultacja z lekarzem.</td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">40 i więcej</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700">Otyłość III stopnia</td>
                        <td class="px-6 py-4 text-sm text-gray-700">Występuje skrajna otyłość. Wymagana pilna interwencja medyczna.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Informacje o BMI -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">O wskaźniku BMI</h2>
        
        <div class="space-y-4">
            <p>BMI (Body Mass Index) to wskaźnik opracowany przez belgijskiego matematyka Adolphe'a Queteleta w XIX wieku. Jest powszechnie stosowany do oceny prawidłowej masy ciała.</p>
            
            <p>BMI oblicza się dzieląc masę ciała (w kilogramach) przez kwadrat wzrostu (w metrach):</p>
            
            <div class="bg-gray-100 p-4 rounded-lg text-center">
                <strong>BMI = masa ciała (kg) / wzrost² (m²)</strong>
            </div>
            
            <p>Jednak należy pamiętać, że BMI ma pewne ograniczenia:</p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li>Nie uwzględnia składu ciała (proporcji mięśni do tłuszczu)</li>
                <li>Może dawać mylne wyniki u sportowców, kobiet w ciąży, osób starszych i dzieci</li>
                <li>Nie bierze pod uwagę rozmieszczenia tkanki tłuszczowej w organizmie</li>
            </ul>
            
            <p>BMI jest dobrym punktem wyjścia do oceny masy ciała, ale w razie wątpliwości zawsze warto skonsultować się z lekarzem lub dietetykiem.</p>
        </div>
    </div>
</div>