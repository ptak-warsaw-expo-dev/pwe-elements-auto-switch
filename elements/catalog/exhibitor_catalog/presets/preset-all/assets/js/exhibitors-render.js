// exhibitors-render.js — przeniesiony render z zachowaniem oryginalnego HTML/CSS
// Zakłada, że exhibitors-core.js publikuje window.Exhibitors z: esc, lower, toTagArray, getId

(function() {
    const onReady = (fn) => {
        if (typeof window.__onExhibitorsReady === "function") return window.__onExhibitorsReady(fn);
        document.addEventListener("exhibitors:dataReady", () => fn(window.Exhibitors), { once: true });
    };

    function truncateWords(str, limit = 40) {
        const text = String(str || "")
            .replace(/<[^>]*>/g, " ")
            .replace(/\s+/g, " ")
            .trim();
        if (!text) return "";
        const words = text.split(" ");
        if (words.length <= limit) return text;
        return words.slice(0, limit).join(" ") + "...";
    }

    onReady(function(X) {
        if (!X) return;
        const { esc, lower, toTagArray, getId } = X;

        function renderCard(obj) {
            // ----- produkt -----
            if (obj?.type === "product") {
                const x = obj;
                return `
          <div class="exhibitor-catalog__item exhibitor-catalog__item--product"
               data-type="product"
               data-id="${esc(getId(x))}"
               data-hall="${esc(lower(x.hallName || ""))}"
               data-tags="${esc((x.tags || []).join(","))}">
            <div class="exhibitor-catalog__item-container">
              <div class="exhibitor-catalog__info">
                <div class="exhibitor-catalog__company-info">
                  <div class="exhibitor-catalog__logo-tile">
                    ${x.img ? `<img src="${esc(x.img)}" alt="${esc(x.name || "Produkt")}" loading="lazy" decoding="async" />` : ""}
                    <div class="exhibitor-catalog__stand">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                        <p>Stoisko ${esc(x.standNumber || "")}</p>
                    </div>
                  </div>
                  <a class="exhibitor-catalog__product-link" href="?exhibitor_id=${x.exhibitorId}" target="_blank">Poznaj wystawcę</a>
                </div>
                <div class="exhibitor-catalog__details">
                  <h3 class="exhibitor-catalog__name">${esc(x.name || "")}</h3>
                  ${x.exhibitorName ? `<div class="exhibitor-catalog__categories"><p class="exhibitor-catalog-product__exh-name">${esc(x.exhibitorName)}</p></div>` : ""}
                  ${x.brand ? `<div class="exhibitor-catalog__brands"><p class="exhibitor-catalog__label">Brand</p><p class="exhibitor-catalog__value">${esc(x.brand)}</p></div>` : ""}
                  ${(x.tags || []).length ? (() => {
                    const tags = Array.isArray(x.tags) ? x.tags.slice(0, 4) : [];
                    const more = Array.isArray(x.tags) && x.tags.length > 4 ? ', ...' : '';
                    return `<div class="exhibitor-catalog__categories">
                        <p class="exhibitor-catalog__label">Kategorie</p>
                        <p class="exhibitor-catalog__value">${esc(tags.join(", "))}${more}</p>
                    </div>`;
                })() : ""}
                  ${x.description ? `<p class="exhibitor-catalog__description">${esc(truncateWords(x.description, 40))}</p>` : ""}
                </div>
              </div>
            </div>
          </div>`;
            }

            // ----- wystawca -----
            const ex = obj;
            const productsTrim = Array.isArray(ex.productsTrim) ? ex.productsTrim : [];
            const documentsTrim = Array.isArray(ex.documentsTrim) ? ex.documentsTrim : [];
            const productsCount = Number(ex.productsCount || 0);
            const documentsCount = Number(ex.documentsCount || 0);

            const headingHTML = Number(ex.isFeatured) ?
                `<div class="exhibitor-catalog__item-heading">Wyróżnieni wystawcy</div>` :
                "";

            const productsHTML = productsCount > 0 ? `
        <div class="exhibitor-catalog__products">
          <h4 class="exhibitor-catalog__products-title">Produkty (${productsCount})</h4>
          <div class="exhibitor-catalog__products-list">
            ${productsTrim.map(p => `
              <div class="exhibitor-catalog__products-list-element">
                <img src="${esc(p.img || "")}" alt="${esc(p.name || "Product")}" loading="lazy" decoding="async" />
              </div>`).join("")}
          </div>
        </div>` : "";

            const documentsHTML = documentsCount > 0 ? `
        <div class="exhibitor-catalog__materials">
          <h4 class="exhibitor-catalog__materials-title">MATERIAŁY DO POBRANIA (${documentsCount})</h4>
          <div class="exhibitor-catalog__materials-list exhibitor-catalog__documents-list">
            ${documentsTrim.map(d => {
              const u = d?.viewUrl || "";
              const t = d?.title || "";
              return `
                <div class="exhibitor-catalog__material exhibitor-catalog__documents-list-element" data-url="${esc(u)}" data-title="${esc(t)}">
                  <p>Dokument</p>
                  <div class="exhibitor-catalog__material-img">
                    <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="${esc(t || "Dokument")}" />
                  </div>
                </div>`;
            }).join("")}
          </div>
        </div>` : "";

            return `
        <div class="exhibitor-catalog__item"
             data-id="${esc(getId(ex))}"
             data-hall="${esc(lower(ex.hallName))}"
             data-tags="${esc(toTagArray(ex.catalogTags).join(','))}">
          ${headingHTML}
          <div class="exhibitor-catalog__item-container">
            <div class="exhibitor-catalog__info">
              <div class="exhibitor-catalog__company-info">
                <div class="exhibitor-catalog__logo-tile">
                  ${ex.logoUrl ? `<img src="${esc(ex.logoUrl)}" alt="${esc(ex.name)}" />` : ""}
                  <div class="exhibitor-catalog__stand">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><path d="M12 21C15.5 17.4 19 14.1764 19 10.2C19 6.22355 15.866 3 12 3C8.13401 3 5 6.22355 5 10.2C5 14.1764 8.5 17.4 12 21Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                    <p>Stoisko ${esc(ex.standNumber || "")}</p>
                  </div>
                </div>
                <div class="exhibitor-catalog__contact">
                  ${ex.website ? `<div class="exhibitor-catalog__contact-item">
                    <svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8 9h22.4M1.8 17h22.4M1 13a12 12 0 1 0 24 0 12 12 0 0 0-24 0" stroke="var(--main2-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.333 1a22.67 22.67 0 0 0 0 24m1.333-24a22.67 22.67 0 0 1 0 24" stroke="var(--main2-color)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    <a href="${esc(ex.website)}" target="_blank" rel="noopener">Strona www</a></div>` : ""}
                  ${ex.contactEmail ? `<div class="exhibitor-catalog__contact-item">
                    <svg width="28" height="22" viewBox="0 0 28 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.2 21.5a2.64 2.64 0 0 1-1.906-.77Q.5 19.96.5 18.875V3.125q0-1.083.794-1.853A2.64 2.64 0 0 1 3.2.5h21.6q1.113 0 1.907.772.795.771.793 1.853v15.75q0 1.083-.793 1.855a2.63 2.63 0 0 1-1.907.77zM14 12.313 24.8 5.75V3.125L14 9.688 3.2 3.125V5.75z" fill="var(--main2-color)"/></svg>
                    <a href="mailto:${esc(ex.contactEmail)}">Email</a></div>` : ""}
                  ${ex.contactInfo ? `<div class="exhibitor-catalog__contact-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M22.6 24q-4.167 0-8.233-1.816t-7.4-5.15-5.15-7.4T0 1.4q0-.6.4-1t1-.4h5.4q.467 0 .833.317.368.318.434.75l.866 4.666q.068.534-.033.9a1.4 1.4 0 0 1-.367.634L5.3 10.533a16 16 0 0 0 1.583 2.383q.916 1.149 2.017 2.217a24 24 0 0 0 2.167 1.918 21 21 0 0 0 2.4 1.616l3.133-3.134q.3-.3.784-.449t.95-.084l4.6.933q.465.134.766.484.3.351.3.783v5.4q0 .6-.4 1t-1 .4" fill="var(--main2-color)"/></svg>
                    <a href="tel:${esc(ex.contactInfo)}">Telefon</a></div>` : ""}
                </div>
              </div>
              <div class="exhibitor-catalog__details">
                <a class="exhibitor-catalog__open-modal-name" href="?exhibitor_id=${ex.idNumeric}" target="_blank"><h3 class="exhibitor-catalog__name">${esc(ex.name)}</h3></a>
                ${ex.description ? `<p class="exhibitor-catalog__description">${esc(truncateWords(ex.description, 40))}</p>` : ""}
                ${Array.isArray(ex.brands) && ex.brands.length ? `<div class="exhibitor-catalog__brands"><p class="exhibitor-catalog__label">Brands</p><p class="exhibitor-catalog__value">${esc(ex.brands.join(", "))}</p></div>` : ""}
                ${(() => {
                    const tags = toTagArray(ex.catalogTags);
                    if (!tags.length) return "";
                    const limited = tags.slice(0, 4);
                    const more = tags.length > 4 ? ", ..." : "";
                    return `<div class="exhibitor-catalog__categories">
                        <p class="exhibitor-catalog__label">Kategorie</p>
                        <p class="exhibitor-catalog__value">${esc(limited.join(", "))}${more}</p>
                    </div>`;
                })()}
              </div>
            </div>
          </div>
          <div class="exhibitor-catalog__extra">${productsHTML}${documentsHTML}</div>
          <a class="exhibitor-catalog__open-modal" href="?exhibitor_id=${ex.idNumeric}" target="_blank">Zobacz szczegóły</a>
        </div>`;
        }

        X.renderCard = renderCard;
    });
})();