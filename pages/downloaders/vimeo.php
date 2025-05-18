<?php
// Setting page title and description
$pageTitle = $lang['vimeo_page_title'] ?? 'Vimeo Downloader - Download Videos from Vimeo | Goorky.com';
$pageDescription = $lang['vimeo_page_description'] ?? 'Free Vimeo Downloader - download videos from Vimeo in high quality. Easy to use, no registration or installation required.';

// Initialize variables
$url = '';
$hasResult = false;
$errorMessage = '';
$videoId = '';
$videoData = null;

// Handle submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_vimeo'])) {
    // Get and validate data
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Check if URL is valid
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Check if it's a Vimeo link
        if (strpos($url, 'vimeo.com') !== false) {
            // Try to extract video ID
            $videoId = getVimeoId($url);
            
            if ($videoId) {
                // Get video metadata via Vimeo API
                $videoData = getVimeoMetadata($videoId);
                
                if ($videoData) {
                    $hasResult = true;
                } else {
                    $errorMessage = $lang['failed_get_video_info'] ?? 'Failed to get video information. The video may be private or deleted.';
                }
            } else {
                $errorMessage = $lang['failed_identify_vimeo'] ?? 'Failed to identify the video. Make sure the link points to a Vimeo video.';
            }
        } else {
            $errorMessage = $lang['not_vimeo_url'] ?? 'The URL provided is not a Vimeo link.';
        }
    } else {
        $errorMessage = $lang['enter_valid_vimeo_url'] ?? 'Please enter a valid Vimeo video URL.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['vimeo_downloader'] ?? 'Vimeo Downloader'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['vimeo_intro'] ?? 'Download videos from Vimeo using a link. Just paste the link to the video and click "Download".'; ?></p>
        
        <form method="POST" action="/vimeo" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2"><?php echo $lang['vimeo_video_link'] ?? 'Vimeo Video Link'; ?></label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://vimeo.com/...">
            </div>
            
            <div class="text-center">
                <button type="submit" name="download_vimeo" class="bg-teal-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-teal-700 transition"><?php echo $lang['download'] ?? 'Download'; ?></button>
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
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['found_video'] ?? 'Found Video'; ?></h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe src="https://player.vimeo.com/video/<?php echo htmlspecialchars($videoId); ?>" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen class="w-full h-full"></iframe>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($videoData['title'] ?? ($lang['vimeo_video'] ?? 'Vimeo Video')); ?></h3>
                    <p class="text-gray-600 text-sm mt-1">
                        <?php echo $lang['author'] ?? 'Author:'; ?> <?php echo htmlspecialchars($videoData['user_name'] ?? ($lang['unknown'] ?? 'Unknown')); ?> | 
                        <?php echo $lang['duration'] ?? 'Duration:'; ?> <?php echo isset($videoData['duration']) ? floor($videoData['duration'] / 60) . ':' . str_pad($videoData['duration'] % 60, 2, '0', STR_PAD_LEFT) : ($lang['unknown'] ?? 'Unknown'); ?>
                    </p>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['vimeo_privacy_notice'] ?? 'Downloading videos from Vimeo may be restricted depending on the privacy settings set by the video author. Our service offers downloading only for public videos.'; ?>
                </div>
                
                <p class="mb-4">
                    <?php echo htmlspecialchars($videoData['description'] ?? ($lang['no_description'] ?? 'No description.')); ?>
                </p>
                
                <div class="mt-4 space-y-2">
                    <?php if (isset($videoData['download_links']) && is_array($videoData['download_links'])): ?>
                        <?php foreach ($videoData['download_links'] as $quality => $link): ?>
                            <a href="#" class="bg-teal-600 text-white font-semibold px-6 py-2 rounded hover:bg-teal-700 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center">
                                <?php echo $lang['download_quality'] ?? 'Download in'; ?> <?php echo htmlspecialchars($quality); ?> <?php echo $lang['quality'] ?? 'quality'; ?>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <a href="#" class="bg-teal-600 text-white font-semibold px-6 py-2 rounded hover:bg-teal-700 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center"><?php echo $lang['download_hd'] ?? 'Download in HD Quality'; ?></a>
                        <a href="#" class="bg-teal-500 text-white font-semibold px-6 py-2 rounded hover:bg-teal-600 transition inline-block disabled opacity-50 cursor-not-allowed w-full text-center"><?php echo $lang['download_sd'] ?? 'Download in SD Quality'; ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- About Vimeo Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_vimeo_downloader'] ?? 'About Vimeo Downloader'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['vimeo_downloader_desc'] ?? 'Vimeo Downloader is a tool that allows you to download videos from the Vimeo platform. However, there are several important aspects to keep in mind:'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['vimeo_api'] ?? 'Vimeo API'; ?></h3>
            <p><?php echo $lang['vimeo_api_desc'] ?? 'Our tool uses the official Vimeo API to retrieve video metadata. This allows us to provide basic information about the video, such as title, author, and description.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['vimeo_terms'] ?? 'Vimeo Terms of Service'; ?></h3>
            <p><?php echo $lang['vimeo_terms_desc_intro'] ?? 'According to Vimeo\'s terms of service, downloading videos may be restricted. Vimeo states:'; ?></p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p><?php echo $lang['vimeo_terms_quote'] ?? 'You may not download, copy, or store Vimeo videos or parts of them unless it is explicitly permitted by the video owner or available through official platform features.'; ?></p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['legal_use_title'] ?? 'Legal Use'; ?></h3>
            <p><?php echo $lang['vimeo_legal_use_desc'] ?? 'There are situations where downloading videos from Vimeo may be allowed:'; ?></p>
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['download_own_videos'] ?? 'Downloading your own videos (which you have published yourself)'; ?></li>
                <li><?php echo $lang['videos_with_download_enabled'] ?? 'Videos that have the download option enabled by the owner'; ?></li>
                <li><?php echo $lang['download_licensed_videos'] ?? 'Videos shared under a license that allows downloading and use'; ?></li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['respect_vimeo_copyright'] ?? 'Always respect the copyright of Vimeo creators. Downloading and using others\' content without permission may violate copyright law and the terms of service.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Instructions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['how_to_download_vimeo'] ?? 'How to Download a Video from Vimeo'; ?></h2>
        
        <div class="space-y-4">
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <strong><?php echo $lang['find_video_vimeo'] ?? 'Find the video on Vimeo'; ?></strong>
                    <p class="mt-1"><?php echo $lang['find_video_vimeo_desc'] ?? 'Open vimeo.com and find the video you want to download.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['copy_video_link'] ?? 'Copy the video link'; ?></strong>
                    <p class="mt-1"><?php echo $lang['copy_video_link_desc'] ?? 'Copy the URL of the video from your browser\'s address bar. The URL should have the format "https://vimeo.com/123456789", where "123456789" is the video ID.'; ?></p>
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
                <p class="text-blue-800"><strong><?php echo $lang['tip'] ?? 'Tip:'; ?></strong> <?php echo $lang['vimeo_download_tip'] ?? 'Some creators on Vimeo make their videos available for download directly on the platform. Check if there\'s a "Download" button under the video - if there is, you can legally download the video directly from Vimeo.'; ?></p>
            </div>
        </div>
    </div>
</div>