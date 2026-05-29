document.addEventListener("DOMContentLoaded", function() {
    const pweHeaderPartners = document.querySelector(".pwe-header__partners");
    const pweHeaderContainer = document.querySelector(".pwe-header__container");

    if (!pweHeaderPartners) {
        return;
    }

    setTimeout(() => {
        pweHeaderPartners.style.opacity = 1;

        const partnersHeight = pweHeaderPartners.offsetHeight;
        const containerHeight = pweHeaderContainer.offsetHeight;

        const diff = Math.abs(partnersHeight - containerHeight);

        if (containerHeight < partnersHeight || diff < 100) {
            pweHeaderContainer.style.minHeight = partnersHeight + 100 + "px";
        }
    }, 300);
    
}); 