<!-- STEP 1: CONTACT INFORMATION -->
<div class="checkout-step active" id="step1" data-step="1">
    <div class="step-header">
        <div class="step-number">1</div>
        <h3 class="step-title">Contact Information</h3>
        <button type="button" class="btn-edit-step" style="display: none;">Edit</button>
    </div>
    
    <div class="step-content">
        <!-- Helper Info -->
        <div class="info-box mb-4">
            <i class="fas fa-info-circle"></i>
            <div>
                Data ini diambil dari akun Anda. 
                <a href="<?= BASEURL ?>/pages/profile.php" class="text-blue-600 hover:underline">Edit di Profile</a>
            </div>
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email Address <span class="required">*</span></label>
            <input 
                type="text" 
                id="email" 
                name="email" 
                class="form-input read-only" 
                value="<?= !empty($user['email']) ? htmlspecialchars($user['email']) : '— Not provided —' ?>"
                readonly
                tabindex="-1">
        </div>

        <div class="form-group">
            <label for="nama_lengkap" class="form-label">Full Name <span class="required">*</span></label>
            <input 
                type="text" 
                id="nama_lengkap" 
                name="nama_lengkap" 
                class="form-input read-only" 
                value="<?= !empty($user['nama_pengguna']) ? htmlspecialchars($user['nama_pengguna']) : '— Not provided —' ?>"
                readonly
                tabindex="-1">
        </div>

        <div class="form-group">
            <label for="nomor_telepon" class="form-label">Phone Number</label>
            <input 
                type="text" 
                id="nomor_telepon" 
                name="nomor_telepon" 
                class="form-input read-only" 
                value="<?= !empty($user['nomor_telepon']) ? htmlspecialchars($user['nomor_telepon']) : '— Not provided —' ?>"
                readonly
                tabindex="-1">
        </div>

        <div class="step-actions">
            <button type="button" class="btn-next" onclick="nextStep(1)">
                Continue to Vehicle Info
            </button>
        </div>
    </div>
</div>
