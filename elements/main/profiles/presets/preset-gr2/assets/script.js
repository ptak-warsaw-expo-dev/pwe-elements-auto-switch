// document.addEventListener("DOMContentLoaded", function() {
//     // "pl-PL" -> "pl"
//     let lang = (document.documentElement.lang || "pl").substring(0,2).toLowerCase();
//     if (!["pl","en"].includes(lang)) lang = "pl";

//     const cards = document.querySelectorAll(".pwe-profiles__card");

//     const texts = {
//         pl: { more: "Pokaż więcej", less: "" },
//         en: { more: "See more", less: "" }
//     };

//     cards.forEach(card => {
//         const button = card.querySelector(".pwe-profiles__show-more-btn");
//         if (!button) return;

//         const label = button.querySelector("span:first-child");

//         card.addEventListener("mouseenter", function() {
//             label.textContent = texts[lang].less;
//         });

//         card.addEventListener("mouseleave", function() {
//             label.textContent = texts[lang].more;
//         });
//     });
// });