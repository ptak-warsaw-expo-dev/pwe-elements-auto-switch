const halls = document.getElementById("pweHalls");

if (halls) {
    const allItems = JSON.parse(halls.dataset.allItems || "[]")
        .filter(item => item.id && item.domain);

    const currentDomain = window.location.hostname.replace(/^www\./, "");
    const svgNamespace = "http://www.w3.org/2000/svg";

    const getHalfElement = (idA, idB) =>
        document.getElementById(`${idA}_${idB}`) || document.getElementById(`${idB}_${idA}`);

    const isCurrentFair = domain => domain.replace(/^www\./, "") === currentDomain;

    const markActive = (el, items) => {
        if (!el || !items.length) return;

        el.classList.add("active");
        if (items.some(item => isCurrentFair(item.domain))) {
            el.classList.add("current-fair");
        }

        // Shared quarters retain the current fair's color when possible.
        const selected = items.find(item => isCurrentFair(item.domain)) || items[0];
        // The color paths are nested inside quarter groups, including for half and full halls.
        el.querySelectorAll(".pwe-halls__element-color").forEach(color => {
            color.style.fill = selected.color;
        });
    };

    const showSingleLogo = (el, item, variant) => {
        if (!el) return;
        markActive(el, [item]);

        const type = variant === "half" ? "favicon" : "logo";
        el.querySelectorAll(`:scope > .pwe-halls__element-${type}-link.${variant}`).forEach(link => {
            const img = link.querySelector(`.pwe-halls__element-${type}`);
            if (!img) return;
            link.setAttribute("href", `https://${item.domain}`);
            img.setAttribute("href", `https://${item.domain}/doc/${type}.webp`);
        });
    };

    // Multiple domains on one quarter: replace its single logo with a grid of
    // individual favicon links. Coordinates come from the existing SVG logo slot.
    const showSharedQuarter = (el, items) => {
        if (!el) return;
        markActive(el, items);

        const originalLink = el.querySelector(":scope > .pwe-halls__element-logo-link.quarter");
        const originalImage = originalLink?.querySelector("image");
        if (!originalImage) return;

        originalLink.style.display = "none";
        const x = parseFloat(originalImage.getAttribute("x")) || 0;
        const y = parseFloat(originalImage.getAttribute("y")) || 0;
        const width = parseFloat(originalImage.getAttribute("width")) || 175;
        const height = parseFloat(originalImage.getAttribute("height")) || 100;

        // Two icons side by side; 3-4 icons use two rows. More use a compact grid.
        const columns = items.length === 2 ? 2 : Math.ceil(Math.sqrt(items.length));
        const rows = Math.ceil(items.length / columns);
        const cellWidth = width / columns;
        const cellHeight = height / rows;
        const iconSize = Math.min(cellWidth, cellHeight) * 0.88;

        items.forEach((item, index) => {
            const column = index % columns;
            const row = Math.floor(index / columns);
            const link = document.createElementNS(svgNamespace, "a");
            link.classList.add("pwe-halls__link", "pwe-halls__element-logo-link", "quarter", "pwe-halls__shared-favicon-link");
            link.setAttribute("href", `https://${item.domain}`);
            link.setAttribute("target", "_blank");
            link.setAttribute("rel", "noopener noreferrer");
            link.setAttribute("aria-label", item.domain);
            link.style.display = "block";
            if (isCurrentFair(item.domain)) link.setAttribute("aria-current", "page");

            const image = document.createElementNS(svgNamespace, "image");
            // Reuse the logo's original perspective/rotation for this hall.
            image.classList.add("pwe-halls__element-logo");
            image.setAttribute("href", `https://${item.domain}/doc/favicon.webp`);
            image.setAttribute("x", String(x + column * cellWidth + (cellWidth - iconSize) / 2));
            image.setAttribute("y", String(y + row * cellHeight + (cellHeight - iconSize) / 2));
            image.setAttribute("width", String(iconSize));
            image.setAttribute("height", String(iconSize));
            image.setAttribute("preserveAspectRatio", "xMidYMid meet");
            link.appendChild(image);
            el.appendChild(link);
        });
    };

    const activateItems = () => {
        // Map actual quarter occupancy, rather than grouping by domain. A full
        // hall assignment covers all four quarters, without hiding other fairs.
        const quartersByHall = new Map();
        document.querySelectorAll("#pweHallsSvg .pwe-halls__element.quarter[id]").forEach(el => {
            const letter = el.id.charAt(0);
            if (!quartersByHall.has(letter)) quartersByHall.set(letter, new Map());
            quartersByHall.get(letter).set(el.id, new Map());
        });

        allItems.forEach(item => {
            const id = String(item.id).trim().toUpperCase();
            const letter = id.charAt(0);
            const quarters = quartersByHall.get(letter);
            if (!quarters) return;
            const targets = id === letter ? [...quarters.keys()] : (quarters.has(id) ? [id] : []);
            targets.forEach(quarterId => {
                const owners = quarters.get(quarterId);
                // The same domain should never produce duplicate favicons.
                owners.set(item.domain.replace(/^www\./, ""), item);
            });
        });

        quartersByHall.forEach((quarters, letter) => {
            const entries = [...quarters.entries()];
            const singleOwner = owners => owners.size === 1 ? [...owners.values()][0] : null;
            const allOwners = entries.map(([, owners]) => singleOwner(owners));

            // Preserve the original full-hall logo when all four parts belong
            // exclusively to the same domain.
            if (entries.length === 4 && allOwners.every(Boolean) &&
                allOwners.every(item => item.domain === allOwners[0].domain)) {
                showSingleLogo(document.getElementById(letter), allOwners[0], "full");
                return;
            }

            const used = new Set();
            entries.forEach(([idA, ownersA]) => {
                if (used.has(idA)) return;
                const first = singleOwner(ownersA);
                if (!first) return;

                for (const [idB, ownersB] of entries) {
                    if (idA === idB || used.has(idB)) continue;
                    const second = singleOwner(ownersB);
                    if (!second || second.domain !== first.domain) continue;
                    const half = getHalfElement(idA, idB);
                    if (!half) continue;

                    showSingleLogo(half, first, "half");
                    used.add(idA);
                    used.add(idB);
                    break;
                }
            });

            entries.forEach(([id, owners]) => {
                if (used.has(id) || owners.size === 0) return;
                const el = document.getElementById(id);
                const items = [...owners.values()];
                if (items.length === 1) showSingleLogo(el, items[0], "quarter");
                else showSharedQuarter(el, items);
            });
        });
    };

    if (document.readyState === "complete") activateItems();
    else window.addEventListener("load", activateItems, { once: true });
}
