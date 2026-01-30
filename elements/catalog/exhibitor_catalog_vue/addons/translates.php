<?php
if (!defined('ABSPATH')) exit;
if (get_locale() !== 'en_US') return;

echo '
<style>
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
  if (window.__PWE_GT_VUECAT_INIT__) return;
  window.__PWE_GT_VUECAT_INIT__ = true;

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

  function setGoogTransCookie(lang) {
    try {
      var v = "/auto/" + lang;
      document.cookie = "googtrans=" + v + "; path=/";
      document.cookie = "googtrans=" + v + "; domain=" + location.hostname + "; path=/";
    } catch (e) {}
  }
  function isGoogTransEN() {
    return document.cookie.indexOf("googtrans=/auto/en") !== -1;
  }
  function ensureFirstRunEN() {
    if (isGoogTransEN()) return;
    setGoogTransCookie("en");
    if (!sessionStorage.getItem("pwe_gt_first_reload_done")) {
      sessionStorage.setItem("pwe_gt_first_reload_done", "1");
      location.reload();
    }
  }
  function loadGoogleTranslateOnce() {
    ensureFirstRunEN();
    if (window.google && window.google.translate) return;
    if (document.querySelector("script[data-pwe-gt]")) return;

    var s = document.createElement("script");
    s.src = "https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    s.async = true;
    s.setAttribute("data-pwe-gt", "1");
    document.head.appendChild(s);
  }
  function forceEN() {
    var sel = document.querySelector("#google_translate_element select");
    if (!sel) return;
    if (sel.value !== "en") {
      sel.value = "en";
      sel.dispatchEvent(new Event("change"));
    }
  }
    
  preBlockSwiper();
  loadGoogleTranslateOnce();

  // =========================
  // 2) RULES
  // =========================
  var ROOT = "#vue-catalog";

  // ============ NO_TRANSLATE =============
  var NO_TRANSLATE = [
    ".exhibitor-title",
    ".brands__list",
    ".filter-section.filter-brands .filter-section__body-inner",
    ".exhibitor-name",
    ".brands__item.brands__item--slide",
    ".catalog-feedback.is-open"
  ].join(",");

  // =========================
  // Helpers
  // =========================
  function $(sel, root){ return (root||document).querySelector(sel); }
  function $all(sel, root){ return Array.prototype.slice.call((root||document).querySelectorAll(sel)); }

  // Cancelling translation if text contains quotes
  function protectQuotedTitles(){
    var selectors = [
      ".exhibitor-product-modal__title",
      ".product-thumbnail__title",
      ".product-card__title"
    ].join(",");

    var q = String.fromCharCode(34); // "

    document.querySelectorAll(selectors).forEach(function(el){
      if (!el) return;

      // save original text
      if (!el.getAttribute("data-pwe-orig")) {
        el.setAttribute("data-pwe-orig", el.textContent || "");
      }

      var orig = el.getAttribute("data-pwe-orig") || "";
      if (orig.indexOf(q) === -1) return;

      // blocking translation
      el.classList.add("notranslate");
      el.setAttribute("translate", "no");

      // cleanup fonts added by GT
      if (typeof stripTranslateFonts === "function") stripTranslateFonts(el);
      el.textContent = orig;
    });
  }



  // delete <font> inside element (Google Translate adds these)
  function stripTranslateFonts(el){
    if (!el) return;
    var fonts = el.querySelectorAll("font");
    fonts.forEach(function(f){
      var t = document.createTextNode(f.textContent || "");
      f.parentNode.replaceChild(t, f);
    });
  }

  function preBlockSwiper(){
    // blocking GT for swiper pagination in modal
    document.querySelectorAll(
      ".exhibitor-product-modal .swiper-pagination, " +
      ".exhibitor-product-modal .swiper-pagination-fraction, " +
      ".exhibitor-product-modal .swiper-pagination-current, " +
      ".exhibitor-product-modal .swiper-pagination-total, " +
      ".exhibitor-product-modal .sep"
    ).forEach(function(el){
      el.setAttribute("translate","no");
      el.classList.add("notranslate");
      stripTranslateFonts(el);
    });
  }

  // adding translate="no" and notranslate class
  function hardNoTranslate(el){
    if (!el) return;
    el.setAttribute("translate","no");
    el.classList.add("notranslate");
    // also for all children
    $all("*", el).forEach(function(ch){
      ch.setAttribute("translate","no");
      ch.classList.add("notranslate");
    });
    stripTranslateFonts(el);
  }

function setLabelTextPreserveCount(labelEl, newText){
  if (!labelEl) return;

  if (labelEl.getAttribute("data-pwe-label") === newText) return;
  labelEl.setAttribute("data-pwe-label", newText);

  stripTranslateFonts(labelEl);
  Array.from(labelEl.childNodes).forEach(function(n){
    if (n.nodeType === Node.TEXT_NODE) {
      // deleting old text like "Hall X "
      n.nodeValue = "";
    }
  });

  // finding span with counter label and inserting before it
  var firstEl = null;
  for (var i=0;i<labelEl.childNodes.length;i++){
    if (labelEl.childNodes[i].nodeType === Node.ELEMENT_NODE) {
      firstEl = labelEl.childNodes[i];
      break;
    }
  }

  var textNode = document.createTextNode(newText + " ");
  if (firstEl) labelEl.insertBefore(textNode, firstEl);
  else labelEl.appendChild(textNode);
}


  // =========================
  // 3) APPLY TRANSLATION SCOPE
  // =========================
  function enableTranslateInRoot(){
    var r = $(ROOT);
    if (!r) return;
    r.setAttribute("translate","yes");
    r.classList.remove("notranslate");
  }

  // 3a) Apply exclusions (hard)
  function applyExclusions(){
    var r = $(ROOT);
    if (!r) return;
    $all(NO_TRANSLATE, r).forEach(hardNoTranslate);
  }

  // =========================
  // 4) FIXES: Halls + Type (manual EN, no GT)
  // =========================
  function fixHalls(){
    var r = $(ROOT);
    if (!r) return;

    // Halls Filter Header
    var h = $(".filter-section.filter-halls .filter-section__header", r);
    if (h){
      hardNoTranslate(h);
      var txt = (h.textContent || "").replace(/\\u00A0/g," ").trim();
      if (/^hala(e)?$/i.test(txt)) h.textContent = "Halls";
    }

    // Halls Filter Labels
    var hallSection = $(".filter-section.filter-halls", r);
    if (!hallSection) return;

    $all(".filter-switch__label", hallSection).forEach(function(lbl){
      // leaving counting part untouched and getting text only
      hardNoTranslate(lbl);
      var raw = (lbl.textContent || "").replace(/\\u00A0/g," ").trim();
      var m = raw.match(/^(?:Hala|Hale|Hall)\\s*([A-Z])\\b/i);
      if (m && m[1]) {
        setLabelTextPreserveCount(lbl, "Hall " + m[1].toUpperCase());
      } else {
        // fallback for hall
        var fixed = raw.replace(/^(Hala|Hale|Hall)\\s+/i, "Hall ");
        setLabelTextPreserveCount(lbl, fixed);
      }
    });
  }


  function fixType(){
    var r = document.querySelector("#vue-catalog");
    if (!r) return;

    var typeSection = r.querySelector("section.filter-section.filter-types");
    if (!typeSection) return;

    hardNoTranslate(typeSection);

    // Header
    var header = typeSection.querySelector(".filter-section__header");
    if (header){
      stripTranslateFonts(header);
      header.textContent = "Type";
    }

    // Setting labels
    var labels = typeSection.querySelectorAll(".filter-switch__label");
    if (!labels || !labels.length) return;

    if (labels[0]) { hardNoTranslate(labels[0]); setLabelTextPreserveCount(labels[0], "Exhibitor"); }
    if (labels[1]) { hardNoTranslate(labels[1]); setLabelTextPreserveCount(labels[1], "Brand"); }
    if (labels[2]) { hardNoTranslate(labels[2]); setLabelTextPreserveCount(labels[2], "Product"); }
  }



  // =========================
  // 6) APPLY + OBSERVER
  // =========================
  function applyAll(){
    enableTranslateInRoot();
    applyExclusions();
    protectQuotedTitles();
    fixHalls();
    fixType();
    preBlockSwiper();
  }

  applyAll();
  setTimeout(applyAll, 600);
  setTimeout(applyAll, 1600);
  setTimeout(applyAll, 3000);

  var watchRoot = $(".filters", $(ROOT)) || $(ROOT) || document.body;
  var t = null;

  new MutationObserver(function () {
    if (t) clearTimeout(t);
    t = setTimeout(applyAll, 250);
  }).observe(watchRoot, { childList: true, subtree: true });
  
  // Looking for modal addition to pre-block swiper there immediately
new MutationObserver(function(muts){
  for (var i=0; i<muts.length; i++){
    var added = muts[i].addedNodes;
    if (!added) continue;
    for (var j=0; j<added.length; j++){
      var n = added[j];
      if (n.nodeType !== 1) continue;
      if (n.matches && (n.matches(".exhibitor-product-modal") || n.querySelector(".exhibitor-product-modal"))){
        preBlockSwiper();
        return;
      }
    }
  }
}).observe(document.body, { childList: true, subtree: true });


})();
</script>
';
