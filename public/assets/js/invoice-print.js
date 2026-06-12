window.addEventListener('load', function () {
    document.body.classList.add('is-printing');
    window.print();
});

window.addEventListener('afterprint', function () {
    document.body.classList.remove('is-printing');
    document.body.classList.add('print-cancelled');
});

document.addEventListener('click', function (event) {
    if (event.target.matches('[data-print-button]')) {
        document.body.classList.add('is-printing');
        window.print();
    }
});
