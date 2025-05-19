<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language'] ?? 'en'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Panel'); ?></title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    
    <style>
    /* Sidebar styling */
    .sidebar-item {
        transition: all 0.2s;
        display: flex;
        align-items: center;
        overflow: hidden;
    }
    .sidebar-item:hover {
        background-color: rgba(59, 130, 246, 0.1);
    }
    .icon-container {
        min-width: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .menu-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Other existing styles */
    .dropdown-menu {
        display: none;
    }
    .language-switch {
        position: relative;
    }
    .language-dropdown {
        display: none;
        position: absolute;
        right: 0;
        top: 100%;
        z-index: 10;
    }
    .language-switch:hover .language-dropdown {
        display: block;
    }
    .mobile-menu-overlay {
        background-color: rgba(0, 0, 0, 0.5);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 29;
    }
    .mobile-menu {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 30;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        width: 250px;
    }
    .mobile-menu.active {
        transform: translateX(0);
    }
</style>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Mobile menu button (only visible on small screens) -->
    <div class="md:hidden fixed top-0 left-0 p-4 z-20">
        <button id="mobile-menu-button" class="text-gray-500 hover:text-gray-900 focus:outline-none">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Mobile menu overlay -->
    <div id="mobile-menu-overlay" class="mobile-menu-overlay hidden"></div>

<!-- Mobile sidebar (hidden by default) -->
<div id="mobile-sidebar" class="mobile-menu bg-white shadow-md">
    <div class="p-6 bg-blue-600">
        <div class="flex justify-between items-center">
            <a href="dashboard.php" class="text-white text-xl font-bold">Admin Panel</a>
            <button id="close-mobile-menu" class="text-white">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
    <nav class="mt-6">
        <div class="px-4 py-2 text-gray-500 uppercase text-xs font-bold">Main</div>
        <a href="dashboard.php" class="flex items-center px-4 py-2 text-sm sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
            <div class="icon-container">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
            </div>
            <span class="ml-3 menu-text"><?php echo $lang['dashboard_heading'] ?? 'Dashboard'; ?></span>
        </a>
            <!-- Same menu items as in the desktop sidebar -->
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
    <?php echo $lang['dashboard_system_settings'] ?? 'System Settings'; ?>
</a>
<a href="ads.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'ads.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
    </svg>
    <?php echo $lang['dashboard_ads_management'] ?? 'Ads Management'; ?>
</a>
<a href="seo.php" class="flex items-center px-6 py-3 sidebar-item <?php echo basename($_SERVER['PHP_SELF']) === 'seo.php' ? 'bg-blue-50 text-blue-600' : 'text-gray-700'; ?>">
    <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
    </svg>
    <?php echo $lang['dashboard_seo_settings'] ?? 'SEO Settings'; ?>
</a>
            <a href="logout.php" class="flex items-center px-6 py-3 sidebar-item text-red-600">
                <svg class="h-5 w-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                <?php echo $lang['logout']; ?>
            </a>
        </nav>
    </div>