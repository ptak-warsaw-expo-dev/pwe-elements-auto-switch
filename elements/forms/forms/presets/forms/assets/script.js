document.addEventListener('DOMContentLoaded', function () {
    var formSelect = document.getElementById('pwe-gf-form-select');

    if (!formSelect) {
        return;
    }

    var panels = Array.prototype.slice.call(document.querySelectorAll('.pwe-gf-select__panel'));

    function showSelectedForm() {
        var selectedId = formSelect.value;

        panels.forEach(function (panel) {
            var isSelected = selectedId !== '' && panel.getAttribute('data-form-id') === selectedId;
            panel.hidden = !isSelected;
        });
    }

    formSelect.addEventListener('change', showSelectedForm);
    showSelectedForm();
});
