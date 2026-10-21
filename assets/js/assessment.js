(function () {
    'use strict';
    var form = document.getElementById('assessmentForm');
    var submitBtn = document.getElementById('submitAssessment');
    var message = document.getElementById('assessmentMessage');
    var csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function showMessage(text, type) {
        message.textContent = text;
        message.className = 'form-message' + (type ? ' is-' + type : '');
    }

    function collectRatings() {
        var ratings = {};
        var cards = form.querySelectorAll('.risk-card');
        var valid = true;
        cards.forEach(function (card) {
            var id = card.dataset.riskId;
            var likelihood = parseInt(card.querySelector('[name="likelihood_' + id + '"]').value, 10);
            var impact = parseInt(card.querySelector('[name="impact_' + id + '"]').value, 10);
            if (!likelihood || !impact) {
                valid = false;
                card.classList.add('has-error');
            } else {
                card.classList.remove('has-error');
                ratings[id] = { likelihood: likelihood, impact: impact };
            }
        });
        return valid ? ratings : null;
    }

    submitBtn.addEventListener('click', function () {
        var ratings = collectRatings();
        if (!ratings) {
            showMessage('Rate every risk before continuing.', 'error');
            return;
        }
        submitBtn.disabled = true;
        showMessage('Saving your ratings...', '');
        fetch('actions/assessment.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                csrf_token: csrfToken,
                ratings: ratings
            })
        }).then(function (response) {
            return response.json().then(function (data) {
                data.status = response.status;
                return data;
            });
        }).then(function (data) {
            if (data.ok) {
                window.location.href = data.redirect;
                return;
            }
            showMessage(data.message || 'Could not save your ratings.', 'error');
            submitBtn.disabled = false;
        }).catch(function () {
            showMessage('Could not reach the server. Check your connection and try again.', 'error');
            submitBtn.disabled = false;
        });
    });
})();
