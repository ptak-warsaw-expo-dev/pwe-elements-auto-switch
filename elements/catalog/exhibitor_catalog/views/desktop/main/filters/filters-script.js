document.addEventListener('click', function (e) {
    const heading = e.target.closest('.exhibitor-catalog__filter-heading');
    if (!heading) return;

    const group = heading.closest('.exhibitor-catalog__filter-group.is-collapsible');
    if (!group) return;

    e.preventDefault();

    const content = group.querySelector('.exhibitor-catalog__labels-container');
    const isOpen = group.classList.contains('is-open');

    if (isOpen) {
        // zamykanie
        content.style.maxHeight = content.scrollHeight + 'px'; // start
        requestAnimationFrame(() => {
            content.style.maxHeight = '0px';
        });
        group.classList.remove('is-open');
    } else {
        // otwieranie
        group.classList.add('is-open');
        content.style.maxHeight = content.scrollHeight + 'px';
    }
});
