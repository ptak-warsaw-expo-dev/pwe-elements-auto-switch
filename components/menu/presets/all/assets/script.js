const menu_transparent = pwe_element_atts.menu_transparent;
const trade_fair_datetotimer = pwe_element_atts.trade_fair_datetotimer;
const trade_fair_enddata = pwe_element_atts.trade_fair_enddata;

document.addEventListener("DOMContentLoaded", function () {
    const adminBar = document.querySelector("#wpadminbar");
    const pweNavMenu = document.querySelector('#pweMenuAutoSwitch');
    const bodyHome = document.querySelector("body.home");
    const pweNavMenuHome = document.querySelector("body.home #pweMenuAutoSwitch");
    const burgerButton = pweNavMenu.querySelector('.pwe-menu-auto-switch__burger');
    const menuContainer = pweNavMenu.querySelector('.pwe-menu-auto-switch__container');

    const mainContainer = document.querySelector('.main-container');

    const uncodePageHeader = document.querySelector("#page-header");
    const pweCustomHeader = document.querySelector("#pweHeader");

    if (menuContainer) {
        menuContainer.style.transition = '.3s'
    }

    if (pweNavMenu && mainContainer) {
        if (!(uncodePageHeader && pweCustomHeader)) {
            mainContainer.style.marginTop = pweNavMenu.offsetHeight + 'px';
        } else if (uncodePageHeader && pweCustomHeader && !bodyHome) {
            mainContainer.style.marginTop = pweNavMenu.offsetHeight + 'px';
        }
    }

    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

    // Uncode sticky element
    const uncodeStickyElement = document.querySelector('.row-container.sticky-element');
    if (uncodeStickyElement && !isMobile) {
        let stickyHeight = uncodeStickyElement.offsetHeight;
        let stickyPos;

        if (adminBar) {
            stickyPos = uncodeStickyElement.getBoundingClientRect().top + window.scrollY - (adminBar.offsetHeight * 2);
        } else {
            stickyPos = uncodeStickyElement.getBoundingClientRect().top + (window.scrollY - pweNavMenu.offsetHeight);
        }

        // Create a negative margin to prevent content "jumps":
        const jumpPreventDiv = document.createElement("div");
        jumpPreventDiv.className = "jumps-prevent";
        uncodeStickyElement.parentNode.insertBefore(jumpPreventDiv, uncodeStickyElement.nextSibling);
        uncodeStickyElement.style.zIndex = "99";

        function jumpsPrevent() {
            stickyHeight = uncodeStickyElement.offsetHeight;
            uncodeStickyElement.style.marginBottom = "-" + stickyHeight + "px";
            uncodeStickyElement.nextElementSibling.style.paddingTop = stickyHeight + "px";
        }

        jumpsPrevent();

        window.addEventListener("resize", function () {
            jumpsPrevent();
        });

        function stickerFn() {
            const winTop = window.scrollY;

            if (winTop >= stickyPos) {
                if (pweNavMenu) {
                    uncodeStickyElement.style.position = 'fixed';

                    if (adminBar) {
                        uncodeStickyElement.style.top = (pweNavMenu.offsetHeight + adminBar.offsetHeight) + 'px';
                    } else {
                        uncodeStickyElement.style.top = pweNavMenu.offsetHeight + 'px';
                    }
                }
            } else {
                uncodeStickyElement.style.position = 'relative';
                uncodeStickyElement.style.top = '0';
            } 
        }

        window.addEventListener("scroll", function () {
            stickerFn();
        });
    }

    // Background color for nav menu
    if (menu_transparent === "true") {
        if (pweNavMenuHome && window.innerWidth >= 1200) {
            if (window.scrollY > pweNavMenu.offsetHeight) {
                pweNavMenuHome.style.background = "var(--accent-color)";
            }
            window.addEventListener("scroll", function () {
                if (window.scrollY > pweNavMenu.offsetHeight) {
                    pweNavMenuHome.style.background = "var(--accent-color)";
                    pweNavMenuHome.classList.add('color');

                } else {
                    pweNavMenuHome.style.background = "transparent";
                    pweNavMenuHome.classList.remove('color');
                }
            });
        } else {
            pweNavMenu.classList.add('color');
        }
    } 
    
    if (burgerButton && pweNavMenu) {
        const lockScroll = () => {
            document.documentElement.style.overflow = 'hidden';
        };

        const unlockScroll = () => {
            document.documentElement.style.overflow = '';
        };

        // Listening for click on burger menu
        burgerButton.addEventListener("click", function () {
            const isOpen = pweNavMenu.classList.toggle("burger-menu");

            if (isOpen) {
                lockScroll();
            } else {
                unlockScroll();
            }

            // If the menu is open, close all submenus
            const openSubmenus = document.querySelectorAll('.pwe-menu-auto-switch__submenu.visible');
            openSubmenus.forEach(submenu => {
                closeSubmenu(submenu);
            });
        });

        // Click outside the menu - close burger menu
        document.addEventListener("click", function (e) {
            if (
                pweNavMenu.classList.contains("burger-menu") &&
                !menuContainer.contains(e.target) &&
                !burgerButton.contains(e.target)
            ) {
                pweNavMenu.classList.remove("burger-menu");
                unlockScroll();

                // Close all open submenus
                const openSubmenus = document.querySelectorAll('.pwe-menu-auto-switch__submenu.visible');
                openSubmenus.forEach(submenu => {
                    closeSubmenu(submenu);
                });
            }
        });
    }

    // Function to close submenu
    const closeSubmenu = (submenu) => {
        if (submenu) {
            submenu.style.height = `${submenu.scrollHeight}px`;
            requestAnimationFrame(() => {
                submenu.style.height = "0";
            });
            submenu.classList.remove("visible");
        }
    };

    // Function to open submenu
    const openSubmenu = (submenu) => {
        if (submenu) {
            submenu.style.height = "0";
            submenu.classList.add("visible");
            requestAnimationFrame(() => {
                submenu.style.height = `${submenu.scrollHeight}px`;
            });
        }
    };

    // Function to switch submenus
    const toggleSubmenu = (link) => {
        const submenu = link.parentElement.querySelector(".pwe-menu-auto-switch__submenu");

        if (submenu) {
            const isVisible = submenu.classList.contains("visible");

            // Close all other submenus on the same level
            const siblings = Array.from(link.parentElement.parentElement.children)
                .filter(item => item !== link.parentElement);

            siblings.forEach(sibling => {
                const siblingSubmenu = sibling.querySelector(".pwe-menu-auto-switch__submenu");
                if (siblingSubmenu && siblingSubmenu.classList.contains("visible")) {
                    closeSubmenu(siblingSubmenu);
                }
            });

            // Open or close the current submenu
            if (isVisible) {
                closeSubmenu(submenu);
            } else {
                openSubmenu(submenu);
            }

            // Remove height after animation is finished
            submenu.addEventListener(
                "transitionend",
                function () {
                    if (submenu.classList.contains("visible")) {
                        submenu.style.height = "auto";
                    }
                },
                { once: true }
            );
        }
    };

    // Handling clicks on submenu links
    const menuLinks = document.querySelectorAll(".pwe-menu-auto-switch__item.has-children > a, .pwe-menu-auto-switch__submenu-item.has-children > a");
    if (menuLinks.length && window.innerWidth < 1199) {
        menuLinks.forEach(link => {
            let clickedOnce = false;

            link.addEventListener("click", function (e) {
                const href = this.getAttribute("href");

                // Links without `href` or with `#` always open/close submenu
                if (!href || href === "#") {
                    e.preventDefault();
                    toggleSubmenu(this);
                    return;
                }

                const submenu = this.parentElement.querySelector(".pwe-menu-auto-switch__submenu");
                if (submenu && !submenu.classList.contains("visible")) {
                    // Block link
                    e.preventDefault();
                    // Open submenu
                    toggleSubmenu(this); 
                    clickedOnce = true;
                } else if (clickedOnce) {
                    // Second click: allow the transition if the link is valid
                    clickedOnce = false;
                } else {
                    // Block link
                    e.preventDefault();
                    // Close submenu if open
                    toggleSubmenu(this); 
                }
            });
        });
    }

    const registerButtons = document.querySelectorAll('.pwe-menu-auto-switch__item.button a');
    const mobileRegisterButton = document.querySelector('.pwe-menu-auto-switch__register-btn a');
    const mobileRegisterButtonContainer = document.querySelector('.pwe-menu-auto-switch__register-btn');

    if (registerButtons.length > 0 && mobileRegisterButton) {
        // Get the page language
        const htmlLang = document.documentElement.lang;

        const labels = {
            'pl-PL': ['weź udział', 'zostań wystawcą'],
            'en-US': ['join us', 'book a stand'],
            'de-DE': ['jetzt teilnehmen', 'stand buchen']
        };

        registerButtons.forEach(registerButton => {
            if (labels[htmlLang]?.includes(registerButton.innerText.toLowerCase())) {
                // Create a Date object based on the trade fair end date
                let endDate = new Date(trade_fair_enddata);

                // Get today's date
                let todayDate = new Date();

                // Add 90 days to today's date
                let threeMonths = new Date(todayDate);
                threeMonths.setDate(todayDate.getDate() + 90);

                const currentDomain = window.location.hostname;
                const b2cDomains = [
                    'animalsdays.eu',
                    'fiwe.pl',
                    'warsawmotorshow.com',
                    'oldtimerwarsaw.com',
                    'motorcycleshow.pl'
                ];

                // Check if the current domain is NOT in the B2C domains list
                if (!b2cDomains.includes(currentDomain)) {
                    let newText, newHref;

                    if (endDate < threeMonths) {
                        newText =
                            htmlLang === 'pl-PL' ? 'WEŹ UDZIAŁ'
                            : htmlLang === 'de-DE' ? 'JETZT TEILNEHMEN'
                            : 'JOIN US';

                        newHref =
                            htmlLang === 'pl-PL' ? '/rejestracja/'
                            : htmlLang === 'de-DE' ? '/de/registrieren/'
                            : '/en/registration/';
                    } else {
                        newText =
                            htmlLang === 'pl-PL' ? 'ZOSTAŃ WYSTAWCĄ'
                            : htmlLang === 'de-DE' ? 'STAND BUCHEN'
                            : 'BOOK A STAND';

                        newHref =
                            htmlLang === 'pl-PL' ? '/zostan-wystawca/'
                            : htmlLang === 'de-DE' ? '/de/einen-stand-buchen/'
                            : '/en/become-an-exhibitor/';
                    }
                    
                    // Check if the trade fair end date is less than 90 days away
                    const cta = {
                        'pl-PL': {
                            early: { text: 'WEŹ UDZIAŁ', href: '/rejestracja/' },
                            late: { text: 'ZOSTAŃ WYSTAWCĄ', href: '/zostan-wystawca/' }
                        },
                        'en-US': {
                            early: { text: 'JOIN US', href: '/en/registration/' },
                            late: { text: 'BOOK A STAND', href: '/en/become-an-exhibitor/' }
                        },
                        'de-DE': {
                            early: { text: 'JETZT TEILNEHMEN', href: '/de/registrieren/' },
                            late: { text: 'STAND BUCHEN', href: '/de/einen-stand-buchen/' }
                        }
                    };

                    const phase = endDate < threeMonths ? 'early' : 'late';
                    const langConfig = cta[htmlLang] || cta['en-GB'];

                    newText = langConfig[phase].text;
                    newHref = langConfig[phase].href;


                    // Update text and link for both desktop and mobile buttons
                    registerButton.innerText = newText;
                    registerButton.href = newHref;
                    mobileRegisterButton.innerText = newText;
                    mobileRegisterButton.href = newHref;
                }
            }
        });

        // window.addEventListener("resize", function () {
        //     if (window.innerWidth < 960) {
        //         mobileRegisterButtonContainer.classList.add("visible");
        //     } else {
        //         mobileRegisterButtonContainer.classList.remove("visible");
        //     }
        // });
        
        // // Run once on page load to set initial state
        // if (window.innerWidth < 960) {
        //     mobileRegisterButtonContainer.classList.add("visible");
        // } else {
        //     mobileRegisterButtonContainer.classList.remove("visible");
        // }
        
    }

    window.addEventListener('load', () => {
        const interval = setInterval(() => {
            const aside = document.querySelector('aside#usercentrics-cmp-ui');
            if (!aside) return;

            const shadow = aside.shadowRoot;
            if (!shadow) return;

            // Funkcja, która ustawia pozycję przycisku i odkrywa aside
            const adjustButton = () => {
                const btn = shadow.querySelector('#uc-main-dialog.privacyButton');
                if (!btn) return;

                btn.style.left = 'unset';
                btn.style.right = '17px';
                btn.style.bottom = '10px';
                btn.style.width = '44px';
                btn.style.height = '44px';

                aside.style.display = 'block';
                aside.style.opacity = '1';
                aside.style.visibility = 'visible';
            };

            // Uruchamiamy na start
            adjustButton();

            // Obserwujemy shadowRoot, żeby reagować na ponowne renderowanie przycisku
            const observer = new MutationObserver(() => {
                adjustButton();
            });

            observer.observe(shadow, { childList: true, subtree: true });

            clearInterval(interval);
        }, 500);
    });

    // Block links on mobile if menu item have children
    const menuItemLinks = document.querySelectorAll('.pwe-menu-auto-switch__item.has-children > a');
    const mq = window.matchMedia('(max-width: 1199px)');

    menuItemLinks.forEach(link => {
        const originalHref = link.getAttribute('href');

        const updateLinkState = () => {
            if (mq.matches) {
            link.removeAttribute('href');
            } else {
            link.setAttribute('href', originalHref);
            }
        };

        updateLinkState();
        mq.addEventListener('change', updateLinkState);
    });

});
 