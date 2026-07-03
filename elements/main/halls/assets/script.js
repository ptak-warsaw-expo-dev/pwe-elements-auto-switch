const halls = document.getElementById("pweHalls");

if (halls) {
    const allItems = JSON.parse(halls.dataset.allItems || "[]")
        .filter(item => item.id && item.domain);

    const currentDomain = window.location.hostname;
    const allActiveItemsObject = [];

    const setElementState = (el, item, variant) => {
        if (!el || !item) return;

        el.classList.add("active");

        if (item.domain === currentDomain) {
            el.classList.add("current-fair");
        }

        const colors = el.querySelectorAll(".pwe-halls__element-color");
        colors.forEach(c => c.style.fill = item.color);

        if (variant === "half") {
            const links = el.querySelectorAll(".pwe-halls__element-favicon-link.half");
            links.forEach(link => {
                const logo = link.querySelector(".pwe-halls__element-favicon");
                if (!logo) return;

                link.setAttribute("href", `https://${item.domain}`);
                logo.setAttribute("href", `https://${item.domain}/doc/favicon.webp`);
            });
        } else {
            const links = el.querySelectorAll(`.pwe-halls__element-logo-link.${variant}`);
            links.forEach(link => {
                const logo = link.querySelector(".pwe-halls__element-logo");
                if (!logo) return;

                link.setAttribute("href", `https://${item.domain}`);
                logo.setAttribute("href", `https://${item.domain}/doc/logo.webp`);
            });
        }

        allActiveItemsObject.push({ id: el.id });
    };

    const getHalfElement = (idA, idB) => {
        return document.getElementById(`${idA}_${idB}`) || document.getElementById(`${idB}_${idA}`);
    };

    const grouped = {};

    allItems.forEach(item => {
        const letter = item.id.charAt(0);
        const key = `${letter}_${item.domain}`;

        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(item);
    });

    const activateItems = () => {
        Object.values(grouped).forEach(items => {
            const letter = items[0].id.charAt(0);
            const fullItem = items.find(item => item.id === letter);
            const subItems = items
                .filter(item => item.id !== letter)
                .sort((a, b) => a.id.localeCompare(b.id, undefined, { numeric: true }));

            // Full hall: type "F" or all parts F1-F4.
            if (fullItem || subItems.length === 4) {
                const el = document.getElementById(letter);
                const sourceItem = fullItem || subItems[0];
                setElementState(el, sourceItem, "full");
                return;
            }

            const usedIds = new Set();

            // First, we try to join existing halves, e.g., F1+F2 or F3+F4.
            for (let i = 0; i < subItems.length; i++) {
                for (let j = i + 1; j < subItems.length; j++) {
                    const first = subItems[i];
                    const second = subItems[j];

                    if (usedIds.has(first.id) || usedIds.has(second.id)) continue;

                    const halfEl = getHalfElement(first.id, second.id);
                    if (!halfEl) continue;

                    setElementState(halfEl, first, "half");
                    usedIds.add(first.id);
                    usedIds.add(second.id);
                }
            }

            // Rest as individual quarters, e.g., F1,F3 or the third hall from F1,F2,F3.
            subItems.forEach(item => {
                if (usedIds.has(item.id)) return;

                const el = document.getElementById(item.id);
                setElementState(el, item, "quarter");
            });
        });
    };

    window.addEventListener("load", activateItems);
}