document.addEventListener("DOMContentLoaded", function () {
  const counters = document.querySelectorAll(".pwe-statistics__tile-number, .pwe-statistics__tile-percent");
  const speed = 100;

  const animateCount = (el) => {
    const target = +el.getAttribute("data-target");
    const increment = Math.max(1, Math.ceil(target / speed));
    const suffix = el.dataset.suffix || ""; // może zawierać HTML
    let value = 0;

    const update = () => {
      value += increment;
      if (value < target) {
        el.innerHTML = value + suffix;
        requestAnimationFrame(update);
      } else {
        el.innerHTML = target + suffix;
      }
    };
    update();
  };

  const observer = new IntersectionObserver((entries, obs) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        animateCount(e.target);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.5 });

  counters.forEach((c) => observer.observe(c));
});
