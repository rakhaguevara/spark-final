/**
 * QR SCANNER JAVASCRIPT
 * Handles camera QR code scanning and validation
 */

let html5QrCode;
let currentScanType = 'entry';
let currentBooking = null;
let recentScans = [];

// Initialize scanner on page load
document.addEventListener('DOMContentLoaded', function () {
    initScanner();
    setupEventListeners();
    loadRecentScans();
});

/**
 * Initialize QR Code Scanner
 */
function initScanner() {
    html5QrCode = new Html5Qrcode("qr-reader");

    const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0
    };

    // Start scanning
    html5QrCode.start(
        { facingMode: "environment" }, // Use back camera
        config,
        onScanSuccess,
        onScanError
    ).catch(err => {
        console.error("Failed to start scanner:", err);
        updateScannerStatus('error', 'Failed to start camera. Please check permissions.');
    });
}

/**
 * Handle successful QR scan
 */
function onScanSuccess(decodedText, decodedResult) {
    console.log("QR Code detected:", decodedText);

    // Pause scanning while processing
    html5QrCode.pause(true);

    // Validate QR code
    validateQR(decodedText);
}

/**
 * Handle scan errors (silent)
 */
function onScanError(errorMessage) {
    // Silent - scanning errors are normal when no QR is in view
}

/**
 * Validate QR Code via API
 */
async function validateQR(qrData) {
    updateScannerStatus('loading', 'Validating QR code...');

    try {
        const response = await fetch(window.BASEURL + '/api/scanner/validate-qr.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                qr_data: qrData,
                scan_type: currentScanType
            })
        });

        const data = await response.json();

        if (data.success && data.can_scan) {
            // Valid QR code
            currentBooking = data.booking;
            showBookingDetails(data.booking);
            updateScannerStatus('success', data.message);

            // Play success sound
            playSound('success');
        } else {
            // Invalid or cannot scan
            updateScannerStatus('error', data.message || 'Invalid QR code');

            // Play error sound
            playSound('error');

            // Resume scanning after 3 seconds
            setTimeout(() => {
                resetScanner();
            }, 3000);
        }
    } catch (error) {
        console.error('Validation error:', error);
        updateScannerStatus('error', 'Failed to validate QR code. Please try again.');

        // Resume scanning after 3 seconds
        setTimeout(() => {
            resetScanner();
        }, 3000);
    }
}

/**
 * Confirm scan and update booking status
 */
async function confirmScan() {
    if (!currentBooking) return;

    const confirmBtn = document.getElementById('confirmScan');
    confirmBtn.disabled = true;
    confirmBtn.textContent = 'Processing...';

    try {
        const response = await fetch(window.BASEURL + '/api/scanner/confirm-scan.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id_booking: currentBooking.id_booking,
                scan_type: currentScanType,
                scanned_by: window.USER_ID
            })
        });

        const data = await response.json();

        if (data.success) {
            // Add to recent scans
            addRecentScan({
                type: currentScanType,
                booking_id: currentBooking.id_booking,
                customer: currentBooking.nama_pengguna,
                location: currentBooking.nama_tempat,
                time: new Date().toLocaleTimeString()
            });

            // Show success message
            updateScannerStatus('success', data.message);

            // Play success sound
            playSound('success');

            // Hide booking details
            document.getElementById('bookingDetails').style.display = 'none';

            // Reset and resume scanning after 2 seconds
            setTimeout(() => {
                resetScanner();
            }, 2000);
        } else {
            alert(data.message || 'Failed to confirm scan');
            confirmBtn.disabled = false;
            confirmBtn.textContent = 'Confirm Scan';
        }
    } catch (error) {
        console.error('Confirm scan error:', error);
        alert('Failed to confirm scan. Please try again.');
        confirmBtn.disabled = false;
        confirmBtn.textContent = 'Confirm Scan';
    }
}

/**
 * Show booking details
 */
function showBookingDetails(booking) {
    const detailsContainer = document.getElementById('bookingDetails');
    const infoContainer = document.getElementById('bookingInfo');

    // Build HTML
    let html = `
        <div class="info-item">
            <span class="info-label">Booking ID</span>
            <span class="info-value">#${booking.id_booking}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Customer Name</span>
            <span class="info-value">${booking.nama_pengguna}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Location</span>
            <span class="info-value">${booking.nama_tempat}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Vehicle Type</span>
            <span class="info-value">${booking.nama_jenis}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Plate Number</span>
            <span class="info-value">${booking.plat_kendaraan}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Start Time</span>
            <span class="info-value">${formatDateTime(booking.waktu_mulai)}</span>
        </div>
        <div class="info-item">
            <span class="info-label">End Time</span>
            <span class="info-value">${formatDateTime(booking.waktu_selesai)}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Status</span>
            <span class="info-value">${booking.status_booking}</span>
        </div>
    `;

    infoContainer.innerHTML = html;
    detailsContainer.style.display = 'block';

    // Re-enable confirm button
    const confirmBtn = document.getElementById('confirmScan');
    confirmBtn.disabled = false;
    confirmBtn.textContent = 'Confirm Scan';
}

/**
 * Update scanner status
 */
function updateScannerStatus(type, message) {
    const statusEl = document.getElementById('scannerStatus');
    statusEl.className = 'scanner-status ' + type;

    let icon = 'fa-camera';
    if (type === 'success') icon = 'fa-check-circle';
    if (type === 'error') icon = 'fa-exclamation-circle';
    if (type === 'loading') icon = 'fa-spinner fa-spin';

    statusEl.innerHTML = `
        <i class="fas ${icon}"></i>
        <p>${message}</p>
    `;
}

/**
 * Reset scanner
 */
function resetScanner() {
    currentBooking = null;
    document.getElementById('bookingDetails').style.display = 'none';
    updateScannerStatus('', 'Ready to scan');

    // Resume scanning
    if (html5QrCode) {
        html5QrCode.resume();
    }
}

/**
 * Add recent scan to list
 */
function addRecentScan(scan) {
    recentScans.unshift(scan);

    // Keep only last 10 scans
    if (recentScans.length > 10) {
        recentScans.pop();
    }

    // Save to localStorage
    localStorage.setItem('recentScans', JSON.stringify(recentScans));

    // Update UI
    renderRecentScans();
}

/**
 * Load recent scans from localStorage
 */
function loadRecentScans() {
    const saved = localStorage.getItem('recentScans');
    if (saved) {
        recentScans = JSON.parse(saved);
        renderRecentScans();
    }
}

/**
 * Render recent scans list
 */
function renderRecentScans() {
    const container = document.getElementById('recentScans');

    if (recentScans.length === 0) {
        container.innerHTML = '<p class="empty-message">No recent scans</p>';
        return;
    }

    let html = '';
    recentScans.forEach(scan => {
        const iconMap = {
            entry: 'fa-sign-in-alt',
            exit: 'fa-sign-out-alt',
            stay: 'fa-parking'
        };

        html += `
            <div class="scan-item ${scan.type}">
                <div class="scan-item-info">
                    <div class="scan-item-icon">
                        <i class="fas ${iconMap[scan.type]}"></i>
                    </div>
                    <div class="scan-item-details">
                        <h4>${scan.customer} - #${scan.booking_id}</h4>
                        <p>${scan.location}</p>
                    </div>
                </div>
                <span class="scan-item-time">${scan.time}</span>
            </div>
        `;
    });

    container.innerHTML = html;
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Scan type buttons
    document.querySelectorAll('.scan-type-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove active class from all
            document.querySelectorAll('.scan-type-btn').forEach(b => {
                b.classList.remove('active');
            });

            // Add active class to clicked
            this.classList.add('active');

            // Update current scan type
            currentScanType = this.dataset.type;

            // Reset scanner
            resetScanner();
        });
    });

    // Confirm scan button
    document.getElementById('confirmScan').addEventListener('click', confirmScan);

    // Cancel button
    document.getElementById('cancelScan').addEventListener('click', resetScanner);

    // Close details button
    document.getElementById('closeDetails').addEventListener('click', resetScanner);
}

/**
 * Format date time
 */
function formatDateTime(dateString) {
    if (!dateString) return '--:--';
    const date = new Date(dateString);
    return date.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Play sound (optional)
 */
function playSound(type) {
    // You can add audio files and play them here
    // For now, we'll use vibration on mobile devices
    if (navigator.vibrate) {
        if (type === 'success') {
            navigator.vibrate(200);
        } else if (type === 'error') {
            navigator.vibrate([100, 50, 100]);
        }
    }
}

/**
 * Cleanup on page unload
 */
window.addEventListener('beforeunload', function () {
    if (html5QrCode) {
        html5QrCode.stop().catch(err => {
            console.error("Failed to stop scanner:", err);
        });
    }
});
