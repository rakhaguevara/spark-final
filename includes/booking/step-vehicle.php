<!-- STEP 2: VEHICLE INFORMATION -->
<div class="checkout-step disabled" id="step2" data-step="2">
    <div class="step-header">
        <div class="step-number">2</div>
        <h3 class="step-title">Vehicle Information</h3>
        <button type="button" class="btn-edit-step" style="display: none;">Edit</button>
    </div>
    
    <div class="step-content" style="display: none;">
        <div class="form-group">
            <label for="jenis_kendaraan" class="form-label">Vehicle Type <span class="required">*</span></label>
            <select id="jenis_kendaraan" name="jenis_kendaraan" class="form-select" required>
                <option value="">Select vehicle type</option>
                <?php if (isset($vehicleTypes) && is_array($vehicleTypes)): ?>
                    <?php foreach ($vehicleTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <small class="error-msg" id="error-jenis_kendaraan"></small>
        </div>

        <div class="form-group">
            <label for="nomor_plat" class="form-label">License Plate Number</label>
            <input 
                type="text" 
                id="nomor_plat" 
                name="nomor_plat" 
                class="form-input" 
                placeholder="B 1234 XYZ"
                style="text-transform: uppercase;">
            <small class="form-hint">Optional - helps parking staff identify your vehicle</small>
        </div>

        <div class="step-actions">
            <button type="button" class="btn-next" onclick="nextStep(2)">
                Continue to Payment
            </button>
        </div>
    </div>
</div>
