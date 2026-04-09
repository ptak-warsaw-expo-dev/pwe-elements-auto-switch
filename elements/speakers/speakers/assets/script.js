document.addEventListener("DOMContentLoaded", () => {
    const speakers = document.querySelectorAll(".pwe-speakers__item");

    speakers.forEach((speaker) => {
        const btn = speaker.querySelector(".pwe-speakers__item-bio-button");
        const img = speaker.querySelector(".pwe-speakers__speaker-img img");
        const companyImg = speaker.querySelector(".pwe-speakers__company-img img");
        const name = speaker.querySelector(".pwe-speakers__item-name");
        const position = speaker.querySelector(".pwe-speakers__item-position");
        const company = speaker.querySelector(".pwe-speakers__item-company");
        const text = speaker.querySelector(".pwe-speakers__item-desc"); 
        
        if (btn) {
            btn.addEventListener("click", function() {
                const modalDiv = document.createElement("div");
                modalDiv.className = "pwe-speakers__speaker-modal";
                modalDiv.innerHTML = `
                    <div class="pwe-speakers__modal-speaker-content">
                        <span class="pwe-speakers__modal-speaker-close">&times;</span>
                        <div class="pwe-speakers__modal-speaker-image-container">
                            <img class="pwe-speakers__modal-speaker-image" src="${img.src}" alt="Speaker Image (${name.innerHTML})">
                        </div>
                        <div class="pwe-speakers__modal-speaker-text-container">
                            <div class="pwe-speakers__modal-speaker-text-container-wrapper">
                                <div class="pwe-speakers__modal-speaker-text-container-column info">
                                    <h5 class="pwe-speakers__modal-speaker-name">${name.innerHTML}</h5>
                                    <p class="pwe-speakers__modal-speaker-position">${position.innerHTML}</p>
                                    <p class="pwe-speakers__modal-speaker-company">${company.innerHTML}</p>
                                </div>
                                <div class="pwe-speakers__modal-speaker-text-container-column logo">
                                    <img class="pwe-speakers__modal-speaker-company-image" src="${companyImg.src}" alt="Speaker Company (${company.innerHTML})" onerror="this.onerror=null; this.style.display=\'none\';">
                                </div>
                            </div>
                            <div class="pwe-speaker-modal-desc">${text.innerHTML}</div>
                        </div>
                    </div>
                `;
                
                document.body.appendChild(modalDiv);
                requestAnimationFrame(() => {
                    modalDiv.classList.add("is-visible");
                });
                disableScroll();

                // Close modal
                modalDiv.querySelector(".pwe-speakers__modal-speaker-close").addEventListener("click", function() {
                    modalDiv.classList.remove("is-visible");
                    setTimeout(() => {
                        modalDiv.remove();
                        enableScroll();
                    }, 300);
                });

                modalDiv.addEventListener("click", function(event) {
                    if (event.target === modalDiv) {
                        modalDiv.classList.remove("is-visible");
                        setTimeout(() => {
                            modalDiv.remove();
                            enableScroll();
                        }, 300);
                    }
                });
            });
        }
    });

    // Functions to turn scrolling off and on
    function disableScroll() {
        document.body.style.overflow = "hidden";
        document.documentElement.style.overflow = "hidden";
    }
    function enableScroll() {
        document.body.style.overflow = "";
        document.documentElement.style.overflow = "";
    }

});