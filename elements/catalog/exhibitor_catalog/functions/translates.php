<?php
if (!defined('ABSPATH')) exit;

if (get_locale() === 'en_US') {

  $output = '
  <style>
    @media(min-width:960px){
      .exhibitor-catalog__filters.is-visible { max-width:29%; }
    }

    .skiptranslate,
    .goog-te-banner-frame,
    .goog-te-balloon-frame,
    #google_translate_element,
    .goog-te-gadget,
    .goog-te-gadget-simple,
    .goog-te-gadget-icon,
    .VIpgJd-ZVi9od-aZ2wEe-wOHMyf { display:none !important; }

    body { top:0 !important; }

    [class^="VIpgJd-"], [class*=" VIpgJd-"],
    [class^="VIpgJd-"]:hover, [class*=" VIpgJd-"]:hover {
      background: transparent !important;
      box-shadow: none !important;
      text-decoration: none !important;
      outline: none !important;
    }
  </style>

  <div id="google_translate_element"></div>

  <script>
  (function () {
    if (window.__PWE_GT_CATALOG_INIT__) return;
    window.__PWE_GT_CATALOG_INIT__ = true;

    // =========================
    // 1) GOOGLE TRANSLATE INIT
    // =========================
    window.googleTranslateElementInit = function (){
      try{
        new google.translate.TranslateElement({
          pageLanguage: "auto",
          includedLanguages: "en",
          autoDisplay: false
        }, "google_translate_element");
      } catch (e) {}

      setTimeout(forceEN, 150);
      setTimeout(forceEN, 600);
      setTimeout(forceEN, 1200);
    };

    // set cookie to force Google Translate to EN (first run only case)
    function setGoogTransCookie(lang) {
      try {
        var v = "/auto/" + lang; // /auto/en
        document.cookie = "googtrans=" + v + "; path=/";
        // sometimes domain is needed
        document.cookie = "googtrans=" + v + "; domain=" + window.location.hostname + "; path=/";
      } catch (e) {}
    }

    function isGoogTransEN() {
      return document.cookie.indexOf("googtrans=/auto/en") !== -1;
    }

    function ensureFirstRunEN() {
      if (isGoogTransEN()) return;

      setGoogTransCookie("en");

      // reload tylko 1 raz na “pierwszy raz”, żeby nie zapętlić
      if (!sessionStorage.getItem("pwe_gt_first_reload_done")) {
        sessionStorage.setItem("pwe_gt_first_reload_done", "1");
        location.reload();
      }
    }


    function loadGoogleTranslateOnce() {
      // KLUCZ: ustaw cookie zanim załadujesz element.js
      ensureFirstRunEN();

      if (window.google && window.google.translate) return;
      if (document.querySelector("script[data-pwe-gt]")) return;

      var s = document.createElement("script");
      s.src = "https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
      s.async = true;
      s.setAttribute("data-pwe-gt", "1");
      document.head.appendChild(s);
    }

    // secondary force to EN (in case cookie + reload not worked)
    function forceEN() {
      var sel = document.querySelector("#google_translate_element select");
      if (!sel) return;
      if (sel.value !== "en") {
        sel.value = "en";
        sel.dispatchEvent(new Event("change"));
      }
    }

    loadGoogleTranslateOnce();

    // =======================================================================
    // 2) ALLOWLIST - Type here what to translate (inside: TRANSLATE_ONLY[])
    // =======================================================================
    var CATALOG_ROOT = "#exhibitorCatalog, .exhibitor-catalog";

    var TRANSLATE_ONLY = [
      ".exhibitor-catalog__exh-card-desc",
      ".exhibitor-catalog__exh-card-brand-container",
      ".filter-group__hall .exhibitor-catalog__filter-label",
      ".filter-group__sector",
      ".filter-group__category",
      ".exhibitor-catalog__product-card-title",
      ".exhibitor-catalog__product-card-desc",
      ".catalog-mobile-product-card__title",
      ".exhibitor-catalog__product-card-category-container"
    ].join(",");

    function blockCatalog(root) {
      (root || document).querySelectorAll(CATALOG_ROOT).forEach(function (el) {
        el.setAttribute("translate", "no");
        el.classList.add("notranslate");
      });
    }

    function allowTranslate(root) {
      (root || document).querySelectorAll(TRANSLATE_ONLY).forEach(function (el) {
        el.setAttribute("translate", "yes");
        el.classList.remove("notranslate");
      });
    }


    // =============================================================
    // 2b) EXHIBITOR PAGE: it translates almost everything,
    //     except for selected elements (EXHIBITOR_NO_TRANSLATE[])
    // =============================================================
    var EXHIBITOR_ROOT = "#exhibitorPage, .exhibitor-page";

    var EXHIBITOR_NO_TRANSLATE = [
      ".exhibitor-page__info-column",
      ".exhibitor-page__title",
      ".exhibitor-page__brand-list",
      ".exhibitor-page__tabs-nav",
      ".exhibitor-page__subtitle",
      ".exhibitor-catalog__filter-heading",
      ".exhibitor-single-mobile__section-title",
      ".collapsible-toggle",
      ".exhibitor-single-mobile__products-title",
      ".exhibitor-single-mobile__documents-title",
      ".exhibitor-single-mobile__title",
      ".exhibitor-single-mobile__stand"
    ].join(",");

    function allowTranslateExhibitor(root) {
      (root||document).querySelectorAll(EXHIBITOR_ROOT).forEach(function (page) {
        // turn on transaltion for the whole exhibitor page container
        page.setAttribute("translate", "yes");
        page.classList.remove("notranslate");

        // turn off translation only for selected blocks
        page.querySelectorAll(EXHIBITOR_NO_TRANSLATE).forEach(function (el) {
          el.setAttribute("translate", "no");
          el.classList.add("notranslate");
        });
      });
    }

    // ==========================================
    // 2c) PRODUCT PAGE: translate almost everything,
    //     except for selected segments
    // ==========================================
    var PRODUCT_ROOT = "#exhPageProduct, .exh-product";
    var PRODUCT_NO_TRANSLATE = [
      ".exh-product__exhibitor-name",
      ".exh-product__header-container",
      ".exh-product__description-title",
      ".exh-product__categories-title",
      ".exhibitor-single-mobile__contact",
      ".exh-product__description-title",
      ".exh-product__categories-title",
      ".exh-product__header-container"
    ].join(",");

    function allowTranslateProduct(root){
      (root|| document).querySelectorAll(PRODUCT_ROOT).forEach(function(page){
      // turn on transaltion for the whole product page container
        page.setAttribute("translate", "yes");
        page.classList.remove("notranslate");

        // turn off translation only for selected blocks
        page.querySelectorAll(PRODUCT_NO_TRANSLATE).forEach(function(el){
          el.setAttribute("translate", "no");
          el.classList.add("notranslate");
        });
      });
    }


    // =====================================================
    // 3) FIX: Street/House/Hala -> Hall (only hall labels)
    // =====================================================
    function fixHallLabels(root) {
      var scope = root || document;
      var group = scope.querySelector(".filter-group__hall") || document.querySelector(".filter-group__hall");
      if (!group) return;

      //No need to change header anymore
      // // header of section
      // var header = group.querySelector(".exhibitor-catalog__filter-title, .exhibitor-catalog__filter-group-title, h3, h4");
      // if (header) {
      //   var h = (header.textContent || "").trim();
      //   if (/^House$/i.test(h)) header.textContent = "Halls";
      // }

      // <font> inside labels
      group.querySelectorAll(".exhibitor-catalog__filter-label font").forEach(function(fontEl){
        var txt = (fontEl.textContent || "").replace(/\\u00A0/g, " ").trim();
        var fixed = txt.replace(/^(Street|House|Hala|Hall)\\s+/i, "Hall ");
        if (fixed !== txt) fontEl.textContent = fixed;
      });
    }

    // ============================================
    // 4) DEDUPLICATION FIX: fix duplicated labels
    // ============================================
    function fixDuplicateLabels(root) {
      var labels = (root || document).querySelectorAll(".exhibitor-catalog__checkbox-label");

      labels.forEach(function (label) {
        var count = label.querySelector(".exhibitor-catalog__count");
        var countHTML = count ? count.outerHTML : "";

        var labelText = (label.innerText || "").replace(/\\(\\d+\\)/g, "").trim();
        if (!labelText) return;

        var match = labelText.match(/^([\\s\\S]+?)\\1+$/);
        var deduped = match ? match[1].trim() : labelText;

        if (deduped === labelText) {
          var words = labelText.split(/\\s+/);
          for (var block = 1; block <= Math.floor(words.length / 2); block++) {
            if (words.length % block !== 0) continue;
            var ok = true;
            for (var i = 0; i < words.length; i++) {
              if (words[i] !== words[i % block]) { ok = false; break; }
            }
            if (ok) { deduped = words.slice(0, block).join(" "); break; }
          }
        }

        if (labelText !== deduped) {
          label.innerHTML = deduped + (countHTML ? " " + countHTML : "");
        }
      });
    }

    // ======================
    // 5) APPLY + OBSERVER
    // ======================
    function applyAll(root) {
      blockCatalog(root);
      allowTranslate(root);
      fixDuplicateLabels(root);
      fixHallLabels(root);
      allowTranslateExhibitor(root);
      allowTranslateProduct(root);
    }

    applyAll(document);
    setTimeout(function(){ applyAll(document); }, 600);
    setTimeout(function(){ applyAll(document); }, 1600);
    setTimeout(function(){ applyAll(document); }, 3000);

    var watchRoot = document.querySelector(".exhibitor-catalog__filters") || document.body;
    var t = null;

    new MutationObserver(function () {
      if (t) clearTimeout(t);
      t = setTimeout(function(){ applyAll(document); }, 250);
    }).observe(watchRoot, { childList: true, subtree: true });

  })();
  </script>
  ';

  echo $output;
}
