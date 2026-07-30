document.addEventListener("DOMContentLoaded", function () {
    const container = document.querySelector(
        ".pwe-potential-exhibitors__form"
    );

    const form = container?.querySelector("form");

    if (!form) {
        console.warn("Nie znaleziono formularza.");
        return;
    }

    const params = new URLSearchParams(window.location.search);

    const getname = params.get("getname");
    const getphone = params.get("getphone");
    const getemail = params.get("getemail");
    const getid = params.get("getid");
    const badge = params.get("badge");
    const firma = params.get("firma");
    const kanal = params.get("kanal");

    if (!getid && !getname) {
        return;
    }

    const idData = getid
        ? getid.split(",").map(value => value.trim())
        : [];

    const fields = {
        name: form.querySelector(".vip-name input"),
        email: form.querySelector(".vip-email input"),
        phone: form.querySelector(".vip-phone input"),
        company: form.querySelector(".vip-company input"),
        channel: form.querySelector(".vip-channel input"),
        badge: form.querySelector(".vip-badge input"),
        id: form.querySelector(".vip-id input"),
        idName: form.querySelector(".vip-id-name input"),
        idEmail: form.querySelector(".vip-id-email input"),
        idPhone: form.querySelector(".vip-id-phone input")
    };

    setValue(fields.name, getname);
    setValue(fields.email, getemail);
    setValue(fields.phone, getphone);
    setValue(fields.company, firma);
    setValue(fields.channel, kanal);
    setValue(fields.badge, badge);
    setValue(fields.id, getid);
    setValue(fields.idName, idData[1]);
    setValue(fields.idEmail, idData[2]);
    setValue(fields.idPhone, idData[3]);

    const formId =
        form.dataset.formid ||
        form.id.match(/^gform_(\d+)$/)?.[1];

    if (formId && window.jQuery) {
        window[`gf_submitting_${formId}`] = false;

        setTimeout(function () {
            window.jQuery(form).trigger("submit", [true]);
        }, 500);
    }

    function setValue(input, value) {
        if (!input) return;

        input.value = value ?? "";
        input.dispatchEvent(new Event("input", { bubbles: true }));
        input.dispatchEvent(new Event("change", { bubbles: true }));
    }
});