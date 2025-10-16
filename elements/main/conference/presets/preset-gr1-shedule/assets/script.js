document.addEventListener("DOMContentLoaded", function() {
    document.querySelectorAll(".pwe-conf-short-info-gr1-schedule__row-link").forEach(function(row) {
        row.style.cursor = "pointer";
        row.addEventListener("click", function() {
            window.location = row.getAttribute("data-href");
        });
    });
});