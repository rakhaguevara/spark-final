<?php
$pageTitle = 'Detail Lahan Parkir';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();
$action = $_POST['action'] ?? $_GET['action'] ?? 'view';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get action from POST first, then fallback to GET
    $form_action = $_POST['action'] ?? $_GET['action'] ?? 'view';
    
    $nama_tempat = trim($_POST['nama_tempat'] ?? '');
    $id_pemilik = (int)($_POST['id_pemilik'] ?? 0);
    $alamat_tempat = trim($_POST['alamat_tempat'] ?? '');
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $total_spot = (int)($_POST['total_spot'] ?? 0);
    $harga_per_jam = (float)($_POST['harga_per_jam'] ?? 0);
    $jam_buka = $_POST['jam_buka'] ?? '00:00';
    $jam_tutup = $_POST['jam_tutup'] ?? '23:59';
    
    try {
        if ($form_action === 'add') {
            // Validasi
            if (empty($nama_tempat)) {
                throw new Exception('Nama tempat wajib diisi');
            }
            if (empty($alamat_tempat)) {
                throw new Exception('Alamat wajib diisi');
            }
            if ($id_pemilik <= 0) {
                throw new Exception('Penyedia lahan wajib dipilih');
            }
            if ($total_spot <= 0) {
                throw new Exception('Total slot harus lebih dari 0');
            }
            if ($harga_per_jam < 0) {
                throw new Exception('Harga per jam tidak valid');
            }
            
            // Insert new parking place
            $stmt = $pdo->prepare("
                INSERT INTO tempat_parkir 
                (id_pemilik, nama_tempat, alamat_tempat, latitude, longitude, total_spot, harga_per_jam, jam_buka, jam_tutup)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $id_pemilik, $nama_tempat, $alamat_tempat, $latitude, $longitude,
                $total_spot, $harga_per_jam, $jam_buka, $jam_tutup
            ]);
            
            $new_id = $pdo->lastInsertId();
            
            // Create slots
            for ($i = 1; $i <= $total_spot; $i++) {
                $stmt = $pdo->prepare("
                    INSERT INTO slot_parkir (id_tempat, nomor_slot, status_slot, id_jenis)
                    VALUES (?, ?, 'available', 1)
                ");
                $stmt->execute([$new_id, 'A' . str_pad($i, 3, '0', STR_PAD_LEFT)]);
            }
            
            $_SESSION['success'] = 'Lahan parkir berhasil ditambahkan dengan ' . $total_spot . ' slot';
            header('Location: ' . BASEURL . '/admin/parking.php');
            exit;
        } elseif ($form_action === 'edit' && $id > 0) {
            // Update parking place
            $stmt = $pdo->prepare("
                UPDATE tempat_parkir 
                SET nama_tempat = ?, alamat_tempat = ?, latitude = ?, longitude = ?,
                    harga_per_jam = ?, jam_buka = ?, jam_tutup = ?
                WHERE id_tempat = ?
            ");
            $stmt->execute([
                $nama_tempat, $alamat_tempat, $latitude, $longitude,
                $harga_per_jam, $jam_buka, $jam_tutup, $id
            ]);
            
            $_SESSION['success'] = 'Lahan parkir berhasil diperbarui';
            header('Location: ' . BASEURL . '/admin/parking-detail.php?id=' . $id);
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

// Get parking detail if viewing/editing
$parking = null;
$slots = [];
$bookings = [];
$provider = null;

if ($id > 0) {
    $stmt = $pdo->prepare("
        SELECT tp.*, dp.nama_pengguna as nama_pemilik, dp.email_pengguna as email_pemilik
        FROM tempat_parkir tp
        LEFT JOIN data_pengguna dp ON tp.id_pemilik = dp.id_pengguna
        WHERE tp.id_tempat = ?
    ");
    $stmt->execute([$id]);
    $parking = $stmt->fetch();
    
    if ($parking) {
        // Get slots
        $stmt = $pdo->prepare("
            SELECT sp.*, jk.nama_jenis
            FROM slot_parkir sp
            LEFT JOIN jenis_kendaraan jk ON sp.id_jenis = jk.id_jenis
            WHERE sp.id_tempat = ?
            ORDER BY sp.nomor_slot
        ");
        $stmt->execute([$id]);
        $slots = $stmt->fetchAll();
        
        // Get recent bookings
        $stmt = $pdo->prepare("
            SELECT bp.*, dp.nama_pengguna
            FROM booking_parkir bp
            JOIN data_pengguna dp ON bp.id_pengguna = dp.id_pengguna
            WHERE bp.id_tempat = ?
            ORDER BY bp.created_at DESC
            LIMIT 10
        ");
        $stmt->execute([$id]);
        $bookings = $stmt->fetchAll();
    }
}

// Get providers list - semua user yang bisa jadi penyedia (bukan admin)
$stmt = $pdo->query("
    SELECT DISTINCT dp.id_pengguna, dp.nama_pengguna, dp.email_pengguna, dp.noHp_pengguna
    FROM data_pengguna dp
    WHERE dp.role_pengguna != 2
    ORDER BY dp.nama_pengguna
");
$providers = $stmt->fetchAll();

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>

<div class="admin-layout">
    <?php require_once __DIR__ . '/includes/sidebar.php'; ?>
    
    <div class="admin-main">
        <?php require_once __DIR__ . '/includes/navbar.php'; ?>
        
        <div class="admin-content">
            <!-- Flash Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="admin-flash-message admin-flash-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="admin-flash-message admin-flash-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
            
            <!-- Back Button -->
            <a href="<?= BASEURL ?>/admin/parking.php" class="admin-btn admin-btn-secondary" style="margin-bottom: 24px;">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <?php if ($action === 'add'): ?>
                <!-- Add Form -->
                <div class="admin-table-container">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">Tambah Lahan Parkir Baru</h2>
                    </div>
                    <form method="POST" style="padding: 32px;">
                        <input type="hidden" name="action" value="add">
                        
                        <!-- Section 1: Informasi Dasar -->
                        <div style="margin-bottom: 48px;">
                            <h3 style="color: var(--spark-yellow); font-size: 18px; font-weight: 600; margin-bottom: 28px; padding-bottom: 12px; border-bottom: 2px solid var(--spark-border);">
                                <i class="fas fa-info-circle" style="margin-right: 8px;"></i> Informasi Dasar
                            </h3>
                            
                            <div style="max-width: 700px;">
                                <div class="admin-form-group" style="margin-bottom: 28px;">
                                    <label class="admin-form-label" style="margin-bottom: 10px;">Nama Tempat <span style="color: #ff4d4f;">*</span></label>
                                    <input type="text" name="nama_tempat" class="admin-form-input" 
                                           placeholder="Contoh: Parkir Mall Ciputra" required style="margin-bottom: 0;">
                                </div>
                                
                                <div class="admin-form-group" style="margin-bottom: 28px;">
                                    <label class="admin-form-label" style="margin-bottom: 10px;">Penyedia Lahan <span style="color: #ff4d4f;">*</span></label>
                                    <select name="id_pemilik" class="admin-form-select" required style="margin-bottom: 0;">
                                        <option value="">-- Pilih Penyedia Lahan --</option>
                                        <?php if (empty($providers)): ?>
                                            <option value="" disabled>Tidak ada penyedia tersedia</option>
                                        <?php else: ?>
                                            <?php foreach ($providers as $prov): ?>
                                                <option value="<?= $prov['id_pengguna'] ?>">
                                                    <?= htmlspecialchars($prov['nama_pengguna']) ?> - <?= htmlspecialchars($prov['email_pengguna']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (empty($providers)): ?>
                                        <small style="color: var(--spark-text-light); margin-top: 8px; display: block; padding-left: 4px;">
                                            Belum ada user yang bisa dijadikan penyedia. Pastikan ada user terdaftar (bukan admin).
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="admin-form-group" style="margin-bottom: 0;">
                                    <label class="admin-form-label" style="margin-bottom: 10px;">Alamat Lengkap <span style="color: #ff4d4f;">*</span></label>
                                    <textarea name="alamat_tempat" class="admin-form-textarea" 
                                              placeholder="Masukkan alamat lengkap lahan parkir" required style="margin-bottom: 0;"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section 2: Koordinat Lokasi -->
                        <div style="margin-bottom: 48px;">
                            <h3 style="color: var(--spark-yellow); font-size: 18px; font-weight: 600; margin-bottom: 28px; padding-bottom: 12px; border-bottom: 2px solid var(--spark-border);">
                                <i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Koordinat Lokasi (Opsional)
                            </h3>
                            
                            <div style="max-width: 700px; display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                                <div class="admin-form-group" style="margin-bottom: 0;">
                                    <label class="admin-form-label" style="margin-bottom: 10px;">Latitude</label>
                                    <input type="number" step="any" name="latitude" class="admin-form-input" 
                                           placeholder="Contoh: -7.7956" style="margin-bottom: 0;">
                                    <small style="color: var(--spark-text-light); font-size: 12px; margin-top: 8px; display: block; padding-left: 4px;">
                                        Koordinat untuk peta
                                    </small>
                                </div>
                                <div class="admin-form-group" style="margin-bottom: 0;">
                                    <label class="admin-form-label" style="margin-bottom: 10px;">Longitude</label>
                                    <input type="number" step="any" name="longitude" class="admin-form-input" 
                                           placeholder="Contoh: 110.3695" style="margin-bottom: 0;">
                                    <small style="color: var(--spark-text-light); font-size: 12px; margin-top: 8px; display: block; padding-left: 4px;">
                                        Koordinat untuk peta
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Section 3: Informasi Parkir -->
                        <div style="margin-bottom: 48px;">
                            <h3 style="color: var(--spark-yellow); font-size: 18px; font-weight: 600; margin-bottom: 28px; padding-bottom: 12px; border-bottom: 2px solid var(--spark-border);">
                                <i class="fas fa-parking" style="margin-right: 8px;"></i> Informasi Parkir
                            </h3>
                            
                            <div style="max-width: 700px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 32px;">
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <label class="admin-form-label" style="margin-bottom: 10px;">Total Slot <span style="color: #ff4d4f;">*</span></label>
                                        <input type="number" name="total_spot" class="admin-form-input" min="1" 
                                               placeholder="Contoh: 50" required style="margin-bottom: 0;">
                                        <small style="color: var(--spark-text-light); font-size: 12px; margin-top: 8px; display: block; padding-left: 4px;">
                                            Jumlah slot parkir
                                        </small>
                                    </div>
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <label class="admin-form-label" style="margin-bottom: 10px;">Harga per Jam (Rp) <span style="color: #ff4d4f;">*</span></label>
                                        <input type="number" name="harga_per_jam" class="admin-form-input" min="0" step="1000" 
                                               placeholder="Contoh: 5000" required style="margin-bottom: 0;">
                                        <small style="color: var(--spark-text-light); font-size: 12px; margin-top: 8px; display: block; padding-left: 4px;">
                                            Harga dalam Rupiah
                                        </small>
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <label class="admin-form-label" style="margin-bottom: 10px;">Jam Buka <span style="color: #ff4d4f;">*</span></label>
                                        <input type="time" name="jam_buka" class="admin-form-input" value="00:00" required style="margin-bottom: 0;">
                                    </div>
                                    <div class="admin-form-group" style="margin-bottom: 0;">
                                        <label class="admin-form-label" style="margin-bottom: 10px;">Jam Tutup <span style="color: #ff4d4f;">*</span></label>
                                        <input type="time" name="jam_tutup" class="admin-form-input" value="23:59" required style="margin-bottom: 0;">
                                    </div>
                                </div>
                                <small style="color: var(--spark-text-light); font-size: 12px; margin-top: 12px; display: block; padding-left: 4px;">
                                    <i class="fas fa-info-circle" style="margin-right: 4px;"></i> Gunakan 00:00 - 23:59 untuk operasional 24 jam
                                </small>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div style="display: flex; gap: 16px; justify-content: flex-end; padding-top: 32px; margin-top: 32px; border-top: 2px solid var(--spark-border);">
                            <a href="<?= BASEURL ?>/admin/parking.php" class="admin-btn admin-btn-secondary" style="padding: 12px 24px;">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" class="admin-btn admin-btn-primary" style="padding: 12px 24px;">
                                <i class="fas fa-save"></i> Simpan Lahan Parkir
                            </button>
                        </div>
                    </form>
                </div>
            <?php elseif ($parking): ?>
                <!-- View/Edit Mode -->
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
                    <!-- Main Info -->
                    <div class="admin-table-container">
                        <div class="admin-table-header">
                            <h2 class="admin-table-title">Informasi Lahan Parkir</h2>
                            <a href="?id=<?= $id ?>&action=edit" class="admin-btn admin-btn-sm admin-btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </div>
                        
                        <?php if ($action === 'edit'): ?>
                            <form method="POST" style="padding: 24px;">
                                <input type="hidden" name="action" value="edit">
                                
                                <div class="admin-form-group">
                                    <label class="admin-form-label">Nama Tempat</label>
                                    <input type="text" name="nama_tempat" class="admin-form-input" 
                                           value="<?= htmlspecialchars($parking['nama_tempat']) ?>" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label class="admin-form-label">Alamat</label>
                                    <textarea name="alamat_tempat" class="admin-form-textarea" required><?= htmlspecialchars($parking['alamat_tempat']) ?></textarea>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">Latitude</label>
                                        <input type="number" step="any" name="latitude" class="admin-form-input" 
                                               value="<?= $parking['latitude'] ?>">
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">Longitude</label>
                                        <input type="number" step="any" name="longitude" class="admin-form-input" 
                                               value="<?= $parking['longitude'] ?>">
                                    </div>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">Harga per Jam</label>
                                        <input type="number" name="harga_per_jam" class="admin-form-input" 
                                               value="<?= $parking['harga_per_jam'] ?>" min="0" step="1000" required>
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">Jam Buka</label>
                                        <input type="time" name="jam_buka" class="admin-form-input" 
                                               value="<?= date('H:i', strtotime($parking['jam_buka'])) ?>" required>
                                    </div>
                                    <div class="admin-form-group">
                                        <label class="admin-form-label">Jam Tutup</label>
                                        <input type="time" name="jam_tutup" class="admin-form-input" 
                                               value="<?= date('H:i', strtotime($parking['jam_tutup'])) ?>" required>
                                    </div>
                                </div>
                                
                                <button type="submit" class="admin-btn admin-btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                                <a href="?id=<?= $id ?>" class="admin-btn admin-btn-secondary">
                                    Batal
                                </a>
                            </form>
                        <?php else: ?>
                            <div style="padding: 24px;">
                                <table style="width: 100%;">
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Nama Tempat</td>
                                        <td style="padding: 12px 0;"><strong><?= htmlspecialchars($parking['nama_tempat']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Penyedia</td>
                                        <td style="padding: 12px 0;">
                                            <?= htmlspecialchars($parking['nama_pemilik'] ?? '-') ?><br>
                                            <small style="color: var(--spark-text-light);">
                                                <?= htmlspecialchars($parking['email_pemilik'] ?? '-') ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Alamat</td>
                                        <td style="padding: 12px 0;"><?= nl2br(htmlspecialchars($parking['alamat_tempat'])) ?></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Koordinat</td>
                                        <td style="padding: 12px 0;">
                                            <?= $parking['latitude'] ? $parking['latitude'] . ', ' . $parking['longitude'] : '-' ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Harga per Jam</td>
                                        <td style="padding: 12px 0;"><strong><?= formatRupiah($parking['harga_per_jam']) ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Jam Operasional</td>
                                        <td style="padding: 12px 0;">
                                            <?= date('H:i', strtotime($parking['jam_buka'])) ?> - 
                                            <?= date('H:i', strtotime($parking['jam_tutup'])) ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 12px 0; color: var(--spark-text-light);">Total Slot</td>
                                        <td style="padding: 12px 0;">
                                            <span class="admin-badge admin-badge-info">
                                                <?= count($slots) ?> slot
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Sidebar Info -->
                    <div>
                        <!-- Slots -->
                        <div class="admin-table-container" style="margin-bottom: 24px;">
                            <div class="admin-table-header">
                                <h3 class="admin-table-title">Slot Parkir</h3>
                            </div>
                            <div style="padding: 16px; max-height: 300px; overflow-y: auto;">
                                <?php if (empty($slots)): ?>
                                    <p style="text-align: center; color: var(--spark-text-light);">Tidak ada slot</p>
                                <?php else: ?>
                                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                                        <?php foreach ($slots as $slot): ?>
                                            <div style="padding: 8px; background: var(--spark-bg); border-radius: 6px; text-align: center;">
                                                <div style="font-weight: 600;"><?= htmlspecialchars($slot['nomor_slot']) ?></div>
                                                <span class="admin-badge <?= $slot['status_slot'] === 'available' ? 'admin-badge-success' : ($slot['status_slot'] === 'booked' ? 'admin-badge-warning' : 'admin-badge-danger') ?>">
                                                    <?= ucfirst($slot['status_slot']) ?>
                                                </span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Recent Bookings -->
                        <div class="admin-table-container">
                            <div class="admin-table-header">
                                <h3 class="admin-table-title">Booking Terbaru</h3>
                            </div>
                            <div style="padding: 16px;">
                                <?php if (empty($bookings)): ?>
                                    <p style="text-align: center; color: var(--spark-text-light);">Belum ada booking</p>
                                <?php else: ?>
                                    <?php foreach (array_slice($bookings, 0, 5) as $booking): ?>
                                        <div style="padding: 12px 0; border-bottom: 1px solid var(--spark-border);">
                                            <div style="font-weight: 600; margin-bottom: 4px;">
                                                <?= htmlspecialchars($booking['nama_pengguna']) ?>
                                            </div>
                                            <div style="font-size: 12px; color: var(--spark-text-light);">
                                                <?= date('d/m/Y H:i', strtotime($booking['waktu_mulai'])) ?>
                                            </div>
                                            <div style="margin-top: 4px;">
                                                <span class="admin-badge <?= $booking['status_booking'] === 'completed' ? 'admin-badge-success' : 'admin-badge-warning' ?>">
                                                    <?= ucfirst($booking['status_booking']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="admin-table-container">
                    <div style="padding: 40px; text-align: center;">
                        <p>Lahan parkir tidak ditemukan</p>
                        <a href="<?= BASEURL ?>/admin/parking.php" class="admin-btn admin-btn-primary">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

