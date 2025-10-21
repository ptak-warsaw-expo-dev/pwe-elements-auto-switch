document.addEventListener("DOMContentLoaded", function() {
    const lang = navigator.language || navigator.userLanguage;

    if (lang === "en-US") {
        const uncodeNavMenu = document.querySelector("#masthead");
        const pweNavMenu = document.querySelector("#pweMenu");

        // Top main menu "For exhibitors"
        const mainMenu = pweNavMenu ? document.querySelector(".pwe-menu__nav") : document.querySelector("ul.menu-primary-inner");
        const secondChild = mainMenu.children[1];
        const dropMenu = pweNavMenu ? secondChild.querySelector(".pwe-menu__submenu") : secondChild.querySelector("ul.drop-menu");

        // Create new element li
        const newMenuItem = document.createElement("li");
        newMenuItem.id = pweNavMenu ? "" : "menu-item-99999";
        newMenuItem.className = pweNavMenu ? "pwe-menu__submenu-item" : "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
        newMenuItem.innerHTML = `<a title="Become an agent" target="_blank" href="https://warsawexpo.eu/en/forms-for-agents/">Become an agent</a>`;

        // Add new element
        dropMenu.appendChild(newMenuItem);

        // --------------------------------------------

        // Bottom main menu "For exhibitors"
        const footerMenu = document.querySelector(".pwe-footer__nav-right-column");
        const footerThirdChild = footerMenu.children[2];
        const footerMenuChild = footerThirdChild.querySelector(".pwe-footer__nav-column .menu");

        // Create new element li
        const newFooterMenuItem = document.createElement("li");
        newFooterMenuItem.id = "menu-item-99999";
        newFooterMenuItem.className = "menu-item menu-item-type-custom menu-item-object-custom menu-item-99999";
        newFooterMenuItem.innerHTML = `<a title="Become an agent" target="_blank" href="https://warsawexpo.eu/en/forms-for-agents/">Become an agent</a>`;

        // Add new element
        footerMenuChild.appendChild(newFooterMenuItem);
    }
});