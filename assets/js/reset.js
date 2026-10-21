(function () {
    'use strict';
    var resetBtn = document.getElementById('resetBtn');
    var message = document.getElementById('formMessage');
    if (!resetBtn) {
        return;
    }
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    resetBtn.addEventListener('click', function () {
        resetBtn.disabled = true;
        fetch('actions/session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'reset', csrf_token: csrfToken })
        }).then(function (response) {
            return response.json();
        }).then(function (data) {
            if (data.ok) {
                window.location.href = 'index.php';
                return;
            }
            if (message) {
                message.textContent = data.message || 'Could not reset your session.';
                message.className = 'form-message is-error';
            }
            resetBtn.disabled = false;
        }).catch(function () {
            resetBtn.disabled = false;
        });
    });
})();
