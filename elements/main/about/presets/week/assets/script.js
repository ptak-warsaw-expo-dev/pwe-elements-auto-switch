document.querySelectorAll(".event-about__header").forEach(header => {
    header.addEventListener("click", () => {
        const targetId = header.dataset.target;

        document.querySelectorAll(".event-about__content").forEach(content => {
            content.classList.remove("is-active");
        });

        document.querySelectorAll(".event-about__header").forEach(h => {
            h.classList.remove("is-active");
        });

        header.classList.add("is-active");
        document.getElementById(targetId).classList.add("is-active");
    });
});