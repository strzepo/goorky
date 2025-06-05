    <!-- Call to Action Section -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-100 border border-blue-200 rounded-lg shadow-md p-6 mb-8">
        <div class="text-center">
            <div class="mb-4">
                <h3 class="text-xl font-semibold text-gray-800 mb-2">
                    <span class="inline-block mr-2">💙</span>
                    <?php echo $lang['cta_heading'] ?? 'Enjoyed using our Free Tools?'; ?>
                </h3>
                <p class="text-gray-600">
                    <?php echo $lang['cta_description'] ?? 'Help others discover this free tool! Share it with your friends or support our work with a small donation.'; ?>
                </p>
            </div>
    
    <!-- Social Media Share Buttons -->
     <div class="flex justify-center gap-2 mb-4 flex-wrap">
                <a href="https://x.com/intent/tweet?text=<?php echo urlencode(($lang['cta_tweet_text'] ?? '🚀 Check out this awesome Free Tools Hub – from BMI & SEO calculators to video downloaders and converters!
🔧 One place. All tools. Forever free.') . ' - '); ?>https://goorky.com/" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition-colors text-sm">
                    <i class="fab fa-x-twitter mr-2"></i>
                    <?php echo $lang['share_on_x'] ?? 'X'; ?>
                </a>
                
                <a href="https://www.facebook.com/sharer/sharer.php?u=https://goorky.com/" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fab fa-facebook mr-2"></i>
                    <?php echo $lang['share_on_facebook'] ?? 'Facebook'; ?>
                </a>
                
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=https://goorky.com/&title=<?php echo urlencode($lang['cta_linkedin_title'] ?? '❤️ I just found a goldmine of free online tools – calculators, converters, SEO boosters & more.
🛠 No logins. No installs. Just pure utility.'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors text-sm">
                    <i class="fab fa-linkedin mr-2"></i>
                    <?php echo $lang['share_on_linkedin'] ?? 'LinkedIn'; ?>
                </a>
                
                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(($lang['cta_whatsapp_text'] ?? '❤️ I just found a goldmine of free online tools – calculators, converters, SEO boosters & more.
🛠 No logins. No installs. Just pure utility.') . ' https://goorky.com/'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm">
                    <i class="fab fa-whatsapp mr-2"></i>
                    <?php echo $lang['share_on_whatsapp'] ?? 'WhatsApp'; ?>
                </a>
                
                <a href="https://t.me/share/url?url=https://goorky.com/&text=<?php echo urlencode($lang['cta_telegram_text'] ?? '❤️ I just found a goldmine of free online tools – calculators, converters, SEO boosters & more.
🛠 No logins. No installs. Just pure utility.'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-blue-400 text-white rounded-lg hover:bg-blue-500 transition-colors text-sm">
                    <i class="fab fa-telegram mr-2"></i>
                    <?php echo $lang['share_on_telegram'] ?? 'Telegram'; ?>
                </a>
                
                <a href="https://pinterest.com/pin/create/button/?url=https://goorky.com/&description=<?php echo urlencode($lang['cta_pinterest_description'] ?? '❤️ I just found a goldmine of free online tools – calculators, converters, SEO boosters & more.
🛠 No logins. No installs. Just pure utility.'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm">
                    <i class="fab fa-pinterest mr-2"></i>
                    <?php echo $lang['share_on_pinterest'] ?? 'Pinterest'; ?>
                </a>
                
                <a href="https://www.reddit.com/submit?url=https://goorky.com/&title=<?php echo urlencode($lang['cta_reddit_title'] ?? '❤️ I just found a goldmine of free online tools – calculators, converters, SEO boosters & more.
🛠 No logins. No installs. Just pure utility.'); ?>" 
                   target="_blank" 
                   class="inline-flex items-center px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">
                    <i class="fab fa-reddit mr-2"></i>
                    <?php echo $lang['share_on_reddit'] ?? 'Reddit'; ?>
                </a>
         <!-- #TODO - naprawić wysyłanie mailem         -->
                <!-- <a href="mailto:?subject=Free%20Unit%20Converter%20Tool&body=Check%20out%20this%20awesome%20free%20unit%20converter:%20https://goorky.com/" 
   class="inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
    <i class="fas fa-envelope mr-2"></i>
    Email
</a> -->
</a>
                </a>
            </div>
            
            <!-- Separator -->
            <div class="flex items-center justify-center my-4">
                <span class="border-b border-gray-300 flex-grow max-w-xs"></span>
                <span class="px-4 text-gray-500 text-sm font-medium uppercase tracking-wide">
                    <?php echo $lang['or'] ?? 'or'; ?>
                </span>
                <span class="border-b border-gray-300 flex-grow max-w-xs"></span>
            </div>
            
            <!-- Buy Coffee Button -->
            <div class="mb-2">
                <a href="https://buycoffee.to/lukson" 
                   target="_blank" 
                   class="inline-flex items-center px-6 py-3 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition-colors shadow-md hover:shadow-lg">
                    <span class="text-lg mr-2">☕</span>
                    <?php echo $lang['buy_me_coffee'] ?? 'Buy me a coffee'; ?>
                </a>
            </div>
            
            <p class="text-sm text-gray-500">
                <?php echo $lang['cta_support_text'] ?? 'Your support helps us maintain and improve our free tools while keeping ads minimal!'; ?>
            </p>
        </div>
    </div>