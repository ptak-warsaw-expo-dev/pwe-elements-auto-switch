(function () {
  function initOne(container){
    if (!container) return;

    var placeholders = Array.prototype.slice.call(container.querySelectorAll(".logo-placeholder"));
    var raw = container.getAttribute("data-logos");
    if (!raw) return;

    var logos;
    try { logos = JSON.parse(raw) || []; } catch(e){ return; }
    if (!Array.isArray(logos) || logos.length === 0) return;

    // Ile pokazać: 6 na mobilce, 9 na większych
    var isMobile = window.matchMedia("(max-width: 570px)").matches;
    var targetCount = isMobile ? 6 : 9;

    // Jeżeli mamy mniej obrazków niż targetCount, dopasuj
    if (logos.length < targetCount) targetCount = logos.length;
    if (placeholders.length < targetCount) targetCount = placeholders.length;

    // Wylosuj unikalne indeksy
    var used = new Set();
    while (used.size < targetCount) {
      used.add(Math.floor(Math.random() * logos.length));
    }
    var pick = Array.from(used).map(function(i){ return logos[i]; });

    // Podmień src + pokaż tylko targetCount pierwszych placeholderów
    setTimeout(function(){
      placeholders.forEach(function(img, i){
        if (i < targetCount) {
          var url = pick[i];
          if (!url) { img.style.display = "none"; return; }
          img.src = url;
          img.style.visibility = "visible";
          img.style.opacity = "1";
          img.style.display = ""; // na wypadek wcześniejszych styli
        } else {
          // Nie ładuj nadmiarowych obrazków na mobilce
          img.removeAttribute("src"); // zostaje 1x1 gif z HTML albo brak src
          img.style.visibility = "hidden";
          img.style.opacity = "0";
          img.style.display = "none";
        }
      });
    }, 10);
  }

  function initAll(){
    var containers = document.querySelectorAll(".pwe-container-logotypes[data-logos]");
    containers.forEach(initOne);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }
})();