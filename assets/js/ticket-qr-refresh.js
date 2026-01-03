/**
 * TICKET QR AUTO-REFRESH
 * Refreshes QR code every 10 seconds
 */

(function () {
    'use strict';

    const ticketId = window.APP_CONFIG?.activeTicketId;
    if (!ticketId) return;

    const qrCanvas = document.getElementById('qrCanvas');
    const countdownEl = document.getElementById('countdown');
    let countdown = 10;
    let countdownInterval;

    // Generate QR code from token
    function generateQR(token) {
        if (!qrCanvas) return;

        QRCode.toCanvas(qrCanvas, token, {
            width: 280,
            margin: 2,
            color: {
                dark: '#000000',
                light: '#ffffff'
            }
        }, function (error) {
            if (error) console.error('QR generation error:', error);
        });
    }

    // Fetch new QR token
    async function refreshQRToken() {
        try {
            const response = await fetch(window.APP_CONFIG.BASEURL + '/api/refresh-qr-token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ticket_id=' + encodeURIComponent(ticketId)
            });

            const data = await response.json();

            if (data.success) {
                generateQR(data.qr_token);
                resetCountdown();
            } else {
                console.error('Token refresh failed:', data.message);
                // Stop refresh if ticket is no longer active
                if (data.message.includes('not active')) {
                    clearInterval(countdownInterval);
                    location.reload();
                }
            }
        } catch (error) {
            console.error('Refresh error:', error);
        }
    }

    // Countdown timer
    function startCountdown() {
        countdownInterval = setInterval(() => {
            countdown--;
            if (countdownEl) {
                countdownEl.textContent = countdown;
            }

            if (countdown <= 0) {
                refreshQRToken();
            }
        }, 1000);
    }

    function resetCountdown() {
        countdown = 10;
        if (countdownEl) {
            countdownEl.textContent = countdown;
        }
    }

    // Initialize
    function init() {
        refreshQRToken();
        startCountdown();
    }

    // Start on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
