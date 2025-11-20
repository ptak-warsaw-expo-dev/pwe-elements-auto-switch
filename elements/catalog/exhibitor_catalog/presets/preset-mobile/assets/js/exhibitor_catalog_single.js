document.addEventListener("DOMContentLoaded", () => {

    // --------------------------------------------
    // 1) Usercentrics – przenoszenie przycisku
    // --------------------------------------------

    const initUsercentricsFix = () => {
        const aside = document.querySelector("aside#usercentrics-cmp-ui");
        if (!aside || !aside.shadowRoot) return;

        const shadow = aside.shadowRoot;

        const adjustButton = () => {
            const btn = shadow.querySelector("#uc-main-dialog.privacyButton");
            if (!btn) return;

            btn.style.left = "17px";
            btn.style.bottom = "80px";

            aside.style.display = "block";
            aside.style.opacity = "1";
            aside.style.visibility = "visible";
        };

        adjustButton();

        new MutationObserver(adjustButton).observe(shadow, {
            childList: true,
            subtree: true
        });
    };

    // Szukamy usercentrics maksymalnie 20× (10 sek)
    const tryUsercentrics = () => {
        let attempts = 0;
        const interval = setInterval(() => {
            attempts++;
            if (attempts > 20) return clearInterval(interval);

            const aside = document.querySelector("aside#usercentrics-cmp-ui");
            if (aside) {
                clearInterval(interval);
                initUsercentricsFix();
            }
        }, 500);
    };

    tryUsercentrics();


    // --------------------------------------------
    // 2) Swiper – wspólna funkcja inicjalizacji
    // --------------------------------------------

    const initSwiper = (selector, options) => {
        const element = document.querySelector(selector);
        if (!element) return;
        new Swiper(selector, options);
    };

    initSwiper(".exhibitor-single-mobile__brands.swiper", {
        slidesPerView: "auto",
        centeredSlides: true,
        slidesPerGroup: 1,
        spaceBetween: 12,
        loop: true
    });

    initSwiper(".exhibitor-single-mobile__industries.swiper", {
        slidesPerView: 2.2,
        slidesPerGroup: 1,
        spaceBetween: 12,
        loop: true
    });

    initSwiper(".exhibitor-single-mobile__categories.swiper", {
        autoHeight: true,
        spaceBetween: 18,
        loop: true
    });

    initSwiper(".exhibitor-single-mobile__products.swiper", {
        slidesPerView: 2.2,
        spaceBetween: 18,
        loop: true
    });

    initSwiper(".exhibitor-single-mobile__documents.swiper", {
        slidesPerView: 1.4,
        spaceBetween: 18,
        loop: true
    });


    // --------------------------------------------
    // 3) Rozwijany tekst – uproszczona, szybka wersja
    // --------------------------------------------

    document.querySelectorAll(".collapsible-text").forEach(box => {
        const btn = box.nextElementSibling;
        if (!btn || !btn.classList.contains("collapsible-toggle")) return;

        const collapsedHeight = 152;
        const fullHeight = box.scrollHeight;

        if (fullHeight <= collapsedHeight + 10) {
            btn.style.display = "none";
            return;
        }

        let expanded = false;
        box.style.height = collapsedHeight + "px";
        btn.style.display = "inline-block";

        btn.addEventListener("click", () => {
            expanded = !expanded;

            const start = box.clientHeight;
            const end = expanded ? fullHeight : collapsedHeight;

            box.style.height = start + "px";
            void box.offsetHeight; // reflow
            box.style.height = end + "px";

            btn.textContent = expanded ? "Pokaż mniej" : "Pokaż więcej";
        });

        box.addEventListener("transitionend", () => {
            if (expanded) box.style.height = "auto";
        });
    });

});
