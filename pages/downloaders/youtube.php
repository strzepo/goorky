<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'YouTube Downloader - Pobieraj filmy z YouTube | ToolsOnline';
$pageDescription = 'Darmowy YouTube Downloader - pobieraj filmy i muzykę z YouTube w wysokiej jakości. Łatwy w użyciu, bez rejestracji i instalacji.';

// Inicjalizacja zmiennych
$url = '';
$videoId = '';
$hasResult = false;
$errorMessage = '';

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_youtube'])) {
    // Pobranie i walidacja danych
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Sprawdzenie czy URL jest poprawny
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Próba wyodrębnienia ID filmu
        $videoId = getYoutubeId($url);
        
        if ($videoId) {
            $hasResult = true;
        } else {
            $errorMessage = 'Nieprawidłowy URL filmu YouTube. Upewnij się, że podałeś poprawny link.';
        }
    } else {
        $errorMessage = 'Wprowadź poprawny adres URL filmu YouTube.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">YouTube Downloader</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Pobieraj filmy z YouTube w różnych formatach i jakościach. Wystarczy wkleić link do filmu YouTube i wybrać format.</p>
        
        <form method="POST" action="/youtube" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2">Link do filmu YouTube</label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_youtube" class="bg-red-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-red-700 transition">Pobierz</button>
            </div>
        </form>
        
        <?php if (!empty($errorMessage)): ?>
        <div class="mt-4 bg-red-50 p-4 rounded-lg text-red-600">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($hasResult): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Informacje o filmie</h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="aspect-w-16 aspect-h-9 overflow-hidden rounded-lg">
                    <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong>Uwaga:</strong> Funkcja pobierania filmów z YouTube jest obecnie niedostępna ze względu na zasady serwisu YouTube. Pobieranie zawartości może naruszać warunki korzystania z YouTube.
                </div>
                
                <p>Jeśli potrzebujesz pobrać wideo do celów edukacyjnych lub osobistych, rozważ użycie YouTube Premium, które oferuje funkcje pobierania filmów do oglądania offline.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o YouTube Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">O YouTube Downloader</h2>
        
        <div class="space-y-4">
            <p>YouTube Downloader to narzędzie, które umożliwia pobieranie filmów z serwisu YouTube. Należy jednak pamiętać o kilku ważnych kwestiach:</p>
            
            <h3 class="text-xl font-semibold mt-4">Ograniczenia prawne</h3>
            <p>Pobieranie filmów z YouTube może być niezgodne z warunkami korzystania z serwisu. Zgodnie z regulaminem YouTube:</p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p>Nie możesz pobierać zawartości, chyba że wyraźnie widzisz link do pobrania lub jest to dozwolone w Warunkach korzystania z usługi.</p>
            </div>
            
            <p>Z tego powodu nasza usługa obecnie oferuje jedynie formularz demonstracyjny, bez faktycznej funkcjonalności pobierania.</p>
            
            <h3 class="text-xl font-semibold mt-4">Legalne alternatywy</h3>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong>YouTube Premium</strong> - płatna usługa oferująca legalne pobieranie filmów do oglądania offline</li>
                <li><strong>YouTube Music</strong> - umożliwia pobieranie muzyki do słuchania offline</li>
                <li><strong>Oficjalne aplikacje YouTube</strong> - na urządzeniach mobilnych umożliwiają czasowe zapisywanie filmów do oglądania offline</li>
            </ul>
            
            <h3 class="text-xl font-semibold mt-4">Dozwolone użycie</h3>
            <p>Istnieją sytuacje, w których pobieranie filmów z YouTube może być dozwolone:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Filmy udostępnione na licencji Creative Commons</li>
                <li>Filmy, których jesteś autorem</li>
                <li>Filmy w domenie publicznej</li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Zawsze sprawdzaj lokalnie obowiązujące przepisy prawa autorskiego i warunki korzystania z serwisów internetowych przed pobieraniem jakichkolwiek treści.</p>
            </div>
        </div>
    </div>
    
    <!-- Często zadawane pytania -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Często zadawane pytania</h2>
        
        <div class="space-y-4">
            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                    <span class="font-medium">Czy pobieranie filmów z YouTube jest legalne?</span>
                    <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                    <p>Pobieranie filmów z YouTube jest generalnie niezgodne z warunkami korzystania z serwisu, chyba że film jest wyraźnie oznaczony jako dostępny do pobrania. Korzystanie z nieoficjalnych narzędzi do pobierania może naruszać regulamin YouTube. Zalecamy korzystanie z oficjalnych metod, takich jak YouTube Premium.</p>
                </div>
            </div>
            
            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                    <span class="font-medium">Dlaczego nie mogę pobrać filmu z YouTube?</span>
                    <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                    <p>Nasza usługa YouTube Downloader obecnie nie oferuje faktycznej funkcjonalności pobierania ze względu na zasady serwisu YouTube. Jeśli potrzebujesz pobierać filmy legalnie, zalecamy korzystanie z YouTube Premium, który umożliwia pobieranie zawartości do oglądania offline.</p>
                </div>
            </div>
            
            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                    <span class="font-medium">Jak mogę legalnie korzystać z treści z YouTube?</span>
                    <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                    <p>Możesz legalnie korzystać z treści YouTube na kilka sposobów:</p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li>Subskrybując YouTube Premium, który umożliwia pobieranie filmów do oglądania offline</li>
                        <li>Korzystając z oficjalnej aplikacji YouTube na urządzeniach mobilnych, która pozwala na czasowe zapisywanie wybranych filmów</li>
                        <li>Oglądając filmy bezpośrednio na platformie YouTube</li>
                        <li>Korzystając z opcji udostępniania, aby dzielić się filmami z innymi</li>
                        <li>Osadzając filmy na swojej stronie przy użyciu oficjalnego kodu do osadzania</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>