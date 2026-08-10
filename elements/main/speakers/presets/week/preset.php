<?php

$speakers_limited = array_slice($speakers, 0, 12);

$output = '
<div id="pweSpeakers" class="pwe-speakers">
    <div class="pwe-speakers__wrapper">
        <div class="pwe-speakers__title">
            <h4 class="pwe-speakers__heading pwe-main-title">' . PWE_Functions::multi_translation('title') . '</h4>
            <div class="swiper-buttons-arrows">
                    <div class="swiper-button-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path d="M16 5l-11 7 11 7z"/>
                        </svg>

                    </div>
                    <div class="swiper-button-next">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
            </div>
        </div>

        <div class="swiper pwe-speakers__items">
            <div class="swiper-wrapper">';
                foreach ($speakers_limited as $speaker) {
                    $color_class = !empty($speaker['color']) ? 'pwe-speakers__item--' . strtolower($speaker['color']) : 'pwe-speakers__item--purple';

                    // Oczyszczanie Bio z niepotrzebnych tagów i usuwanie niepotrzebnych spacji
                    $clean_bio = !empty($speaker['bio']) ? trim(strip_tags($speaker['bio'])) : '';

                    $output .= '
                    <div class="pwe-speakers__item swiper-slide ' . $color_class . '">
                        <div class="pwe-speakers__speaker-img">
                            <img data-no-lazy="1" src="'. esc_url($speaker['img']) .'" onerror="this.onerror=null; this.style.display=\'none\';" alt="Speaker photo"/>';
                            if(!empty($speaker['logo'])){
                                $output .= '
                                <div class="pwe-speakers__company-img">
                                    <img data-no-lazy="1" src="'. esc_url($speaker['logo']) .'" alt="Company logo"/>
                                </div>';
                            }

                        $output .='
                        </div>
                        <div class="pwe-speakers__item-text">
                            <h3 class="pwe-speakers__item-name">'. esc_html($speaker['name']) .'</h3>
                            <p class="pwe-speakers__item-position">'. esc_html($speaker['position']) .'</p>
                            <div class="pwe-speakers__item-company-wrapper">
                                <p class="pwe-speakers__item-company">'. esc_html($speaker['company']) .'</p>
                            </div>';

                            // Guzik Bio pokazuje się tylko, gdy bio nie jest puste
                            if (!empty($clean_bio)) {
                                $output .= '
                                <button type="button" class="pwe-speakers__bio-btn js-open-speaker-bio"
                                    data-name="'. esc_attr($speaker['name']) .'"
                                    data-position="'. esc_attr($speaker['position']) .'"
                                    data-company="'. esc_attr($speaker['company']) .'"
                                    data-img="'. esc_url($speaker['img']) .'"
                                    data-bio="'. esc_attr($clean_bio) .'">
                                    Bio
                                </button>';
                            }

                        $output .= '
                        </div>
                    </div>';
                }
            $output .= '
            </div>
            <div class="swiper-nav">
                <div class="swiper-dots" aria-label="Slider navigation" role="tablist"></div>
            </div>
        </div>';

        if (count($speakers) > 6) {
            $output .= '
            <div class="pwe-speakers__bottom">
                <div class="pwe-speakers__btn">
                    <a class="pwe-main-btn--primary" href="' . PWE_Functions::languageChecker('/prelegenci/', '/en/speakers/') . '">' . PWE_Functions::multi_translation('all_speakers_btn') . '</a>
                </div>
            </div>';
        }

    $output .= '
    </div>
</div>

<!-- Modal / Popup z biogramem -->
<div id="pweSpeakerBioModal" class="pwe-speaker-modal" aria-hidden="true">
    <div class="pwe-speaker-modal__overlay" data-close-modal></div>
    <div class="pwe-speaker-modal__content">
        <button type="button" class="pwe-speaker-modal__close" data-close-modal>&times;</button>
        <div class="pwe-speaker-modal__body">
            <div class="pwe-speaker-modal__header">
                <img id="pweModalImg" src="" alt="Speaker photo" class="pwe-speaker-modal__img"/>
                <div class="pwe-speaker-modal__info">
                    <h3 id="pweModalName" class="pwe-speaker-modal__name"></h3>
                    <p id="pweModalPosition" class="pwe-speaker-modal__position"></p>
                    <p id="pweModalCompany" class="pwe-speaker-modal__company"></p>
                </div>
            </div>
            <div id="pweModalBio" class="pwe-speaker-modal__text"></div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.getElementById("pweSpeakerBioModal");
    if (!modal) return;

    const modalImg = document.getElementById("pweModalImg");
    const modalName = document.getElementById("pweModalName");
    const modalPosition = document.getElementById("pweModalPosition");
    const modalCompany = document.getElementById("pweModalCompany");
    const modalBio = document.getElementById("pweModalBio");

    document.querySelectorAll(".js-open-speaker-bio").forEach(button => {
        button.addEventListener("click", function () {
            modalImg.src = this.dataset.img || "";
            modalImg.style.display = this.dataset.img ? "block" : "none";
            modalName.textContent = this.dataset.name || "";
            modalPosition.textContent = this.dataset.position || "";
            modalCompany.textContent = this.dataset.company || "";
            modalBio.textContent = this.dataset.bio || "";

            modal.classList.add("is-active");
            modal.setAttribute("aria-hidden", "false");
            document.body.style.overflow = "hidden"; // Blokada przewijania strony
        });
    });

    const closeModal = () => {
        modal.classList.remove("is-active");
        modal.setAttribute("aria-hidden", "true");
        document.body.style.overflow = "";
    };

    modal.querySelectorAll("[data-close-modal]").forEach(el => {
        el.addEventListener("click", closeModal);
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && modal.classList.contains("is-active")) {
            closeModal();
        }
    });
});
</script>';

$output .= PWE_Swiper::swiperScripts('#pweSpeakers', null, true, true);

return $output;