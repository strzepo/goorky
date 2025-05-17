<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Facebook Downloader - Pobieraj filmy z Facebooka | ToolsOnline';
$pageDescription = 'Darmowy Facebook Downloader - pobieraj publiczne filmy z Facebooka w wysokiej jakości. Łatwy w użyciu, bez rejestracji i instalacji.';

// Inicjalizacja zmiennych
$url = '';
$hasResult = false;
$errorMessage = '';
$videoId = '';

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_facebook'])) {
    // Pobranie i walidacja danych
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Sprawdzenie czy URL jest poprawny
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Sprawdzanie czy to link do Facebooka
        if (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false) {
            // Próba wyodrębnienia ID filmu
            $videoId = getFacebookVideoId($url);
            
            if ($videoId) {
                $hasResult = true;
            } else {
                $errorMessage = 'Nie udało się zidentyfikować filmu. Upewnij się, że link prowadzi do publicznego filmu na Facebooku.';
            }
        } else {
            $errorMessage = 'Wprowadzony URL nie jest linkiem do Facebooka.';
        }
    } else {
        $errorMessage = 'Wprowadź poprawny adres URL filmu na Facebooku.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Facebook Downloader</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Pobieraj publiczne filmy z Facebooka za pomocą linku. Wystarczy wkleić link do filmu i kliknąć "Pobierz".</p>
        
        <form method="POST" action="/facebook" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2">Link do filmu na Facebooku</label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.facebook.com/watch?v=...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_facebook" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">Pobierz</button>
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
        <h2 class="text-2xl font-semibold mb-4">Znaleziony film</h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <div class="aspect-w-16 aspect-h-9">
                        <div class="w-full h-full bg-gray-800 flex items-center justify-center text-white">
                            <div class="text-center">
                                <svg class="h-16 w-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mt-2">Podgląd filmu z Facebooka</p>
                                <p class="mt-1 text-gray-400">ID filmu: <?php echo htmlspecialchars($videoId); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong>Uwaga:</strong> Ze względu na ograniczenia API Facebooka, bezpośrednie pobieranie filmów może być utrudnione. Nasza usługa wykrywa URL, ale funkcjonalność pobierania może być ograniczona.
                </div>
                
                <p class="mb-4">Pobieranie filmów z Facebooka może naruszać warunki korzystania z serwisu. Zalecamy pobieranie tylko własnych treści lub treści, do których masz uprawnienia.</p>
                
                <div class="mt-4 space-y-2">
                    <a href="#" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded hover:bg-blue-700 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center">Pobierz w jakości HD</a>
                    <a href="#" class="bg-blue-500 text-white font-semibold px-6 py-2 rounded hover:bg-blue-600 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center">Pobierz w jakości SD</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o Facebook Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">O Facebook Downloader</h2>
        
        <div class="space-y-4">
            <p>Facebook Downloader to narzędzie, które umożliwia pobieranie publicznych filmów z Facebooka. Należy jednak pamiętać o kilku ważnych kwestiach:</p>
            
            <h3 class="text-xl font-semibold mt-4">Ograniczenia API Facebooka</h3>
            <p>Facebook regularnie aktualizuje swoje API i zabezpieczenia, co może wpływać na działanie narzędzi do pobierania zawartości. Z tego powodu nasza usługa może mieć ograniczoną funkcjonalność.</p>
            
            <h3 class="text-xl font-semibold mt-4">Warunki korzystania z Facebooka</h3>
            <p>Zgodnie z warunkami korzystania z Facebooka, pobieranie zawartości może być niezgodne z regulaminem serwisu. Facebook stanowi:</p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p>Nie będziesz zbierać treści ani informacji innych użytkowników ani w inny sposób uzyskiwać dostępu do Facebooka przy użyciu zautomatyzowanych środków (takich jak roboty indeksujące, boty lub skrobaki) bez naszej uprzedniej zgody.</p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4">Legalne wykorzystanie</h3>
            <p>Istnieją sytuacje, w których pobieranie filmów z Facebooka może być dozwolone:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Pobieranie własnych treści (które samodzielnie opublikowałeś)</li>
                <li>Treści, do których otrzymałeś wyraźną zgodę od twórcy</li>
                <li>Treści udostępnione na licencji pozwalającej na pobieranie</li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Zawsze szanuj prawa autorskie innych użytkowników Facebooka. Pobieranie i wykorzystywanie cudzych treści bez zgody może naruszać prawo autorskie.</p>
            </div>
        </div>
    </div>
    
    <!-- Instrukcje -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Jak pobrać film z Facebooka</h2>
        
        <div class="space-y-4">
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <strong>Znajdź film na Facebooku</strong>
                    <p class="mt-1">Otwórz aplikację Facebook lub stronę facebook.com i znajdź film, który chcesz pobrać.</p>
                </li>
                
                <li>
                    <strong>Skopiuj link do filmu</strong>
                    <p class="mt-1">Na komputerze, kliknij prawym przyciskiem myszy na film i wybierz "Kopiuj adres linku" lub "Kopiuj URL". W aplikacji mobilnej, naciśnij przycisk "Udostępnij" i wybierz opcję "Kopiuj link".</p>
                </li>
                
                <li>
                    <strong>Wklej link do naszego narzędzia</strong>
                    <p class="mt-1">Wklej skopiowany link w pole powyżej i kliknij przycisk "Pobierz".</p>
                </li>
                
                <li>
                    <strong>Wybierz jakość i pobierz</strong>
                    <p class="mt-1">Po przetworzeniu linku, wybierz preferowaną jakość wideo i kliknij odpowiedni przycisk, aby pobrać film.</p>
                </li>
            </ol>
            
            <div class="bg-blue-50 p-4 rounded-lg mt-4">
                <p class="text-blue-800"><strong>Wskazówka:</strong> Upewnij się, że film pochodzi z publicznego postu na Facebooku. Narzędzie nie działa z prywatnymi postami, do których nie masz dostępu.</p>
            </div>
        </div>
    </div>
</div>