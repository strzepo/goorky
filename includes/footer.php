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
    
    <!-- Close button -->
    <button onclick="document.getElementById('cta-popup').style.display='none'; resetPopupCountdown();" 
            class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 text-xl font-bold">&times;</button>

    <!-- Popup content -->
    <p class="text-gray-800 dark:text-gray-200 mb-4 font-medium">
      <?php echo $lang['popup_message'] ?? 'Enjoyed this tool? Share it or buy me a coffee ☕!'; ?>
    </p>

    <!-- Social Media -->
    <div class="flex text-gray-200 justify-center gap-4 mb-4 text-2xl">
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
      <span class="uppercase font-medium tracking-wide"><?php echo $lang['or'] ?? 'or'; ?></span>
      <span class="border-b border-gray-300 flex-grow ml-2"></span>
    </div>

    <!-- Buy coffee -->
    <div class="mb-4">
      <a href="https://buycoffee.to/lukson" target="_blank" class="text-yellow-600 font-semibold hover:underline">
        ☕ <?php echo $lang['buy_me_coffee'] ?? 'Buy me a coffee'; ?>
      </a>
    </div>

    <!-- Form parameters -->
    <div id="popup-form-parameters" class="bg-gray-100 text-gray-800 p-3 rounded-lg mb-4 text-left">
      <!-- Parameters will be inserted here via JavaScript -->
    </div>

    <!-- Confirm with timer -->
    <button id="cta-submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded font-medium transition" disabled>
      <?php echo $lang['continue_action'] ?? 'Continue'; ?> (5s)
    </button>
  </div>
</div>
    </footer>
    
    <!-- Additional scripts -->
     <!-- Popup - Call to actions  -->
     <script>
document.addEventListener('DOMContentLoaded', function () {
    let activeForm = null;
    const popup = document.getElementById('cta-popup');
    const popupSubmit = document.getElementById('cta-submit');
    const popupParameters = document.getElementById('popup-form-parameters');
    const continueText = '<?php echo $lang['continue_action'] ?? 'Continue'; ?>';
    let timeLeft = 5;
    let countdownTimer = null;
    
    // Function to start countdown
    function startPopupCountdown() {
        // Disable button at start
        popupSubmit.disabled = true;
        timeLeft = 5;
        
        // Update button text with time
        popupSubmit.textContent = `${continueText} (${timeLeft}s)`;
        
        // Start timer
        countdownTimer = setInterval(function() {
            timeLeft--;
            popupSubmit.textContent = `${continueText} (${timeLeft}s)`;
            
            if (timeLeft <= 0) {
                // Stop timer and enable button after countdown
                clearInterval(countdownTimer);
                popupSubmit.disabled = false;
                popupSubmit.textContent = continueText;
            }
        }, 1000);
    }
    
    // Function to reset countdown
    function resetPopupCountdown() {
        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }
    }
    
    // Make resetPopupCountdown available globally
    window.resetPopupCountdown = resetPopupCountdown;
    
    // Setup popup trigger buttons
    document.querySelectorAll('.trigger-popup').forEach(button => {
        button.addEventListener('click', function (e) {
            const form = button.closest('form');
            if (!form) return;

            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Generate form parameters display
            let parametersHTML = '';
            
            // BMI form parameters
            if (form.querySelector('[name="action_type"][value="calculate_bmi"]')) {
                const weight = form.querySelector('[name="weight"]').value;
                const height = form.querySelector('[name="height"]').value;
                
                parametersHTML += `<p><strong><?php echo $lang['weight'] ?? 'Weight'; ?>:</strong> ${weight} <?php echo $lang['kg'] ?? 'kg'; ?></p>`;
                parametersHTML += `<p><strong><?php echo $lang['height'] ?? 'Height'; ?>:</strong> ${height} <?php echo $lang['cm'] ?? 'cm'; ?></p>`;
            }
            
            // Calories form parameters
            else if (form.querySelector('[name="calculate_calories"]')) {
                const gender = form.querySelector('[name="gender"]:checked').value;
                const age = form.querySelector('[name="age"]').value;
                const weight = form.querySelector('[name="weight"]').value;
                const height = form.querySelector('[name="height"]').value;
                const activity = form.querySelector('[name="activity"]').value;
                
                const genderText = gender === 'male' ? '<?php echo $lang['male'] ?? 'Male'; ?>' : '<?php echo $lang['female'] ?? 'Female'; ?>';
                
                let activityText = '';
                switch(activity) {
                    case 'sedentary': activityText = '<?php echo $lang['activity_sedentary_option'] ?? 'Sedentary'; ?>'; break;
                    case 'light': activityText = '<?php echo $lang['activity_light_option'] ?? 'Light activity'; ?>'; break;
                    case 'moderate': activityText = '<?php echo $lang['activity_moderate_option'] ?? 'Moderate activity'; ?>'; break;
                    case 'active': activityText = '<?php echo $lang['activity_active_option'] ?? 'Active'; ?>'; break;
                    case 'very_active': activityText = '<?php echo $lang['activity_very_active_option'] ?? 'Very active'; ?>'; break;
                }
                
                parametersHTML += `<p><strong><?php echo $lang['gender'] ?? 'Gender'; ?>:</strong> ${genderText}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['age'] ?? 'Age'; ?>:</strong> ${age} <?php echo $lang['years'] ?? 'years'; ?></p>`;
                parametersHTML += `<p><strong><?php echo $lang['weight'] ?? 'Weight'; ?>:</strong> ${weight} <?php echo $lang['kg'] ?? 'kg'; ?></p>`;
                parametersHTML += `<p><strong><?php echo $lang['height'] ?? 'Height'; ?>:</strong> ${height} <?php echo $lang['cm'] ?? 'cm'; ?></p>`;
                parametersHTML += `<p><strong><?php echo $lang['activity_level'] ?? 'Activity level'; ?>:</strong> ${activityText}</p>`;
            }
            
            // Date calculator form parameters
            else if (form.querySelector('[name="calculate_dates"]')) {
                const operation = form.querySelector('[name="operation"]').value;
                
                let operationText = '';
                switch(operation) {
                    case 'difference': operationText = '<?php echo $lang['date_diff'] ?? 'Difference between dates'; ?>'; break;
                    case 'add': operationText = '<?php echo $lang['date_add'] ?? 'Add days to date'; ?>'; break;
                    case 'subtract': operationText = '<?php echo $lang['date_subtract'] ?? 'Subtract days from date'; ?>'; break;
                }
                
                parametersHTML += `<p><strong><?php echo $lang['choose_operation'] ?? 'Operation'; ?>:</strong> ${operationText}</p>`;
                
                if (operation === 'difference') {
                    const date1 = form.querySelector('[name="date1"]').value;
                    const date2 = form.querySelector('[name="date2"]').value;
                    
                    parametersHTML += `<p><strong><?php echo $lang['first_date'] ?? 'First date'; ?>:</strong> ${date1}</p>`;
                    parametersHTML += `<p><strong><?php echo $lang['second_date'] ?? 'Second date'; ?>:</strong> ${date2}</p>`;
                } else {
                    const date1 = form.querySelector('[name="date1"]').value;
                    const days = form.querySelector('[name="days"]').value;
                    
                    parametersHTML += `<p><strong><?php echo $lang['date'] ?? 'Date'; ?>:</strong> ${date1}</p>`;
                    parametersHTML += `<p><strong><?php echo $lang['number_of_days'] ?? 'Number of days'; ?>:</strong> ${days}</p>`;
                }
            }
            
            // Unit converter form parameters
            else if (form.querySelector('[name="convert_units"]')) {
                const type = form.querySelector('[name="type"]:checked').value;
                const value = form.querySelector('[name="value"]').value;
                const from = form.querySelector('[name="from"]').value;
                const to = form.querySelector('[name="to"]').value;
                
                let typeText = '';
                switch(type) {
                    case 'length': typeText = '<?php echo $lang['type_length'] ?? 'Length'; ?>'; break;
                    case 'weight': typeText = '<?php echo $lang['type_weight'] ?? 'Weight'; ?>'; break;
                    case 'temperature': typeText = '<?php echo $lang['type_temperature'] ?? 'Temperature'; ?>'; break;
                }
                
                parametersHTML += `<p><strong><?php echo $lang['conversion_type'] ?? 'Conversion type'; ?>:</strong> ${typeText}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['value'] ?? 'Value'; ?>:</strong> ${value}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['from'] ?? 'From'; ?>:</strong> ${from}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['to'] ?? 'To'; ?>:</strong> ${to}</p>`;
            }
            
            // Password generator form parameters
            else if (form.querySelector('[name="generate_password"]')) {
                const length = form.querySelector('[name="length"]').value;
                const useUpper = form.querySelector('[name="use_upper"]')?.checked || false;
                const useLower = form.querySelector('[name="use_lower"]')?.checked || false;
                const useNumbers = form.querySelector('[name="use_numbers"]')?.checked || false;
                const useSpecial = form.querySelector('[name="use_special"]')?.checked || false;
                
                const yesText = '<?php echo $lang['yes'] ?? 'Yes'; ?>';
                const noText = '<?php echo $lang['no'] ?? 'No'; ?>';
                
                parametersHTML += `<p><strong><?php echo $lang['password_length'] ?? 'Password length'; ?>:</strong> ${length}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['uppercase_letters'] ?? 'Uppercase letters'; ?>:</strong> ${useUpper ? yesText : noText}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['lowercase_letters'] ?? 'Lowercase letters'; ?>:</strong> ${useLower ? yesText : noText}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['numbers'] ?? 'Numbers'; ?>:</strong> ${useNumbers ? yesText : noText}</p>`;
                parametersHTML += `<p><strong><?php echo $lang['special_chars'] ?? 'Special characters'; ?>:</strong> ${useSpecial ? yesText : noText}</p>`;
            }
            
            // YouTube/Facebook/Instagram/Vimeo downloader form parameters
            else if (form.querySelector('[name="download_youtube"]') || 
                     form.querySelector('[name="download_facebook"]') ||
                     form.querySelector('[name="download_instagram"]') ||
                     form.querySelector('[name="download_vimeo"]')) {
                const url = form.querySelector('[name="url"]').value;
                parametersHTML += `<p><strong>URL:</strong> ${url}</p>`;
            }
            
            // Set parameters HTML and store the active form
            popupParameters.innerHTML = parametersHTML;
            activeForm = form;
            
            // Show popup and start countdown
            popup.style.display = 'flex';
            startPopupCountdown();
        });
    });

    // Handle submit button click
    popupSubmit.addEventListener('click', function () {
        if (popupSubmit.disabled || !activeForm) return;
        
        resetPopupCountdown();
        popup.style.display = 'none';
        
        // Create hidden inputs for each form type
        function addHiddenInput(name, value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            return input;
        }
        
        // Add appropriate hidden input based on form type
        if (activeForm.querySelector('[name="action_type"]')) {
            // BMI form already has the needed input
        } 
        else if (activeForm.querySelector('[name="calculate_calories"]')) {
            activeForm.appendChild(addHiddenInput('calculate_calories', '1'));
        }
        else if (activeForm.querySelector('[name="calculate_dates"]')) {
            activeForm.appendChild(addHiddenInput('calculate_dates', '1'));
        }
        else if (activeForm.querySelector('[name="convert_units"]')) {
            activeForm.appendChild(addHiddenInput('convert_units', '1'));
        }
        else if (activeForm.querySelector('[name="generate_password"]')) {
            activeForm.appendChild(addHiddenInput('generate_password', '1'));
        }
        else if (activeForm.querySelector('[name="download_youtube"]')) {
            activeForm.appendChild(addHiddenInput('download_youtube', '1'));
        }
        else if (activeForm.querySelector('[name="download_facebook"]')) {
            activeForm.appendChild(addHiddenInput('download_facebook', '1'));
        }
        else if (activeForm.querySelector('[name="download_instagram"]')) {
            activeForm.appendChild(addHiddenInput('download_instagram', '1'));
        }
        else if (activeForm.querySelector('[name="download_vimeo"]')) {
            activeForm.appendChild(addHiddenInput('download_vimeo', '1'));
        }
        
        // Submit the form
        activeForm.submit();
    });
});
</script>
</body>
</html>