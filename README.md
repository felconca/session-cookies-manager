# Session Cookies Manager

A lightweight and secure PHP library for managing sessions with ease.  
It provides a simple, object-oriented interface to **create**, **read**, **update**, **regenerate**, and **destroy** sessions — all while supporting **secure cookie settings**, **namespaces**, and **automatic session timeout** for idle users.

## 🚀 Features

- 🔒 Secure session cookie configuration (`HttpOnly`, `Secure`, `SameSite`)
- 🧩 Namespaced session storage (isolated data per module)
- ⏱️ Automatic idle timeout expiration
- 🔁 Session ID regeneration for security
- 💣 Safe session destruction
- 🧠 Compatible with **PHP 7.4+ and PHP 8+**

## 📦 Installation

### Option 1: Manual Installation

1. Download or clone this repository:

   ```bash
   git clone https://github.com/felconca/session-cookies-manager.git
   cd session-manager
   ```

2. Include the class in your project:

   ```php
   require_once __DIR__ . '/src/SessionManager.php';
   ```

### Option 2: Composer (Recommended)

If you use Composer, add this package to your `composer.json`:

```bash
composer require felconca/session-cookies-manager
```

Then include Composer’s autoloader:

```php
require_once __DIR__ . '/vendor/autoload.php';
```

## ⚙️ Usage Example

```php
<?php

use Cookies\Session\SessionManager;

// Create a session instance with secure configuration
$session = new SessionManager('user', [
    'lifetime' => 3600,        // Cookie lifespan (1 hour)
    'idle_timeout' => 900,     // Auto-logout after 15 minutes idle
    'secure' => false,         // Set true if using HTTPS
    'httponly' => true,
    'samesite' => 'Strict',
    'name' => 'MYAPPSESSID'
]);

// Store data
$session->set('username', 'john_doe');
$session->set('role', 'admin');

// Retrieve data
echo "User: " . $session->get('username') . PHP_EOL;

// Check if a session variable exists
if ($session->has('role')) {
    echo "Role: " . $session->get('role') . PHP_EOL;
}

// Regenerate session ID
$session->regenerate();

// Clear a specific key
$session->remove('role');

// Destroy session completely
// $session->destroy();
```

## ⚡ Configuration Options

| Key            | Description                                               | Default       |
| -------------- | --------------------------------------------------------- | ------------- |
| `lifetime`     | Cookie lifespan in seconds                                | `1800`        |
| `idle_timeout` | Idle timeout before session auto-expires                  | `900`         |
| `path`         | Cookie path                                               | `'/'`         |
| `domain`       | Cookie domain                                             | `''`          |
| `secure`       | Send cookie only over HTTPS                               | `auto-detect` |
| `httponly`     | Prevent JavaScript from accessing cookies                 | `true`        |
| `samesite`     | Restrict cross-site requests (`Lax`, `Strict`, or `None`) | `'Lax'`       |
| `name`         | Custom session name                                       | `'PHPSESSID'` |

## 🧩 Namespaces

Each instance of `SessionManager` can manage a unique namespace:

```php
$userSession = new SessionManager('user');
$cartSession = new SessionManager('cart');

$userSession->set('id', 10);
$cartSession->set('items', ['apple', 'banana']);
```

Namespaces prevent key collisions between different parts of your app.

## 🛡️ Security Notes

- Always set `'secure' => true` when using HTTPS.
- Use `'samesite' => 'Strict'` or `'Lax'` to mitigate CSRF risks.
- Combine this library with server-level session settings for maximum security.

## 🧑‍💻 Requirements

- **PHP 7.4 or higher**
- No external dependencies (pure PHP)

## 🪪 License

MIT License © 2025
