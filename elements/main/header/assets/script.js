(function(){

    // --- KONFIG ---
    const INTERVAL = 6000;
    const images = [
        "/doc/header_mobile.webp",
        "/wp-content/plugins/pwe-media/media/bg_mobile_2.webp",
        "/wp-content/plugins/pwe-media/media/bg_mobile_3.webp"
    ];

    const root = document.querySelector(".pwe-header__container");
    if(!root || !images.length) return;

    // Create layers from image array
    const layers = images.map(src => {
        const d = document.createElement("div");
        d.className = "pwe-bg-image";
        d.style.backgroundImage = `url("${src}")`;
        root.prepend(d);
        return d;
    });

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const delay = reduceMotion ? Math.max(INTERVAL, 12000) : INTERVAL;

    // Start
    let current = 0;
    layers[current].classList.add("is-active");

    let firstRun = true;

    function nextBg(){
        const prev = current;

        if (firstRun) {
            // First time: we go normally through all 3
            current = (current + 1) % layers.length;
            if (current === layers.length - 1) {
                // If it comes to the last one → the next turn is only between 2 and 3
                firstRun = false;
            }
        } else {
            // After the first rotation: we only jump between indexes 1 and 2
            current = current === 1 ? 2 : 1;
        }

        layers[prev].classList.remove("is-active");
        layers[current].classList.add("is-active");
    }

    let ticker = setInterval(nextBg, delay);

    document.addEventListener("visibilitychange", () => {
        if (document.hidden) { clearInterval(ticker); }
        else { ticker = setInterval(nextBg, delay); }
    });

})();
