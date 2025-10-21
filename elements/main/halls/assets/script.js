const halls = document.getElementById("pweHalls");

const allItems = JSON.parse(halls.dataset.allItems);
const activeItems = JSON.parse(halls.dataset.activeItems);

const allItemsObject = [];
const allActiveItemsObject = [];

const addActiveClassToFullObject = () => {
    let activeItemsFull = [];
    activeItems.forEach(item => {
        if (/^[A-Z]$/.test(item.id)) {
            activeItemsFull.push({
                id: item.id,
                color: item.color
            });
        }
    });

    // Iterate over active elements (full halls)
    activeItemsFull.forEach(item => {
        const fullObject = document.querySelector(`#${item.id}`);

        if (fullObject) {
            // Adding the "active" class to the hall
            fullObject.classList.add("active");

            // Find all elements with class "pwe-halls__element-color" in the active element
            const fullObjectColors = fullObject.querySelectorAll(".pwe-halls__element-color");
            fullObjectColors.forEach(colorElement => {
                // Set the color for the active element
                colorElement.style.fill = item.color;
            });
        }

        allActiveItemsObject.push({
            id: fullObject.id
        });
    });
}

addActiveClassToFullObject();


const addActiveClassToHalfObject = () => {
    let activeItemsQuarter = [];
    activeItems.forEach(item => {
        if (/^[A-Z]\d$/.test(item.id)) {
            activeItemsQuarter.push({
                id: item.id,
                color: item.color
            });
        }
    });

    // Iterate over active elements (half halls)
    const combinedId = [];
    activeItemsQuarter.forEach((item1, index) => {
        activeItemsQuarter.slice(index + 1).forEach(item2 => {

            const combinedIds = [
                `${item1.id}_${item2.id}`,
                `${item2.id}_${item1.id}`
            ];

            combinedIds.forEach(id => {
                const combinedElement = document.getElementById(id);
                if (combinedElement) {
                    // Sprawdzamy rodzica z klasą pwe-halls__element-full
                    const parentFullElement = combinedElement.closest(".pwe-halls__element.full");
                    if (parentFullElement && !parentFullElement.classList.contains("active")) {
                        combinedElement.classList.add("active");

                        // Znalezienie wszystkich elementów z klasą "pwe-halls__element-color" w aktywnym elemencie
                        const fullObjectColors = combinedElement.querySelectorAll(".pwe-halls__element-color");
                        fullObjectColors.forEach(colorElement => {
                            // Ustawienie koloru dla aktywnego elementu
                            colorElement.style.fill = item1.color;
                        });
                    }

                    allActiveItemsObject.push({
                        id: combinedElement.id
                    });
                }
            });
        });
    });
}

addActiveClassToHalfObject();

const addActiveClassToQuarterObject = () => {
    let activeItemsQuarter = [];
    activeItems.forEach(item => {
        if (/^[A-Z]\d$/.test(item.id)) {
            activeItemsQuarter.push({
                id: item.id,
                color: item.color
            });
        }
    });

    // Iterate over active elements (quarter halls)
    activeItemsQuarter.forEach(item => {
        const quarterObject = document.querySelector(`#${item.id}`);
        if (quarterObject) {
            // Check the parent with class pwe-halls__element-full
            const parentFullElement = quarterObject.closest(".pwe-halls__element.half");
            if (parentFullElement && !parentFullElement.classList.contains("active")) {
                quarterObject.classList.add("active");

                // Find all elements with class "pwe-halls__element-color" in the active element
                const quarterObjectColors = quarterObject.querySelectorAll(".pwe-halls__element-color");
                quarterObjectColors.forEach(colorElement => {
                    // Ustawienie koloru dla aktywnego elementu
                    colorElement.style.fill = item.color;
                });
            }

            allActiveItemsObject.push({
                id: quarterObject.id
            });
        }
    });
}

addActiveClassToQuarterObject();

const addLogoToFullObject = () => {
    let allItemsFull = [];
    allItems.forEach(item => {
        if (/^[A-Z]$/.test(item.id)) {
            allItemsFull.push({
                id: item.id,
                domain: item.domain,
                color: item.color
            });
        }
    });

    // Iterate over all elements (full halls)
    allItemsFull.forEach(item => {
        const fullObject = document.querySelector(`#${item.id}`);

        if (fullObject) {
            
            const fullObjectsLogotypes = fullObject.querySelectorAll(".pwe-halls__element-logo-link.full");
            fullObjectsLogotypes.forEach(logoElement => {
                const logo = logoElement.querySelector(".pwe-halls__element-logo");
                // if (fullObject.classList.contains("active")) {
                    // Set white logo for active element
                    logoElement.setAttribute("href", `https://${item.domain}`);
                    logo.setAttribute("href", `https://${item.domain}/doc/logo.webp`);
                // } else {
                //     // Set a colored logo for the active element
                //     logoElement.setAttribute("href", `https://${item.domain}`);
                //     logo.setAttribute("href", `https://${item.domain}/doc/logo-color.webp`);
                // }
            });

            const fullObjectsColors = fullObject.querySelectorAll(".pwe-halls__element-color");
            fullObjectsColors.forEach(colorElement => {
                // Set the color for the element
                colorElement.style.fill = item.color;
            });

            allItemsObject.push({
                id: fullObject.id
            });
        }
    });
}

addLogoToFullObject();

const addLogoToHalfObject = () => {
    let allItemsQuarter = [];
    allItems.forEach(item => {
        if (/^[A-Z]\d$/.test(item.id)) {
            allItemsQuarter.push({
                id: item.id,
                domain: item.domain,
                color: item.color
            });
        }
    });

    // Iterate over all elements (half halls)
    const combinedId = [];
    allItemsQuarter.forEach((item1, index) => {
        allItemsQuarter.slice(index + 1).forEach(item2 => {
            // Check if domains are the same
            if (item1.domain === item2.domain) {
                const combinedIds = [
                    `${item1.id}_${item2.id}`,
                    `${item2.id}_${item1.id}`
                ];

                combinedIds.forEach(id => {
                    const combinedElement = document.getElementById(id);
                    if (combinedElement) {

                        const halfObjectsLogotypes = combinedElement.querySelectorAll(".pwe-halls__element-favicon-link.half");
                        halfObjectsLogotypes.forEach(logoElement => {
                            const logo = logoElement.querySelector(".pwe-halls__element-favicon");
                            // if (combinedElement.classList.contains("active")) {
                                // Set white logo for active element
                                logoElement.setAttribute("href", `https://${item1.domain}`);
                                logo.setAttribute("href", `https://${item1.domain}/doc/favicon.webp`);
                            // } else {
                            //     // Set a colored logo for the active element
                            //     logoElement.setAttribute("href", `https://${item1.domain}`);
                            //     logo.setAttribute("href", `https://${item1.domain}/doc/favicon-color.webp`);
                            // }
                        });

                        const halfObjectsColors = combinedElement.querySelectorAll(".pwe-halls__element-color");
                        halfObjectsColors.forEach(colorElement => {
                            // Set the color for the element
                            colorElement.style.fill = item1.color;
                        });

                        allItemsObject.push({
                            id: combinedElement.id
                        });
                    }
                });
            }
        });
    });
}

addLogoToHalfObject();

const addLogoToQuarterObject = () => {
    let allItemsQuarter = [];
    allItems.forEach(item => {
        if (/^[A-Z]\d$/.test(item.id)) {
            allItemsQuarter.push({
                id: item.id,
                domain: item.domain,
                color: item.color
            });
        }
    });

    // Iterate over all elements (quarter halls)
    allItemsQuarter.forEach(item => {
        const quarterObject = document.querySelector(`#${item.id}`);

        if (quarterObject) {
            
            const quarterObjectsLogotypes = quarterObject.querySelectorAll(".pwe-halls__element-logo-link.quarter");
            quarterObjectsLogotypes.forEach(logoElement => {
                const logo = logoElement.querySelector(".pwe-halls__element-logo");
                // if (quarterObject.classList.contains("active")) {
                    // Set white logo for active element
                    logoElement.setAttribute("href", `https://${item.domain}`);
                    logo.setAttribute("href", `https://${item.domain}/doc/logo.webp`);
                // } else {
                //     // Set a colored logo for the active element
                //     logoElement.setAttribute("href", `https://${item.domain}`);
                //     logo.setAttribute("href", `https://${item.domain}/doc/logo-color.webp`);
                // }
            });

            const quarterObjectColors = quarterObject.querySelectorAll(".pwe-halls__element-color");
            quarterObjectColors.forEach(colorElement => {
                // Set the color for the element
                colorElement.style.fill = item.color;
            });

            allItemsObject.push({
                id: quarterObject.id
            });
        }
    });
}

addLogoToQuarterObject();

const filterCombinedIds = (items) => {
    // Extract all composite ids
    const combinedIds = items
        .filter(item => item.id.includes("_")) // Filtruje elementy zawierające "_"
        .map(item => item.id.split("_"));    // Dzieli je na poszczególne identyfikatory

    // Convert array of composite ids into single ids
    const idsToRemove = new Set(combinedIds.flat());

    // Filter the array by removing objects with IDs in `idsToRemove`
    return items.filter(item => !idsToRemove.has(item.id));
};

const filteredAllItems = filterCombinedIds(allItemsObject);
const filteredActiveItems = filterCombinedIds(allActiveItemsObject);

// Iterate over all elements that match JSON
filteredAllItems.forEach(item => {
    const svgElement = document.querySelector(`#${item.id}`);
    if (svgElement) {
        // Check if the element is active
        const isActive = filteredActiveItems.some(activeItem => activeItem.id === item.id);
        if (!isActive) {
            // Dodanie klasy "unactive"
            svgElement.classList.add("unactive");
        }
    }
});

// if (window.innerWidth >= 960) {
//     const svgHale = document.querySelector("#pweHallsSvg");
//     const zoomFactor = 2;

//     if (svgHale) {
//         // Get original viewBox
//         const originalViewBox = svgHale.getAttribute("viewBox");
//         const viewBoxValues = originalViewBox.split(" ").map(Number);

//         svgHale.addEventListener("mousemove", (e) => {
//             const rect = svgHale.getBoundingClientRect();
//             const x = e.clientX - rect.left;
//             const y = e.clientY - rect.top;

//             // Calculating a new viewBox
//             const viewBoxX = viewBoxValues[0] + (x / rect.width) * viewBoxValues[2] - (viewBoxValues[2] / zoomFactor) / 2;
//             const viewBoxY = viewBoxValues[1] + (y / rect.height) * viewBoxValues[3] - (viewBoxValues[3] / zoomFactor) / 2;
//             const viewBoxWidth = viewBoxValues[2] / zoomFactor;
//             const viewBoxHeight = viewBoxValues[3] / zoomFactor;

//             svgHale.setAttribute("viewBox", `${viewBoxX} ${viewBoxY} ${viewBoxWidth} ${viewBoxHeight}`);
//         });

//         svgHale.addEventListener("mouseleave", () => {
//             svgHale.setAttribute("viewBox", originalViewBox); // Resetujemy viewBox do oryginalnego
//         });
//     }
// }