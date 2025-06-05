<?php
// Setting page title and description
$pageTitle = $lang['instagram_page_title'] ?? 'Instagram Downloader - Download Photos and Videos from Instagram | Goorky.com';
$pageDescription = $lang['instagram_page_description'] ?? 'Free Instagram Downloader - download photos and videos from Instagram in high quality. Easy to use, no registration or installation required.';

// Initialize variables
$url = '';
$hasResult = false;
$errorMessage = '';
$mediaType = '';
$mediaUrl = '';

// Handle submitted form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download_instagram'])) {
    // Get and validate data
    $url = sanitizeInput($_POST['url'] ?? '');
    
    // Check if URL is valid
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Check if it's an Instagram link
        if (strpos($url, 'instagram.com') !== false) {
            // In a real implementation, we would call an API to fetch content
            // For example purposes, we assume everything works correctly
            $hasResult = true;
            
            // Randomly choose a media type (in reality, this would be determined based on the URL)
            $mediaType = rand(0, 1) ? 'image' : 'video';
        } else {
            $errorMessage = $lang['not_instagram_url'] ?? 'The URL provided is not an Instagram link.';
        }
    } else {
        $errorMessage = $lang['enter_valid_instagram_url'] ?? 'Please enter a valid Instagram post URL.';
    }
}
?>

<div class="max-w-4xl mx-auto">
    <h1 class="text-3xl font-bold mb-6"><?php echo $lang['instagram_downloader'] ?? 'Instagram Downloader'; ?></h1>
    
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <p class="mb-4"><?php echo $lang['instagram_intro'] ?? 'Download photos and videos from Instagram using a post link. Just paste the link and click "Download".'; ?></p>
        
        <form method="POST" action="/instagram" class="space-y-6">
            <div>
                <label for="url" class="block text-gray-700 font-medium mb-2"><?php echo $lang['instagram_post_link'] ?? 'Instagram Post Link'; ?></label>
                <input type="url" name="url" id="url" value="<?php echo htmlspecialchars($url); ?>" required class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="https://www.instagram.com/p/...">
            </div>
            
            <div class="text-center">
                <button type="button" name="download_instagram" class="trigger-popup bg-purple-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-purple-700 transition"><?php echo $lang['download'] ?? 'Download'; ?></button>

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
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['found_media'] ?? 'Found Media'; ?></h2>
        
        <div class="flex flex-col md:flex-row mb-6">
            <div class="md:w-1/2 mb-4 md:mb-0 md:mr-6">
                <div class="bg-gray-100 rounded-lg overflow-hidden">
                    <?php if ($mediaType === 'image'): ?>
                        <div class="aspect-w-1 aspect-h-1">
                            <img src="/assets/images/instagram-placeholder.jpg" alt="<?php echo $lang['instagram_image_preview'] ?? 'Instagram image preview'; ?>" class="w-full h-full object-cover">
                        </div>
                    <?php else: ?>
                        <div class="aspect-w-16 aspect-h-9">
                            <div class="w-full h-full bg-gray-800 flex items-center justify-center text-white">
                                <div class="text-center">
                                    <svg class="h-16 w-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mt-2"><?php echo $lang['video_preview'] ?? 'Video Preview'; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="md:w-1/2">
                <div class="bg-yellow-50 p-4 rounded-lg text-yellow-800 mb-4">
                    <strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['instagram_api_limitations'] ?? 'Due to Instagram API updates, direct content downloading may be limited. Our service detects the URL, but downloading functionality may not be available.'; ?>
                </div>
                
                <p class="mb-4"><?php echo $lang['instagram_terms_warning'] ?? 'Downloading content from Instagram may violate the terms of service. We recommend only downloading your own content or content you have permission to download.'; ?></p>
                
                <div class="mt-4">
                    <a href="#" class="bg-purple-600 text-white font-semibold px-6 py-2 rounded hover:bg-purple-700 transition inline-block disabled opacity-50 cursor-not-allowed">
                        <?php echo $mediaType === 'image' ? ($lang['download_image'] ?? 'Download Image') : ($lang['download_video'] ?? 'Download Video'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Social Media Buttons -->
    <?php include BASE_PATH . '/includes/social.php'; ?>

    <!-- About Instagram Downloader -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['about_instagram_downloader'] ?? 'About Instagram Downloader'; ?></h2>
        
        <div class="space-y-4">
            <p><?php echo $lang['instagram_downloader_desc'] ?? 'Instagram Downloader is a tool that allows you to save photos and videos from Instagram to your device. However, there are some important considerations to keep in mind:'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['instagram_api_limitations_title'] ?? 'Instagram API Limitations'; ?></h3>
            <p><?php echo $lang['instagram_api_limitations_desc'] ?? 'Instagram regularly updates its API and security measures, which can affect the functionality of content downloading tools. As a result, our service may have limited functionality.'; ?></p>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['instagram_terms_title'] ?? 'Instagram Terms of Service'; ?></h3>
            <p><?php echo $lang['instagram_terms_quote_intro'] ?? 'According to Instagram\'s terms of service:'; ?></p>
            
            <div class="bg-gray-100 p-4 rounded-lg my-4">
                <p><?php echo $lang['instagram_terms_quote'] ?? 'You cannot attempt to access content without explicit permission from the account owner, and you cannot use automated means to collect information.'; ?></p>
            </div>
            
            <h3 class="text-xl font-semibold mt-4"><?php echo $lang['legal_use_title'] ?? 'Legal Use'; ?></h3>
            <p><?php echo $lang['instagram_legal_use_desc'] ?? 'There are situations where downloading photos and videos from Instagram may be allowed:'; ?></p>
            <ul class="list-disc pl-6 space-y-2">
                <li><?php echo $lang['download_own_content'] ?? 'Downloading your own content (which you have published yourself)'; ?></li>
                <li><?php echo $lang['download_with_permission'] ?? 'Content for which you have received explicit permission from the creator'; ?></li>
                <li><?php echo $lang['download_licensed_content'] ?? 'Content shared under a license that allows downloading'; ?></li>
            </ul>
            
            <div class="bg-yellow-50 p-4 rounded-lg mt-4">
                <p class="text-yellow-800"><strong><?php echo $lang['note'] ?? 'Note:'; ?></strong> <?php echo $lang['respect_instagram_copyright'] ?? 'Always respect the copyright of other Instagram users. Downloading and using others\' content without permission may violate copyright law.'; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Instructions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-4"><?php echo $lang['how_to_download_instagram'] ?? 'How to Download a Photo or Video from Instagram'; ?></h2>
        
        <div class="space-y-4">
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <strong><?php echo $lang['find_post'] ?? 'Find the post on Instagram'; ?></strong>
                    <p class="mt-1"><?php echo $lang['find_post_desc'] ?? 'Open the Instagram app or instagram.com and find the post you want to download.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['copy_post_link'] ?? 'Copy the post link'; ?></strong>
                    <p class="mt-1"><?php echo $lang['copy_post_link_desc'] ?? 'In the mobile app, tap the "..." icon and select "Copy Link". In a browser, copy the URL from the address bar.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['paste_link'] ?? 'Paste the link to our tool'; ?></strong>
                    <p class="mt-1"><?php echo $lang['paste_link_desc'] ?? 'Paste the copied link in the field above and click the "Download" button.'; ?></p>
                </li>
                
                <li>
                    <strong><?php echo $lang['download_file'] ?? 'Download the file'; ?></strong>
                    <p class="mt-1"><?php echo $lang['download_file_desc'] ?? 'After processing the link, click the "Download Image" or "Download Video" button to save the file to your device.'; ?></p>
                </li>
            </ol>
            
            <div class="bg-blue-50 p-4 rounded-lg mt-4">
                <p class="text-blue-800"><strong><?php echo $lang['tip'] ?? 'Tip:'; ?></strong> <?php echo $lang['public_account_tip'] ?? 'Make sure the link comes from a publicly accessible Instagram account. The tool won\'t work with private accounts you don\'t have access to.'; ?></p>
            </div>
        </div>
    </div>
</div>