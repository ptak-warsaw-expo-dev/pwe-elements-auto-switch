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
document.addEventListener('DOMContentLoaded', function () {
    new Swiper('.exhibitor-page__brands.swiper', {
        slidesPerView: 3,
        spaceBetween: 36,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            0: { slidesPerView: 1, slidesPerGroup: 1 },
            640: { slidesPerView: 2, slidesPerGroup: 2 },
            1024: { slidesPerView: 3, slidesPerGroup: 3 },
        },
    });
});

document.addEventListener('DOMContentLoaded', function () {
    if (window.innerWidth >= 960) {
        const sticky = document.querySelector('.exhibitor-page__info-sticky');
        const parent = document.querySelector('.exhibitor-page__info-column');

        function updatePosition() {
            const parentRect = parent.getBoundingClientRect();
            const stickyHeight = sticky.offsetHeight;

            // Koniec kontenera (zostają np. 20px marginesu)
            const stopPoint = parentRect.bottom - stickyHeight - 20;

            if (stopPoint <= 0) {
                sticky.style.position = 'absolute';
                sticky.style.top = 'auto';
                sticky.style.bottom = '18px';
                sticky.style.width = '92%';
            } else {
                sticky.style.position = 'fixed';
                sticky.style.top = '0';
                sticky.style.width = '28%';
            }
        }

        window.addEventListener('scroll', updatePosition);
        window.addEventListener('resize', updatePosition);

        updatePosition();
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const btns = document.querySelectorAll('.exhibitor-page__tab-btn');
    const tabs = document.querySelectorAll('.exhibitor-page__tab-content');

    // Funkcja aktywująca tab
    function activate(tabName) {
        btns.forEach((b) => b.classList.remove('is-active'));
        tabs.forEach((t) => t.classList.remove('is-active'));

        const btn = document.querySelector(
            `.exhibitor-page__tab-btn[data-tab="${tabName}"]`
        );
        const tab = document.getElementById(`tab-${tabName}`);

        if (btn) btn.classList.add('is-active');
        if (tab) tab.classList.add('is-active');
    }

    // Obsługa kliknięć
    btns.forEach((btn) => {
        btn.addEventListener('click', () => {
            activate(btn.dataset.tab);
        });
    });

    // Jeśli nic nie jest aktywne → aktywuj pierwszy dostępny tab
    const firstBtn = btns[0];
    if (firstBtn) {
        activate(firstBtn.dataset.tab);
    }
});
