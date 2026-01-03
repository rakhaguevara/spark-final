/**
 * BOOKING-PAGE.JS
 * Step-by-step progressive disclosure logic
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
    });

    // Make nextStep global so onclick works
    window.nextStep = function (step) {
        if (validateStep(step)) {
            // Mark current step as completed
            const stepEl = document.getElementById(`step${step}`);
            stepEl.classList.add('completed');
            stepEl.classList.remove('active');

            // Move to next
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

            if (i === currentStep) {
                // Active Step
                stepEl.classList.add('active');
                stepEl.classList.remove('disabled');
                contentEl.style.display = 'block';
                if (editBtn) editBtn.style.display = 'none';
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
                highlightError(input);
            } else {
                removeError(input);
            }

            // Email validation for step 1
            if (step === 1 && input.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value.trim())) {
                    isValid = false;
                    highlightError(input);
                    alert('Please enter a valid email address.');
                }
            }
        });

        if (!isValid) {
            alert('Please fill in all required fields to continue.');
        }

        return isValid;
    }

    function highlightError(input) {
        input.style.borderColor = '#ef4444';
        input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
    }

    function removeError(input) {
        input.style.borderColor = '';
        input.style.boxShadow = '';
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
