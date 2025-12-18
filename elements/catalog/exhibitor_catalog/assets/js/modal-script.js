document.addEventListener('click', function (e) {
    const trigger = e.target.closest(
        '.exhibitor-page__product, .exhibitor-catalog__exh-card-files-product, .exhibitor-single-mobile__product-item'
    );
    if (!trigger) return;

    const productEl = trigger;
    const container = productEl.closest('[data-exhibitor-id]');
    if (!container) return;

    const exhibitorId = container.dataset.exhibitorId;
    const exhibitorData = window[`EXHIBITOR_${exhibitorId}`];
    if (!exhibitorData || !exhibitorData.products) return;

    const products = exhibitorData.products;
    const allProducts = container.querySelectorAll(
        '.exhibitor-catalog__exh-card-files-product, .exhibitor-page__product'
    );

    let index = Array.from(allProducts).indexOf(productEl);
    if (index < 0) {
        // fallback aby zawsze był poprawny slajd
        index = 0;
    }

    // === Modal ===
    const modal = document.createElement('div');
    modal.className = 'exhibitor-product-modal';
    modal.innerHTML = `
    <div class="exhibitor-product-modal__overlay"></div>
    <div class="exhibitor-product-modal__container">
        <button class="exhibitor-product-modal__close" aria-label="Zamknij">×</button>
        <div class="exhibitor-product-modal__content">
            <div class="exhibitor-product-modal__swiper swiper">
                <div class="swiper-wrapper">
                ${products
                    .map(
                        (p) => `
                    <div class="exhibitor-product-modal__item swiper-slide">
                        <img class="exhibitor-product-modal__img" src="${p.img || ''}" alt="${p.name || ''}">
                        <h3 class="exhibitor-product-modal__title">${p.name || ''}</h3>
                        <p class="exhibitor-product-modal__desc">${p.description || ''}</p>
                    </div>`
                    )
                    .join('')}
                </div>
                <div class="exhibitor-product-modal__button swiper-button-prev"></div>
                <div class="exhibitor-product-modal__button swiper-button-next"></div>
                <div class="exhibitor-product-modal__pagination"></div>
            </div>
        </div>
    </div>
  `;

    document.body.appendChild(modal);
    document.body.classList.add('modal-open');

    setTimeout(() => modal.classList.add('animate'), 10);

    // === Swiper ===
    const swiper = new Swiper('.exhibitor-product-modal__swiper', {
        initialSlide: index,
        slidesPerView: 1,
        spaceBetween: 100,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.exhibitor-product-modal__pagination',
            type: 'fraction',
            formatFractionCurrent: (num) => String(num).padStart(2, '0'),
            formatFractionTotal: (num) => String(num).padStart(2, '0'),
            renderFraction: (currentClass, totalClass) => `
        <span class="${currentClass}"></span>
        <span class="sep"> / </span>
        <span class="${totalClass}"></span>
      `,
        },
        keyboard: { enabled: true },
    });

    // === Zamknięcie modala ===
    const closeModal = () => {
        modal.classList.remove('animate');
        setTimeout(() => modal.remove(), 250);
        document.body.classList.remove('modal-open');
    };

    // kliknięcie w overlay
    modal.querySelector('.exhibitor-product-modal__overlay').addEventListener('click', closeModal);

    // kliknięcie w X
    modal.querySelector('.exhibitor-product-modal__close').addEventListener('click', closeModal);

    // klik poza kontenerem (w tło modala)
    modal.addEventListener('click', function (event) {
        const isInsideContainer = event.target.closest('.exhibitor-product-modal__container');
        if (!isInsideContainer) {
            closeModal();
        }
    });
});
