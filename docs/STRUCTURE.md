# 📁 Project Structure - SPARK

Complete overview of SPARK's directory structure and file organization.

## Table of Contents
- [Overview](#overview)
- [Root Directory](#root-directory)
- [Core Directories](#core-directories)
- [Key Files](#key-files)
- [Naming Conventions](#naming-conventions)

---

## Overview

SPARK follows a modular MVC-inspired architecture with clear separation of concerns:

```
spark/
├── 📂 actions/          → Form processing & business logic
├── 📂 admin/            → Admin panel (dashboard, management)
├── 📂 api/              → API endpoints (QR, payment, tickets)
├── 📂 assets/           → Static files (CSS, JS, images)
├── 📂 config/           → Configuration files
├── 📂 database/         → SQL files & setup scripts
├── 📂 docs/             → Documentation (you are here!)
├── 📂 functions/        → Helper functions & utilities
├── 📂 includes/         → Reusable components & partials
├── 📂 lang/             → Language files (i18n)
├── 📂 lib/              → Third-party libraries
├── 📂 pages/            → User-facing pages
├── 📂 public/           → Public entry point (alternative)
├── 📂 uploads/          → User-generated content
├── 📄 index.php         → Main entry point
├── 📄 README.md         → Quick start guide
└── 📄 SETUP_GUIDE.md    → Detailed setup instructions
```

---

## Root Directory

### Entry Points
- **index.php** - Main application entry point (redirects to pages/home.php)
- **dashboard.css** - Global dashboard styling
- **logout.php** - Global logout handler

### Setup Scripts
- **setup.sh** - Automated setup for Linux/Mac
- **setup.bat** - Automated setup for Windows
- **docker-compose.yml** - Docker container configuration
- **Dockerfile** - Docker image definition

### Database
- **spark (2).sql** - Main database schema with sample data

### Documentation
- **README.md** - Project overview and quick start
- **SETUP_GUIDE.md** - Detailed setup instructions
- **ADMIN_IMPROVEMENTS.md** - Admin panel changelog

### Configuration
- **.gitignore** - Git ignore rules
- **.env.example** - Environment variables template (if present)

---

## Core Directories

### 📂 `/actions/`
**Purpose**: Backend processing for user actions

```
actions/
├── app-settings-handler.php       → User app settings (notifications, etc)
├── cancel-ticket.php              → Cancel booking functionality
├── check-status.php               → Check booking/ticket status
├── create-booking.php             → Initialize booking process
├── generate-ticket.php            → Generate QR ticket after booking
├── notification-handler.php       → Handle notification preferences
├── password-handler.php           → Change password
├── process-booking.php            → Process booking with payment
└── profile-handler.php            → Update user profile
```

**Key Concepts**:
- Each file handles specific POST requests
- Uses sessions for user authentication
- Redirects to appropriate pages after processing
- Implements transaction handling for database operations

---

### 📂 `/admin/`
**Purpose**: Complete admin panel for management

```
admin/
├── dashboard.php                  → Main admin dashboard with stats
├── login.php                      → Admin authentication
├── logout.php                     → Admin logout
├── parking.php                    → Manage parking locations
├── parking-detail.php             → Add/edit parking details
├── providers.php                  → Manage parking providers
├── statistics.php                 → Advanced analytics
├── transactions.php               → View all bookings/transactions
├── users.php                      → User management
└── includes/
    ├── footer.php                 → Admin footer component
    ├── header.php                 → Admin header with auth check
    ├── navbar.php                 → Top navigation bar
    └── sidebar.php                → Left sidebar navigation
```

**Features**:
- Role-based access control (admin only)
- Interactive charts with Chart.js
- CRUD operations for all entities
- Real-time statistics
- Export capabilities

---

### 📂 `/api/`
**Purpose**: RESTful API endpoints

```
api/
├── add-payment-method.php         → Add payment method to wallet
├── generate-qr-image.php          → Generate QR code image
├── refresh-qr-token.php           → Refresh QR token for security
├── remove-payment-method.php      → Remove payment method
├── set-default-payment.php        → Set default payment
├── test-generate-ticket.php       → Test ticket generation
├── ticket-checkin.php             → Scan QR for check-in
├── ticket-checkout.php            → Scan QR for check-out
└── validate-ticket.php            → Validate ticket status
```

**Response Format**: JSON
```json
{
  "success": true,
  "data": {},
  "message": "Success message"
}
```

---

### 📂 `/assets/`
**Purpose**: Static files and resources

```
assets/
├── css/
│   ├── admin.css                  → Admin panel styles (970+ lines)
│   ├── booking-*.css              → Booking flow styles
│   ├── dashboard-user.css         → User dashboard (1500+ lines)
│   ├── login-style.css            → Authentication pages
│   ├── profile.css                → User profile page
│   └── ...                        → Other page-specific styles
├── img/
│   ├── logo.png                   → SPARK logo
│   ├── parking-area/              → Parking location images
│   └── ...                        → Other images
└── js/
    ├── booking.js                 → Booking flow logic
    ├── dashboard.js               → Dashboard interactions
    └── ...                        → Other scripts
```

**CSS Architecture**:
- Modular approach (one file per page/component)
- Dark theme with yellow accent (#FFE100)
- Responsive design (mobile-first)
- Custom animations and transitions

---

### 📂 `/config/`
**Purpose**: Application configuration

```
config/
├── app.php                        → Base URL and app settings
├── config.php                     → Auto-detect BASEURL & SECRET_SALT
└── database.php                   → Database connection settings
```

**config/database.php**:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'spark');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');
```

---

### 📂 `/database/`
**Purpose**: Database schemas and migrations

```
database/
├── setup.php                      → Auto-fix database schema ⭐
├── add_profile_image.sql          → Add profile_image column
├── add_user_preferences.sql       → User preferences table
├── booking_parkir.sql             → Booking table schema
├── dummy_data.sql                 → Sample data for testing
├── qr_sessions.sql                → QR session management
├── tickets.sql                    → Tickets table
├── wallet_methods.sql             → Payment methods table
└── MIGRATION_INSTRUCTIONS.md      → Migration guide
```

**Important**: Always run `php database/setup.php` after cloning!

---

### 📂 `/functions/`
**Purpose**: Reusable helper functions

```
functions/
├── admin-auth.php                 → Admin authentication helpers
├── admin-login-proses.php         → Admin login processing
├── auth.php                       → User authentication helpers
├── flash.php                      → Flash message handling
├── format.php                     → Data formatting (currency, date)
├── login-proses.php               → User login processing
├── register.php                   → User registration logic
├── register-proses.php            → Registration processing
└── translate.php                  → Multi-language support
```

**Common Functions**:
- `isLoggedIn()` - Check if user is authenticated
- `getCurrentUser()` - Get current user data
- `formatRupiah($amount)` - Format currency
- `setFlash($message, $type)` - Set flash message

---

### 📂 `/includes/`
**Purpose**: Reusable UI components

```
includes/
├── booking-data.php               → Booking data retrieval
├── booking-form.php               → Booking form component
├── booking-modal.php              → Booking modal popup
├── booking-summary.php            → Booking summary display
├── bookpark.php                   → Book parking component
├── dashboard-navbar.php           → User dashboard navbar
├── dashboard-sidebar.php          → User dashboard sidebar
├── explore.php                    → Explore section
├── footer.php                     → Main footer
├── header.php                     → Main header
├── logo-clients.php               → Client logos
├── navbar.php                     → Main navigation
├── parking-card-components.php    → Parking card UI
└── booking/                       → Booking sub-components
```

**Usage**:
```php
<?php require_once __DIR__ . '/includes/header.php'; ?>
```

---

### 📂 `/lang/`
**Purpose**: Internationalization (i18n)

```
lang/
├── en.php                         → English translations
└── id.php                         → Indonesian translations (default)
```

**Structure**:
```php
return [
    'welcome' => 'Selamat Datang',
    'login' => 'Masuk',
    'register' => 'Daftar',
    // ...
];
```

---

### 📂 `/lib/`
**Purpose**: Third-party libraries

```
lib/
└── phpqrcode-2010100721_1.1.4/    → QR code generation library
```

---

### 📂 `/pages/`
**Purpose**: User-facing pages

```
pages/
├── booking.php                    → Booking page with map
├── dashboard.php                  → User dashboard with parking list
├── history.php                    → Booking history
├── home.php                       → Landing page
├── login.php                      → User login
├── my-ticket.php                  → Active tickets with QR
├── profile.php                    → User profile settings
├── register.php                   → User registration
└── wallet.php                     → Payment methods management
```

**Page Structure**:
```php
<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../functions/auth.php';

startSession();
if (!isLoggedIn()) {
    header('Location: ' . BASEURL . '/pages/login.php');
    exit;
}

$user = getCurrentUser();
?>
<!DOCTYPE html>
<!-- Page content -->
```

---

### 📂 `/uploads/`
**Purpose**: User-generated content

```
uploads/
├── profile/                       → User profile images
│   ├── .gitkeep                   → Keep directory in git
│   └── user-{id}-{timestamp}.jpg
└── tickets/                       → Generated ticket PDFs (future)
```

**Permissions**: `chmod 777` required

---

## Key Files

### Database Connection
**File**: `config/database.php`
```php
function getDBConnection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
```

### Authentication
**File**: `functions/auth.php`
```php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    // Fetch user from database
}
```

### Admin Authentication
**File**: `functions/admin-auth.php`
```php
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . BASEURL . '/admin/login.php');
        exit;
    }
}
```

---

## Naming Conventions

### Files
- **Pages**: lowercase with hyphens (`my-ticket.php`)
- **Classes**: PascalCase (`UserController.php`) - if used
- **Functions**: snake_case (`get_user_by_id()`)
- **Assets**: kebab-case (`dashboard-user.css`)

### Database
- **Tables**: snake_case (`booking_parkir`, `data_pengguna`)
- **Columns**: snake_case (`id_booking`, `nama_tempat`)
- **Foreign Keys**: `id_{table}` (`id_pengguna`, `id_tempat`)

### CSS Classes
- **Admin**: Prefixed with `admin-` (`admin-stat-card`)
- **User**: Descriptive names (`parking-card`, `booking-modal`)
- **Utilities**: Short names (`flex`, `gap-20`)

### Variables
- **PHP**: snake_case (`$user_id`, `$parking_spots`)
- **JavaScript**: camelCase (`userId`, `parkingSpots`)
- **CSS Custom Props**: kebab-case (`--spark-yellow`)

---

## Architecture Patterns

### Request Flow
```
User Request
    ↓
index.php / page.php
    ↓
includes/header.php (auth check)
    ↓
Database Query
    ↓
Display Content
    ↓
includes/footer.php
```

### Form Processing
```
Form Submit (POST)
    ↓
actions/{action}.php
    ↓
Validate Input
    ↓
Process (Database)
    ↓
Set Flash Message
    ↓
Redirect to Page
```

### Admin Access
```
admin/login.php
    ↓
functions/admin-auth.php
    ↓
admin/includes/header.php (requireAdminLogin)
    ↓
admin/dashboard.php
```

---

## Best Practices

### Adding New Pages
1. Create file in `/pages/` or `/admin/`
2. Include authentication check
3. Use header/footer includes
4. Create corresponding CSS in `/assets/css/`
5. Add to navigation if needed

### Adding New Features
1. Create action handler in `/actions/`
2. Add database migrations in `/database/`
3. Update `database/setup.php` for auto-fix
4. Add helper functions in `/functions/`
5. Document in `/docs/`

### Modifying Styles
1. Use existing CSS classes when possible
2. Add new classes with proper naming
3. Maintain mobile responsiveness
4. Test on multiple browsers

---

## Related Documentation

- [Installation Guide](INSTALLATION.md)
- [Configuration Guide](CONFIGURATION.md)
- [API Documentation](API.md)
- [Troubleshooting](TROUBLESHOOTING.md)

---

**Last Updated**: 2026-01-05  
**Maintainers**: SPARK Team
