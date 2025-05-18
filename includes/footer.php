<!-- Banner AdSense - bottom of page -->
<?php 
// Get site settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    // Silently fail if settings table doesn't exist yet
}

if ((!isset($settings['show_ads']) || $settings['show_ads'] == '1') && !empty($settings['ad_footer'])): 
?>
<div class="w-full bg-gray-100 text-center py-4 mt-8 mb-8">
    <!-- Google AdSense Code -->
    <?php echo $settings['ad_footer']; ?>
</div>
<?php else: ?>
<div class="w-full bg-gray-100 text-center py-4 mt-8 mb-8">
    <!-- Placeholder for ad -->
    <div class="text-gray-500"><?php echo $lang['ad_placeholder'] ?? 'Advertisement space'; ?></div>
</div>
<?php endif; ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Column 1 -->
                <div>
                    <div class="mb-4">
                        <?php if (!empty($settings['footer_logo'])): ?>
                            <img src="<?php echo htmlspecialchars($settings['footer_logo']); ?>" alt="<?php echo htmlspecialchars($settings['site_name'] ?? 'ToolsOnline'); ?>" class="h-10 w-auto">
                        <?php else: ?>
                            <span class="text-xl font-bold text-white"><?php echo htmlspecialchars($settings['site_name'] ?? 'ToolsOnline'); ?></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-gray-400"><?php echo $lang['footer_description'] ?? 'Free online tools: calculators, converters and downloaders all in one place.'; ?></p>
                </div>
                
                <!-- Column 2 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4"><?php echo $lang['footer_calculators'] ?? 'Calculators'; ?></h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/bmi" class="hover:text-white"><?php echo $lang['menu_bmi'] ?? 'BMI Calculator'; ?></a></li>
                        <li><a href="/calories" class="hover:text-white"><?php echo $lang['menu_calories'] ?? 'Calorie Calculator'; ?></a></li>
                        <li><a href="/units" class="hover:text-white"><?php echo $lang['menu_units'] ?? 'Unit Converter'; ?></a></li>
                        <li><a href="/dates" class="hover:text-white"><?php echo $lang['menu_dates'] ?? 'Date Calculator'; ?></a></li>
                    </ul>
                </div>
                
                <!-- Column 3 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4"><?php echo $lang['footer_tools'] ?? 'Tools'; ?></h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="/password-generator" class="hover:text-white"><?php echo $lang['menu_password'] ?? 'Password Generator'; ?></a></li>
                        <li><a href="/youtube" class="hover:text-white"><?php echo $lang['menu_youtube'] ?? 'YouTube Downloader'; ?></a></li>
                        <li><a href="/instagram" class="hover:text-white"><?php echo $lang['menu_instagram'] ?? 'Instagram Downloader'; ?></a></li>
                        <li><a href="/facebook" class="hover:text-white"><?php echo $lang['menu_facebook'] ?? 'Facebook Downloader'; ?></a></li>
                        <li><a href="/vimeo" class="hover:text-white"><?php echo $lang['menu_vimeo'] ?? 'Vimeo Downloader'; ?></a></li>
                    </ul>
                </div>
                
                <!-- Column 4 -->
                <div>
                    <h3 class="text-lg font-semibold mb-4"><?php echo $lang['footer_contact'] ?? 'Contact'; ?></h3>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white"><?php echo $lang['footer_about'] ?? 'About Us'; ?></a></li>
                        <li><a href="#" class="hover:text-white"><?php echo $lang['footer_privacy'] ?? 'Privacy Policy'; ?></a></li>
                        <li><a href="#" class="hover:text-white"><?php echo $lang['footer_terms'] ?? 'Terms of Use'; ?></a></li>
                        <li><a href="#" class="hover:text-white"><?php echo $lang['footer_contact'] ?? 'Contact'; ?></a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400">
                <p><?php echo $settings['copyright_text'] ?? ('&copy; ' . date('Y') . ' ToolsOnline. ' . ($lang['footer_rights'] ?? 'All rights reserved.')); ?></p>
            </div>
        </div>
    </footer>
    
    <!-- Additional scripts -->
    <script>
        // Additional JavaScript can be placed here
    </script>
</body>
</html>