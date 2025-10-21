// document.addEventListener("DOMContentLoaded", () => {
//   /* ===== MODAL (delegacja) ===== */
//   const modal = document.getElementById("exhibitorModal");

//   // Dodaj przycisk zamykania, jeśli go nie ma
//   if (!modal.querySelector(".exhibitor-modal__close")) {
//     const closeBtn = document.createElement("button");
//     closeBtn.type = "button";
//     closeBtn.textContent = "✕";
//     closeBtn.className = "exhibitor-modal__close";
//     modal.prepend(closeBtn);
//   }

//   function openModal() {
//     modal.classList.add("is-active");
//     modal.scrollTop = 0;
//     document.body.classList.add("no-scroll");
//   }
//   function closeModal() {
//     modal.classList.remove("is-active");
//     document.body.classList.remove("no-scroll");
//   }

//   // Otwieraj TYLKO po kliknięciu w przycisk w karcie (działa też dla elementów doładowanych)
//   document.addEventListener("click", (e) => {
//     if (e.target.closest(".exhibitor-catalog__open-modal")) {
//       e.preventDefault();
//       openModal();
//       return;
//     }
//     if (e.target.closest(".exhibitor-modal__close")) {
//       e.preventDefault();
//       closeModal();
//       return;
//     }
//     // klik w tło (overlay)
//     if (e.target === modal) {
//       closeModal();
//     }
//   });

//   // ESC
//   document.addEventListener("keydown", (e) => {
//     if (e.key === "Escape" && modal.classList.contains("is-active")) {
//       closeModal();
//     }
//   });

//   /* ===== FILTRY, SORTOWANIE, PAGINACJA, WYSZUKIWANIE ===== */
//   (function(){
//     const DATA_ALL = Array.isArray(window.__EXHIBITORS__) ? window.__EXHIBITORS__ : [];
//     const PER_PAGE = window.__PER_PAGE__ || 20;

//     // DOM
//     const root        = document.getElementById("exhibitorCatalog");
//     if (!root) return;
//     const listEl      = root.querySelector(".exhibitor-catalog__list");
//     const counterEl   = root.querySelector(".exhibitor-catalog__counter");
//     const filtersRoot = root.querySelector(".exhibitor-catalog__filters");
//     const searchInput = root.querySelector(".exhibitor-catalog__search input, .exhibitor-catalog__search-input");
//     const sentinel    = document.getElementById("infiniteSentinel");
//     const loader      = document.getElementById("infiniteLoader");
//     if (!listEl || !filtersRoot || !sentinel || !loader) return;

//     // utils
//     const lower = (s) => (s||"").toString().toLowerCase().trim();
//     const uniq  = (arr) => Array.from(new Set(arr));
//     const esc   = (s) => (s||"").toString().replace(/[&<>"]/g, c => ({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;" }[c]));
//     const norm  = (s) => lower(s).normalize("NFKD").replace(/[\u0300-\u036f]/g,"");
//     const splitTags = (csv) => csv ? csv.split(",").map(t=>lower(t)).filter(Boolean) : [];
//     const getId   = (x) => Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0); // string -> number
//     const getArea = (x) => Number(x?.areaSum ?? x?.boothArea ?? 0);
//     const num    = (v) => Number(v || 0);
//     const len    = (a) => Array.isArray(a) ? a.length : 0;
//     const filled = (v) => !!(v && String(v).trim());

//     /** Więcej = wyżej. Wagi możesz łatwo korygować. */
//     function completenessScore(x){
//         let s = 0;

//         // Produkty / dokumenty (liczby mogą być stringami)
//         const prod = num(x.productsCount) || len(x.productsTrim);
//         const docs = num(x.documentsCount) || len(x.documentsTrim);
//         s += prod * 3;          // produkty ważniejsze
//         s += docs * 2;

//         // Media / treści
//         s += filled(x.logoUrl)      ? 2   : 0; // logo mocno podbija
//         s += filled(x.description)  ? 1.5 : 0;

//         // Dane kontaktowe / meta
//         s += filled(x.website)      ? 1   : 0;
//         s += filled(x.contactEmail) ? 0.8 : 0;
//         s += filled(x.contactInfo)  ? 0.8 : 0;
//         s += filled(x.brands)       ? 0.8 : 0;
//         s += filled(x.catalogTags)  ? 0.6 : 0;

//         // Stoisko / hala
//         s += filled(x.standNumber)  ? 0.6 : 0;
//         s += filled(x.hallName)     ? 0.4 : 0;

//         return s;
//     }

//     // === WYZNACZ / USTAL WYRÓŻNIONYCH ===
//     (function ensureFeaturedFlag(){
//     if (!Array.isArray(DATA_ALL) || !DATA_ALL.length) return;

//     // Czy API już dało jakichś wyróżnionych?
//     let anyFeaturedFromApi = DATA_ALL.some(x => Number(x?.isFeatured) === 1);

//     if (!anyFeaturedFromApi) {
//         // Fallback: wyznacz wg areaSum (max 30% i max 30 szt.)
//         const getId   = (x) => Number(x?.idNumeric ?? x?.exhibitor_id ?? x?.exhibitorId ?? 0);
//         const getArea = (x) => Number(x?.areaSum ?? x?.boothArea ?? 0);

//         const maxFeatured = Math.min(30, Math.floor(DATA_ALL.length * 0.30));

//         let sorted = [...DATA_ALL].sort((a,b) => getArea(b) - getArea(a));
//         const topArea = getArea(sorted[0] || {});
//         if (!(topArea > 0)) {
//         // jeśli brak sensownych powierzchni → fallback po „nowości” (ID malejąco)
//         sorted = [...DATA_ALL].sort((a,b) => getId(b) - getId(a));
//         }

//         const featuredSet = new Set(sorted.slice(0, maxFeatured).map(getId));
//         DATA_ALL.forEach(x => { x.isFeatured = featuredSet.has(getId(x)) ? 1 : 0; });
//     } else {
//         // API-first: tylko znormalizuj do 0/1 (bo mogą przyjść stringi)
//         DATA_ALL.forEach(x => { x.isFeatured = Number(x?.isFeatured) ? 1 : 0; });
//     }
//     })();

//     // unikalne hale i tagi
//     const ALL_HALLS = uniq(DATA_ALL.map(x => lower(x.hallName)).filter(Boolean));
//     const ALL_TAGS  = uniq(DATA_ALL.flatMap(x => splitTags(x.catalogTags))).sort();

//     // stan
//     const state = { q:"", halls:new Set(), tags:new Set(), onlyNew:false, onlyBig:false };

//     // render karty
//     function renderCard(ex){
//       const productsTrim   = Array.isArray(ex.productsTrim) ? ex.productsTrim : [];
//       const documentsTrim  = Array.isArray(ex.documentsTrim) ? ex.documentsTrim : [];
//       const productsCount  = Number(ex.productsCount || 0);
//       const documentsCount = Number(ex.documentsCount || 0);
//       const headingHTML = Number(ex.isFeatured) ? `
//         <div class="exhibitor-catalog__item-heading">Wyróżnieni wystawcy</div>
//         ` : "";

//       const productsHTML = productsTrim.length ? `
//         <div class="exhibitor-catalog__products">
//           <h4 class="exhibitor-catalog__products-title">Produkty (${productsCount})</h4>
//           <div class="exhibitor-catalog__products-list">
//             ${productsTrim.map(p => `
//               <div class="exhibitor-catalog__products-list-element">
//                 <img src="${esc(p.img||"")}" alt="${esc(p.name||"Product")}" loading="lazy" decoding="async" />
//               </div>`).join("")}
//           </div>
//         </div>` : "";

//       const documentsHTML = documentsTrim.length ? `
//         <div class="exhibitor-catalog__materials">
//           <h4 class="exhibitor-catalog__materials-title">MATERIAŁY DO POBRANIA (${documentsCount})</h4>
//           <div class="exhibitor-catalog__materials-list">
//             ${documentsTrim.map(d => `
//               <div class="exhibitor-catalog__material">
//                 <p>${esc(d.category||"")}</p>
//                 <div class="exhibitor-catalog__material-img">
//                   <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/document.png" alt="${esc(d.title||"Dokument")}" />
//                 </div>
//               </div>`).join("")}
//           </div>
//         </div>` : "";

//       const name   = ex.name || "";
//       const desc   = ex.description || "";
//       const www    = ex.website || "";
//       const mail   = ex.contactEmail || "";
//       const phone  = ex.contactInfo || "";
//       const brands = ex.brands || "";
//       const cats   = ex.catalogTags || "";
//       const stand  = ex.standNumber || "";
//       const hall   = ex.hallName || "";

//       return `
// <div class="exhibitor-catalog__item" data-hall="${esc(lower(hall))}" data-tags="${esc(lower(cats))}" data-created="${getId(ex)}" data-area="${getArea(ex)}">
//   ${headingHTML}
//   <div class="exhibitor-catalog__item-container">
//     <div class="exhibitor-catalog__info">
//       <div class="exhibitor-catalog__company-info">
//         <div class="exhibitor-catalog__logo-tile">
//           ${ex.logoUrl ? `<img src="${esc(ex.logoUrl)}" alt="Exhibitor logo" />` : ""}
//           <div class="exhibitor-catalog__stand">
//             <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="10" r="3" stroke="#ffffff" stroke-width="2"></circle><path d="M19 9.75C19 15.375 12 21 12 21C12 21 5 15.375 5 9.75C5 6.02208 8.13401 3 12 3C15.866 3 19 6.02208 19 9.75Z" stroke="#ffffff" stroke-width="2"></path></svg>
//             <p>Stoisko ${esc(stand)}</p>
//           </div>
//         </div>
//         <div class="exhibitor-catalog__contact">
//           ${www  ? `<div class="exhibitor-catalog__contact-item"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/world.png" /><a href="${esc(www)}">Strona www</a></div>` : ""}
//           ${mail ? `<div class="exhibitor-catalog__contact-item"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/email.png" alt="" /><a href="mailto:${esc(mail)}">Email</a></div>` : ""}
//           ${phone? `<div class="exhibitor-catalog__contact-item"><img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/phone.png" alt="" /><a href="tel:${esc(phone)}">Telefon</a></div>` : ""}
//         </div>
//       </div>
//       <div class="exhibitor-catalog__details">
//         <h3 class="exhibitor-catalog__name">${esc(name)}</h3>
//         ${desc   ? `<p class="exhibitor-catalog__description">${esc(desc)}</p>` : ""}
//         ${brands ? `<div class="exhibitor-catalog__brands"><p class="exhibitor-catalog__label">Brands</p><p class="exhibitor-catalog__value">${esc(brands)}</p></div>` : ""}
//         ${cats   ? `<div class="exhibitor-catalog__categories"><p class="exhibitor-catalog__label">Categories</p><p class="exhibitor-catalog__value">${esc(cats)}</p></div>` : ""}
//       </div>
//     </div>
//   </div>
//   <div class="exhibitor-catalog__extra">
//     ${productsHTML}
//     ${documentsHTML}
//   </div>
// <button type="button" class="exhibitor-catalog__open-modal">Zobacz szczegóły</button>
// </div>`;
//     }

//     // filtracja + sortowanie
//     function applyFilters(dataAll){
//       let arr = dataAll;

//       // tekst: nazwa / opis / marki / numer stoiska (z normalizacją)
//       if (state.q && state.q.trim()) {
//         const q = norm(state.q);
//         arr = arr.filter(x => {
//           const name  = norm(x.name||"");
//           const desc  = norm(x.description||"");
//           const br    = norm(x.brands||"");
//           const stand = norm(String(x.standNumber||""));
//           return name.includes(q) || desc.includes(q) || br.includes(q) || stand.includes(q);
//         });
//       }

//       // hale
//       if (state.halls.size) {
//         arr = arr.filter(x => state.halls.has(lower(x.hallName||"")));
//       }

//       // tagi (AND)
//       if (state.tags.size) {
//         arr = arr.filter(x => {
//           const tags = new Set(lower(x.catalogTags||"").split(",").map(s=>s.trim()).filter(Boolean));
//           for (const t of state.tags) if (!tags.has(t)) return false;
//           return true;
//         });
//       }

//       // Nowi (top 30 po ID) — przekrój
//       if (state.onlyNew) {
//         const topNew = [...dataAll].sort((a,b)=> getId(b)-getId(a)).slice(0,30).map(getId);
//         const setNew = new Set(topNew);
//         arr = arr.filter(x => setNew.has(getId(x)));
//       }

//       // Najwięksi (top 30 po area) — przekrój
//       if (state.onlyBig) {
//         const topBig = [...dataAll].filter(x => getArea(x)>0).sort((a,b)=> getArea(b)-getArea(a)).slice(0,30).map(getId);
//         const setBig = new Set(topBig);
//         arr = arr.filter(x => setBig.has(getId(x)));
//       }

//       // KOŃCOWE SORTOWANIE
//       if (state.onlyBig && state.onlyNew) {
//         arr.sort((a,b) => (getArea(b)-getArea(a)) || (getId(b)-getId(a)));
//       } else if (state.onlyBig) {
//         arr.sort((a,b) => getArea(b)-getArea(a));
//       } else if (state.onlyNew) {
//         arr.sort((a,b) => getId(b)-getId(a)); // najnowsi pierwsi
//       } else {
//             // DOMYŚLNIE: najpierw „bardziej kompletni”, potem A→Z
//             arr.sort((a,b) =>
//                 (completenessScore(b) - completenessScore(a)) ||
//                 (a.name||"").localeCompare(b.name||"", "pl", {sensitivity:"base"})
//             );
//         }

//       return arr;
//     }

//     // renderowanie/infinite
//     let CURRENT  = [];
//     let rendered = 0;
//     let io;

//     function updateCounter(){ if (counterEl) counterEl.textContent = CURRENT.length + " Wyszukiwań"; }
//     function clearListKeepSentinel(){ listEl.querySelectorAll(".exhibitor-catalog__item").forEach(n => n.remove()); }

//     function renderChunk(){
//       if (rendered >= CURRENT.length) return;
//       loader.style.display = "block";
//       const next  = Math.min(rendered + PER_PAGE, CURRENT.length);
//       const slice = CURRENT.slice(rendered, next);
//       const html  = slice.map(renderCard).join("");
//       const tmp   = document.createElement("div");
//       tmp.innerHTML = html;
//       while (tmp.firstChild) listEl.insertBefore(tmp.firstChild, sentinel);
//       rendered = next;
//       loader.style.display = "none";
//     }

//     function reapplyAndReset(){
//       CURRENT = applyFilters(DATA_ALL);
//       updateCounter();
//       clearListKeepSentinel();
//       rendered = 0;
//       renderChunk();
//     }

//     function setupIO(){
//       if (io) io.disconnect();
//       io = new IntersectionObserver(entries => {
//         entries.forEach(e => { if (e.isIntersecting) renderChunk(); });
//       }, { rootMargin: "400px 0px" });
//       io.observe(sentinel);
//     }

//     // budowa UI filtrów + search
//     function buildFiltersUI(){
//       // liczniki
//       const hallCounts = {};
//       DATA_ALL.forEach(x => {
//         const h = lower(x.hallName);
//         if (!h) return;
//         hallCounts[h] = (hallCounts[h] || 0) + 1;
//       });

//       const tagCounts = {};
//       DATA_ALL.forEach(x => {
//         const ut = uniq(splitTags(x.catalogTags));
//         ut.forEach(t => tagCounts[t] = (tagCounts[t] || 0) + 1);
//       });

//       // pierwsza grupa — Nowi/Najwięksi
//       const firstGroup = filtersRoot.querySelector(".exhibitor-catalog__category-group");
//       if (firstGroup) {
//         firstGroup.querySelectorAll(".exhibitor-catalog__checkbox").forEach(n => n.remove());

//         const topN = Math.min(30, DATA_ALL.length);
//         const lblNew = document.createElement("label");
//         lblNew.className = "exhibitor-catalog__checkbox";
//         lblNew.innerHTML = `
//           <input type="checkbox" id="filter-new" class="exhibitor-catalog__checkbox-input" />
//           <span class="exhibitor-catalog__checkbox-label">Nowi wystawcy (top ${topN})</span>`;
//         firstGroup.appendChild(lblNew);

//         const countBig = Math.min(30, DATA_ALL.filter(x => getArea(x) > 0).length);
//         const lblBig = document.createElement("label");
//         lblBig.className = "exhibitor-catalog__checkbox";
//         lblBig.innerHTML = `
//           <input type="checkbox" id="filter-big" class="exhibitor-catalog__checkbox-input" />
//           <span class="exhibitor-catalog__checkbox-label">Najwięksi wystawcy (top ${countBig})</span>`;
//         firstGroup.appendChild(lblBig);

//         firstGroup.querySelector("#filter-new").addEventListener("change", (e)=>{ state.onlyNew = !!e.target.checked; reapplyAndReset(); });
//         firstGroup.querySelector("#filter-big").addEventListener("change", (e)=>{ state.onlyBig = !!e.target.checked; reapplyAndReset(); });
//       }

//       // HALE
//       const hallsGroup = Array.from(filtersRoot.querySelectorAll(".exhibitor-catalog__category-group"))
//         .find(g => g.querySelector(".exhibitor-catalog__category-heading")?.textContent?.toLowerCase().includes("hale"));

//       if (hallsGroup) {
//         Array.from(hallsGroup.children).forEach(ch => {
//           if (!ch.classList.contains("exhibitor-catalog__heading-container")) ch.remove();
//         });

//         if (ALL_HALLS.length < 2) {
//           hallsGroup.style.display = "none";
//         } else {
//           hallsGroup.style.display = "";
//           ALL_HALLS.forEach(h => {
//             const id = "hall-" + h.replace(/\s+/g,"-");
//             const el = document.createElement("label");
//             el.className = "exhibitor-catalog__checkbox";
//             el.innerHTML = `
//               <input type="checkbox" id="${id}" class="exhibitor-catalog__checkbox-input" data-hall-value="${h}" />
//               <span class="exhibitor-catalog__checkbox-label">
//                 ${h || "—"} <span class="exhibitor-catalog__checkbox-count">(${hallCounts[h]||0})</span>
//               </span>`;
//             hallsGroup.appendChild(el);
//           });

//           hallsGroup.addEventListener("change", (e)=>{
//             const input = e.target.closest("input[data-hall-value]");
//             if (!input) return;
//             const val = input.getAttribute("data-hall-value");
//             if (input.checked) state.halls.add(val); else state.halls.delete(val);
//             reapplyAndReset();
//           });
//         }
//       }

//       // TAGI
//       let tagsGroup = Array.from(filtersRoot.querySelectorAll(".exhibitor-catalog__category-group"))
//         .find(g => g.querySelector(".exhibitor-catalog__category-heading")?.textContent?.toLowerCase().includes("sektory technologiczne"));

//       if (!tagsGroup) {
//         tagsGroup = document.createElement("div");
//         tagsGroup.className = "exhibitor-catalog__category-group";
//         tagsGroup.innerHTML = `
//           <div class="exhibitor-catalog__heading-container">
//             <h3 class="exhibitor-catalog__category-heading">Sektory technologiczne</h3>
//             <img src="/wp-content/plugins/pwe-elements-auto-switch/elements/catalog/exhibitor_catalog/media/arrow.png" />
//           </div>`;
//         filtersRoot.appendChild(tagsGroup);
//       }

//       Array.from(tagsGroup.children).forEach(ch => {
//         if (!ch.classList.contains("exhibitor-catalog__heading-container")) ch.remove();
//       });

//       ALL_TAGS.forEach(t => {
//         const id = "tag-" + t.replace(/\s+/g,"-");
//         const el = document.createElement("label");
//         el.className = "exhibitor-catalog__checkbox";
//         el.innerHTML = `
//           <input type="checkbox" id="${id}" class="exhibitor-catalog__checkbox-input" data-tag-value="${t}" />
//           <span class="exhibitor-catalog__checkbox-label">
//             ${t} <span class="exhibitor-catalog__checkbox-count">(${tagCounts[t]||0})</span>
//           </span>`;
//         tagsGroup.appendChild(el);
//       });

//       tagsGroup.addEventListener("change", (e)=>{
//         const input = e.target.closest("input[data-tag-value]");
//         if (!input) return;
//         const val = input.getAttribute("data-tag-value");
//         if (input.checked) state.tags.add(val); else state.tags.delete(val);
//         reapplyAndReset();
//       });

//       // SEARCH: nazwa / stoisko
//       if (searchInput && !searchInput.__wired) {
//         let t = null;
//         searchInput.addEventListener("input", (e)=>{
//           clearTimeout(t);
//           t = setTimeout(()=>{
//             state.q = e.target.value || "";
//             reapplyAndReset();
//           }, 180);
//         });
//         searchInput.__wired = true;
//       }
//     }

//     // init
//     function init(){
//       buildFiltersUI();
//       // domyślnie A→Z
//       let CURRENT = applyFilters(DATA_ALL);
//       // wyczyść SSR i włącz infinite
//       listEl.querySelectorAll(".exhibitor-catalog__item").forEach(n=>n.remove());
//       // expose CURRENT/rendered/io do zasięgu zewnętrznego:
//       window.__exhibitors_state__ = { CURRENT, rendered:0, io:null };

//       function updateCounter(){ if (counterEl) counterEl.textContent = window.__exhibitors_state__.CURRENT.length + " Wyszukiwań"; }
//       function renderChunk(){
//         const st = window.__exhibitors_state__;
//         if (st.rendered >= st.CURRENT.length) return;
//         loader.style.display = "block";
//         const next  = Math.min(st.rendered + PER_PAGE, st.CURRENT.length);
//         const slice = st.CURRENT.slice(st.rendered, next);
//         const html  = slice.map(renderCard).join("");
//         const tmp   = document.createElement("div");
//         tmp.innerHTML = html;
//         while (tmp.firstChild) listEl.insertBefore(tmp.firstChild, sentinel);
//         st.rendered = next;
//         loader.style.display = "none";
//       }
//       function reapplyAndReset(){
//         const st = window.__exhibitors_state__;
//         st.CURRENT = applyFilters(DATA_ALL);
//         updateCounter();
//         listEl.querySelectorAll(".exhibitor-catalog__item").forEach(n => n.remove());
//         st.rendered = 0;
//         renderChunk();
//       }
//       function setupIO(){
//         const st = window.__exhibitors_state__;
//         if (st.io) st.io.disconnect();
//         st.io = new IntersectionObserver(entries => {
//           entries.forEach(e => { if (e.isIntersecting) renderChunk(); });
//         }, { rootMargin: "400px 0px" });
//         st.io.observe(sentinel);
//       }

//       // podłącz na window, żeby funkcje były dostępne w wewnętrznych handlerach
//       window.__exhibitors_hooks__ = { renderChunk, reapplyAndReset, updateCounter, setupIO };

//       updateCounter();
//       window.__exhibitors_state__.rendered = 0;
//       renderChunk();
//       setupIO();
//     }

//     init();
//   })();
// });