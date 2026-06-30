function animateCount(element) {
    // Uniwersalne pobieranie wartości docelowej: najpierw sprawdza data-count, potem data-target
    const targetAttr = element.getAttribute("data-count") || element.getAttribute("data-target");
    const targetValue = parseInt(targetAttr, 10);

    // Jeśli wartość nie jest poprawną liczbą, przerywamy funkcję
    if (isNaN(targetValue)) return;

    // Pobieramy suffix (np. "+" lub " m²"), jeśli go nie ma - pusto
    const suffix = element.getAttribute("data-suffix") || "";
    const duration = 3000;

    const startTime = performance.now();
    const update = (currentTime) => {
        const elapsedTime = currentTime - startTime;
        const progress = Math.min(elapsedTime / duration, 1);
        const currentValue = Math.floor(progress * targetValue);

        // Doklejamy suffix do odliczanej wartości
        element.innerHTML = currentValue + suffix;

        if (progress < 1) {
            requestAnimationFrame(update);
        } else {
            // Na sam koniec upewniamy się, że wskoczyła pełna, finalna wartość z suffixem
            element.innerHTML = targetValue + suffix;
        }
    };
    requestAnimationFrame(update);
}

function animateBars(sectionElement) {
    const animationSpeed = parseInt(sectionElement.dataset.speed, 10) || 3000;

    const bars = sectionElement.querySelectorAll(".pwe-statistics__map-bar-number-item");
    bars.forEach(bar => {
        const percentage = parseFloat(bar.getAttribute("data-count"));
        if (isNaN(percentage)) return;

        bar.style.width = "0";

        let currentHeight = 0;
        const frameRate = 15;
        const totalFrames = animationSpeed / frameRate;
        const heightIncrement = percentage / totalFrames;

        const interval = setInterval(() => {
            currentHeight += heightIncrement;

            if (currentHeight >= percentage) {
                currentHeight = percentage;
                clearInterval(interval);
            }

            bar.style.width = currentHeight + "%";

        }, frameRate);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    // Szukamy elementów posiadających EITHER klasę .countup LUB .pwe-statistics__tile-number
    const countUpElements = document.querySelectorAll(".countup, .pwe-statistics__tile-number");
    const barSections = document.querySelectorAll(".pwe-statistics__map-bar");

    const observer = new IntersectionObserver(
        (entries, observerInstance) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;

                    // Sprawdzamy, czy element ma jedną z poszukiwanych klas dla liczników
                    if (target.classList.contains("countup") || target.classList.contains("pwe-statistics__tile-number")) {
                        animateCount(target);
                    } else if (target.classList.contains("pwe-statistics__map-bar") && !target.dataset.animated) {
                        animateBars(target);
                        target.dataset.animated = true;
                    }

                    observerInstance.unobserve(target);
                }
            });
        },
        {
            threshold: 0.1
        }
    );

    countUpElements.forEach(element => observer.observe(element));
    barSections.forEach(section => {
        section.dataset.speed = "3000";
        observer.observe(section);
    });
});