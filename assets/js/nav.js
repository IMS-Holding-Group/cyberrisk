(function () {
    'use strict';
    var menuToggle = document.getElementById('menuToggle');
    var mainNav = document.getElementById('mainNav');
    if (!menuToggle || !mainNav) {
        return;
    }
    menuToggle.addEventListener('click', function () {
        var isOpen = mainNav.classList.toggle('is-open');
        menuToggle.setAttribute('aria-expanded', String(isOpen));
    });
})();
