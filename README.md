# SecureCore for Laravel

SecureCore is a professional security hardening framework for Laravel applications. It provides multiple layers of defense to protect sensitive data, prevent unauthorized scanning, and ensure environment integrity.

---

## Installation

Install the package via composer:

```bash
composer require amreljako/secure-core
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag="secure-core-config"
```

---

## Configuration

The package behavior is controlled via `config/secure-core.php`. You can override these settings in your `.env` file:

```env
SECURE_CORE_ENCRYPTION=true
SECURE_CORE_SIGNATURE_CHECK=true
SECURE_CORE_HONEYPOT=true
```

---

## Feature Implementation Guide

### 1. Database Encryption
To encrypt sensitive data in your models, use the `HasSecureAttributes` trait and define an `$encryptable` array.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Amreljako\SecureCore\Traits\HasSecureAttributes;

class User extends Model
{
    use HasSecureAttributes;

    /**
     * Attributes to be encrypted in the database.
     */
    protected $encryptable = [
        'phone_number',
        'national_id',
        'secret_answer',
    ];
}
```

### 2. API Request Signature Verification
Protect your API from data tampering. This requires an `X-Secure-Signature` header (HMAC-SHA256 hash of the payload using the `APP_KEY`).

**Step 1: Apply Middleware in `routes/api.php`**
```php
Route::middleware(['secure.signature'])->group(function () {
    Route::post('/v1/account/update', [AccountController::class, 'update']);
});
```

**Step 2: Client-side logic (Example)**
The client should generate the signature as follows:
```php
$payload = json_encode($data);
$signature = hash_hmac('sha256', $payload, config('app.key'));
// Send $signature in X-Secure-Signature header
```

### 3. HoneyPot Intrusion Detection
The HoneyPot works globally to trap automated scanners. You can customize the trap paths in the config file.

```php
// config/secure-core.php
'honeypot' => [
    'enabled' => true,
    'auto_block' => true,
    'traps' => [
        'admin', 
        '.env', 
        'wp-login.php', 
        'phpinfo'
    ],
],
```

### 4. Logging Masking
Sensitive fields are automatically scrubbed from your log files. Add fields to the mask list in the config.

```php
// config/secure-core.php
'logging' => [
    'masked_fields' => [
        'password',
        'password_confirmation',
        'cvv',
        'card_number',
        'api_key',
    ],
],
```

### 5. Production Shield (Automated)
When `APP_ENV` is set to `production`, SecureCore automatically:
- Forces `APP_DEBUG` to `false`.
- Masks sensitive Environment variables in error pages.
- Injects Security Headers (X-Frame-Options, HSTS, etc.) into all responses.

---

## Security Headers (Automatic)
The following headers are applied to every response via middleware:
- **X-Content-Type-Options:** nosniff
- **X-Frame-Options:** SAMEORIGIN
- **X-XSS-Protection:** 1; mode=block
- **Strict-Transport-Security:** max-age=31536000; includeSubDomains
- **Content-Security-Policy:** upgrade-insecure-requests

---

## License
The MIT License (MIT). Please see the License File for more information.

Developed by Amr Elsayed (https://github.com/amreljako)
