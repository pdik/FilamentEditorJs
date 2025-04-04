# Filament EditorJS

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pdik/filamenteditorjs.svg?style=flat-square)](https://packagist.org/packages/pdik/filamenteditorjs)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/pdik/filamenteditorjs/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/pdik/filamenteditorjs/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/pdik/filamenteditorjs/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/pdik/filamenteditorjs/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/pdik/filamenteditorjs.svg?style=flat-square)](https://packagist.org/packages/pdik/filamenteditorjs)

A **FilamentPHP custom form field** that brings the power of [Editor.js](https://editorjs.io/) into your admin panel. This field gives you a clean, structured, block-style content editor that stores data as JSON — perfect for building rich, flexible, and API-friendly content.

Ideal for use cases like blog posts, lesson content, FAQs, or any interface where structured content matters.

---

## ✨ Features

- ⚡ Block-style editing with [Editor.js](https://editorjs.io/)
- 🔌 Drop-in Filament form component
- 🧱 Configurable tools (header, paragraph, image, list, and more)
- 💾 Stores content as JSON
- 🎨 Built for the TALL stack (Livewire + Alpine.js)
- 🔧 Publishable config and views for customization

---

## 📦 Installation

Install via composer:

```bash
composer require pdik/filamenteditorjs
```

Optional:

```bash
php artisan vendor:publish --tag="filamenteditorjs-config"
php artisan vendor:publish --tag="filamenteditorjs-views"
```

---

## 🛠 Usage

Inside a Filament form:

```php
use Pdik\FilamentEditorJs\Forms\Components\EditorJs;

\Pdik\FilamentEditorJs\FilamentEditorJs::make('content')
    ->label('Content')
    ->tools(['header', 'paragraph', 'list', 'image']) // Optional: configure active tools
    ->required(),
```

The field will save content as Editor.js-compatible JSON. You can render it on the frontend with the official Editor.js renderer or any custom solution.
Soon there will be a Entry Field to Render the content
---

## 🧪 Testing

```bash
composer test
```

---

## 🗺 Coming Soon

- 🧾 **EditorJS Entries Viewer** – Easily preview and manage stored Editor.js content inside your Filament panel
- 🧩 Tool plugin registration via config
- 🛡️ Validation support for Editor.js blocks
- 📄 Blade component renderer for frontend use

---

## 📜 Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## 🤝 Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

---

## 🔐 Security

Please review [our security policy](../../security/policy) for guidelines on reporting vulnerabilities.

---

## 👥 Credits

- [Pepijn Dik](https://github.com/pdik)
- [All Contributors](../../contributors)

---

## ⚖️ License

The MIT License (MIT). See [License File](LICENSE.md) for more information.
