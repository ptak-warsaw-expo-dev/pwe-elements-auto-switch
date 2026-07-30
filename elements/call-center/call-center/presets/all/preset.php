<?php

$secret = SECURE_AUTH_KEY;
$file_url = plugin_dir_url(__FILE__) . 'assets/add_entry.php';

$contacts = [
    "gr3" => [
        "Platyna",
        "Stara Platyna",
        "Kross",
        "Złote",
        "Stare CC",
        "Srebrne",
        "Papierzyce",
        "Dogrzanie",
        "Platyna EN",
        "Stara Platyna EN",
        "Kross EN",
        "Złote EN",
        "Stare CC EN",
        "Srebrne EN",
        "Papierzyce EN",
        "Dogrzanie EN"
    ],
    "gr2" => [
        "Platyna",
        "Stara Platyna",
        "Kross",
        "Złote",
        "Stare CC",
        "Srebrne",
        "Papierzyce",
        "Dogrzanie",
        "Platyna EN",
        "Stara Platyna EN",
        "Kross EN",
        "Złote EN",
        "Stare CC EN",
        "Srebrne EN",
        "Papierzyce EN",
        "Dogrzanie EN"
    ],
    "gr1" => [
        "Platyna",
        "Stara Platyna",
        "Kross",
        "Złote",
        "Stare CC",
        "Srebrne",
        "Papierzyce",
        "Dogrzanie",
        "Platyna EN",
        "Stara Platyna EN",
        "Kross EN",
        "Złote EN",
        "Stare CC EN",
        "Srebrne EN",
        "Papierzyce EN",
        "Dogrzanie EN"
    ]
];


$pwe_groups_data = PWE_Functions::get_database_groups_data();
$pwe_callcenter_data = PWE_Functions::get_database_groups_callcenter_data();
$senders = [];

$current_domain = $_SERVER['HTTP_HOST'];
$current_group = "gr2";

foreach ($pwe_callcenter_data as $item) {
    if ($item->sender_group === $current_group) {

        $data = json_decode($item->sender_data, true);

        if (!empty($data['sender_name'])) {
            $senders[] = $data['sender_name'];
        }
    }
}

$senders_json = json_encode($senders, JSON_UNESCAPED_UNICODE);

$channels = isset($contacts[$current_group]) ? $contacts[$current_group] : [];
$channels_json = json_encode($channels);

$output = '';
$output .= '
<style>
    .cc-registery__form{
        max-width: 600px;
        margin: auto;
    }
    .cc-registery__form :is(input, select){
        border-color: black !important;
    }
    .cc-registery__form select{
        padding: 10px 15px !important;
    }
    .gform_wrapper :is(label, span, .gfield_description) {
        color: black !important;
    }';
    if(isset($_GET['firma'])){
        $output .= '
            li:has(input[placeholder^="ID"]),
            li:has(select){
                display: none;
            }
        ';
    }
$output .= '
</style>

<div class="cc-registery__form">
    ' . do_shortcode('[gravityform id=' . $form_id . ' title="false" description="false" ajax="true"]') . '
</div>

<script>

    jQuery(document).ready(function($) {';
        if(isset($_GET['firma'])){
            $output .= '
                $(`input[placeholder^="ID"]`).removeAttr("required").removeAttr("aria-required");
                $(`select`).removeAttr("required").removeAttr("aria-required");
            ';
        }
        $output .= '
        const create_modal = (entries) => {
            $(".cc-registery__form").after(`<div style="border: 2px solid; border-radius: 15px; padding: 0 36px 18px; margin: auto; text-align: center; width: fit-content; position: fixed; top: 30%; left: 50%; transform: translateX(-50%); background: white;"><h3>Wysłano powiadomienia dla ` + entries.length + ` osób. <br> Za chwile strona same się odświeży.</h3></div>`);

            setTimeout(() => {
                location.reload();
            }, 2000);
        }

        $(".cc-registery__form :is(input, textarea, select)[aria-required=true]").on("input", function(){
            $(this).closest(".gfield").next(".input-error").remove();
        });

        $(".cc-registery__form :is(input, textarea, select)[aria-required=true]").each(function(){
            $(this).attr("required", true);
        });

        $(`input[placeholder*="name"]`).parent().after(`<hr style="border: 1px solid; margin: 12px 0px 0px;">`);

        $(document).on("input", `input[placeholder*="name"]`, function() {
            if($(this).val().length > 3 && $(this).next().length == 0){
                $(this).after(`<input name="` + $(this).attr("name") + `" class="large" type="text" placeholder="First name and last name">`);
            } else if($(this).val().length < 1 && $(this).next().length > 0){
                $(this).remove();
            }
        });

        $(`input[type="submit"]`).siblings().remove();
        $(`input[type="submit"]`).attr("onclick", "");

        $(`input[type="submit"]`).on("click", function(event){
            event.preventDefault();

            let validate = true;

            $(".cc-registery__form :is(input, textarea, select)[aria-required=true]").each(function(){
                if(!this.checkValidity()){
                    if($(this).closest(".gfield").next(".input-error").length == 0){
                        $(this).closest(".gfield").after(`<p class="input-error" style="background-color: rgb(255, 0, 0, 0.05); color: red; border-bottom: solid; margin: 0; padding: 0 18px;">To pole musi być wypełnione</p>`);
                    }
                    validate = false;
                }
            });

            if (!validate){
                return;
            };

            $(`.cc-registery__form`).find(`input[type="submit"]`).after("<div id=spinner class=spinner></div>");

            let allInputs = {};
            allInputs["form_id"] = ' . $form_id . ';
            allInputs["getkanal"] = "' . $_GET['firma'] . '";
            allInputs["all_names"] = [];

            let keyNames = 0;
            $(".cc-registery__form :is(input, textarea, select)").map(function() {
                if(!$(this).hasClass("gform_hidden") && $(this).attr("type") != "hidden" && $(this).attr("type") != "submit" && $(this).val() != ""){
                    if($(this).prop("nodeName").toLowerCase() != "select"){
                        if($(this).prop("placeholder").includes("name")){
                            if (allInputs["name_id"] === undefined){
                                allInputs["name_id"] = $(this).prop("name");
                            }
                            allInputs["all_names"][keyNames] = $(this).val();
                            keyNames += 1;
                        } else if ($(this).attr("type") && $(this).attr("type") == "checkbox") {
                            allInputs[$(this).prop("name")] = $(this).prop("checked");
                        } else if ($(this).attr("type") && $(this).attr("type") == "radio"){
                            if ($(this).prop("checked") === true) {
                                allInputs[$(this).prop("name")] = $(this).val();
                            }
                        } else if ($(this).prop("placeholder").toLowerCase().includes("phone")) {
                            allInputs["phone_id"] = $(this).prop("name");
                            allInputs["phone"] = $(this).val();
                        } else if ($(this).attr("id")){
                            allInputs[$(this).prop("name")] = $(this).val();
                        }
                    } else {
                        allInputs[$(this).prop("name")] = $(this).find("option:selected").val();
                        allInputs["notification"] = $(this).find("option:selected").val();
                        allInputs["channel"] = $(this).find("option:selected").val();
                    }
                }
            });
            allInputs["kanal_id"] = $("select").prop("name");
            const dataToSend = JSON.stringify(allInputs);
            $.post("' . $file_url . '",
                {
                    secret: "' . $secret . '",
                    data: dataToSend,
                },
                function(response) {
                    const report = JSON.parse(response);
                    console.log("Odpowiedź serwera:", report);

                    $("#spinner").remove();

                    if(report["status"] === true){
                        create_modal(report["entries"]);
                        console.log("true");
                    } else {
                        console.log("false");
                    }
                }
            );
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const label = Array.from(document.querySelectorAll("label.gfield_label"))
            .find(el => el.textContent.replace(/\s+/g, " ").trim().includes("Kanał wysyłki"));

        const newOptions = '. $channels_json .';

        if (!label) return;

        const selectId = label.getAttribute("for");
        const select = document.getElementById(selectId);
        if (!select) return;

        select.innerHTML = "";

        const emptyOption = document.createElement("option");
        emptyOption.value = "";
        emptyOption.textContent = "Wybierz kanał wysyłki";
        select.appendChild(emptyOption);

        newOptions.forEach(optionText => {
            const option = document.createElement("option");
            option.value = optionText;
            option.textContent = optionText;
            select.appendChild(option);
        });

    });
</script>
';

$output .= '
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const label = Array.from(document.querySelectorAll("label.gfield_label"))
            .find(el => el.textContent.trim().startsWith("ID Platyna"));

        if (!label) return;

        const selectId = label.getAttribute("for");

        const originalSelect = document.getElementById(selectId);
        if (!originalSelect) return;

        const newOptions = '. $senders_json . ';

        originalSelect.style.display = "none";

        const dropdownWrapper = document.createElement("div");
        dropdownWrapper.className = "custom-dropdown-container";

        const dropdownButton = document.createElement("div");
        dropdownButton.className = "custom-dropdown-button";
        dropdownButton.textContent = "Wybierz ID"; // Tekst początkowy
        dropdownWrapper.appendChild(dropdownButton);

        const customList = document.createElement("ul");
        customList.className = "custom-dropdown-list";

        newOptions.forEach(name => {
            const listItem = document.createElement("li");
            listItem.className = "custom-dropdown-item";
            listItem.textContent = name;


            listItem.addEventListener("click", function(e) {
                e.stopPropagation();

                dropdownButton.textContent = name;
                dropdownButton.classList.add("has-value");
                console.log(name);


                let optionExists = false;
                for (let i = 0; i < originalSelect.options.length; i++) {
                    if (originalSelect.options[i].value === name) {
                        optionExists = true;
                        break;
                    }
                }

                if (!optionExists) {
                    const newOpt = document.createElement("option");
                    newOpt.value = name;
                    newOpt.textContent = name;
                    originalSelect.appendChild(newOpt);
                }

                originalSelect.value = name;

                originalSelect.dispatchEvent(new Event("change", { bubbles: true }));

                dropdownWrapper.classList.remove("is-open");
            });

            customList.appendChild(listItem);
        });

        dropdownWrapper.appendChild(customList);

        dropdownButton.addEventListener("click", function(e) {
            e.stopPropagation();
            dropdownWrapper.classList.toggle("is-open");
        });

        document.addEventListener("click", function() {
            dropdownWrapper.classList.remove("is-open");
        });

        originalSelect.parentNode.insertBefore(dropdownWrapper, originalSelect.nextSibling);
    });
</script>';

return $output;