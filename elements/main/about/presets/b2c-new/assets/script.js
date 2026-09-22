document.addEventListener("DOMContentLoaded", () => {
    const about = document.getElementById("pweAbout");
    if (!about) return;

    const allImages = JSON.parse(about.dataset.images || "[]");
    const items = [...about.querySelectorAll(".pwe-about__media-item:not(.video)")];

    let displayed = [];

    let busy = new Set();

    items.forEach((item, i) => {
        const frontImg = item.querySelector(".front img");
        displayed[i] = frontImg ?.src || "";
    });

    function pickUniqueImage(excludeList) {
        const pool = allImages.filter(src => !excludeList.includes(src));
        if (!pool.length) return null;
        return pool[Math.floor(Math.random() * pool.length)];
    }

    function preload(src) {
        return new Promise(resolve => {
            const img = new Image();
            img.onload = resolve;
            img.onerror = resolve;
            img.src = src;
        });
    }

    async function flip(item, index) {
        if (busy.has(index)) return;

        const frontImg = item.querySelector(".front img");
        const backImg = item.querySelector(".back img");

        if (!frontImg || !backImg) return;

        busy.add(index);

        const isFlipped = item.classList.contains("flipped");
        const hiddenImg = isFlipped ? frontImg : backImg;

        const nextSrc = pickUniqueImage(displayed);
        if (!nextSrc) {
            busy.delete(index);
            return;
        }

        await preload(nextSrc);
        hiddenImg.src = nextSrc;

        item.classList.toggle("flipped");

        item.addEventListener("transitionend", () => {
            displayed[index] = nextSrc;
            busy.delete(index);
        }, { once: true });
    }

    setInterval(() => {
        const availableIndexes = items.map((_, i) => i).filter(i => !busy.has(i));
        if (!availableIndexes.length) return;

        const idx = availableIndexes[Math.floor(Math.random() * availableIndexes.length)];
        flip(items[idx], idx);
    }, 3000);

    items.forEach((item, idx) => {
        item.addEventListener("click", () => flip(item, idx));
    });
});