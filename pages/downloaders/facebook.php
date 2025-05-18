<?php
function getFacebookVideoDirectUrl($fbUrl) {
    $escapedUrl = escapeshellarg($fbUrl);
    $scriptPath = realpath(__DIR__ . '/../../../scripts/fb_scraper.js');

    if (!$scriptPath) {
        return ''; // Script not found
    }

    $command = "node " . escapeshellarg($scriptPath) . " $escapedUrl";
    $output = shell_exec($command);
    return trim($output);
}
// Setting page title and description
$pageTitle = $lang['facebook_page_title'] ?? 'Facebook Downloader - Download Facebook Videos | Goorky.com';
$pageDescription = $lang['facebook_page_description'] ?? 'Free Facebook Downloader - download public videos from Facebook in high quality. Easy to use, no registration or installation required.';

// Initialize variables
$url = '';
$hasResult = false;
$errorMessage = '';
$videoId = '';
$directVideoUrl = '';

// Handle submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_facebook'])) {
    // Get and validate data
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Check if URL is valid
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Check if it's a Facebook link
        if (strpos($url, 'facebook.com') !== false || strpos($url, 'fb.watch') !== false) {
            // Try to extract video ID
            $videoId = getFacebookVideoId($url);
            
            if ($videoId) {
                $hasResult = true;
                $directVideoUrl = getFacebookVideoDirectUrl($url);
                if (!$directVideoUrl) {
                    $errorMessage = $lang['failed_get_video_link'] ?? 'Failed to get video link.';
                    $hasResult = false;
                }
            } else {
                $errorMessage = $lang['failed_identify_video'] ?? 'Failed to identify the video. Make sure the link points to a public Facebook video.';
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
                <button type="submit" name="download_facebook" class="bg-blue-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['download'] ?? 'Download'; ?></button>
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
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['found_video'] ?? 'Found Video'; ?></h2>
        
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
                                <p class="mt-2"><?php echo $lang['facebook_video_preview'] ?? 'Facebook Video Preview'; ?></p>
                                <p class="mt-1 text-gray-400"><?php echo $lang['video_id'] ?? 'Video ID:'; ?> <?php echo htmlspecialchars($videoId); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['facebook_api_limitations'] ?? 'Due to Facebook API limitations, direct video downloading may be restricted. Our service detects the URL, but downloading functionality may be limited.'; ?>
                </div>
                
                <p class="mb-4"><?php echo $lang['facebook_terms_warning'] ?? 'Downloading videos from Facebook may violate the terms of service. We recommend only downloading your own content or content you have permission to download.'; ?></p>
                
                <div class="mt-4 space-y-2">
                    <a href="<?php echo htmlspecialchars($directVideoUrl); ?>" download class="bg-blue-600 text-white font-semibold px-6 py-2 rounded hover:bg-blue-700 transition inline-block w-full text-center">
                        <?php echo $lang['download_hd'] ?? 'Download in HD Quality'; ?>
                    </a>
                    <a href="<?php echo htmlspecialchars($directVideoUrl); ?>" download class="bg-blue-500 text-white font-semibold px-6 py-2 rounded hover:bg-blue-600 transition inline-block w-full text-center">
                        <?php echo $lang['download_sd'] ?? 'Download in SD Quality'; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- About Facebook Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_facebook_downloader'] ?? 'About Facebook Downloader'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['facebook_downloader_desc'] ?? 'Facebook Downloader is a tool that allows you to download public videos from Facebook. However, there are some important considerations to keep in mind:'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['facebook_api_limitations_title'] ?? 'Facebook API Limitations'; ?></h3>
            <p><?php echo $lang['facebook_api_limitations_desc'] ?? 'Facebook regularly updates its API and security measures, which can affect the functionality of downloading tools. As a result, our service may have limited functionality.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['facebook_terms_title'] ?? 'Facebook Terms of Service'; ?></h3>
            <p><?php echo $lang['facebook_terms_desc'] ?? 'According to Facebook\'s terms of service, downloading content may be against their rules. Facebook states:'; ?></p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p><?php echo $lang['facebook_terms_quote'] ?? 'You will not collect users\' content or information, or otherwise access Facebook, using automated means (such as harvesting bots, robots, spiders, or scrapers) without our prior permission.'; ?></p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['legal_use_title'] ?? 'Legal Use'; ?></h3>
            <p><?php echo $lang['facebook_legal_use_desc'] ?? 'There are situations where downloading videos from Facebook may be allowed:'; ?></p>
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['download_own_content'] ?? 'Downloading your own content (which you have published yourself)'; ?></li>
                <li><?php echo $lang['download_with_permission'] ?? 'Content for which you have received explicit permission from the creator'; ?></li>
                <li><?php echo $lang['download_licensed_content'] ?? 'Content shared under a license that allows downloading'; ?></li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['respect_copyright'] ?? 'Always respect the copyright of other Facebook users. Downloading and using others\' content without permission may violate copyright law.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Instructions -->
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
</div>