<?php
if (!defined('ABSPATH')) {
    exit;
}

$has_exhibitor_session = !empty($_SESSION['pwe_exhibitor_entry']['entry_id']);

$msg_required  = esc_js(PWE_Functions::multi_translation('required_fields') ?: 'Uzupełnij wszystkie wymagane pola');
$msg_success   = esc_js(PWE_Functions::multi_translation('confirm_text') ?: 'Dziękujemy, dane zostały zapisane');
$btn_text      = esc_js(PWE_Functions::multi_translation('generate_an_offer') ?: 'Wyślij');
$main_page_btn = esc_html(PWE_Functions::multi_translation('back_to_main_page') ?: 'Strona główna');
$back_link     = esc_url(PWE_Functions::multi_translation('back_link') ?: home_url());

$output = '
<div class="pwe-confirmation-exhibitors">
    <div class="pwe-confirmation-exhibitors__layout" id="pweForm">
        <div class="pwe-confirmation-exhibitors__content form-left">
            <div>
                <div class="pwe-confirmation-exhibitors__heading">
                    ' . do_shortcode(PWE_Functions::multi_translation('thank_you_for_registering')) . '
                </div>
            </div>
        </div>

        <div class="pwe-confirmation-exhibitors__form form">
            <div class="pwe-confirmation-exhibitors__intro display-before-submit">
                ' . do_shortcode(PWE_Functions::multi_translation('provide_additional_details')) . '
            </div>';

if ($has_exhibitor_session) {
    $output .= '
            <form id="exhibitorUpdateForm">
                <div class="gform_fields">
                    <div class="gfield">
                        <label for="exhibitor_name">' . esc_html(PWE_Functions::multi_translation('name')) . ' *</label>
                        <input type="text" id="exhibitor_name" required>
                    </div>
                    <div class="gfield">
                        <label for="exhibitor_nip">' . esc_html(PWE_Functions::multi_translation('tax')) . ' *</label>
                        <input type="text" id="exhibitor_nip" required>
                    </div>
                    <div class="gfield">
                        <label for="exhibitor_company">' . esc_html(PWE_Functions::multi_translation('company_desc')) . ' *</label>
                        <input type="text" id="exhibitor_company" required>
                    </div>
                    <div class="gfield exhibitor-area-field">
                        <label for="exhibitor_area">' . esc_html(PWE_Functions::multi_translation('area')) . ' *</label>
                        <input type="text" id="exhibitor_area" required>
                    </div>
                    <div id="exhibitorStatus" class="status-message"></div>
                </div>
                <div class="buttonSubmit">
                    <button type="button" id="exhibitorSubmit" class="button">
                    ' . $btn_text . '
                    </button>
                </div>
            </form>

            <div class="pwe-submitting-buttons display-after-submit">
                <a href="' . $back_link . '">
                    <button type="button" class="btn pwe-btn pwe_reg_exhibitor">' . $main_page_btn . '</button>
                </a>
            </div>';

    $ajax_url = admin_url('admin-ajax.php');
    $nonce    = wp_create_nonce('update_exhibitor_data_nonce');

    $output .= '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const button = document.querySelector("#exhibitorSubmit");
        if (!button) return;

        button.addEventListener("click", function() {
            const name = document.querySelector("#exhibitor_name").value.trim();
            const nip = document.querySelector("#exhibitor_nip").value.trim();
            const company = document.querySelector("#exhibitor_company").value.trim();
            const area = document.querySelector("#exhibitor_area").value.trim();
            const status = document.querySelector("#exhibitorStatus");

            if (!name || !nip || !company || !area) {
                status.classList.add("error");
                status.innerHTML = "' . $msg_required . '";
                return;
            }

            button.disabled = true;
            button.innerHTML = "Zapisywanie...";

            const formData = new FormData();
            formData.append("action", "update_exhibitor_data");
            formData.append("nonce", "' . $nonce . '");
            formData.append("name", name);
            formData.append("nip", nip);
            formData.append("company", company);
            formData.append("area", area);

            fetch("' . $ajax_url . '", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.querySelector("#exhibitorUpdateForm").innerHTML = `
                        <div class="gform_confirmation_message">' . $msg_success . '</div>
                    `;

                    const formContainer = document.querySelector(".pwe-confirmation-exhibitors__form");
                    if (formContainer) {
                        formContainer.classList.add("submitted");
                    }
                } else {
                    status.classList.add("error");
                    status.innerHTML = data.data.message || "Wystąpił błąd";
                    button.disabled = false;
                    button.innerHTML = "' . $btn_text . '";
                }
            })
            .catch(err => {
                console.error(err);
                status.classList.add("error");
                status.innerHTML = "Wystąpił błąd AJAX";
                button.disabled = false;
                button.innerHTML = "' . $btn_text . '";
            });
        });
    });
    </script>';
} else {
    $output .= $gravity_form;

    $output .= '
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var sessionCleared = false;

        function clearSessionViaAjax() {
            if (sessionCleared) return;
            sessionCleared = true;

            var formData = new FormData();
            formData.append("action", "clear_pwe_session");

            fetch("' . admin_url('admin-ajax.php') . '", {
                method: "POST",
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                console.log("PWE Session cleared successfully:", data);
            })
            .catch(function(err) {
                console.error("Error clearing PWE Session:", err);
            });
        }

        if (typeof jQuery !== "undefined") {
            jQuery(document).on("gform_confirmation_loaded", function(event, formId) {
                clearSessionViaAjax();
            });
        }

        var observer = new MutationObserver(function(mutations) {
            if (document.querySelector(".gform_confirmation_message, .gforms_confirmation_message")) {
                clearSessionViaAjax();
                observer.disconnect();
            }
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });

        if (document.querySelector(".gform_confirmation_message, .gforms_confirmation_message")) {
            clearSessionViaAjax();
        }
    });
    </script>';
}

$output .= '
        </div>

        <div class="pwe-confirmation-exhibitors__offer form-right">
            <img class="img-stand" src="/wp-content/plugins/pwe-media/media/zabudowa.webp" alt="zdjęcie przykładowej zabudowy"/>
            <h5>' . esc_html(PWE_Functions::multi_translation('dedicated_market_place')) . '</h5>
            <a class="pwe-link btn pwe-btn btn-stand" target="_blank" ' . PWE_Functions::multi_translation('see_the_offer') . '</a>
        </div>
    </div>
</div>';

$output .= '
<script>
document.addEventListener("DOMContentLoaded", function () {

    const targetContainer = document.querySelector(".input-area") || document.querySelector(".exhibitor-area-field");
    if (!targetContainer) return;

    const sliderContainer = document.createElement("div");
    sliderContainer.className = "input-range-container";
    sliderContainer.innerHTML = `
        <div class="input-range-wrapper">
            <p style="font-size:14px; font-weight:700; margin-top:18px;">' . PWE_Functions::multi_translation('exhibition_space') . '</p>
            <div class="input-range-inputs">
                <div class="input-range-track"></div>
                <input type="range" min="16" max="100" value="16" id="inputRange1">
                <input type="range" min="16" max="100" value="36" id="inputRange2">
            </div>
            <div class="input-range-values">
                <span class="input-range-value-label">' . PWE_Functions::multi_translation('from') . '</span>
                <div class="input-container">
                    <input type="number" min="0" max="999" value="16" id="inputRangeValue1">
                    <span class="unit-label">m²</span>
                </div>
                <span class="input-range-value-label">' . PWE_Functions::multi_translation('to') . '</span>
                <div class="input-container">
                    <input type="number" min="0" max="999" value="36" id="inputRangeValue2">
                    <span class="unit-label">m²</span>
                </div>
            </div>
        </div>
    `;

    targetContainer.insertAdjacentElement("afterend", sliderContainer);

    const sliderOne = document.getElementById("inputRange1");
    const sliderTwo = document.getElementById("inputRange2");
    const displayValOne = document.getElementById("inputRangeValue1");
    const displayValTwo = document.getElementById("inputRangeValue2");
    const sliderTrack = document.querySelector(".input-range-track");

    const minGap = 1;
    const sliderMaxValue = sliderOne ? parseInt(sliderOne.max) : 100;
    const sliderMinValue = sliderOne ? parseInt(sliderOne.min) : 16;

    function updateArea() {
        const areaContainerGF = document.getElementsByClassName("input-area")[0];
        if (areaContainerGF) {
            areaContainerGF.style = "display:none !important;";
            const areaInput = areaContainerGF.getElementsByTagName("input")[0];
            if (areaInput && displayValOne && displayValTwo) {
                areaInput.value = displayValOne.value + " - " + displayValTwo.value + " m²";
            }
        }

        const areaInputCustom = document.getElementById("exhibitor_area");
        if (areaInputCustom && displayValOne && displayValTwo) {
            areaInputCustom.value = displayValOne.value + " - " + displayValTwo.value + " m²";
        }
    }

    function fillColor() {
        if (!sliderOne || !sliderTwo || !sliderTrack) return;
        let percent1 = ((sliderOne.value - sliderMinValue) / (sliderMaxValue - sliderMinValue)) * 100;
        let percent2 = ((sliderTwo.value - sliderMinValue) / (sliderMaxValue - sliderMinValue)) * 100;
        sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}%, #007bff ${percent1}%, #007bff ${percent2}%, #dadae5 ${percent2}%)`;
    }

    function slideOne() {
        if (!sliderOne || !sliderTwo) return;
        if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) < minGap) {
            sliderOne.value = parseInt(sliderTwo.value) - minGap;
        }
        if (displayValOne) displayValOne.value = sliderOne.value;
        fillColor();
        updateArea();
    }

    function slideTwo() {
        if (!sliderOne || !sliderTwo) return;
        if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) < minGap) {
            sliderTwo.value = parseInt(sliderOne.value) + minGap;
        }
        if (displayValTwo) displayValTwo.value = sliderTwo.value;
        fillColor();
        updateArea();
    }

    function preventTyping(event) {
        if (event.key !== "ArrowUp" && event.key !== "ArrowDown" && event.key !== "Tab") {
            event.preventDefault();
        }
    }

    if (sliderOne) sliderOne.addEventListener("input", slideOne);
    if (sliderTwo) sliderTwo.addEventListener("input", slideTwo);

    if (displayValOne) {
        displayValOne.addEventListener("input", function () {
            let val = parseInt(displayValOne.value);
            if (isNaN(val) || val < sliderMinValue) val = sliderMinValue;
            if (val > parseInt(sliderTwo.value) - minGap) val = parseInt(sliderTwo.value) - minGap;
            sliderOne.value = val;
            slideOne();
        });
        displayValOne.addEventListener("keydown", preventTyping);
    }

    if (displayValTwo) {
        displayValTwo.addEventListener("input", function () {
            let val = parseInt(displayValTwo.value);
            if (isNaN(val) || val > sliderMaxValue) val = sliderMaxValue;
            if (val < parseInt(sliderOne.value) + minGap) val = parseInt(sliderOne.value) + minGap;
            sliderTwo.value = val;
            slideTwo();
        });
        displayValTwo.addEventListener("keydown", preventTyping);
    }

    slideOne();
    slideTwo();
});
</script>';

return $output;