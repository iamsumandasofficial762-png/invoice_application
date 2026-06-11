window.addEventListener('load', function () {
    window.print();
});

document.addEventListener('click', function (event) {
    if (event.target.matches('[data-print-button]')) {
        window.print();
    }
});
