const fullscreenIcon = document.querySelector(".pwe-premieres__fullscreen-icon");
const image = document.querySelector(".pwe-premieres__image img");

fullscreenIcon.addEventListener("click", () => {
    const imgUrl = image.src;

    const fullscreen = document.createElement("div");
    fullscreen.id = "pwePremieresFullscreen";
    fullscreen.className = "pwe-premieres__fullscreen";

    const img = document.createElement("img");
    img.className = "pwe-premieres__fullscreen-image";
    img.src = imgUrl;

    const closeBtn = document.createElement("span");
    closeBtn.className = "pwe-premieres__fullscreen-close";
    closeBtn.innerHTML = "&times;";

    const closeFullscreen = () => {
        fullscreen.remove();
        document.removeEventListener("keydown", escHandler);
    };

    fullscreen.addEventListener("click", (e) => {
        if (e.target === fullscreen || e.target === closeBtn) {
            closeFullscreen();
        }
    });

    const escHandler = (e) => {
        if (e.key === "Escape") {
            closeFullscreen();
        }
    };
    document.addEventListener("keydown", escHandler);

    fullscreen.appendChild(img);
    fullscreen.appendChild(closeBtn);
    document.body.appendChild(fullscreen);
});