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
<!-- CTA Popup -->
<div id="cta-popup" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" style="display: none;">
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-md p-6 relative text-center">
    
    <!-- Zamknięcie -->
    <button onclick="document.getElementById('cta-popup').style.display='none'" 
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xl font-bold">&times;</button>

    <!-- Treść popupu -->
    <p class="text-gray-800 dark:text-gray-200 mb-4 font-medium">
      <?php echo $lang['popup_message'] ?? 'Enjoyed this tool? Share it or buy me a coffee ☕!'; ?>
    </p>

    <!-- Social Media -->
    <div class="flex text-gray-800 justify-center gap-4 mb-4 text-2xl">
      <a href="https://x.com/intent/tweet?text=Check+this+awesome+tool:+https://goorky.com" target="_blank" aria-label="Share on X" class="hover:text-blue-500"><i class="fab fa-x-twitter"></i></a>
      <a href="https://www.facebook.com/sharer/sharer.php?u=https://goorky.com" target="_blank" aria-label="Share on Facebook" class="hover:text-blue-600"><i class="fab fa-facebook"></i></a>
      <a href="https://www.linkedin.com/shareArticle?mini=true&url=https://goorky.com" target="_blank" aria-label="Share on LinkedIn" class="hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
      <a href="https://api.whatsapp.com/send?text=https://goorky.com" target="_blank" aria-label="Share on WhatsApp" class="hover:text-green-500"><i class="fab fa-whatsapp"></i></a>
      <a href="https://t.me/share/url?url=https://goorky.com" target="_blank" aria-label="Share on Telegram" class="hover:text-blue-400"><i class="fab fa-telegram"></i></a>
      <a href="https://www.reddit.com/submit?url=https://goorky.com"  target="_blank" aria-label="Share on Reddit" 
   class="hover:text-red-500"><i class="fab fa-reddit"></i></a>
    </div>
    <!-- Separator -->
<div class="flex items-center justify-center my-4 text-gray-400 text-sm">
  <span class="border-b border-gray-300 flex-grow mr-2"></span>
  <span class="uppercase font-medium tracking-wide">or</span>
  <span class="border-b border-gray-300 flex-grow ml-2"></span>
</div>

    <!-- Buy coffee -->
    <div class="mb-4">
      <a href="https://buycoffee.to/lukson" target="_blank" class="text-yellow-600 font-semibold hover:underline">
        ☕ <?php echo $lang['buy_me_coffee'] ?? 'Buy me a coffee'; ?>
      </a>
    </div>

    <!-- Potwierdzenie z timerem -->
    <button id="cta-submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-medium transition" disabled>
      <?php echo $lang['continue_action'] ?? 'Continue'; ?> (30s)
    </button>
  </div>
</div>
    </footer>
    
    <!-- Additional scripts -->
     <!-- Popup - Call to actions  -->
     <!-- <script>
document.addEventListener('DOMContentLoaded', function () {
    let activeForm = null;
    const popup = document.getElementById('cta-popup');
    const popupSubmit = document.getElementById('cta-submit');
    const continueText = popupSubmit.textContent.split('(')[0].trim();
    let timeLeft = 15;
    let countdownTimer = null;
    
    // Funkcja rozpoczynająca odliczanie
    function startCountdown() {
        // Zablokuj przycisk na początku
        popupSubmit.disabled = true;
        timeLeft = 15;
        
        // Aktualizacja tekstu przycisku z czasem
        popupSubmit.textContent = `${continueText} (${timeLeft}s)`;
        
        // Uruchomienie timera
        countdownTimer = setInterval(function() {
            timeLeft--;
            popupSubmit.textContent = `${continueText} (${timeLeft}s)`;
            
            if (timeLeft <= 0) {
                // Zatrzymaj timer i odblokuj przycisk po zakończeniu odliczania
                clearInterval(countdownTimer);
                popupSubmit.disabled = false;
                popupSubmit.textContent = continueText;
            }
        }, 1000);
    }
    
    // Czyszczenie timera przy zamknięciu popupu
    function resetCountdown() {
        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }
    }
    
    // Obsługa przycisku zamknięcia
    document.querySelector('#cta-popup button:first-child').addEventListener('click', function() {
        resetCountdown();
    });

    document.querySelectorAll('.trigger-popup').forEach(button => {
        button.addEventListener('click', function (e) {
            const form = button.closest('form');
            if (!form) return;

            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            activeForm = form;
            popup.style.display = 'flex';
            startCountdown(); // Rozpocznij odliczanie po otwarciu popupu
        });
    });

    popupSubmit.addEventListener('click', function () {
        if (!popupSubmit.disabled && activeForm) {
            resetCountdown();
            popup.style.display = 'none';
            activeForm.submit();
        }
    });
});
</script> -->
</body>
</html>