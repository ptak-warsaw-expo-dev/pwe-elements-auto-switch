document.addEventListener("DOMContentLoaded", function () {
    const fullscreenIcons = document.querySelectorAll(".pwe-premieres__fullscreen-icon");

    fullscreenIcons.forEach(icon => {
        icon.addEventListener("click", () => {
            const container = icon.closest(".pwe-premieres__image");
            if (!container) return;

            const image = container.querySelector("img");
            if (!image) return;

            const fullscreen = document.createElement("div");
            fullscreen.id = "pwePremieresFullscreen";
            fullscreen.className = "pwe-premieres__fullscreen";

            const img = document.createElement("img");
            img.className = "pwe-premieres__fullscreen-image";
            img.src = image.src;

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
    });
});