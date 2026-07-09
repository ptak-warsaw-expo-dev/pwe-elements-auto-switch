document.addEventListener("DOMContentLoaded", function () {
    const utm = "'. $source_utm .'";

    function getCookie(name) {
        let value = "; " + document.cookie;
        let parts = value.split("; " + name + "=");
        if (parts.length === 2) return parts.pop().split(";").shift();
        return null;
    }

    function deleteCookie(name) {
        document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    }

    let utmPWE = utm;
    let utmCookie = getCookie("utm_params");
    let utmInput = document.querySelector(".utm-class input");

    if (utmInput) {
        utmInput.value = utmPWE;
    }
});
