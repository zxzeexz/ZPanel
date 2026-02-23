// global-form.js
// For ZPanel v1.0

document.addEventListener('DOMContentLoaded', function () {
    // Find ALL forms on the page
    document.querySelectorAll('form').forEach(form => {
        // Only apply to forms that have a submit button
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');

        if (submitBtn) {
            form.addEventListener('submit', function (event) {
                // Prevent multiple submissions
                if (form.dataset.submitted === 'true') {
                    event.preventDefault();
                    return;
                }

                // Mark as submitted
                form.dataset.submitted = 'true';

                // Disable button
                submitBtn.disabled = true;

                // Show spinner + processing text (Bootstrap 5 style)
                const originalHTML = submitBtn.innerHTML;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Processing...
                `;

                // Optional: If you ever want to re-enable (e.g. on AJAX error)
                // You can add a custom event listener later if needed
            });
        }
    });
});