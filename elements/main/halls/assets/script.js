const halls = document.getElementById("pweHalls");

if (halls) {

    const allItems = JSON.parse(halls.dataset.allItems);
    const currentDomain = window.location.hostname;

    const allActiveItemsObject = [];

    // =========================
    // GROUP (letter + domain)
    // =========================
    const grouped = {};

    allItems.forEach(item => {
        const letter = item.id[0];
        const key = `${letter}_${item.domain}`;

        if (!grouped[key]) grouped[key] = [];
        grouped[key].push(item);
    });

    // =========================
    // FULL
    // =========================
    const activateFull = () => {
        const letters = [...new Set(allItems.map(item => item.id[0]))];

        letters.forEach(letter => {
            const itemsForLetter = allItems.filter(item => item.id[0] === letter);

            // Extracting all unique domains
            const domains = [...new Set(itemsForLetter.map(i => i.domain))];
            if (domains.length !== 1) return;

            // If exist full element (e.g. F)
            const fullLetterItem = itemsForLetter.find(i => i.id.length === 1);
            if (fullLetterItem) {
                const el = document.getElementById(letter);
                if (!el) return;

                el.classList.add("active");

                if (itemsForLetter[0].domain === currentDomain) {
                    el.classList.add("current-fair");
                }

                const colors = el.querySelectorAll(".pwe-halls__element-color");
                colors.forEach(c => c.style.fill = fullLetterItem.color);

                const logoLinksFull = el.querySelectorAll(".pwe-halls__element-logo-link.full");
                logoLinksFull.forEach(link => {
                    const logo = link.querySelector(".pwe-halls__element-logo");
                    if (!logo) return;

                    link.setAttribute("href", `https://${fullLetterItem.domain}`);
                    logo.setAttribute("href", `https://${fullLetterItem.domain}/doc/logo.webp`);
                });

                allActiveItemsObject.push({ id: letter });
                return;
            }

            // If all sub-elements (F1..F4) exist and are of the same domain, activate full element
            const subItems = itemsForLetter.filter(i => i.id.length > 1);
            if (subItems.length === 4) {
                const el = document.getElementById(letter);
                if (!el) return;

                el.classList.add("active");

                if (itemsForLetter[0].domain === currentDomain) {
                    el.classList.add("current-fair");
                }

                const colors = el.querySelectorAll(".pwe-halls__element-color");
                colors.forEach(c => c.style.fill = subItems[0].color);

                const logoLinksFull = el.querySelectorAll(".pwe-halls__element-logo-link.full");
                logoLinksFull.forEach(link => {
                    const logo = link.querySelector(".pwe-halls__element-logo");
                    if (!logo) return;

                    link.setAttribute("href", `https://${subItems[0].domain}`);
                    logo.setAttribute("href", `https://${subItems[0].domain}/doc/logo.webp`);
                });

                allActiveItemsObject.push({ id: letter });
            }
        });
    };

    // =========================
    // HALF
    // =========================
    const activateHalf = () => {
        Object.entries(grouped).forEach(([key, items]) => {
            if (items.length !== 2) return;

            const id = `${items[0].id}_${items[1].id}`;
            const reverseId = `${items[1].id}_${items[0].id}`;

            let el = document.getElementById(id);
            if (!el) el = document.getElementById(reverseId);
            if (!el) return;

            el.classList.add("active");

            if (items[0].domain === currentDomain) {
                el.classList.add("current-fair");
            }

            const colors = el.querySelectorAll(".pwe-halls__element-color");
            colors.forEach(c => c.style.fill = items[0].color);

            const logoLinksHalf = el.querySelectorAll(".pwe-halls__element-favicon-link.half");
            logoLinksHalf.forEach(link => {
                const logo = link.querySelector(".pwe-halls__element-favicon");
                if (!logo) return;

                link.setAttribute("href", `https://${items[0].domain}`);
                logo.setAttribute("href", `https://${items[0].domain}/doc/favicon.webp`);
            });

            allActiveItemsObject.push({ id: el.id });
        });
    };

    // =========================
    // QUARTER
    // =========================
    const activateQuarter = () => {
        Object.entries(grouped).forEach(([key, items]) => {
            if (items.length !== 1) return;

            const item = items[0];
            const el = document.getElementById(item.id);
            if (!el) return;

            el.classList.add("active");

            const colors = el.querySelectorAll(".pwe-halls__element-color");
            colors.forEach(c => c.style.fill = item.color);

            const logoLinks = el.querySelectorAll(".pwe-halls__element-logo-link.quarter");
            logoLinks.forEach(link => {
                const logo = link.querySelector(".pwe-halls__element-logo");
                if (!logo) return;

                link.setAttribute("href", `https://${item.domain}`);
                logo.setAttribute("href", `https://${item.domain}/doc/logo.webp`);
            });

            allActiveItemsObject.push({ id: item.id });
        });
    };

    // =========================
    // INIT
    // =========================
    window.addEventListener("load", () => {
        activateFull();
        activateHalf();
        activateQuarter();
    });

}