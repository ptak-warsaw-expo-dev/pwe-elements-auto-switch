document.addEventListener("DOMContentLoaded", function () {
    var urlParams = new URLSearchParams(window.location.search);

    var getname = urlParams.get("getname");
    var getphone = urlParams.get("getphone");
    var getemail = urlParams.get("getemail");
    var getid = urlParams.get("getid");
    var badge = urlParams.get("badge");
    var firma = urlParams.get("firma");
    var kanal = urlParams.get("kanal");

    if (!getid && !getname) {
        console.log("Brak parametrów w URL - formularz nie zostanie wypełniony ani wysłany.");
        return;
    }

    let idmail = [];
    if (getid) idmail = getid.split(",");

    let inputName, inputEmail, inputPhone, inputCompany, inputChannel, inputBadge, inputID, inputIDname, inputIDemail, inputIDphone;

    var fields = document.querySelectorAll("#pweConfVip .gfield");

    fields.forEach(function (field) {
        if (field.classList.contains("gform_validation_container")) return;

        var label = field.querySelector("label");
        if (!label) return;

        const labelText = label.textContent.toLowerCase().trim();

        if (labelText.includes("' . $input_name . '") && !labelText.includes("id")) {
            inputName = field.querySelector("input");
        }
        if (labelText.includes("' . $input_email . '") && !labelText.includes("id")) {
            inputEmail = field.querySelector("input");
        }
        if (labelText.includes("' . $input_phone . '") && !labelText.includes("id")) {
            inputPhone = field.querySelector("input");
        }
        if (labelText.includes("' . $input_company . '")) {
            inputCompany = field.querySelector("input");
        }
        if (labelText.includes("' . $input_channel . '")) {
            inputChannel = field.querySelector("input");
        }
        if (labelText.includes("' . $input_badge . '")) {
            inputBadge = field.querySelector("input");
        }
        if (labelText.includes("' . $input_id . '") && !labelText.includes("name") && !labelText.includes("email") && !labelText.includes("phone")) {
            inputID = field.querySelector("input");
        }
        if (labelText.includes("' . $input_idname . '")) {
            inputIDname = field.querySelector("input");
        }
        if (labelText.includes("' . $input_idemail . '")) {
            inputIDemail = field.querySelector("input");
        }
        if (labelText.includes("' . $input_idphone . '")) {
            inputIDphone = field.querySelector("input");
        }
    });

    const inputVipName = inputName || document.querySelector("#input_' . $form_id . '_1");
    const inputVipPhone = inputPhone || document.querySelector("#input_' . $form_id . '_5");
    const inputVipEmail = inputEmail || document.querySelector("#input_' . $form_id . '_4");
    const inputVipBadge = inputBadge || document.querySelector("#input_' . $form_id . '_10");
    const inputVipCompany = inputCompany || document.querySelector("#input_' . $form_id . '_11");
    const inputVipChannel = inputChannel || document.querySelector("#input_' . $form_id . '_18");
    const inputVipID = inputID || document.querySelector("#input_' . $form_id . '_9");
    const inputVipIDname = inputIDname || document.querySelector("#input_' . $form_id . '_17");
    const inputVipIDemail = inputIDemail || document.querySelector("#input_' . $form_id . '_13");
    const inputVipIDphone = inputIDphone || document.querySelector("#input_' . $form_id . '_15");

    if (inputVipName) inputVipName.value = getname || "";
    if (inputVipEmail) inputVipEmail.value = getemail || "";
    if (inputVipPhone) inputVipPhone.value = getphone || "";
    if (inputVipCompany) inputVipCompany.value = firma || "";
    if (inputVipChannel) inputVipChannel.value = kanal || "";
    if (inputVipBadge) inputVipBadge.value = badge || "";
    if (inputVipID) inputVipID.value = getid || "";
    if (inputVipIDname && idmail.length > 1) inputVipIDname.value = idmail[1].trim();
    if (inputVipIDemail && idmail.length > 2) inputVipIDemail.value = idmail[2].trim();
    if (inputVipIDphone && idmail.length > 3) inputVipIDphone.value = idmail[3].trim();

    const form = document.getElementById("gform_' . (int) $form_id . '");
    if (form && window.jQuery) {
        window["gf_submitting_' . (int) $form_id . '"] = false;
        setTimeout(function() {
            jQuery(form).trigger("submit", [true]);
        }, 500);
    }
});