(function () {


    let wrapper = null;

    document.addEventListener("DOMContentLoaded", function () {
        wrapper = document.getElementById("pweConfirmationVisitorsRegistration");
        if (!wrapper) {
            return;
        }

        initAutocomplete();

        const button = wrapper.querySelector("#pweSendStepTwo");
        if (button) {
            button.addEventListener("click", updateGravityForm);
        }
    });

    function initAutocomplete() {
        const streetInput = wrapper.querySelector("#street");

        console.log(streetInput);

        if (!streetInput || typeof google === "undefined") {
            return;
        }

        const autocomplete = new google.maps.places.Autocomplete(
            streetInput,
            {
                types: ["address"],
                componentRestrictions: {
                    country: "PL"
                }
            }
        );

        autocomplete.addListener("place_changed", function () {
            const place = autocomplete.getPlace();
            if (!place.address_components) {
                return;
            }



            let street = "";
            let house = "";
            let apartment = "";
            let city = "";
            let postCode = "";

            place.address_components.forEach(function (component) {
                if (component.types.includes("route")) {
                    street = component.long_name;
                } else if (component.types.includes("street_number")) {
                    house = component.long_name;
                } else if (component.types.includes("postal_code")) {
                    postCode = component.long_name;
                } else if (component.types.includes("locality")) {
                    city = component.long_name;
                } else if (component.types.includes("subpremise")) {
                    apartment = component.long_name;
                }
            });

            wrapper.querySelector("#street").value = street;
            wrapper.querySelector("#house").value = house;
            wrapper.querySelector("#local").value = apartment;
            wrapper.querySelector("#city").value = city;

            if (postCode.includes("-")) {
                let parts = postCode.split("-");
                wrapper.querySelector("#post1").value = parts[0];
                wrapper.querySelector("#post2").value = parts[1];
            }
        });
    }

    function updateGravityForm() {
        clearMessage();

        const requiredFields = ["name", "street", "house", "post1", "post2", "city"];
        let hasError = false;

        requiredFields.forEach(function (id) {
            const field = wrapper.querySelector("#" + id);

            if (!field || !field.value.trim()) {
                field.classList.add("error-border");
                hasError = true;
            } else {
                field.classList.remove("error-border");
            }
        });

        if (hasError) {
            showMessage("Wszystkie pola są wymagane!");
            return;
        }

        const postCode = wrapper.querySelector("#post1").value.trim() + "-" + wrapper.querySelector("#post2").value.trim();
        const postPattern = /^\d{2}-\d{3}$/;

        if (!postPattern.test(postCode)) {
            wrapper.querySelector("#post1").classList.add("error-border");
            wrapper.querySelector("#post2").classList.add("error-border");
            showMessage("Niepoprawny format kodu pocztowego (XX-XXX).");
            return;
        }

        const formId = wrapper.dataset.formId;
        const data = new URLSearchParams();

        data.append("action", "update_registration_address");
        data.append("form_id", formId);
        data.append("name", wrapper.querySelector("#name").value.trim());
        data.append("street", wrapper.querySelector("#street").value.trim());
        data.append("house", wrapper.querySelector("#house").value.trim());
        data.append("apartment", wrapper.querySelector("#local").value.trim());
        data.append("post", postCode);
        data.append("city", wrapper.querySelector("#city").value.trim());

        const ajaxUrl = window.ajaxurl || "/wp-admin/admin-ajax.php";
        const button = wrapper.querySelector("#pweSendStepTwo");

        if (button) {
            button.disabled = true;
        }

        fetch(ajaxUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: data
        })
        .then(response => response.json())
        .then(function (result) {
            if (result.success) {
                showSuccess();
            } else {
                showMessage(result.data ? result.data.message : (result.message || "Wystąpił błąd."));
                if (button) {
                    button.disabled = false;
                }
            }
        })
        .catch(function (error) {
            console.error(error);
            showMessage("Wystąpił problem z aktualizacją.");
            if (button) {
                button.disabled = false;
            }
        });
    }

    function showSuccess() {
        const wrapper = document.getElementById("pweConfirmationVisitorsRegistration");
        if (!wrapper) return;

        // 1. Dodajemy klasę is-complete na głównym kontenerze (zgodnie z CSS)
        wrapper.classList.add("is-complete");

        // 2. Obsługa sekcji przed i po wysłaniu formularza
        const before = wrapper.querySelector(".pwe-confirmation-visitors-registration__before-submit");
        const success = wrapper.querySelector(".pwe-confirmation-visitors-registration__success");

        if (before) {
            before.style.display = "none";
        }
        if (success) {
            success.style.display = "flex";
        }

        const displayBefore = wrapper.querySelectorAll(".pwe-confirmation-visitors-registration__display-befor-subbmit");
        displayBefore.forEach(el => {
            el.style.display = "none";
        });

        const displayAfter = wrapper.querySelectorAll(".pwe-confirmation-visitors-registration__display-after-subbmit");
        displayAfter.forEach(el => {
            el.style.display = "block";
        });
    }

    function clearMessage() {
        const message = wrapper.querySelector("#statusMessage");
        if (message) {
            message.innerHTML = "";
            message.classList.remove("error");
        }
    }

    function showMessage(text) {
        const message = wrapper.querySelector("#statusMessage");
        if (message) {
            message.innerHTML = text;
            message.classList.add("error");
        }
    }
})();