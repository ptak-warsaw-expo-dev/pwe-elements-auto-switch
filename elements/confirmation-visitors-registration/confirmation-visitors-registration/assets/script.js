(function () {
    let wrapper = null;
    let googlePlacesFailed = false;

    /*
     * Google wywołuje tę funkcję globalnie przy błędach autoryzacji,
     * np. RefererNotAllowedMapError.
     */
    window.gm_authFailure = function () {
        googlePlacesFailed = true;

        restoreStreetInput();

        /*
         * Google może zmienić klasy i placeholder chwilę po zgłoszeniu błędu.
         * Robimy kilka skończonych prób, bez MutationObservera i bez pętli.
         */
        setTimeout(restoreStreetInput, 100);
        setTimeout(restoreStreetInput, 500);
        setTimeout(restoreStreetInput, 1500);
    };

    document.addEventListener("DOMContentLoaded", function () {
        wrapper = document.getElementById(
            "pweConfirmationVisitorsRegistration"
        );

        if (!wrapper) {
            return;
        }

        /*
         * Pole od początku ma pozwalać na ręczne wpisywanie.
         */
        restoreStreetInput();

        initAutocomplete();

        const button = wrapper.querySelector("#pweSendStepTwo");

        if (button) {
            button.addEventListener("click", updateGravityForm);
        }
    });

    function restoreStreetInput() {
        const currentWrapper =
            wrapper ||
            document.getElementById(
                "pweConfirmationVisitorsRegistration"
            );

        if (!currentWrapper) {
            return;
        }

        const streetInput = currentWrapper.querySelector("#street");

        if (!streetInput) {
            return;
        }

        /*
         * Pole zawsze pozostaje aktywne.
         */
        streetInput.disabled = false;
        streetInput.readOnly = false;

        streetInput.removeAttribute("disabled");
        streetInput.removeAttribute("readonly");

        streetInput.setAttribute(
            "autocomplete",
            "street-address"
        );

        /*
         * Elementy dodawane przez Google przy błędzie.
         */
        streetInput.classList.remove(
            "gm-err-autocomplete",
            "pac-target-input"
        );

        streetInput.style.removeProperty("background-image");
        streetInput.style.removeProperty("background-position");
        streetInput.style.removeProperty("background-repeat");
        streetInput.style.removeProperty("background-size");

        const errorPlaceholder =
            streetInput.placeholder === "Ups... Coś poszło nie tak." ||
            streetInput.placeholder === "Oops! Something went wrong.";

        if (googlePlacesFailed || errorPlaceholder) {
            streetInput.placeholder = "Ulica";
        }
    }

    function initAutocomplete() {
        const streetInput = wrapper.querySelector("#street");

        if (!streetInput) {
            return;
        }

        /*
         * Sprawdzamy cały obiekt, a nie tylko samo window.google.
         */
        if (
            typeof window.google === "undefined" ||
            !window.google.maps ||
            !window.google.maps.places ||
            typeof window.google.maps.places.Autocomplete !== "function"
        ) {
            restoreStreetInput();
            return;
        }

        try {
            const autocomplete =
                new window.google.maps.places.Autocomplete(
                    streetInput,
                    {
                        types: ["address"],
                        componentRestrictions: {
                            country: "PL"
                        }
                    }
                );

            autocomplete.addListener(
                "place_changed",
                function () {
                    /*
                     * Przy błędzie API nie korzystamy z danych Google.
                     * Użytkownik nadal może wpisać adres ręcznie.
                     */
                    if (googlePlacesFailed) {
                        restoreStreetInput();
                        return;
                    }

                    const place = autocomplete.getPlace();

                    if (
                        !place ||
                        !place.address_components
                    ) {
                        return;
                    }

                    let street = "";
                    let house = "";
                    let apartment = "";
                    let city = "";
                    let postCode = "";

                    place.address_components.forEach(
                        function (component) {
                            if (
                                component.types.includes("route")
                            ) {
                                street = component.long_name;
                            } else if (
                                component.types.includes(
                                    "street_number"
                                )
                            ) {
                                house = component.long_name;
                            } else if (
                                component.types.includes(
                                    "postal_code"
                                )
                            ) {
                                postCode = component.long_name;
                            } else if (
                                component.types.includes("locality")
                            ) {
                                city = component.long_name;
                            } else if (
                                component.types.includes("subpremise")
                            ) {
                                apartment = component.long_name;
                            }
                        }
                    );

                    const houseInput =
                        wrapper.querySelector("#house");

                    const apartmentInput =
                        wrapper.querySelector("#local");

                    const cityInput =
                        wrapper.querySelector("#city");

                    const post1Input =
                        wrapper.querySelector("#post1");

                    const post2Input =
                        wrapper.querySelector("#post2");

                    /*
                     * Nie nadpisujemy ręcznie wpisanej ulicy pustą wartością.
                     */
                    if (street) {
                        streetInput.value = street;
                    }

                    if (houseInput && house) {
                        houseInput.value = house;
                    }

                    if (apartmentInput && apartment) {
                        apartmentInput.value = apartment;
                    }

                    if (cityInput && city) {
                        cityInput.value = city;
                    }

                    if (
                        postCode.includes("-") &&
                        post1Input &&
                        post2Input
                    ) {
                        const parts = postCode.split("-");

                        post1Input.value = parts[0] || "";
                        post2Input.value = parts[1] || "";
                    }
                }
            );
        } catch (error) {
            googlePlacesFailed = true;

            restoreStreetInput();

            setTimeout(restoreStreetInput, 100);
            setTimeout(restoreStreetInput, 500);

            console.warn(
                "Google Places jest niedostępne. Pole ulicy działa ręcznie.",
                error
            );
        }
    }

    function updateGravityForm() {
        clearMessage();

        const requiredFields = [
            "name",
            "street",
            "house",
            "post1",
            "post2",
            "city"
        ];

        let hasError = false;

        requiredFields.forEach(function (id) {
            const field = wrapper.querySelector("#" + id);

            /*
             * W oryginale przy braku pola następowało:
             * field.classList.add(), mimo że field było null.
             */
            if (!field) {
                hasError = true;
                return;
            }

            if (!field.value.trim()) {
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

        const post1 = wrapper.querySelector("#post1");
        const post2 = wrapper.querySelector("#post2");

        const postCode =
            post1.value.trim() +
            "-" +
            post2.value.trim();

        const postPattern = /^\d{2}-\d{3}$/;

        if (!postPattern.test(postCode)) {
            post1.classList.add("error-border");
            post2.classList.add("error-border");

            showMessage(
                "Niepoprawny format kodu pocztowego (XX-XXX)."
            );

            return;
        }

        const formId = wrapper.dataset.formId;
        const data = new URLSearchParams();

        data.append(
            "action",
            "update_registration_address"
        );

        data.append("form_id", formId);

        data.append(
            "name",
            wrapper.querySelector("#name").value.trim()
        );

        data.append(
            "street",
            wrapper.querySelector("#street").value.trim()
        );

        data.append(
            "house",
            wrapper.querySelector("#house").value.trim()
        );

        data.append(
            "apartment",
            wrapper.querySelector("#local").value.trim()
        );

        data.append("post", postCode);

        data.append(
            "city",
            wrapper.querySelector("#city").value.trim()
        );

        const ajaxUrl =
            window.ajaxurl ||
            "/wp-admin/admin-ajax.php";

        const button =
            wrapper.querySelector("#pweSendStepTwo");

        if (button) {
            button.disabled = true;
        }

        fetch(ajaxUrl, {
            method: "POST",
            headers: {
                "Content-Type":
                    "application/x-www-form-urlencoded"
            },
            body: data
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (result.success) {
                    showSuccess();
                } else {
                    showMessage(
                        result.data
                            ? result.data.message
                            : result.message ||
                                "Wystąpił błąd."
                    );

                    if (button) {
                        button.disabled = false;
                    }
                }
            })
            .catch(function (error) {
                console.error(error);

                showMessage(
                    "Wystąpił problem z aktualizacją."
                );

                if (button) {
                    button.disabled = false;
                }
            });
    }

    function showSuccess() {
        const currentWrapper =
            document.getElementById(
                "pweConfirmationVisitorsRegistration"
            );

        if (!currentWrapper) {
            return;
        }

        currentWrapper.classList.add("is-complete");

        const before =
            currentWrapper.querySelector(
                ".pwe-confirmation-visitors-registration__before-submit"
            );

        const success =
            currentWrapper.querySelector(
                ".pwe-confirmation-visitors-registration__success"
            );

        if (before) {
            before.style.display = "none";
        }

        if (success) {
            success.style.display = "flex";
        }

        const displayBefore =
            currentWrapper.querySelectorAll(
                ".pwe-confirmation-visitors-registration__display-before-submit"
            );

        displayBefore.forEach(function (element) {
            element.style.display = "none";
        });

        const displayAfter =
            currentWrapper.querySelectorAll(
                ".pwe-confirmation-visitors-registration__display-after-submit"
            );

        displayAfter.forEach(function (element) {
            element.style.display = "block";
        });
    }

    function clearMessage() {
        const message =
            wrapper.querySelector("#statusMessage");

        if (message) {
            message.innerHTML = "";
            message.classList.remove("error");
        }
    }

    function showMessage(text) {
        const message =
            wrapper.querySelector("#statusMessage");

        if (message) {
            message.textContent = text;
            message.classList.add("error");
        }
    }
})();