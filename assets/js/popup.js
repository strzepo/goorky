/**
 * System popupów dla ToolsOnline
 * @author ToolsOnline
 * @version 1.0
 */

// Inicjalizacja systemu popupów po załadowaniu dokumentu
document.addEventListener("DOMContentLoaded", function () {
    initPopupSystem();
});

// Zmienne globalne dla odliczania
let countdownInterval;
let countdownTime = 15; // 15 sekund domyślnie

function initPopupSystem() {
    // Stwórz kontener popupu, jeśli nie istnieje
    if (!document.getElementById("popup-container")) {
        const popupContainer = document.createElement("div");
        popupContainer.id = "popup-container";
        popupContainer.className =
            "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 hidden";
        popupContainer.innerHTML = `
            <div id="popup-content" class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-full overflow-auto p-6 m-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="popup-title" class="text-xl font-bold"></h3>
                    <button id="close-popup" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="popup-body"></div>
                
                <!-- Sekcja social media -->
                <div id="social-share-section" class="mt-6 pt-4 border-t border-gray-200">
                    <p id="social-text" class="text-center mb-3"></p>
                    
                    <!-- Social Media -->
                    <div class="flex text-gray-800 justify-center gap-4 mb-4 text-2xl">
                      <a href="https://x.com/intent/tweet?text=Check+this+awesome+tool:+https://goorky.com" target="_blank" aria-label="Share on X" class="hover:text-blue-500"><i class="fab fa-x-twitter"></i></a>
                      <a href="https://www.facebook.com/sharer/sharer.php?u=https://goorky.com" target="_blank" aria-label="Share on Facebook" class="hover:text-blue-600"><i class="fab fa-facebook"></i></a>
                      <a href="https://www.linkedin.com/shareArticle?mini=true&url=https://goorky.com" target="_blank" aria-label="Share on LinkedIn" class="hover:text-blue-700"><i class="fab fa-linkedin"></i></a>
                      <a href="https://api.whatsapp.com/send?text=https://goorky.com" target="_blank" aria-label="Share on WhatsApp" class="hover:text-green-500"><i class="fab fa-whatsapp"></i></a>
                      <a href="https://t.me/share/url?url=https://goorky.com" target="_blank" aria-label="Share on Telegram" class="hover:text-blue-400"><i class="fab fa-telegram"></i></a>
                      <a href="https://www.reddit.com/submit?url=https://goorky.com" target="_blank" aria-label="Share on Reddit" class="hover:text-red-500"><i class="fab fa-reddit"></i></a>
                    </div>
                    
                    <!-- Separator -->
                    <div class="flex items-center justify-center my-4 text-gray-400 text-sm">
                      <span class="border-b border-gray-300 flex-grow mr-2"></span>
                      <span id="or-text" class="uppercase font-medium tracking-wide"></span>
                      <span class="border-b border-gray-300 flex-grow ml-2"></span>
                    </div>
                    
                    <!-- Buy coffee -->
                    <div class="mb-4 text-center">
                      <a href="https://buycoffee.to/lukson" target="_blank" class="text-yellow-600 font-semibold hover:underline">
                        ☕ <span id="buy-coffee-text"></span>
                      </a>
                    </div>
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button id="confirm-popup" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mr-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span id="confirm-text"></span> (<span id="countdown-timer">15</span>)
                    </button>
                    <button id="cancel-popup" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400"></button>
                </div>
            </div>
        `;
        document.body.appendChild(popupContainer);

        // Ustawienie przycisków zamykania
        document
            .getElementById("close-popup")
            .addEventListener("click", closePopup);
        document
            .getElementById("cancel-popup")
            .addEventListener("click", closePopup);
        document
            .getElementById("confirm-popup")
            .addEventListener("click", confirmPopup);

        // Zamknij popup po kliknięciu poza nim
        popupContainer.addEventListener("click", function (e) {
            if (e.target === popupContainer) {
                closePopup();
            }
        });
    }

    // Dołącz nasłuchiwacze zdarzeń do wszystkich elementów z klasą trigger-popup
    const triggers = document.querySelectorAll(".trigger-popup");
    triggers.forEach(function (trigger) {
        trigger.addEventListener("click", function (e) {
            e.preventDefault();

            // Pobierz dane formularza, jeśli przycisk jest wewnątrz formularza
            const form = trigger.closest("form");
            if (form) {
                const formData = new FormData(form);
                showPopup(form, formData);
            }
        });
    });
}

function showPopup(form, formData) {
    const popupContainer = document.getElementById("popup-container");
    const popupTitle = document.getElementById("popup-title");
    const popupBody = document.getElementById("popup-body");
    const confirmButton = document.getElementById("confirm-popup");
    const confirmText = document.getElementById("confirm-text");
    const cancelButton = document.getElementById("cancel-popup");
    const socialText = document.getElementById("social-text");
    const countdownTimer = document.getElementById("countdown-timer");
    const orText = document.getElementById("or-text");
    const buyCoffeeText = document.getElementById("buy-coffee-text");

    // Pobierz teksty z globalnych zmiennych lang, które są ładowane z plików językowych
    const lang = window.lang || {}; // Upewnij się, że lang istnieje

    // Ustaw tytuł na podstawie akcji formularza
    let title = lang["confirm_action"] || "Confirm Action";
    if (form.action.includes("bmi")) {
        title = lang["confirm_bmi"] || "Confirm BMI Calculation";
    } else if (form.action.includes("calories")) {
        title = lang["confirm_calories"] || "Confirm Calorie Calculation";
    } else if (form.action.includes("dates")) {
        title = lang["confirm_dates"] || "Confirm Date Calculation";
    } else if (form.action.includes("units")) {
        title = lang["confirm_units"] || "Confirm Unit Conversion";
    } else if (form.action.includes("password-generator")) {
        title = lang["confirm_password"] || "Confirm Password Generation";
    } else if (
        form.action.includes("youtube") ||
        form.action.includes("instagram") ||
        form.action.includes("facebook") ||
        form.action.includes("vimeo")
    ) {
        title = lang["confirm_download"] || "Confirm Download Request";
    }

    popupTitle.textContent = title;

    // Zbuduj treść popupu na podstawie danych formularza
    let content = '<div class="space-y-3">';
    content += `<p>${
        lang["please_confirm"] ||
        "Please confirm that you want to proceed with the following parameters:"
    }</p>`;
    content += '<div class="bg-gray-100 p-4 rounded-lg space-y-2">';

    // Wyświetl dane formularza w przyjazny dla użytkownika sposób
    for (const [key, value] of formData.entries()) {
        if (key !== "action_type") {
            let label = key.replace("_", " ");
            label = label.charAt(0).toUpperCase() + label.slice(1);

            // Formatuj wartość
            let displayValue = value;

            // Obsługa pól wyboru (checkboxów)
            if (value === "on") {
                displayValue = lang["yes"] || "Yes";
            } else if (key.startsWith("use_") && !value) {
                displayValue = lang["no"] || "No";
            }

            // Tłumaczenie nazw pól
            const fieldKey = "field_" + key;
            if (lang[fieldKey]) {
                label = lang[fieldKey];
            }

            // Tłumaczenie wartości pól dla specyficznych przypadków
            if (key === "gender") {
                if (value === "male") displayValue = lang["male"] || "Male";
                else if (value === "female")
                    displayValue = lang["female"] || "Female";
            } else if (key === "activity") {
                const activityKey = "activity_" + value;
                if (lang[activityKey]) {
                    displayValue = lang[activityKey];
                }
            } else if (key === "type") {
                const typeKey = "type_" + value;
                if (lang[typeKey]) {
                    displayValue = lang[typeKey];
                }
            }

            content += `<p><strong>${label}:</strong> ${displayValue}</p>`;
        }
    }

    content += "</div>";
    content += `<p>${
        lang["by_clicking"] ||
        'By clicking "Confirm", the calculation will be performed.'
    }</p>`;
    content += "</div>";

    popupBody.innerHTML = content;

    // Ustaw teksty dla przycisków i sekcji social
    confirmText.textContent = lang["confirm_button"] || "Confirm";
    cancelButton.textContent = lang["cancel_button"] || "Cancel";
    socialText.textContent =
        lang["share_text"] ||
        "If you like our tools, please share with your friends!";
    orText.textContent = lang["or"] || "or";
    buyCoffeeText.textContent = lang["buy_me_coffee"] || "Buy me a coffee";

    // Ustaw URL dla przycisków social media
    const pageUrl = encodeURIComponent(window.location.href);
    const shareText = encodeURIComponent(
        lang["share_message"] || "Check this awesome tool:"
    );

    // Aktualizacja linków udostępniania
    document.querySelectorAll("#social-share-section a").forEach((link) => {
        const href = link.getAttribute("href");
        if (href.includes("facebook.com")) {
            link.href = `https://www.facebook.com/sharer/sharer.php?u=${pageUrl}`;
        } else if (href.includes("x.com") || href.includes("twitter.com")) {
            link.href = `https://x.com/intent/tweet?text=${shareText}+${pageUrl}`;
        } else if (href.includes("linkedin.com")) {
            link.href = `https://www.linkedin.com/shareArticle?mini=true&url=${pageUrl}`;
        } else if (href.includes("whatsapp.com")) {
            link.href = `https://api.whatsapp.com/send?text=${shareText} ${pageUrl}`;
        } else if (href.includes("t.me")) {
            link.href = `https://t.me/share/url?url=${pageUrl}&text=${shareText}`;
        } else if (href.includes("reddit.com")) {
            link.href = `https://www.reddit.com/submit?url=${pageUrl}&title=${shareText}`;
        }
    });

    // Rozpocznij odliczanie i zablokuj przycisk Potwierdź
    countdownTime = 15;
    countdownTimer.textContent = countdownTime;
    confirmButton.disabled = true;

    if (countdownInterval) {
        clearInterval(countdownInterval);
    }

    countdownInterval = setInterval(function () {
        countdownTime--;
        countdownTimer.textContent = countdownTime;

        if (countdownTime <= 0) {
            clearInterval(countdownInterval);
            confirmButton.disabled = false;
            countdownTimer.textContent = "0";
        }
    }, 1000);

    // Ustaw przycisk potwierdzenia, aby wysłał formularz
    confirmButton.onclick = function () {
        if (!confirmButton.disabled) {
            form.submit();
            closePopup();
        }
    };

    // Pokaż popup
    popupContainer.classList.remove("hidden");
}

function closePopup() {
    const popupContainer = document.getElementById("popup-container");
    popupContainer.classList.add("hidden");

    // Zatrzymaj odliczanie
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
}

function confirmPopup() {
    // Ta funkcja jest wywoływana po kliknięciu przycisku potwierdzenia
    // Rzeczywista akcja jest ustawiana w funkcji showPopup
    if (!document.getElementById("confirm-popup").disabled) {
        closePopup();
    }
}
