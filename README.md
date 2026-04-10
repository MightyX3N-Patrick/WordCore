# WordCore
### A lightweight PHP CMS platform with an addon/theme system and flat-file JSON storage.

[![Open Source](https://img.shields.io/badge/Open%20Source-PHP%20CMS-blue?style=flat-square)](#)
[![Version](https://img.shields.io/badge/PHP-8.1+-777bb4?style=flat-square)](#)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](#)

WordCore is a modern, flat-file PHP CMS designed for speed and simplicity. No MySQL, no migrations, no hosting headaches. Just upload and go.

[**Download WordCore**](https://github.com/your-username/wordcore/releases) • [**Browse Addon Repo**](https://repo.wordcore.space/)

---

## ✨ Core Features

* **Flat-file JSON storage:** Data lives in `data/` as plain JSON. No database to configure.
* **Built-in Admin Panel:** A clean management interface located at `/wc-admin`.
* **Addon & Theme System:** Install from a repo URL in one click, or drop a folder in manually.
* **Hooks & Filters:** A lightweight event system lets addons fire actions without coupling.
* **Security:** Session-based authentication and CSRF protection built into the core.

---

## 🚀 Quick Start

### Requirements
- **PHP 8.1+**
- **Apache** with `mod_rewrite` (or Nginx equivalent)
- `allow_url_fopen = On` in php.ini (for repo downloads)
- `ZipArchive` PHP extension (for .zip installs)

### Setup
1. Upload the `wordcore/` folder to your web root.
2. Ensure `AllowOverride All` is set for the directory (Apache).
3. Make the `data/` directory writable: `chmod -R 755 data/`
4. Visit your site — WordCore auto-installs on first load.

> [!CAUTION]
> **Default credentials:** `admin` / `admin` — change these immediately after your first login.

---

## 📁 File Structure

```text
wordcore/
├── index.php           # Entry point
├── .htaccess           # URL rewriting
├── core/               # Framework core (do not edit)
│   ├── Storage.php     # Storage abstraction
│   ├── Hooks.php       # Event/filter system
│   └── ...             # Drivers, Auth, Router
├── admin/              # Admin panel controllers & views
├── addons/             # Installed addons
├── themes/             # Installed themes
└── data/               # JSON data storage (auto-created)
    └── core/           # Settings, users, and repo configs
```

---

## 🛠️ Developer Guide

### Building an Addon
Create `addons/my-addon/addon.json`:
```json
{
  "slug": "my-addon",
  "name": "My Addon",
  "version": "1.0.0",
  "description": "What it does.",
  "author": "You"
}
```

Create `addons/my-addon/addon.php`:
```php
<?php
Hooks::on('wordcore_loaded', function () {
    // your code here
});

// Read/write persistent data — namespaced to your addon
$value = Storage::get('my-addon/settings');
Storage::set('my-addon/settings', ['key' => 'value']);

// Add an admin sidebar link
AddonManager::registerNavItem('My Addon', '/admin/my-addon', 30);
```

### Hooks & Storage API

| Hook | When |
|------|------|
| `wordcore_loaded` | After all active addons are loaded |
| `addon_activated` | When an addon is activated (`$slug` passed) |
| `settings_permissions_ui` | Inside the Permissions settings section |

```php
Storage::get('namespace/key', $default);
Storage::set('namespace/key', $value);
Storage::delete('namespace/key');
```

---

## 🔌 Official Addons
Add **repo.wordcore.space** in your Settings to install these in one click:

| Addon | Description |
| :--- | :--- |
| **📄 Pages** | Slug-based routing and a menu builder. |
| **✏️ Blog** | Post management with categories and archives. |
| **🖼️ Media** | Drag-and-drop file uploads with MIME validation. |
| **📈 Analytics** | Self-hosted page view tracking and dashboard. |

---

## 📄 License
WordCore is free and open-source software licensed under the [MIT License](LICENSE).
