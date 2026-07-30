document.addEventListener("DOMContentLoaded", function () {
    const fileInputs = document.querySelectorAll(".ginput_container_fileupload input[type=\'file\']");

    const allowedExtensions = ["jpg", "jpeg", "png", "gif", "pdf", "webp"];
    const maxFileSize = 1048576; // 1 MB

    const isPL = document.documentElement.lang.toLowerCase().startsWith("pl");

    const t = {
        addFile: isPL ? "Dodaj plik" : "Add file",
        noFile: isPL ? "Brak wybranego pliku" : "No file selected",
        invalidFormat: isPL ? "Niedozwolony format pliku" : "Invalid file format",
        fileTooLarge: isPL ? "Plik jest zbyt duży" : "The file is too large",
        maxSize: isPL ? "(maks. 1 MB)" : "(max. 1 MB)"
    };

    fileInputs.forEach(function (fileInput) {
        fileInput.style.display = "none";

        const label = document.createElement("label");
        label.setAttribute("for", fileInput.id);
        label.classList.add("custom-upload-label");
        label.innerHTML = "📎 " + t.addFile;

        const fileNameSpan = document.createElement("span");
        fileNameSpan.classList.add("custom-upload-filename");
        fileNameSpan.textContent = t.noFile;

        fileInput.parentNode.insertBefore(label, fileInput);
        fileInput.parentNode.insertBefore(fileNameSpan, fileInput.nextSibling);

        fileInput.addEventListener("change", function (event) {
            const file = event.target.files[0];
            if (!file) {
                fileNameSpan.textContent = t.noFile;
                fileNameSpan.classList.remove("error");
                return;
            }

            const fileName = file.name;
            const fileExtension = fileName.split(".").pop().toLowerCase();
            const fileSize = file.size;

            if (!allowedExtensions.includes(fileExtension)) {
                fileNameSpan.textContent = "❌ " + t.invalidFormat + " (" + fileExtension + ")";
                fileNameSpan.classList.add("error");
                fileInput.value = "";
                return;
            }

            // Size validation
            if (fileSize > maxFileSize) {
                fileNameSpan.textContent = "❌ " + t.fileTooLarge + " " + t.maxSize;
                fileNameSpan.classList.add("error");
                fileInput.value = "";
                return;
            }

            fileNameSpan.textContent = fileName;
            fileNameSpan.classList.remove("error");
        });
    });


    // Medal Ceremony Categories
    const data = window.medalCeremonyData;
    const lang = window.currentLang || 'pl';

    if (!data || !data.categories) return;

    const containerTarget = document.querySelector('.pwe-categories-cap');
    if (!containerTarget) return;

    // Wrapper
    const wrapper = document.createElement('li');
    wrapper.className = 'pwe-medal-checkboxes';

    // Title
    const title = document.createElement('label');
    title.innerText = lang === 'pl'
        ? 'W jakiej kategorii konkursowej chcesz zgłosić swój udział?'
        : 'In which competition category would you like to submit your entry?';

    wrapper.appendChild(title);

    // List
    const list = document.createElement('ul');
    list.className = 'pwe-medal-checkboxes__list';

    Object.keys(data.categories).forEach(key => {
        const cat = data.categories[key][lang];

        const label = document.createElement('li');
        label.className = 'pwe-medal-checkbox';

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.value = cat.name;

        const span = document.createElement('span');
        span.innerText = cat.name + ` - ${cat.description}`;

        label.appendChild(checkbox);
        label.appendChild(span);

        list.appendChild(label);
    });

    wrapper.appendChild(list);

    // Insert before element
    containerTarget.parentNode.insertBefore(wrapper, containerTarget);

    // Target input
    const input = document.querySelector('.pwe-categories-cap .ginput_container_text input');

    function updateInput() {
        const selected = wrapper.querySelectorAll('input:checked');
        const values = Array.from(selected).map(el => `• ${el.value} `);

        if (input) {
            input.value = values.length ? `${values.join('')}` : '';
        }
    }

    wrapper.addEventListener('change', updateInput);

    // VALIDATION
    const formId = window.formID ;
    const submitBtn = document.querySelector(`#gform_submit_button_${formId}`);

    if (submitBtn) {
        submitBtn.addEventListener('click', function (e) {
            const checked = wrapper.querySelectorAll('input:checked');

            if (checked.length === 0) {
                e.preventDefault();
                e.stopPropagation();

                window[`gf_submitting_${formId}`] = false; // stop GF

                alert(
                    lang === 'pl'
                        ? 'Wybierz przynajmniej jedną kategorię.'
                        : 'Select at least one category.'
                );

                return false;
            }
        }, true); // capture phase
    }
});
