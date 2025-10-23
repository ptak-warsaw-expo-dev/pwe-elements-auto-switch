(function() {
    const INTERVAL = 6000;
    const images = [
        "/doc/header_mobile.webp",
        "/wp-content/plugins/pwe-media/media/bg_mobile_2.webp",
        "/wp-content/plugins/pwe-media/media/bg_mobile_3.webp"
    ];

    const root = document.querySelector(".pwe-header__container");
    if (!root || !images.length) return;

    let layers = [];
    let current = 0;
    let firstRun = true;
    let ticker;

    const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const delay = reduceMotion ? Math.max(INTERVAL, 12000) : INTERVAL;

    function createLayers() {
        layers = images.map(src => {
            const d = document.createElement("div");
            d.className = "pwe-bg-image";
            d.style.backgroundImage = `url("${src}")`;
            root.prepend(d);
            return d;
        });
        layers[current].classList.add("is-active");
    }

    function nextBg() {
        const prev = current;
        if (firstRun) {
            current = (current + 1) % layers.length;
            if (current === layers.length - 1) firstRun = false;
        } else {
            current = current === 1 ? 2 : 1;
        }
        layers[prev].classList.remove("is-active");
        layers[current].classList.add("is-active");
    }

    function startTicker() {
        clearInterval(ticker);
        ticker = setInterval(nextBg, delay);
    }

    function stopTicker() {
        clearInterval(ticker);
    }

    function init() {
        if (window.innerWidth < 960 && layers.length === 0) {
            createLayers();
            startTicker();
        } else if (window.innerWidth >= 960 && layers.length > 0) {
            // Remove layers if someone zooms in
            layers.forEach(l => l.remove());
            layers = [];
            stopTicker();
            current = 0;
            firstRun = true;
        }
    }

    window.addEventListener('resize', init);
    document.addEventListener("visibilitychange", () => {
        if (document.hidden) stopTicker();
        else if (window.innerWidth < 960) startTicker();
    });

    init();
})();
