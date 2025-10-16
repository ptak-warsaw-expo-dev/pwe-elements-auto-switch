document.addEventListener("DOMContentLoaded", function() {
    const wrapper = document.querySelector(".pwe-countdown__wrapper");
    if (!wrapper) return;

    const startDate = new Date(wrapper.dataset.start);
    const endDate = new Date(wrapper.dataset.end);
    const lang = wrapper.dataset.lang;
    const showSeconds = wrapper.dataset.seconds === "1";

    const labelEl = wrapper.querySelector(".pwe-countdown__label");
    const timerEl = wrapper.querySelector(".pwe-countdown__timer");

    function pluralize(num, forms) {
        if (lang === "pl") {
            if (num === 1) return forms[0];
            if (num % 10 >= 2 && num % 10 <= 4 && (num % 100 < 10 || num % 100 >= 20)) {
                return forms[1];
            }
            return forms[2];
        } else {
            return num === 1 ? forms[0] : forms[1];
        }
    }

    function updateCountdown() {
        const now = new Date();
        let distance, label;

        if (now < startDate) {
            label = lang === "pl" ? "Do targów pozostało:" : "Until the fair starts:";
            distance = startDate - now;
        } else if (now >= startDate && now <= endDate) {
            label = lang === "pl" ? "Do końca targów pozostało:" : "Until the fair ends:";
            distance = endDate - now;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);
        const seconds = Math.floor((distance / 1000) % 60);

        const dWord = pluralize(days, lang === "pl" ? ["dzień", "dni", "dni"] : ["day", "days"]);
        const hWord = pluralize(hours, lang === "pl" ? ["godzina", "godziny", "godzin"] : ["hour", "hours"]);
        const mWord = pluralize(minutes, lang === "pl" ? ["minuta", "minuty", "minut"] : ["minute", "minutes"]);

        let output = `<span>${days} ${dWord}</span> <span>${hours} ${hWord}</span> <span>${minutes} ${mWord}</span>`;

        if (showSeconds) {
            const sWord = pluralize(seconds, lang === "pl" ? ["sekunda", "sekundy", "sekund"] : ["second", "seconds"]);
            output += ` <span>${seconds} ${sWord}</span>`;
        }

        labelEl.textContent = label;
        timerEl.innerHTML = output;
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);

    // if opening hours are active
    const openingHours = document.querySelector(".opening-hours");
    if (openingHours) {
        openingHours.classList.add("sticky-element");
    }
    
});
