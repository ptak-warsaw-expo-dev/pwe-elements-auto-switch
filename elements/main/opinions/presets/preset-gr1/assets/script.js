// jQuery(function($) {
//     // const swiper = new Swiper("#pweElementsAutoSwitch .pwe-opinions__items", {
//     //     slidesPerView: 2,
//     //     spaceBetween: 20,
//     //     grabCursor: true,
//     //     loop: true,
//     //     autoplay: {
//     //         delay: 3000,
//     //         disableOnInteraction: false,
//     //         pauseOnMouseEnter: true,
//     //     },
//     //     scrollbar: {
//     //         el: "#pweElementsAutoSwitch .pwe-opinions .swiper-scrollbar",
//     //         draggable: false,
//     //     },
//     //     breakpoints: {
//     //         0: {
//     //             slidesPerView: 1
//     //         },
//     //         960: {
//     //             slidesPerView: 2
//     //         }
//     //     }
//     // });

//     // Function to set equal height
//     function setEqualHeight() {
//         let maxHeight = 0;

//         // Reset the heights before calculations
//         $("#pweElementsAutoSwitch .pwe-opinions__item").css("height", "auto");

//         // Calculate the maximum height
//         $("#pweElementsAutoSwitch .pwe-opinions__item").each(function() {
//             const thisHeight = $(this).outerHeight();
//             if (thisHeight > maxHeight) {
//                 maxHeight = thisHeight;
//             }
//         });

//         // Set the same height for all
//         $("#pweElementsAutoSwitch .pwe-opinions__item").css("minHeight", maxHeight);
//     }

//     // Call the function after loading the slider
//     $("#pweElementsAutoSwitch .pwe-opinions__items").on("init", function() {
//         setEqualHeight();
//     });

//     // Call the function when changing the slide
//     $("#pweElementsAutoSwitch .pwe-opinions__items").on("afterChange", function() {
//         setEqualHeight();
//     });

//     // Call the function at the beginning
//     setEqualHeight();

//     $("#pweElementsAutoSwitch #pweOpinions").css("visibility", "visible").animate({ opacity: 1 }, 500);
// });