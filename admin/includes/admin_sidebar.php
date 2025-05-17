<aside class="w-64 bg-white shadow-md hidden md:block">
    <div class="p-6 bg-blue-600">
        <a href="dashboard.php" class="text-white text-xl font-bold">ToolsOnline Admin</a>
    </div>
    <nav class="mt-6">
        <div class="px-4 py-2 text-gray-500 uppercase text-xs font-bold">Main</div>
        <a href="dashboard.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <?php echo $lang['dashboard_heading']; ?>
        </a>
        <a href="users.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'users.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <?php echo $lang['dashboard_manage_users']; ?>
        </a>
        <a href="tools.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'tools.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <?php echo $lang['dashboard_manage_tools']; ?>
        </a>
        
        <div class="px-4 py-2 text-gray-500 uppercase text-xs font-bold mt-6">Settings</div>
        <a href="profile.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <?php echo $lang['profile']; ?>
        </a>
        <a href="language.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'language.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
            </svg>
            <?php echo $lang['dashboard_language_settings']; ?>
        </a>
        <a href="settings.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <?php echo $lang['dashboard_system_settings']; ?>
        </a>
        
        <div class="px-4 py-2 text-gray-500 uppercase text-xs font-bold mt-6">Links</div>
        <a href="../index.php" target="_blank" class="flex items-center px-6 py-3 sidebar-item text-gray-700">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
            </svg>
            <?php echo $lang['dashboard_main_site']; ?>
        </a>
        <a href="logout.php" class="flex items-center px-6 py-3 sidebar-item text-red-600">
            <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <?php echo $lang['logout']; ?>
        </a>
    </nav>
</aside>