<div class="text-center py-16">
    <div class="mb-8">
        <svg class="h-24 w-24 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
    </div>
    <h1 class="text-4xl font-bold mb-4 text-gray-800"><?php echo $lang['404_title'] ?? '404 - Page Not Found'; ?></h1>
    <p class="text-xl text-gray-600 mb-8"><?php echo $lang['404_message'] ?? 'Sorry, the page you are looking for does not exist.'; ?></p>
    <div class="flex justify-center">
        <a href="/" class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700 transition"><?php echo $lang['back_to_home'] ?? 'Back to Homepage'; ?></a>
    </div>

    <div class="mt-16 max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold mb-4"><?php echo $lang['check_our_tools'] ?? 'Check our popular tools:'; ?></h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="/bmi" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                <h3 class="font-semibold mb-2"><?php echo $lang['menu_bmi'] ?? 'BMI Calculator'; ?></h3>
                <p class="text-gray-600 text-sm"><?php echo $lang['bmi_short_desc'] ?? 'Calculate your Body Mass Index.'; ?></p>
            </a>
            <a href="/password-generator" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                <h3 class="font-semibold mb-2"><?php echo $lang['menu_password'] ?? 'Password Generator'; ?></h3>
                <p class="text-gray-600 text-sm"><?php echo $lang['password_short_desc'] ?? 'Create secure, random passwords.'; ?></p>
            </a>
            <a href="/youtube" class="p-4 bg-white rounded-lg shadow-md hover:shadow-lg transition">
                <h3 class="font-semibold mb-2"><?php echo $lang['menu_youtube'] ?? 'YouTube Downloader'; ?></h3>
                <p class="text-gray-600 text-sm"><?php echo $lang['youtube_short_desc'] ?? 'Download videos from YouTube.'; ?></p>
            </a>
        </div>
    </div>
</div>