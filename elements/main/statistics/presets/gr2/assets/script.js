function animateCount(element) {
    const targetValue = parseInt(element.getAttribute("data-count"), 10);
    const duration = 3000;

    const startTime = performance.now();
    const update = (currentTime) => {
        const elapsedTime = currentTime - startTime;
        const progress = Math.min(elapsedTime / duration, 1);
        const currentValue = Math.floor(progress * targetValue);

        element.textContent = currentValue;

        if (progress < 1) {
            requestAnimationFrame(update);
        }
    };
    requestAnimationFrame(update);
}

function animateBars(sectionElement) {
    const animationSpeed = parseInt(sectionElement.dataset.speed, 6);

    const bars = sectionElement.querySelectorAll(".pwe-statistics__map-bar-number-item");
    bars.forEach(bar => {
        const percentage = parseFloat(bar.getAttribute("data-count"));

        // Default for top level
        targetWidth = percentage + "%";
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
    const countUpElements = document.querySelectorAll(".countup");
    const barSections = document.querySelectorAll(".pwe-statistics__map-bar");

    const observer = new IntersectionObserver(
        (entries, observerInstance) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = entry.target;

                    if (target.classList.contains("countup")) {
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