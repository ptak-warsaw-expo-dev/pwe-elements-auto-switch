// funkcja do wyboru wersji językowej
function langChecker(plText, enText) {
    const lang = document.documentElement.lang || "pl"; 
    return lang.startsWith("pl") ? plText : enText;
}

document.addEventListener("DOMContentLoaded", () => {
    const pweSummary = document.querySelector(".pwe-summary__logos");
    if (!pweSummary) return;

    const infoIcon = pweSummary.querySelector(".icon-info");

    // Tworzymy X (zamknięcie), jeśli jeszcze nie istnieje
    let closeIcon = pweSummary.querySelector(".icon-close");
    if (!closeIcon) {
        closeIcon = document.createElement("img");
        closeIcon.src = "/wp-content/plugins/pwe-media/media/numbers-el/close-icon.svg";
        closeIcon.alt = "Zamknij";
        closeIcon.className = "icon-close";

        if (infoIcon && infoIcon.parentNode) {
            infoIcon.parentNode.insertBefore(closeIcon, infoIcon.nextSibling);
        } else {
            pweSummary.appendChild(closeIcon);
        }
    }

    // Tworzymy blok tekstowy, jeśli jeszcze nie istnieje
    let infoText = pweSummary.querySelector(".ufi-info-text");
    if (!infoText) {
        infoText = document.createElement("div");
        infoText.className = "ufi-info-text";
        infoText.innerHTML = `
            <p>${langChecker(
                "<strong>Certyfikat UFI Member</strong> to oficjalne potwierdzenie, że firma <strong>PTAK WARSAW EXPO SP. z o.o.</strong> jest członkiem UFI – The Global Association of the Exhibition Industry (Światowego Stowarzyszenia Przemysłu Targowego). UFI (Union des Foires Internationales) to międzynarodowa organizacja branżowa, która zrzesza kluczowych graczy globalnego rynku targów i wystaw, w tym m.in: organizatorów targów i wystaw, zarządców centrów wystawienniczych.",
                "<strong>UFI Member Certificate</strong> is an official confirmation that <strong>PTAK WARSAW EXPO SP. z o.o.</strong> is a member of UFI – The Global Association of the Exhibition Industry. UFI (Union des Foires Internationales) is an international industry organization that brings together key players in the global trade fair and exhibition market, including: trade fair and exhibition organizers, exhibition center managers."
            )}</p>
        `;
        pweSummary.appendChild(infoText);
    }

    // Kliknięcie w ikonę info
    if (infoIcon) {
        infoIcon.addEventListener("click", () => {
            pweSummary.classList.add("hide-images", "show-text");
        });
    }

    // Kliknięcie w ikonę X (zamknięcie)
    closeIcon.addEventListener("click", () => {
        pweSummary.classList.remove("hide-images", "show-text");
    });
});