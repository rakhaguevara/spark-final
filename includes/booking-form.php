<!-- BOOKING FORM (Left Column) -->
<form id="bookingForm" action="<?= BASEURL ?>/actions/create-booking.php" method="POST" class="booking-form">
    <input type="hidden" name="id_tempat" value="<?= $parking['id_tempat'] ?>">
    <input type="hidden" name="harga_per_jam" value="<?= $parking['harga_per_jam'] ?>">
    <input type="hidden" name="tanggal_booking" value="<?= $bookingData['date'] ?>">
    <input type="hidden" name="waktu_mulai" value="<?= $bookingData['time_start'] ?>">
    <input type="hidden" name="durasi_jam" value="<?= $bookingData['duration'] ?>">

    <!-- STEP 1: CONTACT INFORMATION -->
    <div class="checkout-step active" id="step1" data-step="1">
        <div class="step-header">
            <div class="step-number">1</div>
            <h3 class="step-title">Contact Information</h3>
            <button type="button" class="btn-edit-step" style="display: none;">Edit</button>
        </div>
        
        <div class="step-content">
            <div class="form-group">
                <label for="email" class="form-label">Email Address <span class="required">*</span></label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-input" 
                    value="<?= $user ? htmlspecialchars($user['email']) : '' ?>"
                    required
                    placeholder="your.email@example.com">
            </div>

            <div class="form-group">
                <label for="nama_lengkap" class="form-label">Full Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    class="form-input" 
                    value="<?= $user ? htmlspecialchars($user['nama_pengguna']) : '' ?>"
                    required
                    placeholder="John Doe">
            </div>

            <div class="form-group">
                <label for="nomor_telepon" class="form-label">Phone Number</label>
                <input 
                    type="tel" 
                    id="nomor_telepon" 
                    name="nomor_telepon" 
                    class="form-input" 
                    value="<?= $user ? htmlspecialchars($user['nomor_telepon'] ?? '') : '' ?>"
                    placeholder="+62 812 3456 7890">
            </div>

            <div class="step-actions">
                <button type="button" class="btn-next" onclick="nextStep(1)">
                    Continue to Vehicle Info
                </button>
            </div>
        </div>
    </div>

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
                    <?php foreach ($vehicleTypes as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                    <?php endforeach; ?>
                </select>
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

    <!-- STEP 4: REVIEW & CONFIRM -->
    <div class="checkout-step disabled" id="step4" data-step="4">
        <div class="step-header">
            <div class="step-number">4</div>
            <h3 class="step-title">Review & Pay</h3>
        </div>
        
        <div class="step-content" style="display: none;">
            <p class="review-text">By clicking the button below, you agree to our Terms of Service and Privacy Policy.</p>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-lock"></i>
                Confirm Booking & Pay
            </button>
        </div>
    </div>
</form>
