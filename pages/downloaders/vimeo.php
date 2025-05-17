<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'Vimeo Downloader - Pobieraj filmy z Vimeo | ToolsOnline';
$pageDescription = 'Darmowy Vimeo Downloader - pobieraj filmy z Vimeo w wysokiej jakości. Łatwy w użyciu, bez rejestracji i instalacji.';

// Inicjalizacja zmiennych
$url = '';
$hasResult = false;
$errorMessage = '';
$videoId = '';
$videoData = null;

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_vimeo'])) {
    // Pobranie i walidacja danych
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Sprawdzenie czy URL jest poprawny
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Sprawdzanie czy to link do Vimeo
        if (strpos($url, 'vimeo.com') !== false) {
            // Próba wyodrębnienia ID filmu
            $videoId = getVimeoId($url);
            
            if ($videoId) {
                // Pobieranie metadanych filmu przez API Vimeo
                $videoData = getVimeoMetadata($videoId);
                
                if ($videoData) {
                    $hasResult = true;
                } else {
                    $errorMessage = 'Nie udało się pobrać informacji o filmie. Film może być prywatny lub usunięty.';
                }
            } else {
                $errorMessage = 'Nie udało się zidentyfikować filmu. Upewnij się, że link prowadzi do filmu na Vimeo.';
            }
        } else {
            $errorMessage = 'Wprowadzony URL nie jest linkiem do Vimeo.';
        }
    } else {
        $errorMessage = 'Wprowadź poprawny adres URL filmu na Vimeo.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6">Vimeo Downloader</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4">Pobieraj filmy z Vimeo za pomocą linku. Wystarczy wkleić link do filmu i kliknąć "Pobierz".</p>
        
        <form method="POST" action="/vimeo" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2">Link do filmu na Vimeo</label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://vimeo.com/...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_vimeo" class="bg-teal-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-teal-700 transition">Pobierz</button>
            </div>
        </form>
        
        <?php if (!empty($errorMessage)): ?>
        <div class="mt-4 bg-red-50 p-4 rounded-lg text-red-600">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($hasResult && $videoData): ?>
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">Znaleziony film</h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="https://player.vimeo.com/video/<?php echo htmlspecialchars($videoId); ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($videoData['title'] ?? 'Film z Vimeo'); ?></h3>
                    <p class="text-gray-600 text-sm mt-1">
                        Autor: <?php echo htmlspecialchars($videoData['user_name'] ?? 'Nieznany'); ?> | 
                        Czas trwania: <?php echo isset($videoData['duration']) ? floor($videoData['duration'] / 60) . ':' . str_pad($videoData['duration'] % 60, 2, '0', STR_PAD_LEFT) : 'Nieznany'; ?>
                    </p>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong>Uwaga:</strong> Pobieranie filmów z Vimeo może być ograniczone w zależności od ustawień prywatności określonych przez autora filmu. Nasza usługa oferuje pobieranie tylko dla filmów publicznych.
                </div>
                
                <p class="mb-4">
                    <?php echo htmlspecialchars($videoData['description'] ?? 'Brak opisu.'); ?>
                </p>
                
                <div class="mt-4 space-y-2">
                    <?php if (isset($videoData['download_links']) && is_array($videoData['download_links'])): ?>
                        <?php foreach ($videoData['download_links'] as $quality => $link): ?>
                            <a href="#" class="bg-teal-600 text-white font-semibold px-6 py-2 rounded hover:bg-teal-700 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center">
                                Pobierz w jakości <?php echo htmlspecialchars($quality); ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#" class="bg-teal-600 text-white font-semibold px-6 py-2 rounded hover:bg-teal-700 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center">Pobierz w jakości HD</a>
                        <a href="#" class="bg-teal-500 text-white font-semibold px-6 py-2 rounded hover:bg-teal-600 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center">Pobierz w jakości SD</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o Vimeo Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4">O Vimeo Downloader</h2>
        
        <div class="space-y-4">
            <p>Vimeo Downloader to narzędzie, które umożliwia pobieranie filmów z platformy Vimeo. Należy jednak pamiętać o kilku ważnych aspektach:</p>
            
            <h3 class="text-xl font-semibold mt-4">API Vimeo</h3>
            <p>Nasze narzędzie wykorzystuje oficjalne API Vimeo do pobierania metadanych filmów. Dzięki temu możemy zapewnić podstawowe informacje o filmie, takie jak tytuł, autor czy opis.</p>
            
            <h3 class="text-xl font-semibold mt-4">Warunki korzystania z Vimeo</h3>
            <p>Zgodnie z warunkami korzystania z Vimeo, pobieranie filmów może być ograniczone. Vimeo stanowi:</p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p>Nie możesz pobierać, kopiować ani przechowywać filmów Vimeo lub ich części, chyba że jest to wyraźnie dozwolone przez właściciela filmu lub dostępne w oficjalnych funkcjach platformy.</p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4">Legalne wykorzystanie</h3>
            <p>Istnieją sytuacje, w których pobieranie filmów z Vimeo może być dozwolone:</p>
            <ul class="list-disc pl-6 space-y-2">
                <li>Pobieranie własnych filmów (które samodzielnie opublikowałeś)</li>
                <li>Filmy, które posiadają włączoną opcję pobierania przez właściciela</li>
                <li>Filmy udostępnione na licencji pozwalającej na pobieranie i wykorzystanie</li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong>Uwaga:</strong> Zawsze szanuj prawa autorskie twórców Vimeo. Pobieranie i wykorzystywanie cudzych treści bez zgody może naruszać prawo autorskie i warunki korzystania z serwisu.</p>
            </div>
        </div>
    </div>
    
    <!-- Instrukcje -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4">Jak pobrać film z Vimeo</h2>
        
        <div class="space-y-4">
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <strong>Znajdź film na Vimeo</strong>
                    <p class="mt-1">Otwórz stronę vimeo.com i znajdź film, który chcesz pobrać.</p>
                </li>
                
                <li>
                    <strong>Skopiuj link do filmu</strong>
                    <p class="mt-1">Skopiuj adres URL filmu z paska adresu przeglądarki. URL powinien mieć format "https://vimeo.com/123456789", gdzie "123456789" to ID filmu.</p>
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
                <p class="text-blue-800"><strong>Wskazówka:</strong> Niektórzy twórcy na Vimeo udostępniają swoje filmy z opcją pobierania bezpośrednio na platformie. Sprawdź, czy pod filmem nie ma przycisku "Download" - jeśli jest, możesz legalnie pobrać film bezpośrednio z Vimeo.</p>
            </div>
        </div>
    </div>
</div>