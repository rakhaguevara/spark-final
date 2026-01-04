# ⚙️ Configuration Guide - SPARK

Complete guide to configure SPARK for different environments.

## Table of Contents
- [Environment Setup](#environment-setup)
- [Database Configuration](#database-configuration)
- [Application Settings](#application-settings)
- [Security Configuration](#security-configuration)
- [Google Maps API](#google-maps-api)
- [Payment Gateway](#payment-gateway)
- [Email Configuration](#email-configuration)

---

## Environment Setup

### Development Environment

**File**: `config/database.php`
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'spark');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }
    return $pdo;
}
```

### Production Environment

Create separate config or use environment variables:

**Option 1: Separate Config File**
```php
// config/database.production.php
define('DB_HOST', 'production-host.com');
define('DB_USER', 'prod_user');
define('DB_PASS', 'strong_password_here');
define('DB_NAME', 'spark_production');
define('DB_PORT', '3306');
```

**Option 2: Environment Variables**
```php
// config/database.php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'spark');
```

Then create `.env` file:
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=spark
```

---

## Database Configuration

### Connection Settings

**File**: `config/database.php`

```php
// Basic settings
define('DB_HOST', 'localhost');     // Database host
define('DB_USER', 'root');          // Database username
define('DB_PASS', '');              // Database password
define('DB_NAME', 'spark');         // Database name
define('DB_PORT', '3306');          // Port (default: 3306)
define('DB_CHARSET', 'utf8mb4');    // Character set

// Connection options
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_PERSISTENT => false,  // Set true for persistent connections
];
```

### Remote Database

For remote MySQL server:

```php
define('DB_HOST', '123.456.789.101');  // Remote IP or hostname
define('DB_PORT', '3306');
define('DB_USER', 'remote_user');
define('DB_PASS', 'remote_password');
```

### Multiple Databases

If you need multiple database connections:

```php
function getDBConnection($type = 'main') {
    static $connections = [];
    
    if (!isset($connections[$type])) {
        switch ($type) {
            case 'main':
                $dsn = "mysql:host=localhost;dbname=spark;charset=utf8mb4";
                break;
            case 'analytics':
                $dsn = "mysql:host=localhost;dbname=spark_analytics;charset=utf8mb4";
                break;
        }
        
        $connections[$type] = new PDO($dsn, DB_USER, DB_PASS);
    }
    
    return $connections[$type];
}
```

---

## Application Settings

### Base URL Configuration

**File**: `config/config.php`

```php
// Auto-detect (recommended for development)
if (!defined('BASEURL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // Auto-detect base path
    if (strpos($scriptPath, '/admin/') !== false) {
        $basePath = dirname(dirname($scriptPath));
    } else {
        $basePath = dirname($scriptPath);
    }
    
    $basePath = rtrim($basePath, '/');
    define('BASEURL', $protocol . '://' . $host . $basePath);
}
```

Or manually set:

```php
// Development
define('BASEURL', 'http://localhost:8000');

// Production
define('BASEURL', 'https://spark-parking.com');

// Subdirectory
define('BASEURL', 'http://localhost/spark');
```

### Security Salt

**File**: `config/config.php`

```php
// Security salt for hashing
if (!defined('SECRET_SALT')) {
    define('SECRET_SALT', getenv('SECRET_SALT') ?: 'spark-default-salt-change-in-production-2026');
}
```

**⚠️ IMPORTANT**: Change this in production!

Generate a secure salt:
```bash
# Linux/Mac
openssl rand -hex 32

# Or PHP
php -r "echo bin2hex(random_bytes(32));"
```

Then set in `.env`:
```env
SECRET_SALT=your_generated_salt_here
```

---

## Security Configuration

### Session Settings

**File**: Create `config/session.php`

```php
<?php
// Session configuration
ini_set('session.cookie_httponly', 1);  // Prevent JavaScript access
ini_set('session.use_only_cookies', 1); // Only use cookies, not URL
ini_set('session.cookie_secure', 1);    // HTTPS only (set to 0 for dev)
ini_set('session.cookie_samesite', 'Strict'); // CSRF protection

// Session lifetime (24 hours)
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_lifetime', 86400);

// Session name (hide default PHPSESSID)
session_name('SPARK_SESSION');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

### Password Hashing

Always use `password_hash()`:

```php
// When registering
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// When logging in
if (password_verify($input_password, $stored_hash)) {
    // Login successful
}
```

### CSRF Protection

Add to forms:

```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// In form
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// Verify on submit
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die('CSRF token mismatch');
}
```

---

## Google Maps API

### Get API Key

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create new project or select existing
3. Enable **Maps JavaScript API**
4. Create credentials → API Key
5. Restrict API key to your domain

### Configure in SPARK

**File**: Update map initialization in dashboard or booking pages

**Current** (pages/dashboard.php or includes/booking-modal.php):
```html
<script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places&callback=initMap" async defer></script>
```

**Replace** `YOUR_API_KEY` with your actual key:
```html
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyXXXXXXXXXXXXXXXXXXXXXXX&libraries=places&callback=initMap" async defer></script>
```

### Environment Variable (Recommended)

**File**: `config/config.php`
```php
define('GOOGLE_MAPS_API_KEY', getenv('GOOGLE_MAPS_API_KEY') ?: '');
```

**.env**:
```env
GOOGLE_MAPS_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXX
```

**Usage in pages**:
```php
<script src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&libraries=places&callback=initMap" async defer></script>
```

---

## Payment Gateway

### Midtrans Configuration (Example)

**File**: Create `config/payment.php`

```php
<?php
// Midtrans configuration
define('MIDTRANS_SERVER_KEY', getenv('MIDTRANS_SERVER_KEY') ?: '');
define('MIDTRANS_CLIENT_KEY', getenv('MIDTRANS_CLIENT_KEY') ?: '');
define('MIDTRANS_IS_PRODUCTION', getenv('APP_ENV') === 'production');
define('MIDTRANS_IS_SANITIZED', true);
define('MIDTRANS_IS_3DS', true);
```

**.env**:
```env
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
APP_ENV=development
```

### Test Mode

Enable test mode by setting:
```php
define('TEST_MODE', true);  // Skip payment, auto-confirm bookings
```

In `actions/process-booking.php`, payment is skipped when `TEST_MODE` is true.

---

## Email Configuration

### SMTP Settings

**File**: Create `config/email.php`

```php
<?php
// Email configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'noreply@spark.com');
define('MAIL_FROM_NAME', 'SPARK Parking');
```

**.env**:
```env
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your-email@gmail.com
SMTP_PASSWORD=your-app-password
```

### Using PHPMailer

```bash
composer require phpmailer/phpmailer
```

```php
use PHPMailer\PHPMailer\PHPMailer;

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;
    
    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->isHTML(true);
    
    $mail->send();
}
```

---

## File Upload Configuration

### Upload Limits

**File**: `.htaccess` or `php.ini`

```apache
# .htaccess
php_value upload_max_filesize 10M
php_value post_max_size 12M
php_value max_execution_time 300
php_value max_input_time 300
```

Or in PHP:
```php
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '12M');
ini_set('max_execution_time', '300');
```

### Allowed File Types

```php
$allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
$max_size = 5 * 1024 * 1024; // 5MB

if (!in_array($_FILES['file']['type'], $allowed_types)) {
    die('Invalid file type');
}

if ($_FILES['file']['size'] > $max_size) {
    die('File too large');
}
```

---

## Error Handling

### Display Errors (Development)

```php
// config/app.php
if (getenv('APP_ENV') === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}
```

### Error Logging

```php
// Log to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-error.log');
```

### Custom Error Handler

```php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("Error [$errno]: $errstr in $errfile on line $errline");
    
    if (getenv('APP_ENV') === 'production') {
        // Show friendly message
        echo "An error occurred. Please try again later.";
    } else {
        // Show detailed error
        echo "<b>Error:</b> $errstr<br>";
        echo "<b>File:</b> $errfile<br>";
        echo "<b>Line:</b> $errline<br>";
    }
});
```

---

## Timezone Configuration

```php
// config/app.php
date_default_timezone_set('Asia/Jakarta');  // Or your timezone
```

Available timezones:
- `Asia/Jakarta` (WIB)
- `Asia/Makassar` (WITA)
- `Asia/Jayapura` (WIT)
- `UTC`

---

## Performance Optimization

### Opcache (Production)

```php
// php.ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### Database Connection Pooling

Use persistent connections:
```php
PDO::ATTR_PERSISTENT => true
```

### Caching

Implement caching for frequently accessed data:

```php
// Simple file cache
function cached($key, $callback, $ttl = 3600) {
    $cache_file = __DIR__ . '/../cache/' . md5($key) . '.cache';
    
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $ttl) {
        return unserialize(file_get_contents($cache_file));
    }
    
    $data = $callback();
    file_put_contents($cache_file, serialize($data));
    
    return $data;
}
```

---

## Environment-Specific Configuration

### Check Environment

```php
function isProduction() {
    return getenv('APP_ENV') === 'production';
}

function isDevelopment() {
    return getenv('APP_ENV') === 'development';
}
```

### Load Config Based on Environment

```php
// config/app.php
$env = getenv('APP_ENV') ?: 'development';

if ($env === 'production') {
    require_once __DIR__ . '/database.production.php';
} else {
    require_once __DIR__ . '/database.php';
}
```

---

## Configuration Checklist

Before deploying to production:

- [ ] Change `SECRET_SALT`
- [ ] Set strong database password
- [ ] Configure Google Maps API key
- [ ] Set up email SMTP
- [ ] Disable error display
- [ ] Enable error logging
- [ ] Set production `BASEURL`
- [ ] Configure payment gateway
- [ ] Set session to HTTPS only
- [ ] Enable Opcache
- [ ] Set proper file permissions
- [ ] Configure backup strategy

---

## Related Documentation

- [Installation Guide](INSTALLATION.md)
- [Project Structure](STRUCTURE.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

**Last Updated**: 2026-01-05  
**Security**: Always use environment variables for sensitive data
