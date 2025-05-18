<?php
/**
 * Plik zawierający wszystkie funkcje pomocnicze aplikacji
 * @author Goorky.com
 * @version 1.0
 */

/**
 * Czyści dane wejściowe, chroniąc przed atakami XSS
 * @param string $data Dane do oczyszczenia
 * @return string Oczyszczone dane
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Czyści dane wejściowe jako liczbę
 * @param mixed $data Dane do oczyszczenia
 * @return float Oczyszczona liczba
 */
function sanitizeNumber($data) {
    if (empty($data)) {
        return 0;
    }
    $data = str_replace(',', '.', $data); // Zamiana przecinka na kropkę
    return (float)$data;
}

/**
 * Oblicza BMI na podstawie wagi i wzrostu
 * @param float $weight Waga w kg
 * @param float $height Wzrost w metrach
 * @return float BMI
 */
function calculateBMI($weight, $height) {
    if ($height <= 0 || $weight <= 0) {
        return 0;
    }
    return $weight / ($height * $height);
}

/**
 * Zwraca kategorię BMI na podstawie wartości BMI
 * @param float $bmi Wartość BMI
 * @return string Kategoria BMI
 */
function getBMICategory($bmi) {
    if ($bmi < 16) {
        return ['Wygłodzenie', 'text-red-700'];
    } elseif ($bmi < 17) {
        return ['Wychudzenie', 'text-red-600'];
    } elseif ($bmi < 18.5) {
        return ['Niedowaga', 'text-yellow-600'];
    } elseif ($bmi < 25) {
        return ['Prawidłowa waga', 'text-green-600'];
    } elseif ($bmi < 30) {
        return ['Nadwaga', 'text-yellow-600'];
    } elseif ($bmi < 35) {
        return ['Otyłość I stopnia', 'text-red-500'];
    } elseif ($bmi < 40) {
        return ['Otyłość II stopnia', 'text-red-600'];
    } else {
        return ['Otyłość III stopnia', 'text-red-700'];
    }
}

/**
 * Oblicza dzienne zapotrzebowanie kaloryczne (BMR + aktywność)
 * @param float $weight Waga w kg
 * @param float $height Wzrost w cm
 * @param int $age Wiek w latach
 * @param string $gender Płeć (male/female)
 * @param string $activity Poziom aktywności
 * @return float Dzienne zapotrzebowanie kaloryczne
 */
function calculateCalories($weight, $height, $age, $gender, $activity) {
    // Mifflin-St Jeor formula
    if ($gender === 'male') {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age + 5;
    } else {
        $bmr = 10 * $weight + 6.25 * $height - 5 * $age - 161;
    }
    
    // Activity multipliers
    $activityFactors = [
        'sedentary' => 1.2,      // Sedentary lifestyle, no physical activity
        'light' => 1.375,        // Light activity (1–3 times/week)
        'moderate' => 1.55,      // Moderate activity (3–5 times/week)
        'active' => 1.725,       // High activity (6–7 times/week)
        'very_active' => 1.9     // Very high activity (twice daily)
    ];
    
    return $bmr * $activityFactors[$activity];
}

/**
 * Konwertuje jednostki
 * @param float $value Wartość do konwersji
 * @param string $from Jednostka źródłowa
 * @param string $to Jednostka docelowa
 * @param string $type Typ konwersji (length, weight, temperature)
 * @return float Wartość po konwersji
 */
function convertUnits($value, $from, $to, $type) {
    // Conversion factors to base units
    $conversionFactors = [
        'length' => [
            'mm' => 0.001,
            'cm' => 0.01,
            'm' => 1,
            'km' => 1000,
            'in' => 0.0254,
            'ft' => 0.3048,
            'yd' => 0.9144,
            'mi' => 1609.344
        ],
        'weight' => [
            'mg' => 0.000001,
            'g' => 0.001,
            'kg' => 1,
            'oz' => 0.0283495,
            'lb' => 0.453592,
            'st' => 6.35029
        ],
        'temperature' => [
            'C' => 'C',
            'F' => 'F',
            'K' => 'K'
        ]
    ];
    
    // Special functions for temperature
    if ($type === 'temperature') {
        if ($from === 'C' && $to === 'F') {
            return ($value * 9/5) + 32;
        } elseif ($from === 'F' && $to === 'C') {
            return ($value - 32) * 5/9;
        } elseif ($from === 'C' && $to === 'K') {
            return $value + 273.15;
        } elseif ($from === 'K' && $to === 'C') {
            return $value - 273.15;
        } elseif ($from === 'F' && $to === 'K') {
            return ($value - 32) * 5/9 + 273.15;
        } elseif ($from === 'K' && $to === 'F') {
            return ($value - 273.15) * 9/5 + 32;
        } else {
            return $value; // Same unit
        }
    }
    
    // Standard conversion for length and weight
    // Convert to base unit
    $baseValue = $value * $conversionFactors[$type][$from];
    
    // Convert from base unit to target unit
    return $baseValue / $conversionFactors[$type][$to];
}

/**
 * Oblicza różnicę w dniach między dwiema datami
 * @param string $date1 Pierwsza data
 * @param string $date2 Druga data
 * @return int Różnica w dniach
 */
function calculateDateDifference($date1, $date2) {
    $datetime1 = new DateTime($date1);
    $datetime2 = new DateTime($date2);
    $interval = $datetime1->diff($datetime2);
    return $interval->days;
}

/**
 * Generates a secure password
 * @param int $length Długość hasła
 * @param bool $useSpecial Czy używać znaków specjalnych
 * @param bool $useNumbers Czy używać cyfr
 * @param bool $useUpper Czy używać wielkich liter
 * @param bool $useLower Czy używać małych liter
 * @return string Wygenerowane hasło
 */
function generatePassword($length, $useSpecial, $useNumbers, $useUpper, $useLower) {
    $chars = '';
    
    if ($useSpecial) {
        $chars .= '!@#$%^&*()_-+=<>?';
    }
    
    if ($useNumbers) {
        $chars .= '0123456789';
    }
    
    if ($useUpper) {
        $chars .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    
    if ($useLower) {
        $chars .= 'abcdefghijklmnopqrstuvwxyz';
    }
    
    // If no option was selected, use all
    if (empty($chars)) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=<>?';
    }
    
    $password = '';
    $charsLength = strlen($chars);
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $charsLength - 1)];
    }
    
    return $password;
}

/**
 * Formats a date in a human-friendly way
 * @param string $date Data w formacie Y-m-d
 * @return string Sformatowana data
 */
function formatFriendlyDate($date) {
    $datetime = new DateTime($date);
    return $datetime->format('j F Y');
}

/**
 * Returns the CSS class for the active page
 * @param string $pageName Nazwa strony do porównania
 * @return string Klasa CSS 'active' lub pusta
 */
function isActivePage($pageName) {
    global $page;
    
    // Obsługa downloaderów
    if (strpos($pageName, 'downloaders_') === 0 && $page === $pageName) {
        return 'text-blue-600';
    }
    
    // Normalne strony
    if ($page === $pageName) {
        return 'text-blue-600';
    }
    
    return '';
}

/**
 * Parses a video URL and extracts its ID
 * @param string $url URL wideo YouTube
 * @return string|false ID wideo lub false w przypadku niepowodzenia
 */
function getYoutubeId($url) {
    $pattern = '/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/';
    
    if (preg_match($pattern, $url, $matches)) {
        return $matches[1];
    }
    
    return false;
}

/**
 * Parses a video URL and extracts its ID
 * @param string $url URL wideo Facebook
 * @return string|false ID wideo lub false w przypadku niepowodzenia
 */
function getFacebookVideoId($url) {
    if (preg_match('/facebook\.com\/.*\/videos\/(\d+)/', $url, $matches)) {
        return $matches[1];
    }
    
    return false;
}

/**
 * Parses a video URL and extracts its ID
 * @param string $url URL wideo Vimeo
 * @return string|false ID wideo lub false w przypadku niepowodzenia
 */
function getVimeoId($url) {
    if (preg_match('/(vimeo\.com\/)(\d+)/', $url, $matches)) {
        return $matches[2];
    }
    
    return false;
}

/**
 * Retrieves Vimeo video metadata via API
 * @param string $id ID wideo Vimeo
 * @return array|false Dane wideo lub false w przypadku niepowodzenia
 */
function getVimeoMetadata($id) {
    $apiUrl = "https://vimeo.com/api/v2/video/{$id}.json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    if ($response) {
        $data = json_decode($response, true);
        return $data[0] ?? false;
    }
    
    return false;
}

/**
 * Retrieves thumbnails from Instagram (simplified implementation)
 * @param string $url URL Instagram
 * @return string|false URL miniatury lub false w przypadku niepowodzenia
 */
function getInstagramMedia($url) {
    // In reality, the official Instagram API would be used
    // This function is simplified for this project
    
    // Zwracamy false, implementacja będzie wymagała integracji z API Instagrama
    return false;
}

/**
 * Generates SEO meta tags for a page
 * @param string $title Tytuł strony
 * @param string $description Opis strony
 * @return string Znaczniki meta HTML
 */
function generateSeoTags($title, $description) {
    $output = "<title>{$title}</title>\n";
    $output .= "<meta name=\"description\" content=\"{$description}\">\n";
    
    // Open Graph tags
    $output .= "<meta property=\"og:title\" content=\"{$title}\">\n";
    $output .= "<meta property=\"og:description\" content=\"{$description}\">\n";
    $output .= "<meta property=\"og:type\" content=\"website\">\n";
    
    // Twitter Card tags
    $output .= "<meta name=\"twitter:card\" content=\"summary\">\n";
    $output .= "<meta name=\"twitter:title\" content=\"{$title}\">\n";
    $output .= "<meta name=\"twitter:description\" content=\"{$description}\">\n";
    
    return $output;
}
?>