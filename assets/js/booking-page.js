/**
 * BOOKING-PAGE.JS
 * Step-by-step progressive disclosure logic with animations
 */

(function () {
    'use strict';

    let currentStep = 1;
    const totalSteps = 4;

    document.addEventListener('DOMContentLoaded', function () {
        // Initialize step visibility
        updateStepVisibility();

        // Add edit button listeners
        document.querySelectorAll('.btn-edit-step').forEach(btn => {
            btn.addEventListener('click', function (e) {
                const stepElement = this.closest('.checkout-step');
                const stepNum = parseInt(stepElement.dataset.step);
                goToStep(stepNum);
            });
        });

        // Initialize form submission
        const form = document.getElementById('bookingForm');
        if (form) {
            form.addEventListener('submit', handleFormSubmit);
        }

        // Auto-uppercase plate number
        const platInput = document.getElementById('nomor_plat');
        if (platInput) {
            platInput.addEventListener('input', function () {
                this.value = this.value.toUpperCase();
            });
        }

        // Real-time validation removal on input
        document.querySelectorAll('.form-input, .form-select').forEach(input => {
            input.addEventListener('input', function () {
                removeError(this);
            });
            input.addEventListener('change', function () {
                removeError(this);
            });
        });
    });

    // Make nextStep global so onclick works
    window.nextStep = function (step) {
        if (validateStep(step)) {
            // Mark current step as completed
            const stepEl = document.getElementById(`step${step}`);
            stepEl.classList.add('completed');
            stepEl.classList.remove('active');

            // Move to next with animation
            currentStep = step + 1;
            updateStepVisibility();
        }
    };

    function goToStep(step) {
        currentStep = step;
        updateStepVisibility();
    }

    function updateStepVisibility() {
        for (let i = 1; i <= totalSteps; i++) {
            const stepEl = document.getElementById(`step${i}`);
            const contentEl = stepEl.querySelector('.step-content');
            const editBtn = stepEl.querySelector('.btn-edit-step');

            // Reset classes
            stepEl.classList.remove('slide-in', 'slide-out');

            if (i === currentStep) {
                // Active Step
                stepEl.classList.add('active');
                stepEl.classList.remove('disabled');

                // Show content with animation
                contentEl.style.display = 'block';
                // Small delay to allow display:block to render before adding opacity/transform logic if we were doing it via CSS
                // But simplified: the active state handles the border/shadow.
                // We'll add a class to trigger content animation if needed.
                stepEl.classList.add('fade-in-content');

                if (editBtn) editBtn.style.display = 'none';

                // Scroll to active step if needed (smooth)
                stepEl.scrollIntoView({ behavior: 'smooth', block: 'center' });

            } else if (i < currentStep) {
                // Completed Step
                stepEl.classList.remove('active', 'disabled');
                stepEl.classList.add('completed');
                contentEl.style.display = 'none';
                if (editBtn) editBtn.style.display = 'block';
            } else {
                // Future Step
                stepEl.classList.remove('active', 'completed');
                stepEl.classList.add('disabled');
                contentEl.style.display = 'none';
                if (editBtn) editBtn.style.display = 'none';
            }
        }
    }

    function validateStep(step) {
        let isValid = true;
        const stepEl = document.getElementById(`step${step}`);
        const inputs = stepEl.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                showError(input, 'This field is required');
            } else {
                removeError(input);
            }

            // Email validation for step 1
            if (step === 1 && input.type === 'email' && input.value.trim()) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value.trim())) {
                    isValid = false;
                    showError(input, 'Please enter a valid email address');
                }
            }
        });

        return isValid;
    }

    function showError(input, message) {
        // Highlight input
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';

        // Find or create error message element
        // We look for a small.error-msg with an ID related to the input or just next sibling
        let errorMsg = input.nextElementSibling;

        // Check if next element is error-msg, if not try to find by id
        if (!errorMsg || !errorMsg.classList.contains('error-msg')) {
            const errorId = 'error-' + input.id;
            errorMsg = document.getElementById(errorId);
        }

        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.style.display = 'block';
            errorMsg.style.color = '#ef4444';
            errorMsg.style.fontSize = '12px';
            errorMsg.style.marginTop = '4px';
        }
    }

    function removeError(input) {
        input.style.borderColor = '';
        input.style.boxShadow = '';

        let errorMsg = input.nextElementSibling;
        if (!errorMsg || !errorMsg.classList.contains('error-msg')) {
            const errorId = 'error-' + input.id;
            errorMsg = document.getElementById(errorId);
        }

        if (errorMsg) {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    }

    function handleFormSubmit(e) {
        if (currentStep !== 4) {
            e.preventDefault();
            return false;
        }

        // Show loading state
        const submitBtn = document.querySelector('.btn-submit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }

})();

