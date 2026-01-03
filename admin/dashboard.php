<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

$pdo = getDBConnection();

// Statistik Total
$stats = [];

// Total Lahan Parkir
$stmt = $pdo->query("SELECT COUNT(*) as total FROM tempat_parkir");
$stats['total_parking'] = $stmt->fetch()['total'];

// Total Penyedia Lahan
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT id_pemilik) as total 
    FROM tempat_parkir
");
$stats['total_providers'] = $stmt->fetch()['total'];

// Total Pengguna (role = 1)
$stmt = $pdo->query("
    SELECT COUNT(*) as total 
    FROM data_pengguna 
    WHERE role_pengguna = 1
");
$stats['total_users'] = $stmt->fetch()['total'];

// Total Transaksi
$stmt = $pdo->query("SELECT COUNT(*) as total FROM booking_parkir");
$stats['total_transactions'] = $stmt->fetch()['total'];

// Total Pendapatan
$stmt = $pdo->query("
    SELECT COALESCE(SUM(total_harga), 0) as total 
    FROM booking_parkir 
    WHERE status_booking = 'completed'
");
$stats['total_revenue'] = $stmt->fetch()['total'];

// Transaksi Hari Ini
$stmt = $pdo->query("
    SELECT COUNT(*) as total 
    FROM booking_parkir 
    WHERE DATE(created_at) = CURDATE()
");
$stats['today_transactions'] = $stmt->fetch()['total'];

// Lahan Parkir Paling Populer (berdasarkan jumlah booking)
$stmt = $pdo->query("
    SELECT 
        tp.id_tempat,
        tp.nama_tempat,
        COUNT(bp.id_booking) as total_booking
    FROM tempat_parkir tp
    LEFT JOIN booking_parkir bp ON tp.id_tempat = bp.id_tempat
    GROUP BY tp.id_tempat
    ORDER BY total_booking DESC
    LIMIT 5
");
$popular_parkings = $stmt->fetchAll();

// Transaksi Terbaru
$stmt = $pdo->query("
    SELECT 
        bp.id_booking,
        bp.waktu_mulai,
        bp.waktu_selesai,
        bp.total_harga,
        bp.status_booking,
        bp.created_at,
        tp.nama_tempat,
        dp.nama_pengguna
    FROM booking_parkir bp
    JOIN tempat_parkir tp ON bp.id_tempat = tp.id_tempat
    JOIN data_pengguna dp ON bp.id_pengguna = dp.id_pengguna
    ORDER BY bp.created_at DESC
    LIMIT 10
");
$recent_transactions = $stmt->fetchAll();

// Statistik per bulan (6 bulan terakhir)
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as bulan,
        COUNT(*) as jumlah_booking,
        COALESCE(SUM(total_harga), 0) as pendapatan
    FROM booking_parkir
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        AND status_booking = 'completed'
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY bulan DESC
");
$monthly_stats = $stmt->fetchAll();

function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function getStatusBadge($status) {
    $badges = [
        'pending' => 'admin-badge-warning',
        'confirmed' => 'admin-badge-info',
        'completed' => 'admin-badge-success',
        'cancelled' => 'admin-badge-danger'
    ];
    return $badges[$status] ?? 'admin-badge-pending';
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
            
            <!-- Statistics Cards -->
            <div class="admin-stats">
                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h3 class="admin-stat-title">Total Lahan Parkir</h3>
                        <div class="admin-stat-icon">
                            <i class="fas fa-parking"></i>
                        </div>
                    </div>
                    <p class="admin-stat-value"><?= $stats['total_parking'] ?></p>
                    <div class="admin-stat-change">
                        <i class="fas fa-building"></i> <?= $stats['total_providers'] ?> penyedia
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h3 class="admin-stat-title">Total Pengguna</h3>
                        <div class="admin-stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <p class="admin-stat-value"><?= $stats['total_users'] ?></p>
                    <div class="admin-stat-change">
                        <i class="fas fa-user-check"></i> Pengguna terdaftar
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h3 class="admin-stat-title">Total Transaksi</h3>
                        <div class="admin-stat-icon">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                    <p class="admin-stat-value"><?= $stats['total_transactions'] ?></p>
                    <div class="admin-stat-change">
                        <i class="fas fa-calendar-day"></i> <?= $stats['today_transactions'] ?> hari ini
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="admin-stat-header">
                        <h3 class="admin-stat-title">Total Pendapatan</h3>
                        <div class="admin-stat-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                    </div>
                    <p class="admin-stat-value"><?= formatRupiah($stats['total_revenue']) ?></p>
                    <div class="admin-stat-change">
                        <i class="fas fa-check-circle"></i> Dari transaksi selesai
                    </div>
                </div>
            </div>
            
            <!-- Content Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
                <!-- Popular Parkings -->
                <div class="admin-table-container">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">Lahan Parkir Paling Populer</h2>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama Tempat</th>
                                <th>Total Booking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($popular_parkings)): ?>
                                <tr>
                                    <td colspan="2" style="text-align: center; color: var(--spark-text-light);">
                                        Belum ada data
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($popular_parkings as $parking): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= BASEURL ?>/admin/parking-detail.php?id=<?= $parking['id_tempat'] ?>" 
                                               style="color: var(--spark-yellow);">
                                                <?= htmlspecialchars($parking['nama_tempat']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <span class="admin-badge admin-badge-info">
                                                <?= $parking['total_booking'] ?> booking
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Recent Transactions -->
                <div class="admin-table-container">
                    <div class="admin-table-header">
                        <h2 class="admin-table-title">Transaksi Terbaru</h2>
                        <a href="<?= BASEURL ?>/admin/transactions.php" class="admin-btn admin-btn-sm admin-btn-secondary">
                            Lihat Semua
                        </a>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Pengguna</th>
                                <th>Tempat</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_transactions)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--spark-text-light);">
                                        Belum ada transaksi
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_transactions as $transaction): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($transaction['nama_pengguna']) ?></td>
                                        <td><?= htmlspecialchars($transaction['nama_tempat']) ?></td>
                                        <td><?= formatRupiah($transaction['total_harga']) ?></td>
                                        <td>
                                            <span class="admin-badge <?= getStatusBadge($transaction['status_booking']) ?>">
                                                <?= ucfirst($transaction['status_booking']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Monthly Statistics Chart -->
            <div class="admin-table-container">
                <div class="admin-table-header">
                    <h2 class="admin-table-title">Statistik 6 Bulan Terakhir</h2>
                </div>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Jumlah Booking</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($monthly_stats)): ?>
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--spark-text-light);">
                                    Belum ada data
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($monthly_stats as $month): ?>
                                <tr>
                                    <td><?= date('F Y', strtotime($month['bulan'] . '-01')) ?></td>
                                    <td><?= $month['jumlah_booking'] ?> booking</td>
                                    <td><?= formatRupiah($month['pendapatan']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

