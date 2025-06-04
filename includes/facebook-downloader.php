<?php
/**
 * Facebook Video Downloader Function
 * Integracja z FDownloader
 */

/**
 * Get Facebook video direct download links
 * 
 * @param string $url Facebook video URL
 * @return array|bool Array with video links or false on failure
 */
function getFacebookVideoLinks($url) {
    // Sanitize URL
    $url = filter_var($url, FILTER_SANITIZE_URL);
    
    // Check if URL is valid
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set options for Mobile User Agent (better for extracting video URLs)
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.162 Mobile Safari/537.36');
    
    // Execute cURL
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // Extract HD quality video URL
    $hdLink = '';
    if (preg_match('/hd_src:"([^"]+)"/', $response, $hdMatches)) {
        $hdLink = $hdMatches[1];
    }
    
    // Extract SD quality video URL
    $sdLink = '';
    if (preg_match('/sd_src:"([^"]+)"/', $response, $sdMatches)) {
        $sdLink = $sdMatches[1];
    } elseif (preg_match('/sd_src_no_ratelimit:"([^"]+)"/', $response, $sdMatches)) {
        $sdLink = $sdMatches[1];
    }
    
    // If no links found, try additional patterns
    if (empty($hdLink) && empty($sdLink)) {
        // Try alternative patterns
        if (preg_match('/"playable_url":"([^"]+)"/', $response, $altMatches)) {
            $sdLink = str_replace('\\/', '/', $altMatches[1]);
        }
        
        if (preg_match('/"playable_url_quality_hd":"([^"]+)"/', $response, $altMatches)) {
            $hdLink = str_replace('\\/', '/', $altMatches[1]);
        }
    }
    
    // Extract video title
    $title = '';
    if (preg_match('/<title>(.*?)<\/title>/s', $response, $titleMatches)) {
        $title = trim($titleMatches[1]);
        // Remove additional text after separator
        $title = preg_replace('/\s*\|.*$/', '', $title);
    }
    
    // Check if we got any download links
    if (empty($hdLink) && empty($sdLink)) {
        return false;
    }
    
    // Return the video download links
    return [
        'title' => $title,
        'hd' => $hdLink,
        'sd' => $sdLink
    ];
}

/**
 * Alternative method using external API for private videos
 * 
 * @param string $url Facebook video URL
 * @return array|bool Array with video links or false on failure
 */
function getFacebookVideoLinksExternalAPI($url) {
    // Sanitize URL
    $url = filter_var($url, FILTER_SANITIZE_URL);
    
    // Check if URL is valid
    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        return false;
    }
    
    // Define API endpoint
    $apiUrl = 'https://api-server-fb-dl.onrender.com/api/facebook?url=' . urlencode($url);
    
    // Initialize cURL
    $ch = curl_init();
    
    // Set options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    // Execute cURL
    $response = curl_exec($ch);
    
    // Check for cURL errors
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    
    curl_close($ch);
    
    // Parse JSON response
    $data = json_decode($response, true);
    
    // Check if we got a valid response
    if (
        $data === null || 
        !isset($data['success']) || 
        $data['success'] !== true || 
        !isset($data['links']) || 
        empty($data['links'])
    ) {
        return false;
    }
    
    // Try to find HD and SD links
    $hdLink = '';
    $sdLink = '';
    $title = $data['title'] ?? '';
    
    foreach ($data['links'] as $link) {
        if (isset($link['quality']) && isset($link['url'])) {
            if (stripos($link['quality'], 'HD') !== false) {
                $hdLink = $link['url'];
            } else {
                $sdLink = $link['url'];
            }
        }
    }
    
    // If we couldn't categorize links by quality, just use the first one
    if (empty($hdLink) && empty($sdLink) && !empty($data['links'][0]['url'])) {
        $sdLink = $data['links'][0]['url'];
    }
    
    // Return the video download links
    return [
        'title' => $title,
        'hd' => $hdLink,
        'sd' => $sdLink
    ];
}

/**
 * Try both methods to get Facebook video links
 * 
 * @param string $url Facebook video URL
 * @return array|bool Array with video links or false on failure
 */
function getVideoLinks($url) {
    // Try direct method first
    $links = getFacebookVideoLinks($url);
    
    // If direct method fails, try external API
    if (!$links || (empty($links['hd']) && empty($links['sd']))) {
        $links = getFacebookVideoLinksExternalAPI($url);
    }
    
    return $links;
}

/**
 * Renders download buttons for Facebook videos
 * 
 * @param string $url Facebook video URL
 * @return string HTML with download buttons or error message
 */
function renderFacebookDownloadButtons($url) {
    $links = getVideoLinks($url);
    
    if (!$links) {
        return '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong>Błąd!</strong> Nie udało się pobrać linków do filmu. Upewnij się, że podany URL jest prawidłowy.
        </div>';
    }
    
    $title = !empty($links['title']) ? htmlspecialchars($links['title']) : 'Facebook Video';
    $html = '<div class="bg-white rounded-lg shadow-md p-6 mb-6">';
    $html .= '<h3 class="text-xl font-semibold mb-4">Pobierz: ' . $title . '</h3>';
    
    $html .= '<div class="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4">';
    
    if (!empty($links['hd'])) {
        $html .= '<a href="' . htmlspecialchars($links['hd']) . '" target="_blank" class="bg-blue-600 text-white font-semibold px-6 py-2 rounded hover:bg-blue-700 transition inline-block text-center">';
        $html .= '<i class="fas fa-download mr-2"></i>Pobierz HD';
        $html .= '</a>';
    }
    
    if (!empty($links['sd'])) {
        $html .= '<a href="' . htmlspecialchars($links['sd']) . '" target="_blank" class="bg-blue-500 text-white font-semibold px-6 py-2 rounded hover:bg-blue-600 transition inline-block text-center">';
        $html .= '<i class="fas fa-download mr-2"></i>Pobierz SD';
        $html .= '</a>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}