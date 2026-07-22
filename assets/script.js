window.onload = function () {
    const langInput = document.querySelector(".lang input");
    if (langInput) {
        const pageLang = (document.documentElement.lang || "").trim().toLowerCase();
        langInput.value = pageLang ? pageLang.substring(0, 2) : "";
    }
}