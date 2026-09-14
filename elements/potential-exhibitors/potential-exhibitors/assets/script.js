document.addEventListener("DOMContentLoaded", function () {

    const container = document.querySelector(".pwe-potential-exhibitors__form");
    if (!container) return;

    const waitForForm = setInterval(() => {

        const form = container.querySelector("form");
        if (!form) return;

        const langInput = form.querySelector(".lang input");

        if (!langInput) return;

        clearInterval(waitForForm);

        init(form);

    }, 100);

    function init(form) {

        // blocked double execution 
        if (form.dataset.autoFilled === "1") return;
        form.dataset.autoFilled = "1";

        const params = new URLSearchParams(window.location.search);

        const getname = params.get("getname");
        const getphone = params.get("getphone");
        const getemail = params.get("getemail");
        const getid = params.get("getid");
        const badge = params.get("badge");
        const firma = params.get("firma");
        const kanal = params.get("kanal");

        if (!getid && !getname) return;

        const idData = getid
            ? getid.split(",").map(v => v.trim())
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
            idPhone: form.querySelector(".vip-id-phone input"),
            lang: form.querySelector(".lang input")
        };

        function setValue(input, value) {
            if (!input) return;
            input.value = value ?? "";
            input.dispatchEvent(new Event("input", { bubbles: true }));
            input.dispatchEvent(new Event("change", { bubbles: true }));
        }

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

        if (fields.lang) {
            const pageLang = (document.documentElement.lang || "")
                .trim()
                .toLowerCase()
                .substring(0, 2);

            fields.lang.value = pageLang || "";
        }

        setTimeout(() => {
            if (form.dataset.submitted === "1") return;
            form.dataset.submitted = "1";

            form.requestSubmit();
        }, 150);
    }
});