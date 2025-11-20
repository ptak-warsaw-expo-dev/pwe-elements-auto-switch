document.addEventListener('click', function (e) {
  const trigger = e.target.closest('.exhibitor-catalog__pdf-open');
  if (!trigger) return;

  const docEl = trigger.closest('.exhibitor-catalog__exh-card-files-document');
  if (!docEl) return;

  const container = docEl.closest('[data-exhibitor-id]');
  if (!container) return;

  const exhibitorId = container.dataset.exhibitorId;
  const exhibitorData = window[`EXHIBITOR_${exhibitorId}`];
  if (!exhibitorData || !exhibitorData.documents) return;

  const documents = exhibitorData.documents;
  const allDocs = container.querySelectorAll('.exhibitor-catalog__exh-card-files-document');
  let index = Array.from(allDocs).indexOf(docEl);
  if (index < 0) index = 0;

  // === Budowa modala ===
  const modal = document.createElement('div');
  modal.className = 'exhibitor-pdf-modal';
  modal.innerHTML = `
    <div class="exhibitor-pdf-modal__overlay"></div>
    <div class="exhibitor-pdf-modal__container">
        <button class="exhibitor-pdf-modal__close" aria-label="Zamknij">×</button>
        <div class="exhibitor-pdf-modal__content">
            <div class="exhibitor-pdf-modal__swiper swiper">
                <div class="swiper-wrapper">
                ${documents.map(d => `
                    <div class="exhibitor-pdf-modal__item swiper-slide">
                        <iframe class="exhibitor-pdf-modal__iframe" src="${d.url}" frameborder="0"></iframe>
                        <h3 class="exhibitor-pdf-modal__title">${d.name || 'Dokument PDF'}</h3>
                    </div>`).join('')}
                </div>
                <div class="exhibitor-pdf-modal__button swiper-button-prev"></div>
                <div class="exhibitor-pdf-modal__button swiper-button-next"></div>
                <div class="exhibitor-pdf-modal__pagination"></div>
            </div>
        </div>
    </div>
  `;
  document.body.appendChild(modal);
  document.body.classList.add('modal-open');

  // Opóźnienie animacji
  setTimeout(() => modal.classList.add('animate'), 10);

  // === Swiper inicjalizacja ===
  const swiper = new Swiper('.exhibitor-pdf-modal__swiper', {
    initialSlide: index,
    slidesPerView: 1,
    spaceBetween: 50,
    loop: false,
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    pagination: {
      el: '.exhibitor-pdf-modal__pagination',
      type: 'fraction',
      formatFractionCurrent: num => String(num).padStart(2, '0'),
      formatFractionTotal: num => String(num).padStart(2, '0'),
      renderFraction: (currentClass, totalClass) => `
        <span class="${currentClass}"></span>
        <span class="sep"> / </span>
        <span class="${totalClass}"></span>
      `,
    },
    keyboard: { enabled: true },
  });

  // === Zamknięcie ===
  const closeModal = () => {
    modal.classList.remove('animate');
    setTimeout(() => modal.remove(), 250);
    document.body.classList.remove('modal-open');
  };

  modal.querySelector('.exhibitor-pdf-modal__overlay').addEventListener('click', closeModal);
  modal.querySelector('.exhibitor-pdf-modal__close').addEventListener('click', closeModal);
});
