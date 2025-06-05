<?php
// Ustawienie tytułu i opisu strony
$pageTitle = $lang['facebook_page_title'] ?? 'Facebook Downloader - Download Facebook Videos | Goorky.com';
$pageDescription = $lang['facebook_page_description'] ?? 'Free Facebook Downloader - download public videos from Facebook in high quality. Easy to use, no registration or installation required.';

// Inicjalizacja zmiennych
$url = '';
$hasResult = false;
$errorMessage = '';
$videoId = '';
$downloadLinks = null;

// Dołączenie funkcji do pobierania filmów z Facebooka
require_once __DIR__ . '/../../includes/facebook-downloader.php';

// Obsługa przesłanego formularza
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_facebook'])) {
    // Pobranie i walidacja danych
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Sprawdzenie czy URL jest poprawny
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Sprawdzenie czy to link do Facebooka
        if (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false) {
            
            // Pobranie linków do filmu
            $downloadLinks = getVideoLinks($url);
            
            if ($downloadLinks && (!empty($downloadLinks['hd']) || !empty($downloadLinks['sd']))) {
                $hasResult = true;
                // Próba uzyskania video ID (opcjonalnie)
                $videoId = getFacebookVideoId($url);
            } else {
                $errorMessage = $lang['failed_get_video_link'] ?? 'Failed to get video link.';
            }
        } else {
            $errorMessage = $lang['not_facebook_url'] ?? 'The URL provided is not a Facebook link.';
        }
    } else {
        $errorMessage = $lang['enter_valid_facebook_url'] ?? 'Please enter a valid Facebook video URL.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['facebook_downloader'] ?? 'Facebook Downloader'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['facebook_intro'] ?? 'Download public videos from Facebook using a link. Just paste the video link and click "Download".'; ?></p>
        
        <form method="POST" action="/facebook" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2"><?php echo $lang['facebook_video_link'] ?? 'Facebook Video Link'; ?></label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.facebook.com/watch?v=...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_facebook" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition">
                    <?php echo $lang['download'] ?? 'Download'; ?>
                </button>
            </div>
        </form>
        
        <?php if (!empty($errorMessage)): ?>
        <div class="mt-4 bg-red-50 p-4 rounded-lg text-red-600">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($hasResult): ?>
    <!-- Wyświetlenie przycisków do pobierania -->
    <?php echo renderFacebookDownloadButtons($url); ?>
    <?php endif; ?>

        <!-- Social Media Buttons -->
        <?php include BASE_PATH . '/includes/social.php'; ?>
    
    <!-- Instrukcje -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['how_to_download_facebook'] ?? 'How to Download a Video from Facebook'; ?></h2>
        
        <div class="space-y-4">
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <strong><?php echo $lang['find_video'] ?? 'Find the video on Facebook'; ?></strong>
                    <p class="mt-1"><?php echo $lang['find_video_desc'] ?? 'Open the Facebook app or facebook.com and find the video you want to download.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['copy_link'] ?? 'Copy the video link'; ?></strong>
                    <p class="mt-1"><?php echo $lang['copy_link_desc'] ?? 'On a computer, right-click on the video and select "Copy link address" or "Copy URL". On the mobile app, tap the "Share" button and choose the "Copy link" option.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['paste_link'] ?? 'Paste the link to our tool'; ?></strong>
                    <p class="mt-1"><?php echo $lang['paste_link_desc'] ?? 'Paste the copied link in the field above and click the "Download" button.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['choose_quality'] ?? 'Choose quality and download'; ?></strong>
                    <p class="mt-1"><?php echo $lang['choose_quality_desc'] ?? 'After processing the link, select your preferred video quality and click the appropriate button to download the video.'; ?></p>
                </li>
            </ol>
            
            <div class="bg-blue-50 p-4 rounded-lg mt-4">
                <p class="text-blue-800"><strong><?php echo $lang['tip'] ?? 'Tip:'; ?></strong> <?php echo $lang['public_post_tip'] ?? 'Make sure the video comes from a public post on Facebook. The tool won\'t work with private posts you don\'t have access to.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Informacje o prywatnych filmach -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-8">
        <h2 class="text-2xl font-semibold mb-4">Jak pobrać prywatne filmy z Facebooka?</h2>
        <p class="mb-4">Aby pobrać prywatne filmy z Facebooka, możesz skorzystać z jednej z poniższych metod:</p>
        
        <div class="space-y-6">
            <div class="bg-gray-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Metoda 1: Użyj kodu źródłowego strony</h3>
                <ol class="list-decimal pl-6 space-y-2">
                    <li>Otwórz stronę Facebooka i zaloguj się do swojego konta</li>
                    <li>Znajdź film, który chcesz pobrać</li>
                    <li>Kliknij na czas publikacji filmu, aby otworzyć go w nowym oknie</li>
                    <li>Naciśnij Ctrl+U (Windows) lub ⌘+Option+U (Mac), aby wyświetlić kod źródłowy strony</li>
                    <li>Wklej kod źródłowy strony w nasze pole formularza powyżej i kliknij "Pobierz"</li>
                </ol>
            </div>
            
            <div class="bg-gray-100 p-4 rounded-lg">
                <h3 class="text-lg font-semibold mb-2">Metoda 2: Użyj urządzenia mobilnego</h3>
                <ol class="list-decimal pl-6 space-y-2">
                    <li>Otwórz aplikację Facebook na swoim telefonie</li>
                    <li>Znajdź film, który chcesz pobrać</li>
                    <li>Dotknij przycisku "Udostępnij" pod filmem</li>
                    <li>Wybierz opcję "Kopiuj link"</li>
                    <li>Wklej link w naszym formularzu powyżej i kliknij "Pobierz"</li>
                </ol>
            </div>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg mt-6">
            <p class="text-yellow-800">
                <strong>Uwaga:</strong> Pobieranie filmów z Facebooka może naruszać warunki korzystania z usługi. 
                Zalecamy pobieranie tylko własnych treści lub treści, na których pobieranie masz pozwolenie.
            </p>
        </div>
    </div>
</div>

<!-- Skrypt do obsługi pobierania -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Funkcja do kopiowania linku do schowka
    window.copyVideoLink = function(link) {
        navigator.clipboard.writeText(link).then(function() {
            alert('Link został skopiowany do schowka!');
        }, function() {
            // Fallback dla starszych przeglądarek
            const textarea = document.createElement('textarea');
            textarea.value = link;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            alert('Link został skopiowany do schowka!');
        });
    };
});
</script>