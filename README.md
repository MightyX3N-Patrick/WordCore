# WordCore
### A lightweight PHP CMS that needs **no database**.

[![Open Source](https://img.shields.io/badge/Open%20Source-PHP%20CMS-blue?style=flat-square)](#)
[![Version](https://img.shields.io/badge/PHP-8.1+-777bb4?style=flat-square)](#)
[![License](https://img.shields.io/badge/License-MIT-green?style=flat-square)](#)

WordCore is a modern, flat-file PHP CMS designed for speed and simplicity. No MySQL, no migrations, no hosting headaches. Just upload and go.

[**Download WordCore**](https://github.com/your-username/wordcore/releases) • [**Browse Addon Repo**](https://repo.wordcore.space/)

---

## ✨ Features

* **Flat-file JSON storage:** Data lives in `data/` as plain JSON. No database to configure. Swap to SQL at any time by implementing the `StorageDriver` interface.
* **Built-in Admin Panel:** A clean, functional management interface located at `/wc-admin`.
* **Addon & Theme System:** Install from a repo URL in one click, or drop a folder into the directory manually.
* **Hooks & Filters:** A lightweight event system lets addons fire actions and intercept values without coupling to each other.
* **Security Baked-in:** Session-based authentication, CSRF protection, and a role system are built into the core.

---

## 🛠️ Developer Friendly
WordCore stays out of your way. Registering a new admin route or hooking into the lifecycle is a one-liner.

```php
// Register a nav item & admin route in your addon.php
AddonManager::registerNavItem('My Addon', '/wc-admin/my-addon', 30);

Router::get('/wc-admin/my-addon', function() {
    Auth::require();
    $data = Storage::get('my-addon/items', []);
    // Render your view...
});

// Hook into the lifecycle
Hooks::on('wordcore_loaded', function() {
    // Runs after all addons load
});
```

---

## 🔌 Official Addons
Extend WordCore in seconds by adding `repo.wordcore.space` as a repository in your Settings.

| Addon | Description |
| :--- | :--- |
| **📄 Pages** | Create pages with slug-based routing and a menu builder. |
| **✏️ Blog** | Full post management with categories and archives. |
| **📬 Contact Form** | Simple `[contact-form]` shortcode with an admin inbox. |
| **🖼️ Media Manager** | Drag-and-drop file uploads with MIME validation. |
| **🔍 SEO** | Per-page meta titles, descriptions, and OG tags. |
| **📈 Analytics** | Self-hosted page view tracking with a built-in dashboard. |

---

## 🚀 Installation

1.  **Download & Upload:** Download the latest release. Upload the `wordcore/` folder to your web root.
2.  **Set Permissions:** Ensure the `data/` folder is writable by your web server (`chmod 775` or similar).
3.  **Visit Your Site:** WordCore auto-installs on first load. 
    * *Security Note: Log in and change the default credentials immediately.*
4.  **Add Repositories:** Go to **Settings → Repositories**, add `repo.wordcore.space`, then install your modules.

---

## 📄 License
WordCore is free and open-source software licensed under the [MIT License](LICENSE).
