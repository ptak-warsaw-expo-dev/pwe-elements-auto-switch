const menu_transparent = pwe_element_atts.menu_transparent;
const trade_fair_datetotimer = pwe_element_atts.trade_fair_datetotimer;
const trade_fair_enddata = pwe_element_atts.trade_fair_enddata;

document.addEventListener("DOMContentLoaded", function () {
    const adminBar = document.querySelector("#wpadminbar");
    const pweNavMenu = document.querySelector('#pweMenuAutoSwitch');
    const bodyHome = document.querySelector("body.home");
    const pweNavMenuHome = document.querySelector("body.home #pweMenuAutoSwitch");

    if(pweNavMenu){
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

            // normalize lang (pl-PL -> pl, en-US -> en itd.)
            const rawLang = document.documentElement.lang || 'en';
            const lang = rawLang.toLowerCase().split('-')[0];

            const labels = {
                pl: ['weź udział', 'zostań wystawcą'],
                en: ['join us', 'book a stand'],
                de: ['jetzt teilnehmen', 'stand buchen'],
                it: ['unisciti a noi', 'prenota uno stand'],
                cs: ['zúčastnit se', 'rezervovat stánek'],
                sk: ['zúčastniť sa', 'rezervovať stánok'],
                uk: ['приєднатися', 'забронювати стенд'],
                lt: ['dalyvauk', 'rezervuoti stendą'],
                lv: ['piedalīties', 'rezervēt stendu'],
                ro: ['alătura-te nouă', 'rezervă un stand'],
                et: ['liitu meiega', 'broneeri stend']
            };

            const cta = {
                pl: {
                    early: { text: 'WEŹ UDZIAŁ', href: '/rejestracja/' },
                    late: { text: 'ZOSTAŃ WYSTAWCĄ', href: '/zostan-wystawca/' }
                },
                en: {
                    early: { text: 'JOIN US', href: '/en/registration/' },
                    late: { text: 'BOOK A STAND', href: '/en/become-an-exhibitor/' }
                },
                de: {
                    early: { text: 'JETZT TEILNEHMEN', href: '/de/anmeldung/' },
                    late: { text: 'STAND BUCHEN', href: '/de/werden-sie-aussteller/' }
                },
                it: {
                    early: { text: 'UNISCITI A NOI', href: '/it/registrazione/' },
                    late: { text: 'PRENOTA UNO STAND', href: '/it/diventa-espositore/' }
                },
                cs: {
                    early: { text: 'ZÚČASTNIT SE', href: '/cs/registrace/' },
                    late: { text: 'REZERVOVAT STÁNEK', href: '/cs/stante-se-vystavovatelem/' }
                },
                sk: {
                    early: { text: 'ZÚČASTNIŤ SA', href: '/sk/registracia/' },
                    late: { text: 'REZERVOVAŤ STÁNOK', href: '/sk/stante-sa-vystavovatelom/' }
                },
                uk: {
                    early: { text: 'ПРИЄДНАТИСЯ', href: '/uk/reyestraciya/' },
                    late: { text: 'ЗАБРОНЮВАТИ СТЕНД', href: '/uk/staty-eksponentom/' }
                },
                lt: {
                    early: { text: 'DALYVAUK', href: '/lt/registracija/' },
                    late: { text: 'REZERVUOTI STENDĄ', href: '/lt/tapkite-eksponentu/' }
                },
                lv: {
                    early: { text: 'PIEDALĪTIES', href: '/lv/registracija/' },
                    late: { text: 'REZERVĒT STENDU', href: '/lv/klusti-par-izstades-dalibnieku/' }
                },
                ro: {
                    early: { text: 'ALĂTURA-TE NOUĂ', href: '/ro/inregistrare/' },
                    late: { text: 'REZERVĂ UN STAND', href: '/ro/deveniti-exhibitor/' }
                },
                et: {
                    early: { text: 'LIITU MEIEGA', href: '/et/registreerimine/' },
                    late: { text: 'BRONEERI STEND', href: '/et/saage-eksponent/' }
                }
            };

            registerButtons.forEach(registerButton => {

                const btnText = registerButton.innerText.trim().toLowerCase();

                // if button text doesn't include any of the expected labels for the current language, skip it
                if (!labels[lang]?.some(label => btnText.includes(label))) return;

                let endDate = new Date(trade_fair_enddata);
                let todayDate = new Date();

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

                if (b2cDomains.includes(currentDomain)) return;

                const phase = endDate < threeMonths ? 'early' : 'late';

                // fallback
                const langConfig = cta[lang] || cta['en'];

                const newText = langConfig[phase].text;
                const newHref = langConfig[phase].href;

                registerButton.innerText = newText;
                registerButton.href = newHref;

                if (mobileRegisterButton) {
                    mobileRegisterButton.innerText = newText;
                    mobileRegisterButton.href = newHref;
                }
            });
        }

        window.addEventListener('load', () => {
            const interval = setInterval(() => {
                const aside = document.querySelector('aside#usercentrics-cmp-ui');
                if (!aside) return;

                const shadow = aside.shadowRoot;
                if (!shadow) return;

                // A function that sets the button position and reveals the aside
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

                // Getting started
                adjustButton();

                // // Observe shadowRoot to react to button re-rendering
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
    }
});