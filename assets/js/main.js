(function () {
    'use strict';

    var ENDPOINT = 'actions/session.php';

    var startBtn   = document.getElementById('startBtn');
    var clearBtn   = document.getElementById('clearBtn');
    var resetBtn   = document.getElementById('resetBtn');
    var form       = document.getElementById('startForm');
    var message    = document.getElementById('formMessage');
    var csrfToken  = document.querySelector('meta[name="csrf-token"]').content;

    var fieldNames = ['name', 'organization', 'asset_type'];

    function showMessage(text, type) {
        message.textContent = text;
        message.className = 'form-message' + (type ? ' is-' + type : '');
    }

    function setFieldError(field, text) {
        var errorEl = document.getElementById(field + 'Error');
        var wrapper = document.getElementById(field).closest('.field');
        errorEl.textContent = text || '';
        wrapper.classList.toggle('has-error', Boolean(text));
        document.getElementById(field).setAttribute('aria-invalid', text ? 'true' : 'false');
    }

    function clearErrors() {
        fieldNames.forEach(function (f) { setFieldError(f, ''); });
        showMessage('', '');
    }

    function getValues() {
        return {
            name:         form.elements.name.value.trim(),
            organization: form.elements.organization.value.trim(),
            asset_type:   form.elements.asset_type.value
        };
    }

    function validate(values) {
        var errors = {};
        if (values.name === '') {
            errors.name = 'Enter your name.';
        } else if (values.name.length < 2 || values.name.length > 50) {
            errors.name = 'Your name must be 2 to 50 characters long.';
        } else if (!/^[\p{L}\s'\-]+$/u.test(values.name)) {
            errors.name = 'Use letters only in your name.';
        }
        if (values.organization.length > 80) {
            errors.organization = 'The organization name must be 80 characters or fewer.';
        }
        if (values.asset_type === '') {
            errors.asset_type = 'Choose what you want to assess.';
        }
        return errors;
    }

    function showErrors(errors) {
        var keys = Object.keys(errors);
        fieldNames.forEach(function (f) { setFieldError(f, errors[f] || ''); });
        if (keys.length) {
            document.getElementById(keys[0]).focus();
        }
        return keys.length > 0;
    }

    function sendToServer(payload) {
        payload.csrf_token = csrfToken;
        return fetch(ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function (response) {
            return response.json().then(function (data) {
                data.status = response.status;
                return data;
            });
        });
    }

    startBtn.addEventListener('click', function () {
        clearErrors();

        var values = getValues();
        if (showErrors(validate(values))) {
            showMessage('Fix the highlighted fields.', 'error');
            return;
        }

        startBtn.disabled = true;
        showMessage('Saving your details...', '');

        sendToServer({
            action:       'start',
            name:         values.name,
            organization: values.organization,
            asset_type:   values.asset_type
        }).then(function (data) {
            if (data.ok) {
                showMessage(data.message, 'success');
                window.location.href = data.redirect;
                return;
            }
            if (data.errors) { showErrors(data.errors); }
            showMessage(data.message, 'error');
            startBtn.disabled = false;
        }).catch(function () {
            showMessage('Could not reach the server. Check your connection and try again.', 'error');
            startBtn.disabled = false;
        });
    });

    clearBtn.addEventListener('click', function () {
        form.reset();
        // form.reset() restores the server-rendered values, so empty them explicitly
        fieldNames.forEach(function (f) { form.elements[f].value = ''; });
        clearErrors();
        form.elements.name.focus();
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            resetBtn.disabled = true;
            sendToServer({ action: 'reset' }).then(function (data) {
                if (data.ok) {
                    window.location.reload();
                } else {
                    showMessage(data.message, 'error');
                    resetBtn.disabled = false;
                }
            }).catch(function () {
                resetBtn.disabled = false;
            });
        });
    }

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        startBtn.click();
    });
})();
