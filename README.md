# SecureCore for Laravel

SecureCore is an advanced security hardening framework designed for Laravel applications. It provides a multi-layered defense strategy to protect sensitive data, mitigate API vulnerabilities (like BOLA), and prevent automated infrastructure scanning through intelligent intrusion detection.

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


## Key Features & Implementation

### 1. Transparent Database Encryption
Protect PII (Personally Identifiable Information) by encrypting model attributes at rest. SecureCore automatically handles encryption/decryption and includes safety checks to prevent errors with legacy non-encrypted data.

### Usage:
Add the  `HasSecureAttributes` trait to your model and define the `$encryptable` array.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Amreljako\SecureCore\Traits\HasSecureAttributes;

class User extends Model
{
    use HasSecureAttributes;

    protected $encryptable = [
        'phone_number',
        'national_id',
    ];
}
```

### 2. Auto-BOLA Protection (ID Obfuscation)
Mitigates Broken Object Level Authorization (BOLA) by masking internal Database IDs using Hashids. This prevents attackers from guessing or enumerating resource IDs (e.g., changing `/api/orders/1` to `/api/orders/vj8k2p`).

### Usage:
Apply the `SecureResource` trait to your model.


```php
namespace App\Models;

use Amreljako\SecureCore\Traits\SecureResource;

class Order extends Model
{
    use SecureResource;
}
```

### 3. Intelligent HoneyPot & Anti-Scanning
A scoring-based intrusion detection system that traps automated bots. It monitors access to sensitive paths and tracks 404 error patterns. Once an IP reaches the suspicion threshold, it is automatically blacklisted.

### Configuration:

```php
// config/secure-core.php
'honeypot' => [
    'enabled' => true,
    'threshold' => 20, // Points before auto-blocking
    'traps' => ['.env', 'wp-login.php', 'setup.php', '.git/config'],
],
```



### 4. API Request Signature Verification
Ensures data integrity for sensitive endpoints. This requires an `X-Secure-Signature` header, which is an HMAC-SHA256 hash of the payload using the `APP_KEY`.

### Implementation:

```php
// Apply to sensitive routes in routes/api.php
Route::middleware(['secure.signature'])->group(function () {
    Route::post('/v1/payments', [PaymentController::class, 'process']);
});
```


### 5. Automated Production Shield

When `APP_ENV` is set to `production`, SecureCore enforces strict security defaults:

- **Forced Debug Disable:** Overrides `APP_DEBUG` to `false`.
- **Environment Scrubbing:** Masks sensitive ENV variables in logs and error reports.
- **Server Masking:** Strips `X-Powered-By` and `Server` headers to reduce information exposure.


### Security Headers (Automatic)
The following headers are injected into every response to enforce browser-level security:

- **Strict-Transport-Security:** max-age=31536000; includeSubDomains

- **Content-Security-Policy:** upgrade-insecure-requests

- **X-Frame-Options:** SAMEORIGIN

- **X-Content-Type-Options:** nosniff

- **X-XSS-Protection:** 1; mode=block


### Configuration Summary
The package behavior can be fine-tuned via `config/secure-core.php`. Environment variables available:


```env
SECURE_CORE_ENCRYPTION=true
SECURE_CORE_SIGNATURE_CHECK=true
SECURE_CORE_HONEYPOT=true
```
---

## License
The MIT License (MIT). Please see the License File for more information.

Developed by Amr Elsayed (https://github.com/amreljako)
