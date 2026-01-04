<!-- STEP 3: PAYMENT METHOD -->
<div class="checkout-step disabled" id="step3" data-step="3">
    <div class="step-header">
        <div class="step-number">3</div>
        <h3 class="step-title">Payment Method</h3>
        <button type="button" class="btn-edit-step" style="display: none;">Edit</button>
    </div>
    
    <div class="step-content" style="display: none;">
        <div class="payment-methods">
            <label class="payment-option disabled">
                <input type="radio" name="payment_method" value="cash" disabled>
                <div class="payment-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Cash (On-site)</span>
                    <span class="coming-soon">Coming Soon</span>
                </div>
            </label>

            <label class="payment-option disabled">
                <input type="radio" name="payment_method" value="qris" disabled>
                <div class="payment-card">
                    <i class="fas fa-qrcode"></i>
                    <span>QRIS</span>
                    <span class="coming-soon">Coming Soon</span>
                </div>
            </label>

            <label class="payment-option disabled">
                <input type="radio" name="payment_method" value="bank_transfer" disabled>
                <div class="payment-card">
                    <i class="fas fa-university"></i>
                    <span>Bank Transfer</span>
                    <span class="coming-soon">Coming Soon</span>
                </div>
            </label>
        </div>
        
        <div class="payment-info">
            <i class="fas fa-info-circle"></i>
            <p>Payment integration coming soon. For now, booking will be created as pending.</p>
        </div>

        <div class="step-actions">
            <button type="button" class="btn-next" onclick="nextStep(3)">
                Continue to Review
            </button>
        </div>
    </div>
</div>
