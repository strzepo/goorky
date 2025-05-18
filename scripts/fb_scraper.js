const puppeteer = require("puppeteer");
const fetch = require("node-fetch"); // Do pobierania pliku
const fs = require("fs"); // Do zapisu do pliku
const path = require("path"); // Do pracy ze ścieżkami plików
const urlModule = require("url"); // Do parsowania URL-i

const url = process.argv[2];

(async () => {
    if (!url) {
        console.error("Użycie: node skrypt.js <URL filmu z Facebooka>");
        console.error("Brak URL filmu jako argumentu.");
        process.exit(1);
    }

    // --- Poprawiona logika zamiany URL-a ---
    let mobileUrl = url;
    try {
        const urlObj = new URL(url);
        urlObj.hostname = "m.facebook.com";
        // Zachowujemy parametr 'v' dla URLi typu watch?v=
        if (urlObj.searchParams.has("v")) {
            // Jeśli jest parametr 'v', zachowaj go i zbuduj URL
            mobileUrl = `https://m.facebook.com/watch?v=${urlObj.searchParams.get(
                "v"
            )}`;
        } else {
            // Dla innych typów URL (np. /permalink/...), po prostu zmień hosta i usuń parametry zapytania
            mobileUrl = urlObj.toString().split("?")[0]; // Usuń wszystkie parametry po ścieżce
            mobileUrl = mobileUrl.replace(
                /^https?:\/\/(www\.|web\.)?facebook\.com/,
                "https://m.facebook.com"
            );
        }
    } catch (e) {
        console.warn(
            "Nie udało się sparsować URL za pomocą obiektu URL. Sprawdzam prostszą metodę."
        );
        // Fallback na prostszą metodę replace
        mobileUrl = url
            .replace(
                /^https?:\/\/(www\.|web\.)?facebook\.com/,
                "https://m.facebook.com"
            )
            .split("&")[0]; // Usuń większość parametrów
    }
    // -------------------------------------

    console.log(`Przetwarzanie URL: ${url}`);
    console.log(`Próba na wersji mobilnej: ${mobileUrl}`);

    const browser = await puppeteer.launch({
        headless: true, // Zmień na false, aby zobaczyć przeglądarkę (do debugowania)
        args: [
            "--no-sandbox",
            "--disable-setuid-sandbox",
            "--disable-notifications", // Wyłącz powiadomienia
            "--disable-popup-blocking", // Wyłącz blokowanie pop-upów
            "--disable-features=site-per-process", // Może pomóc na niektórych stronach
            "--disable-gpu", // Czasami pomaga na serwerach/kontenerach
        ],
        // executablePath: 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe', // Opcjonalnie: ścieżka do Chrome, jeśli puppeteer nie może znaleźć
    });

    let foundVideoUrl = null;
    let foundVideoUrlHD = null;
    const potentialNetworkUrls = new Set();

    try {
        const page = await browser.newPage();

        // Ustaw realistyczny User Agent dla urządzenia mobilnego
        await page.setUserAgent(
            "Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Mobile/15E148 Safari/604.1"
        );

        // Nasłuchuj na odpowiedzi sieciowe
        page.on("response", async (response) => {
            const url = response.url();
            const contentType = response.headers()["content-type"];

            // Szukaj URL-i wyglądających na linki do wideo lub streamingu
            if (
                url.includes(".mp4") ||
                url.includes("video_redirect_url") ||
                (url.includes("fbcdn.net") && url.includes("/v/")) || // Typowy wzorzec URL wideo na fbcdn
                url.includes(".m3u8") || // HLS manifest
                url.includes(".mpd") || // DASH manifest
                // Content type wskazujący na wideo, HLS, MPD lub JSON z danymi wideo
                (contentType &&
                    (contentType.includes("video/") ||
                        contentType.includes("application/vnd.apple.mpegurl") ||
                        contentType.includes("application/dash+xml") ||
                        (contentType.includes("application/json") &&
                            url.includes("video"))))
            ) {
                // Sprawdź czy URL nie jest zbyt krótki i wygląda sensownie
                if (url.length > 50 && url.startsWith("http")) {
                    potentialNetworkUrls.add(url);
                    // console.log(`[NETWORK] Znaleziono potencjalny URL wideo/streamu/JSON: ${url} (Type: ${contentType || 'N/A'})`); // Debugowanie sieci
                }
            }
        });

        // Przejdź do strony
        await page.goto(mobileUrl, {
            waitUntil: "domcontentloaded", // Poczekaj na załadowanie podstawowego DOM
            timeout: 90000, // Dłuższy timeout (90 sekund)
        });

        console.log("Strona załadowana. Szukam linku wideo...");

        // Poczekaj chwilę na załadowanie podstawowych elementów strony
        await new Promise((resolve) => setTimeout(resolve, 5000));

        // --- Symulacja przewijania i kliknięcia ---
        console.log(
            "Przewijam stronę, aby upewnić się, że wideo jest widoczne..."
        );
        // Przewiń na sam dół, a potem trochę do góry, żeby aktywować lazy loading
        await page.evaluate(() =>
            window.scrollTo(0, document.body.scrollHeight)
        );
        await new Promise((resolve) => setTimeout(resolve, 2000)); // Poczekaj 2 sekundy
        await page.evaluate(() => window.scrollTo(0, 0)); // Przewiń z powrotem na górę (opcjonalnie, czasem pomaga)
        await new Promise((resolve) => setTimeout(resolve, 2000)); // Poczekaj 2 sekundy
        await page.evaluate(() => window.scrollTo(0, window.innerHeight / 2)); // Przewiń do połowy strony (opcjonalnie)
        await new Promise((resolve) => setTimeout(resolve, 2000)); // Poczekaj 2 sekundy

        console.log(
            "Próbuję znaleźć i kliknąć element wideo/przycisk odtwarzania..."
        );

        // Bardziej ogólne selektory dla elementu wideo lub jego kontenera
        const videoContainerSelectors = [
            "video", // Bezpośredni tag video
            "[data-story-pc] video", // Tag video w typowym kontenerze
            '[data-e2e="video-player"] video', // Tag video w innym kontenerze
            ".fb-video", // Czasami używana klasa
            '[data-sigil*="play"]', // Element z sigil odpowiedzialnym za odtwarzanie (często kontener)
            '[data-testid*="play_button"]', // Czasami używany data-testid przycisku
        ];

        let videoElementFound = false;
        let clicked = false;

        // Próbuj czekać na którykolwiek z potencjalnych selektorów kontenera/wideo
        for (const selector of videoContainerSelectors) {
            try {
                console.log(`Próbuję czekać na selektor: ${selector}`);
                // Czekaj do 15 sekund na pojawienie się elementu i upewnij się, że jest widoczny
                const element = await page.waitForSelector(selector, {
                    timeout: 15000,
                    visible: true,
                });
                if (element) {
                    console.log(
                        `Znaleziono element dla selektora: ${selector}. Próbuję kliknąć.`
                    );
                    videoElementFound = true;
                    // Spróbuj kliknąć znaleziony element
                    await element.click();
                    clicked = true;
                    console.log("Kliknięto element wideo/kontener.");
                    break; // Przerywamy pętlę po pierwszym udanym znalezieniu i kliknięciu
                }
            } catch (e) {
                // console.log(`Nie znaleziono lub nie kliknięto elementu dla selektora: ${selector} (błąd: ${e.message})`); // Debugowanie
            }
        }

        if (clicked) {
            console.log(
                "Symulacja kliknięcia zakończona. Czekam dłużej na załadowanie strumienia i przechwycenie URL-i..."
            );
            // Poczekaj trochę dłużej po kliknięciu na załadowanie strumienia
            await new Promise((resolve) => setTimeout(resolve, 12000)); // Czekaj 12 sekund
        } else if (videoElementFound) {
            console.warn(
                "Znaleziono element wideo/kontener, ale nie udało się go kliknąć. Czekam mimo to..."
            );
            await new Promise((resolve) => setTimeout(resolve, 10000)); // Czekaj 10 sekund
        } else {
            console.warn(
                "Nie znaleziono żadnego potencjalnego elementu wideo/odtwarzania do kliknięcia. Czekam mimo to..."
            );
            // Czekaj mimo wszystko, bo może stream ładuje się bez klikania lub po innym zdarzeniu, lub JSON jest dostępny
            await new Promise((resolve) => setTimeout(resolve, 8000));
        }
        // ----------------------------------------------------------

        // Spróbuj znaleźć link wideo w DOM lub w danych na stronie (po potencjalnym kliknięciu i czekaniu)
        const { domVideoUrl, domVideoUrlHD } = await page.evaluate(() => {
            let videoUrlSD = null;
            let videoUrlHD = null;

            // Szukaj standardowego tagu <video> lub <source>
            const videoElement = document.querySelector("video");
            if (videoElement && videoElement.src) {
                videoUrlSD = videoElement.src;
                // Czasami HD jest w atrybucie 'data-hd-src' lub podobnym
                videoUrlHD =
                    videoElement.getAttribute("data-hd-src") ||
                    videoElement.getAttribute("data-src-hd");
            }

            // Szukaj linków w kodzie HTML/skryptach (często osadzone jako JSON)
            // Używamy outerHTML, żeby złapać wszystko, w tym skrypty z danymi
            const pageHTML = document.documentElement.outerHTML;
            try {
                // Szukaj playable_url (SD) i playable_url_quality_hd (HD)
                const sdMatch = pageHTML.match(/"playable_url":"(.*?)"/);
                if (sdMatch && sdMatch[1]) {
                    // Upewnij się, że to jest poprawny URL, dekodując encje HTML & i backslashe
                    videoUrlSD = sdMatch[1]
                        .replace(/\\/g, "")
                        .replace(/&/g, "&");
                }

                const hdMatch = pageHTML.match(
                    /"playable_url_quality_hd":"(.*?)"/
                );
                if (hdMatch && hdMatch[1]) {
                    videoUrlHD = hdMatch[1]
                        .replace(/\\/g, "")
                        .replace(/&/g, "&");
                }

                // Szukaj innych wzorców, np. w data-store na elementach (powtarzamy, bo w evaluate są izolowane środowiska)
                const dataStoreElements =
                    document.querySelectorAll("[data-store]");
                for (const element of dataStoreElements) {
                    try {
                        const dataStoreStr = element.getAttribute("data-store");
                        if (dataStoreStr) {
                            const dataStore = JSON.parse(dataStoreStr);
                            if (
                                dataStore.video &&
                                dataStore.video.playable_url
                            ) {
                                videoUrlSD = dataStore.video.playable_url
                                    .replace(/\\/g, "")
                                    .replace(/&/g, "&");
                            }
                            if (
                                dataStore.video &&
                                dataStore.video.playable_url_quality_hd
                            ) {
                                videoUrlHD =
                                    dataStore.video.playable_url_quality_hd
                                        .replace(/\\/g, "")
                                        .replace(/&/g, "&");
                            }
                        }
                    } catch (e) {
                        /* ignore parsing errors */
                    }
                }

                // Sprawdź również okno.location.href, czasem przekierowuje bezpośrednio po odtworzeniu
                // Ale to rzadkie dla plików wideo, częściej dla innych treści
                // if (window.location.href.includes(".mp4") || window.location.href.includes("fbcdn.net/v/")) {
                //     // Można by tu dodać logikę, ale potencjalne URL-e z sieci są lepsze
                // }
            } catch (e) {
                console.error(
                    "Błąd podczas parsowania danych strony w evaluate:",
                    e
                );
            }

            // Filtruj potencjalne URL-e znalezione w DOM - muszą być pełnymi URLami
            if (videoUrlSD && !videoUrlSD.startsWith("http")) videoUrlSD = null;
            if (videoUrlHD && !videoUrlHD.startsWith("http")) videoUrlHD = null;

            return { domVideoUrl: videoUrlSD, domVideoUrlHD: videoUrlHD };
        });

        foundVideoUrl = domVideoUrl;
        foundVideoUrlHD = domVideoUrlHD;

        //--- Podsumowanie i wybór najlepszego URL ---

        let finalVideoUrl = null;

        // 1. Preferuj HD znalezione w DOM/danych na stronie
        if (foundVideoUrlHD) {
            finalVideoUrl = foundVideoUrlHD;
            console.log("Znaleziono link wideo (HD) w danych strony.");
        }
        // 2. Jeśli nie ma HD, użyj SD znalezionego w DOM/danych
        else if (foundVideoUrl) {
            finalVideoUrl = foundVideoUrl;
            console.log("Znaleziono link wideo (SD) w danych strony.");
        }
        // 3. Jeśli nie ma linków w danych strony, sprawdź linki przechwycone przez sieć
        // Wybierz najlepszego kandydata z przechwyconych URL-i
        let bestNetUrl = null;
        // Posortuj potencjalne URL-e według heurystyki: MP4 > fbcdn/v/ > stream > inne
        const sortedPotentialUrls = Array.from(potentialNetworkUrls).sort(
            (a, b) => {
                const scoreA =
                    (a.includes(".mp4") ? 4 : 0) +
                    (a.includes("fbcdn.net/v/") ? 3 : 0) +
                    (a.includes(".m3u8") || a.includes(".mpd") ? 2 : 0) +
                    (a.includes("application/json") && a.includes("video")
                        ? 1
                        : 0);
                const scoreB =
                    (b.includes(".mp4") ? 4 : 0) +
                    (b.includes("fbcdn.net/v/") ? 3 : 0) +
                    (b.includes(".m3u8") || b.includes(".mpd") ? 2 : 0) +
                    (b.includes("application/json") && b.includes("video")
                        ? 1
                        : 0);
                // Sortuj malejąco po "score", a potem malejąco po długości URL (dłuższe URL-e fbcdn bywają bardziej szczegółowe)
                return scoreB - scoreA || b.length - a.length;
            }
        );

        if (sortedPotentialUrls.length > 0) {
            bestNetUrl = sortedPotentialUrls[0];
            if (bestNetUrl.startsWith("http")) {
                finalVideoUrl = bestNetUrl;
                console.log(
                    `Wybrano najlepszy potencjalny link wideo z sieci: ${finalVideoUrl}`
                );
            }
        }

        //--- Wynik i Pobieranie ---
        if (finalVideoUrl && finalVideoUrl.startsWith("http")) {
            console.log("\n--- Znaleziono link do pobrania ---");
            console.log(finalVideoUrl);
            console.log("-----------------------------------");

            // Sprawdź, czy link to manifest streamu HLS/DASH
            if (
                finalVideoUrl.includes(".m3u8") ||
                finalVideoUrl.includes(".mpd")
            ) {
                console.warn(
                    "Uwaga: Znaleziony link wskazuje na strumień wideo (HLS/DASH), a nie pojedynczy plik MP4."
                );
                console.warn(
                    "Pobranie strumienia wymaga bardziej zaawansowanego parsowania manifestu i pobierania chunków."
                );
                console.warn(
                    "Standardowe pobieranie tego linku może pobrać tylko plik manifestu, a nie wideo."
                );
                console.log(
                    "Zalecane narzędzia do pobierania strumieni: yt-dlp (obsługuje linki FB) lub specjalizowane skrypty/aplikacje do HLS/DASH."
                );
                // Nie próbujemy pobierać strumienia w tym skrypcie, tylko informujemy.
            } else {
                // ### Sekcja pobierania ###
                console.log("Rozpoczynam pobieranie filmu...");

                // Spróbuj wygenerować nazwę pliku
                let filename = "facebook_video";
                try {
                    const parsedUrl = new URL(url);
                    if (parsedUrl.searchParams.has("v")) {
                        filename = parsedUrl.searchParams.get("v");
                    } else {
                        const pathSegments = parsedUrl.pathname
                            .split("/")
                            .filter((segment) => segment);
                        if (pathSegments.length > 0) {
                            filename = pathSegments[pathSegments.length - 1];
                        }
                    }
                } catch (e) {
                    console.warn(
                        "Nie udało się wygenerować nazwy pliku z URL:",
                        e.message
                    );
                }
                // Spróbuj odgadnąć rozszerzenie z finalVideoUrl, ale domyślnie .mp4
                let extension = ".mp4";
                if (finalVideoUrl.includes(".mp4")) extension = ".mp4";
                else if (finalVideoUrl.includes(".webm")) extension = ".webm";
                // Można dodać więcej typów, ale MP4 jest najczęstszy

                filename = `${filename}${extension}`; // Dodaj rozszerzenie

                const filePath = path.join(__dirname, filename); // Zapisz w tym samym folderze

                try {
                    const response = await fetch(finalVideoUrl);

                    if (!response.ok) {
                        throw new Error(
                            `Błąd HTTP podczas pobierania: ${response.status} ${response.statusText}`
                        );
                    }

                    const fileStream = fs.createWriteStream(filePath);
                    await new Promise((resolve, reject) => {
                        response.body.pipe(fileStream);
                        response.body.on("error", reject);
                        fileStream.on("finish", resolve);
                        fileStream.on("error", reject);
                    });

                    console.log(`\nPomyślnie pobrano film do: ${filePath}`);
                } catch (downloadError) {
                    console.error(
                        "\nBłąd podczas pobierania filmu:",
                        downloadError.message
                    );
                    console.error("Link:", finalVideoUrl);
                    console.error(
                        "Sprawdź, czy link nie wygasł lub nie wymaga specjalnych nagłówków."
                    );
                }
                // ########################
            }
        } else {
            console.error("\nNie udało się znaleźć linku do pobrania wideo.");
            console.error("Możliwe przyczyny:");
            console.error(
                "- Film jest prywatny lub dostępny tylko dla znajomych (skrypt nie loguje się)."
            );
            console.error(
                "- Strona Facebooka lub struktura wideo zmieniła się znacząco."
            );
            console.error(
                "- Film wymaga innej interakcji niż kliknięcie/przewinięcie."
            );
            console.error("- Problem z załadowaniem strony lub błąd sieci.");

            if (potentialNetworkUrls.size > 0) {
                console.log(
                    "\nPotencjalne URL-e znalezione w sieci (do analizy):"
                );
                potentialNetworkUrls.forEach((url) => console.log(url));
                console.log(
                    "\nPowyższe URL-e mogą wskazywać na manifesty streamów (.m3u8, .mpd) lub fragmenty wideo."
                );
                console.log(
                    "Analiza tych linków może wymagać ręcznego sprawdzenia lub zaawansowanych narzędzi."
                );
            } else {
                console.log(
                    "\nNie znaleziono żadnych potencjalnych URL-i w sieci, co wskazuje na brak załadowania strumienia wideo."
                );
            }
        }
    } catch (error) {
        console.error("Wystąpił błąd ogólny:", error.message);
        console.error("Szczegóły błędu:", error);
    } finally {
        await browser.close();
        console.log("Przeglądarka zamknięta.");
    }
})();
