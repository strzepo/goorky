<?php
// Ustawienie tytułu i opisu strony
$pageTitle = 'ToolsOnline - Darmowe narzędzia online | Kalkulatory, Konwertery i Downloadery';
$pageDescription = 'Darmowe narzędzia online: kalkulatory BMI i kalorii, konwertery jednostek, generatory haseł oraz downloadery wideo z YouTube, Instagram, Facebook i Vimeo.';
?>

<!-- Hero sekcja -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16 rounded-lg shadow-lg mb-12">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Darmowe narzędzia online</h1>
        <p class="text-xl md:text-2xl mb-8">Kalkulatory, konwertery i downloadery wideo - wszystko w jednym miejscu!</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="/bmi" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition">Kalkulator BMI</a>
            <a href="/password-generator" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition">Generator haseł</a>
            <a href="/youtube" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition">YouTube Downloader</a>
        </div>
    </div>
</div>

<!-- Popularne kalkulatory -->
<div class="mb-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Popularne kalkulatory</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Kalkulator BMI -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-blue-100 p-4">
                <svg class="h-10 w-10 text-blue-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Kalkulator BMI</h3>
                <p class="text-gray-600 mb-4">Oblicz swój wskaźnik masy ciała i sprawdź, czy masz prawidłową wagę.</p>
                <a href="/bmi" class="block text-center bg-blue-600 text-white font-medium px-4 py-2 rounded hover:bg-blue-700 transition">Sprawdź teraz</a>
            </div>
        </div>
        
        <!-- Kalkulator kalorii -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-green-100 p-4">
                <svg class="h-10 w-10 text-green-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Kalkulator kalorii</h3>
                <p class="text-gray-600 mb-4">Sprawdź swoje dzienne zapotrzebowanie kaloryczne zależnie od aktywności.</p>
                <a href="/calories" class="block text-center bg-green-600 text-white font-medium px-4 py-2 rounded hover:bg-green-700 transition">Sprawdź teraz</a>
            </div>
        </div>
        
        <!-- Konwerter jednostek -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-yellow-100 p-4">
                <svg class="h-10 w-10 text-yellow-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Konwerter jednostek</h3>
                <p class="text-gray-600 mb-4">Konwertuj różne jednostki miary: długość, wagę i temperaturę.</p>
                <a href="/units" class="block text-center bg-yellow-600 text-white font-medium px-4 py-2 rounded hover:bg-yellow-700 transition">Sprawdź teraz</a>
            </div>
        </div>
        
        <!-- Kalkulator dat -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-red-100 p-4">
                <svg class="h-10 w-10 text-red-600 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Kalkulator dat</h3>
                <p class="text-gray-600 mb-4">Oblicz różnicę między dwiema datami lub dodaj/odejmij dni.</p>
                <a href="/dates" class="block text-center bg-red-600 text-white font-medium px-4 py-2 rounded hover:bg-red-700 transition">Sprawdź teraz</a>
            </div>
        </div>
    </div>
</div>

<!-- Downloadery wideo -->
<div class="mb-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Downloadery wideo</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- YouTube Downloader -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-red-100 p-4">
                <svg class="h-10 w-10 text-red-600 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">YouTube Downloader</h3>
                <p class="text-gray-600 mb-4">Pobieraj ulubione filmy z YouTube w wysokiej jakości.</p>
                <a href="/youtube" class="block text-center bg-red-600 text-white font-medium px-4 py-2 rounded hover:bg-red-700 transition">Sprawdź teraz</a>
            </div>
        </div>
        
        <!-- Instagram Downloader -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-purple-100 p-4">
                <svg class="h-10 w-10 text-purple-600 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Instagram Downloader</h3>
                <p class="text-gray-600 mb-4">Pobierz zdjęcia i filmy z Instagrama za pomocą linku.</p>
                <a href="/instagram" class="block text-center bg-purple-600 text-white font-medium px-4 py-2 rounded hover:bg-purple-700 transition">Sprawdź teraz</a>
            </div>
        </div>
        
        <!-- Facebook Downloader -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-blue-100 p-4">
                <svg class="h-10 w-10 text-blue-600 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.675 0h-21.35c-.732 0-1.325.593-1.325 1.325v21.351c0 .731.593 1.324 1.325 1.324h11.495v-9.294h-3.128v-3.622h3.128v-2.671c0-3.1 1.893-4.788 4.659-4.788 1.325 0 2.463.099 2.795.143v3.24l-1.918.001c-1.504 0-1.795.715-1.795 1.763v2.313h3.587l-.467 3.622h-3.12v9.293h6.116c.73 0 1.323-.593 1.323-1.325v-21.35c0-.732-.593-1.325-1.325-1.325z"/>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Facebook Downloader</h3>
                <p class="text-gray-600 mb-4">Pobieraj publiczne filmy z Facebooka w najwyższej jakości.</p>
                <a href="/facebook" class="block text-center bg-blue-600 text-white font-medium px-4 py-2 rounded hover:bg-blue-700 transition">Sprawdź teraz</a>
            </div>
        </div>
        
        <!-- Vimeo Downloader -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
            <div class="bg-indigo-100 p-4">
                <svg class="h-10 w-10 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.9765 6.4196c-.096 2.0992-1.5635 4.968-4.404 8.6525-2.9385 3.7965-5.4225 5.697-7.476 5.697-1.2645 0-2.3295-1.152-3.2025-3.486-.582-2.1337-1.164-4.266-1.7475-6.3982-.648-2.319-1.3395-3.486-2.079-3.486-.159 0-.7275.3397-1.6965 1.0155L2.349 7.0725c1.0665-.933 2.118-1.869 3.1515-2.8035 1.422-1.2225 2.4855-1.869 3.1935-1.9365 1.68-.1605 2.709.9915 3.0975 3.4515.417 2.6332.711 4.2705.873 4.9095.486 2.1975 1.0185 3.2985 1.6035 3.2985.453 0 1.1355-.7125 2.0445-2.142.9075-1.428 1.3965-2.5155 1.461-3.2595.1305-1.233-.3555-1.848-1.461-1.848-.519 0-1.0545.1193-1.6005.3532 1.0605-3.465 3.0885-5.157 6.084-5.0625 2.2185.0645 3.264 1.5038 3.141 4.3245z"/>
                </svg>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-2">Vimeo Downloader</h3>
                <p class="text-gray-600 mb-4">Pobieraj filmy z Vimeo w wysokiej rozdzielczości za darmo.</p>
                <a href="/vimeo" class="block text-center bg-indigo-600 text-white font-medium px-4 py-2 rounded hover:bg-indigo-700 transition">Sprawdź teraz</a>
            </div>
        </div>
    </div>
</div>

<!-- Informacje o stronie -->
<div class="bg-gray-100 rounded-lg p-8 mb-12">
    <h2 class="text-3xl font-bold mb-4">O naszych narzędziach</h2>
    <div class="space-y-4">
        <p>ToolsOnline to zestaw darmowych narzędzi internetowych, które pomagają w codziennych zadaniach. Nasza strona oferuje kalkulatory, konwertery i downloadery wideo - wszystko w jednym miejscu i całkowicie za darmo.</p>
        
        <p>Wszystkie nasze narzędzia są:</p>
        <ul class="list-disc pl-6 space-y-2">
            <li><strong>Szybkie i wydajne</strong> - działają natychmiast bez zbędnego oczekiwania</li>
            <li><strong>Łatwe w obsłudze</strong> - intuicyjny interfejs, który każdy zrozumie</li>
            <li><strong>Dostępne wszędzie</strong> - działają na komputerach, tabletach i telefonach</li>
            <li><strong>Całkowicie darmowe</strong> - bez ukrytych opłat i bez rejestracji</li>
        </ul>
        
        <p>Niezależnie od tego, czy chcesz obliczyć swoje BMI, sprawdzić dzienne zapotrzebowanie kaloryczne, konwertować jednostki czy pobrać wideo z popularnych serwisów - nasze narzędzia są tutaj, aby Ci pomóc.</p>
    </div>
</div>

<!-- FAQ -->
<div class="mb-12">
    <h2 class="text-3xl font-bold mb-6 text-center">Często zadawane pytania</h2>
    
    <div class="space-y-4">
        <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
            <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                <span class="font-medium">Czy korzystanie z narzędzi jest darmowe?</span>
                <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                <p>Tak, wszystkie narzędzia na naszej stronie są całkowicie darmowe i nie wymagają rejestracji ani logowania.</p>
            </div>
        </div>
        
        <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
            <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                <span class="font-medium">Czy pobieranie wideo z YouTube jest legalne?</span>
                <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                <p>Pobieranie wideo z YouTube do użytku osobistego może być legalne w niektórych krajach, ale warto zapoznać się z regulaminem serwisu. Nasza usługa YouTube Downloader obecnie oferuje jedynie formularz demonstracyjny, zgodnie z warunkami serwisu YouTube.</p>
            </div>
        </div>
        
        <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
            <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                <span class="font-medium">Jak dokładny jest kalkulator BMI?</span>
                <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                <p>Kalkulator BMI oblicza wskaźnik masy ciała na podstawie wagi i wzrostu. Jest to powszechnie stosowana metoda oceny masy ciała, jednak należy pamiętać, że BMI nie uwzględnia składu ciała (proporcji mięśni do tłuszczu) i może nie być odpowiedni dla każdego, np. sportowców.</p>
            </div>
        </div>
        
        <div x-data="{ open: false }" class="border border-gray-200 rounded-lg overflow-hidden">
            <button @click="open = !open" class="flex justify-between items-center w-full p-4 text-left bg-white hover:bg-gray-50">
                <span class="font-medium">Czy moje dane są bezpieczne?</span>
                <svg class="h-5 w-5 text-gray-500" :class="{'transform rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="open" class="p-4 bg-gray-50 border-t border-gray-200">
                <p>Tak, wszystkie obliczenia są wykonywane lokalnie w Twojej przeglądarce. Nie przechowujemy żadnych danych osobowych ani wyników obliczeń na naszych serwerach.</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-10 px-8 rounded-lg shadow-lg text-center">
    <h2 class="text-3xl font-bold mb-4">Wypróbuj nasze narzędzia już teraz!</h2>
    <p class="text-xl mb-8">Szybko, łatwo i całkowicie za darmo - bez rejestracji.</p>
    <div class="flex flex-wrap justify-center gap-4">
        <a href="/bmi" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition">Kalkulator BMI</a>
        <a href="/calories" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition">Kalkulator kalorii</a>
        <a href="/youtube" class="bg-white text-blue-600 font-semibold px-6 py-3 rounded-lg hover:bg-blue-50 transition">YouTube Downloader</a>
    </div>
</div>