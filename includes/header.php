<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php echo generateSeoTags($pageTitle, $pageDescription); ?>
    
    <!-- TailwindCSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.10.3/dist/cdn.min.js" defer></script>
    
    <!-- Custom CSS -->
    <link href="/assets/css/styles.css" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Dodatkowe skrypty -->
    <script src="/assets/js/main.js" defer></script>
    
    <!-- Miejsce na kod AdSense -->
    <?php /* 
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXXXXXXXX" crossorigin="anonymous"></script>
    */ ?>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex flex-col">
    <!-- Nagłówek -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="/" class="text-2xl font-bold text-blue-600">ToolsOnline</a>
                
                <!-- Menu mobilne -->
                <div class="md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                
                <!-- Menu dla desktop -->
                <nav class="hidden md:flex space-x-8">
                    <a href="/" class="text-gray-600 hover:text-blue-600 <?php echo isActivePage('home'); ?>">Strona główna</a>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-gray-600 hover:text-blue-600 flex items-center focus:outline-none">
                            Kalkulatory
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="/bmi" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('bmi'); ?>">Kalkulator BMI</a>
                            <a href="/calories" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('calories'); ?>">Kalkulator kalorii</a>
                            <a href="/units" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('units'); ?>">Konwerter jednostek</a>
                            <a href="/dates" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('dates'); ?>">Kalkulator dat</a>
                        </div>
                    </div>
                    <a href="/password-generator" class="text-gray-600 hover:text-blue-600 <?php echo isActivePage('password_generator'); ?>">Generator haseł</a>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-gray-600 hover:text-blue-600 flex items-center focus:outline-none">
                            Downloadery
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute z-10 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="/youtube" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_youtube'); ?>">YouTube</a>
                            <a href="/instagram" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_instagram'); ?>">Instagram</a>
                            <a href="/facebook" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_facebook'); ?>">Facebook</a>
                            <a href="/vimeo" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo isActivePage('downloaders_vimeo'); ?>">Vimeo</a>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-xl">
                        <a href="?lang=pl" title="Zmień język" class="text-gray-600 hover:text-blue-600">🌐</a>
                        <a href="/admin/login.php" title="Zaloguj się" class="text-gray-600 hover:text-blue-600">👤</a>
                    </div>
                </nav>
            </div>
            
            <!-- Menu mobilne -->
            <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" class="md:hidden mt-2 py-2 border-t border-gray-200">
                <a href="/" class="block py-2 text-gray-600 hover:text-blue-600">Strona główna</a>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center w-full py-2 text-gray-600 hover:text-blue-600">
                        Kalkulatory
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4">
                        <a href="/bmi" class="block py-2 text-gray-600 hover:text-blue-600">Kalkulator BMI</a>
                        <a href="/calories" class="block py-2 text-gray-600 hover:text-blue-600">Kalkulator kalorii</a>
                        <a href="/units" class="block py-2 text-gray-600 hover:text-blue-600">Konwerter jednostek</a>
                        <a href="/dates" class="block py-2 text-gray-600 hover:text-blue-600">Kalkulator dat</a>
                    </div>
                </div>
                <a href="/password-generator" class="block py-2 text-gray-600 hover:text-blue-600">Generator haseł</a>
                <div x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center w-full py-2 text-gray-600 hover:text-blue-600">
                        Downloadery
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open" class="pl-4">
                        <a href="/youtube" class="block py-2 text-gray-600 hover:text-blue-600">YouTube</a>
                        <a href="/instagram" class="block py-2 text-gray-600 hover:text-blue-600">Instagram</a>
                        <a href="/facebook" class="block py-2 text-gray-600 hover:text-blue-600">Facebook</a>
                        <a href="/vimeo" class="block py-2 text-gray-600 hover:text-blue-600">Vimeo</a>
                    </div>
                    <div class="flex items-center space-x-4 pt-4 px-2 text-xl">
                        <a href="?lang=pl" title="Zmień język" class="text-gray-600 hover:text-blue-600">🌐</a>
                        <a href="/admin/auth/login.php" title="Zaloguj się" class="text-gray-600 hover:text-blue-600">👤</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Główna zawartość -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <!-- Banner AdSense - góra strony -->
        <div class="w-full bg-gray-100 text-center py-4 mb-8">
            <!-- Kod reklamy Google AdSense -->
            <div class="text-gray-500">Miejsce na reklamę</div>
        </div>