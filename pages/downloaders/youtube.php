<?php
// Ustawienie tytułu i opisu strony
$pageTitle = $lang['youtube_page_title'] ?? 'YouTube Downloader - Download videos from YouTube | Goorky.com';
$pageDescription = $lang['youtube_page_description'] ?? 'Free YouTube Downloader - download videos and music from YouTube in high quality. Easy to use, no registration or installation needed.';

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
            $errorMessage = $lang['invalid_youtube_url'] ?? 'Invalid YouTube video URL. Please make sure the link is correct.';
        }
    } else {
        $errorMessage = $lang['enter_valid_url'] ?? 'Please enter a valid YouTube video URL.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['youtube_downloader'] ?? 'YouTube Downloader'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['youtube_intro'] ?? 'Download YouTube videos in various formats and qualities. Just paste a YouTube link and choose a format.'; ?></p>
        
        <form method="POST" action="/youtube" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2"><?php echo $lang['youtube_video_link'] ?? 'YouTube video link'; ?></label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.youtube.com/watch?v=...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_youtube" class="bg-red-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-red-700 transition"><?php echo $lang['download'] ?? 'Download'; ?></button>
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
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['video_info'] ?? 'Video Information'; ?></h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="aspect-w-16 aspect-h-9 overflow-hidden rounded-lg">
                    <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($videoId); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['youtube_terms_notice'] ?? 'The video download feature is currently unavailable due to YouTube\'s terms. Downloading content may violate YouTube\'s terms of service.'; ?>
                </div>
                
                <p><?php echo $lang['youtube_premium_suggestion'] ?? 'If you need to download a video for educational or personal purposes, consider using YouTube Premium which offers offline viewing features.'; ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Informacje o YouTube Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_youtube_downloader'] ?? 'About YouTube Downloader'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['youtube_downloader_desc'] ?? 'YouTube Downloader is a tool that allows downloading videos from YouTube. However, please note the following:'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['legal_restrictions'] ?? 'Legal Restrictions'; ?></h3>
            <p><?php echo $lang['youtube_terms_info'] ?? 'Downloading YouTube videos may violate the platform\'s terms of service. According to YouTube\'s Terms:'; ?></p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p><?php echo $lang['youtube_terms_quote'] ?? 'You may not download content unless a download link is clearly provided or allowed under the Terms of Service.'; ?></p>
            </div>
            
            <p><?php echo $lang['demo_form_notice'] ?? 'For this reason, our service currently offers a demo form only, without actual downloading functionality.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['legal_alternatives'] ?? 'Legal Alternatives'; ?></h3>
            <ul class="list-disc pl-6 space-y-2">
                <li><strong><?php echo $lang['youtube_premium'] ?? 'YouTube Premium'; ?></strong> - <?php echo $lang['youtube_premium_desc'] ?? 'a paid service offering legal downloads for offline viewing'; ?></li>
                <li><strong><?php echo $lang['youtube_music'] ?? 'YouTube Music'; ?></strong> - <?php echo $lang['youtube_music_desc'] ?? 'allows downloading music for offline listening'; ?></li>
                <li><strong><?php echo $lang['youtube_apps'] ?? 'Official YouTube Apps'; ?></strong> - <?php echo $lang['youtube_apps_desc'] ?? 'on mobile devices allow temporary saving of videos for offline viewing'; ?></li>
            </ul>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['permitted_use'] ?? 'Permitted Use'; ?></h3>
            <p><?php echo $lang['permitted_use_info'] ?? 'There are cases where downloading from YouTube is permitted:'; ?></p>
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['creative_commons'] ?? 'Videos licensed under Creative Commons'; ?></li>
                <li><?php echo $lang['own_videos'] ?? 'Videos you own'; ?></li>
                <li><?php echo $lang['public_domain'] ?? 'Videos in the public domain'; ?></li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['check_local_laws'] ?? 'Always check local copyright laws and the website\'s terms of use before downloading any content.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Często zadawane pytania -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['faq'] ?? 'Frequently Asked Questions'; ?></h2>
        
        <div class="space-y-4">
            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                    <span class="font-medium"><?php echo $lang['is_youtube_download_legal'] ?? 'Is it legal to download videos from YouTube?'; ?></span>
                    <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                    <p><?php echo $lang['is_youtube_download_legal_answer'] ?? 'Downloading videos from YouTube generally violates the terms of service unless clearly marked as downloadable. Using unofficial tools may break YouTube rules. We recommend using official methods like YouTube Premium.'; ?></p>
                </div>
            </div>
            
            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                    <span class="font-medium"><?php echo $lang['why_cant_download'] ?? 'Why can\'t I download a YouTube video?'; ?></span>
                    <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                    <p><?php echo $lang['why_cant_download_answer'] ?? 'Our YouTube Downloader currently does not support actual downloading due to YouTube policies. For legal downloads, use YouTube Premium which enables offline viewing.'; ?></p>
                </div>
            </div>
            
            <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                    <span class="font-medium"><?php echo $lang['how_to_use_youtube_legally'] ?? 'How can I legally use YouTube content?'; ?></span>
                    <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                    <p><?php echo $lang['legal_youtube_use_intro'] ?? 'You can legally use YouTube content in several ways:'; ?></p>
                    <ul class="list-disc pl-6 mt-2 space-y-1">
                        <li><?php echo $lang['youtube_premium_option'] ?? 'By subscribing to YouTube Premium, which allows downloading videos for offline viewing'; ?></li>
                        <li><?php echo $lang['youtube_app_option'] ?? 'By using the official YouTube app on mobile devices, which enables temporary video saving'; ?></li>
                        <li><?php echo $lang['youtube_watch_option'] ?? 'By watching directly on the YouTube platform'; ?></li>
                        <li><?php echo $lang['youtube_share_option'] ?? 'By using the share feature to share videos with others'; ?></li>
                        <li><?php echo $lang['youtube_embed_option'] ?? 'By embedding videos on your site using the official embed code'; ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>