document.addEventListener("DOMContentLoaded", function () {
    const wrappers = document.querySelectorAll(".pwe-countdown__wrapper");
    if (!wrappers.length) return;

    wrappers.forEach(wrapper => {
        const startDate = new Date(wrapper.dataset.start);
        const endDate = new Date(wrapper.dataset.end);
        const lang = wrapper.dataset.lang;
        const showSeconds = wrapper.dataset.seconds === "1";

        const labelEl = wrapper.querySelector(".pwe-countdown__label");
        const timerEl = wrapper.querySelector(".pwe-countdown__timer");

        // Tworzymy jednostki czasu
        const units = ["days", "hours", "minutes"];
        if (showSeconds) units.push("seconds");

        units.forEach(unit => {
            const unitEl = document.createElement("span");
            unitEl.className = `pwe-countdown__unit ${unit}`;
            unitEl.dataset.value = "0";

            const digitsEl = document.createElement("span");
            digitsEl.className = "digits";

            const labelUnitEl = document.createElement("span");
            labelUnitEl.className = "label";
            labelUnitEl.textContent = unit;

            unitEl.appendChild(digitsEl);
            unitEl.appendChild(labelUnitEl);
            timerEl.appendChild(unitEl);
        });

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

        const labels = {
            day: {
                pl: ["dzień", "dni", "dni"],
                en: ["day", "days"]
            },
            hour: {
                desktop: {
                    pl: ["godzina", "godziny", "godzin"],
                    en: ["hour", "hours"]
                },
                mobile: {
                    pl: ["godz", "godz", "godz"],
                    en: ["hour", "hours"]
                }
            },
            minute: {
                desktop: {
                    pl: ["minuta", "minuty", "minut"],
                    en: ["minute", "minutes"]
                },
                mobile: {
                    pl: ["min", "min", "min"],
                    en: ["min", "min"]
                }
            },
            second: {
                desktop: {
                    pl: ["sekunda", "sekundy", "sekund"],
                    en: ["second", "seconds"]
                },
                mobile: {
                    pl: ["sek", "sek", "sek"],
                    en: ["sec", "sec"]
                }
            }
        };

        function animateDigits(digitsEl, newNumber) {
            const oldNumber = digitsEl.dataset.value || "";
            const oldDigits = oldNumber.split("");
            const newDigits = String(newNumber).split("");

            const maxLength = Math.max(oldDigits.length, newDigits.length);
            while (oldDigits.length < maxLength) oldDigits.unshift("0");
            while (newDigits.length < maxLength) newDigits.unshift("0");

            if (
                oldDigits.join("") === newDigits.join("") &&
                digitsEl.innerHTML !== ""
            ) return;

            digitsEl.innerHTML = "";

            newDigits.forEach((digit, i) => {
                const oldDigit = oldDigits[i];
                const digitWrapper = document.createElement("span");
                digitWrapper.className = "digit";

                if (oldDigit === digit) {
                    const span = document.createElement("span");
                    span.textContent = digit;
                    span.className = "static";
                    digitWrapper.appendChild(span);
                } else {
                    const oldSpan = document.createElement("span");
                    oldSpan.textContent = oldDigit;
                    oldSpan.className = "old";

                    const newSpan = document.createElement("span");
                    newSpan.textContent = digit;
                    newSpan.className = "new";

                    digitWrapper.appendChild(oldSpan);
                    digitWrapper.appendChild(newSpan);

                    setTimeout(() => {
                        oldSpan.style.transform = "translateY(-100%)";
                        newSpan.style.transform = "translateY(0)";
                    }, 10);
                }

                digitsEl.appendChild(digitWrapper);
            });

            digitsEl.dataset.value = String(newNumber);
        }

        function updateCountdown() {
            const now = new Date();
            let distance, label;

            const isMobile = window.innerWidth < 960;

            if (!isMobile) {
                if (now < startDate) {
                    label = lang === "pl" ? "Do targów pozostało:" : "Until the fair starts:";
                    distance = startDate - now;
                } else if (now >= startDate && now <= endDate) {
                    label = lang === "pl" ? "Do końca targów pozostało:" : "Until the fair ends:";
                    distance = endDate - now;
                } else {
                    label = lang === "pl" ? "Targi zakończone" : "Fair ended";
                    distance = 0;
                }
            } else {
                if (now < startDate) {
                    label = "START:";
                    distance = startDate - now;
                } else if (now >= startDate && now <= endDate) {
                    label = lang === "pl" ? "KONIEC:" : "END:";
                    distance = endDate - now;
                } else {
                    label = lang === "pl" ? "Targi zakończone" : "Fair ended";
                    distance = 0;
                }
            }

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((distance / (1000 * 60)) % 60);
            const seconds = Math.floor((distance / 1000) % 60);

            labelEl.textContent = label;

            const langKey = lang === "pl" ? "pl" : "en";
            const viewKey = isMobile ? "mobile" : "desktop";

            const dayLabel = pluralize(days, labels.day[langKey]);
            const hourLabel = pluralize(hours, labels.hour[viewKey][langKey]);
            const minuteLabel = pluralize(minutes, labels.minute[viewKey][langKey]);
            const secondLabel = pluralize(seconds, labels.second[viewKey][langKey]);

            animateDigits(timerEl.querySelector(".days .digits"), days);
            animateDigits(timerEl.querySelector(".hours .digits"), hours);
            animateDigits(timerEl.querySelector(".minutes .digits"), minutes);
            if (showSeconds) {
                animateDigits(timerEl.querySelector(".seconds .digits"), seconds);
            }

            timerEl.querySelector(".days .label").textContent = dayLabel;
            timerEl.querySelector(".hours .label").textContent = hourLabel;
            timerEl.querySelector(".minutes .label").textContent = minuteLabel;
            if (showSeconds) timerEl.querySelector(".seconds .label").textContent = secondLabel;
        }

        updateCountdown();
        setInterval(updateCountdown, 1000);
    });
});
