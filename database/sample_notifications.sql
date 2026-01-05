-- Sample Notifications for Testing
-- Insert these into notifikasi_pengguna table

-- Replace 1 with actual user ID from pengguna table

-- Booking Success (Today)
INSERT INTO notifikasi_pengguna (id_pengguna, judul, pesan, is_read, created_at) VALUES
(1, 'Booking Confirmed', 'Your parking reservation at Plaza Indonesia has been confirmed. Show your QR code at the entrance.', 0, NOW()),
(1, 'Payment Success', 'Payment of Rp 15,000 has been successfully processed for your parking booking.', 0, DATE_SUB(NOW(), INTERVAL 2 HOUR));

-- Reminder (Today)
INSERT INTO notifikasi_pengguna (id_pengguna, judul, pesan, is_read, created_at) VALUES
(1, 'Upcoming Booking Reminder', 'Your parking reservation at Grand Indonesia starts in 1 hour. Don\'t forget to bring your QR code!', 0, DATE_SUB(NOW(), INTERVAL 30 MINUTE));

-- Scan Success (Yesterday)
INSERT INTO notifikasi_pengguna (id_pengguna, judul, pesan, is_read, created_at) VALUES
(1, 'Entry Scan Success', 'You have successfully entered the parking area at Senayan City. Enjoy your visit!', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(1, 'Exit Scan Success', 'You have successfully exited the parking area. Total parking time: 2 hours 15 minutes.', 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Cancelled (Yesterday)
INSERT INTO notifikasi_pengguna (id_pengguna, judul, pesan, is_read, created_at) VALUES
(1, 'Booking Cancelled', 'Your booking at Pacific Place has been cancelled. Refund will be processed within 1-3 business days.', 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- System Updates (Earlier)
INSERT INTO notifikasi_pengguna (id_pengguna, judul, pesan, is_read, created_at) VALUES
(1, 'Profile Incomplete Reminder', 'Complete your profile to get personalized parking recommendations and exclusive offers.', 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 'New Feature Available', 'SPARK now supports monthly parking subscriptions! Check out our new subscription plans.', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(1, 'Welcome to SPARK', 'Thank you for joining SPARK! Find and book parking spots easily across Jakarta.', 1, DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Scan Failed (Earlier)
INSERT INTO notifikasi_pengguna (id_pengguna, judul, pesan, is_read, created_at) VALUES
(1, 'QR Scan Failed', 'Your QR code could not be scanned. Please ensure your screen brightness is at maximum and try again.', 1, DATE_SUB(NOW(), INTERVAL 4 DAY));
