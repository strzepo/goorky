/**
 * Główny plik JavaScript dla ToolsOnline
 */

// Funkcja wykonywana po załadowaniu strony
document.addEventListener("DOMContentLoaded", function () {
    // Obsługa kopiowania do schowka
    setupCopyButtons();

    // Inicjalizacja dynamicznych formularzy
    setupDynamicForms();

    // Obsługa animacji elementów
    setupAnimations();
});

/**
 * Konfiguracja przycisków kopiowania do schowka
 */
function setupCopyButtons() {
    const copyButtons = document.querySelectorAll("[data-copy]");

    copyButtons.forEach((button) => {
        button.addEventListener("click", function () {
            const targetId = this.getAttribute("data-copy");
            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                // Tworzenie tymczasowego elementu textarea
                const textArea = document.createElement("textarea");
                textArea.value = targetElement.textContent;
                document.body.appendChild(textArea);

                // Zaznaczenie i skopiowanie tekstu
                textArea.select();
                document.execCommand("copy");

                // Usunięcie tymczasowego elementu
                document.body.removeChild(textArea);

                // Informacja o skopiowaniu
                const originalText = this.innerHTML;
                this.innerHTML =
                    '<svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>';

                setTimeout(() => {
                    this.innerHTML = originalText;
                }, 2000);
            }
        });
    });
}

/**
 * Konfiguracja dynamicznych formularzy
 */
function setupDynamicForms() {
    // Obsługa dynamicznego przełączania formularzy
    const formSwitchers = document.querySelectorAll("[data-form-switch]");

    formSwitchers.forEach((switcher) => {
        switcher.addEventListener("change", function () {
            const targetForm = this.getAttribute("data-form-switch");
            const forms = document.querySelectorAll(".switchable-form");

            forms.forEach((form) => {
                if (form.id === targetForm) {
                    form.classList.remove("hidden");
                } else {
                    form.classList.add("hidden");
                }
            });
        });
    });

    // Obsługa range inputów
    const rangeInputs = document.querySelectorAll('input[type="range"]');

    rangeInputs.forEach((input) => {
        const valueDisplay = document.getElementById(input.id + "Value");

        if (valueDisplay) {
            input.addEventListener("input", function () {
                valueDisplay.textContent = this.value;
            });
        }
    });
}

/**
 * Konfiguracja animacji elementów
 */
function setupAnimations() {
    // Animacja elementów podczas przewijania strony
    const animatedElements = document.querySelectorAll(".animate-on-scroll");

    if (animatedElements.length > 0) {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add("animated");
                    }
                });
            },
            { threshold: 0.1 }
        );

        animatedElements.forEach((element) => {
            observer.observe(element);
        });
    }
}

/**
 * Funkcja walidująca formularz przed wysłaniem
 * @param {HTMLFormElement} form - Element formularza do walidacji
 * @returns {boolean} - Czy formularz jest poprawny
 */
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll("[required]");

    requiredFields.forEach((field) => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add("border-red-500");

            // Dodanie komunikatu o błędzie
            const errorMessage = field.nextElementSibling;
            if (
                errorMessage &&
                errorMessage.classList.contains("error-message")
            ) {
                errorMessage.classList.remove("hidden");
            } else {
                const newErrorMessage = document.createElement("p");
                newErrorMessage.classList.add(
                    "error-message",
                    "text-red-500",
                    "text-sm",
                    "mt-1"
                );
                newErrorMessage.textContent = "To pole jest wymagane";
                field.parentNode.insertBefore(
                    newErrorMessage,
                    field.nextSibling
                );
            }
        } else {
            field.classList.remove("border-red-500");

            // Ukrycie komunikatu o błędzie
            const errorMessage = field.nextElementSibling;
            if (
                errorMessage &&
                errorMessage.classList.contains("error-message")
            ) {
                errorMessage.classList.add("hidden");
            }
        }
    });

    return isValid;
}

/**
 * Funkcja do formatowania liczb
 * @param {number} number - Liczba do sformatowania
 * @param {number} decimals - Liczba miejsc po przecinku
 * @returns {string} - Sformatowana liczba
 */
function formatNumber(number, decimals = 2) {
    return number.toLocaleString("pl-PL", {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

/**
 * Funkcja do obsługi komunikatów flash
 * @param {string} message - Treść komunikatu
 * @param {string} type - Typ komunikatu (success, error, info)
 */
function showFlashMessage(message, type = "info") {
    const flashContainer = document.getElementById("flash-messages");

    if (flashContainer) {
        const flashMessage = document.createElement("div");
        flashMessage.classList.add("flash-message", type);
        flashMessage.textContent = message;

        // Dodanie przycisku zamknięcia
        const closeButton = document.createElement("button");
        closeButton.innerHTML = "&times;";
        closeButton.classList.add("ml-2", "font-bold");
        closeButton.addEventListener("click", function () {
            flashContainer.removeChild(flashMessage);
        });

        flashMessage.appendChild(closeButton);
        flashContainer.appendChild(flashMessage);

        // Automatyczne ukrycie po 5 sekundach
        setTimeout(() => {
            if (flashContainer.contains(flashMessage)) {
                flashContainer.removeChild(flashMessage);
            }
        }, 5000);
    }
}
