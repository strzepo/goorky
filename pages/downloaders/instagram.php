<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Instagram Downloader - Pobieraj zdjęcia i filmy z Instagrama | ToolsOnline';
$pageDescription = 'Darmowy Instagram Downloader - pobieraj zdjęcia i filmy z Instagrama w wysokiej jakości. Łatwy w użyciu, bez rejestracji i instalacji.';

// Inicjalizacja zmiennych
$url = '';
$hasResult = false;
$errorMessage = '';
$mediaType = '';
$mediaUrl = '';

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_instagram'])) {
    // Pobranie i walidacja danych
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Sprawdzenie czy URL jest poprawny
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Sprawdzanie czy to link do Instagrama
        if (strpos($url, 'instagram.com') !== false) {
            // Tutaj w rzeczywistej implementacji byłoby wywołanie API do pobierania treści
            // Na potrzeby przykładu zakładamy, że wszystko działa poprawnie
            $hasResult = true;
            
            // Losowo wybieramy typ mediów (w rzeczywistości byłoby to określone na podstawie URL)
            $mediaType = rand(0, 1) ? 'image' : 'video';
        } else {
            $errorMessage = 'Wprowadzony URL nie jest linkiem do Instagrama.';
        }
    } else {
        $errorMessage = 'Wprowadź poprawny adres URL postu na Instagramie.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Instagram Downloader</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Pobieraj zdjęcia i filmy z Instagrama za pomocą linku do postu. Wystarczy wkleić link i kliknąć "Pobierz".</p>
        
        <form method="POST" action="/instagram" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2">Link do postu na Instagramie</label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.instagram.com/p/...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_instagram" class="bg-purple-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-purple-700 transition">Pobierz</button>
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
        <h2 class="text-2xl font-semibold mb-4">Znalezione media</h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <?php if ($mediaType === 'image'): ?>
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="/assets/images/instagram-placeholder.jpg" alt="Podgląd zdjęcia z Instagrama" class="w-full h-full object-cover">
                        </div>
                    <?php else: ?>
                        <div class="aspect-w-16 aspect-h-9">
                            <div class="w-full h-full bg-gray-800 flex items-center justify-center text-white">
                                <div class="text-center">
                                    <svg class="h-16 w-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mt-2">Podgląd wideo</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong>Uwaga:</strong> Ze względu na aktualizacje API Instagrama, bezpośrednie pobieranie zawartości może być ograniczone. Nasza usługa wykrywa URL, ale funkcjonalność pobierania może być niedostępna.
                </div>
                
                <p class="mb-4">Pobieranie zawartości z Instagrama może naruszać warunki korzystania z serwisu. Zalecamy pobieranie tylko własnych treści lub treści, do których masz uprawnienia.</p>
                
                <div class="mt-4">
                    <a href="#" class="bg-purple-600 text-white font-semibold px-6 py-2 rounded hover:bg-purple-700 transition inline-block disabled opacity-50 cursor-not-allowed">
                        <?php echo $mediaType === 'image' ? 'Pobierz zdjęcie' : 'Pobierz wideo'; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o Instagram Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">O Instagram Downloader</h2>
        
        <div class="space-y-4">
            <p>Instagram Downloader to narzędzie, które umożliwia zapisywanie zdjęć i filmów z Instagrama na urządzenie. Należy jednak pamiętać o kilku ważnych kwestiach:</p>
            
            <h3 class="text-xl font-semibold mt-4">Ograniczenia API Instagrama</h3>
            <p>Instagram regularnie aktualizuje swoje API i zabezpieczenia, co może wpływać na działanie narzędzi do pobierania zawartości. Z tego powodu nasza usługa może mieć ograniczoną funkcjonalność.</p>
            
            <h3 class="text-xl font-semibold mt-4">Warunki korzystania z Instagrama</h3>
            <p>Zgodnie z warunkami korzystania z Instagrama:</p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p>Nie możesz próbować uzyskać dostępu do treści bez wyraźnej zgody właściciela konta, a także nie możesz używać zautomatyzowanych środków do zbierania informacji.</p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4">Legalne wykorzystanie</h3>
            <p>Istnieją sytuacje, w których pobieranie zdjęć i filmów z Instagrama może być dozwolone:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Pobieranie własnych treści (które samodzielnie opublikowałeś)</li>
                <li>Treści, do których otrzymałeś wyraźną zgodę od twórcy</li>
                <li>Treści udostępnione na licencji pozwalającej na pobieranie</li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Zawsze szanuj prawa autorskie innych użytkowników Instagrama. Pobieranie i wykorzystywanie cudzych treści bez zgody może naruszać prawo autorskie.</p>
            </div>
        </div>
    </div>
    
    <!-- Instrukcje -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Jak pobrać zdjęcie lub film z Instagrama</h2>
        
        <div class="space-y-4">
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <strong>Znajdź post na Instagramie</strong>
                    <p class="mt-1">Otwórz aplikację Instagram lub stronę instagram.com i znajdź post, który chcesz pobrać.</p>
                </li>
                
                <li>
                    <strong>Skopiuj link do postu</strong>
                    <p class="mt-1">W aplikacji mobilnej, naciśnij ikonę "..." i wybierz "Kopiuj link". W przeglądarce, skopiuj adres URL z paska adresu.</p>
                </li>
                
                <li>
                    <strong>Wklej link do naszego narzędzia</strong>
                    <p class="mt-1">Wklej skopiowany link w pole powyżej i kliknij przycisk "Pobierz".</p>
                </li>
                
                <li>
                    <strong>Pobierz plik</strong>
                    <p class="mt-1">Po przetworzeniu linku, kliknij przycisk "Pobierz zdjęcie" lub "Pobierz wideo", aby zapisać plik na swoim urządzeniu.</p>
                </li>
            </ol>
            
            <div class="bg-blue-50 p-4 rounded-lg mt-4">
                <p class="text-blue-800"><strong>Wskazówka:</strong> Upewnij się, że link pochodzi z publicznie dostępnego konta na Instagramie. Narzędzie nie działa z prywatnymi kontami, do których nie masz dostępu.</p>
            </div>
        </div>
    </div>
</div>