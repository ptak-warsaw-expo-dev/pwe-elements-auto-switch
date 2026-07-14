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

            // walidacja rozmiaru
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
});
