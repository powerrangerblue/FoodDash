# FoodDash

🚀 FoodDash is a lightweight, extensible food ordering and delivery platform built on CodeIgniter 4.

This repository contains the web application (admin, restaurant, and API) and supporting code used by the FoodDash service. The project includes mobile clients for customers and riders developed in Android Studio.

Key links:

- Android mobile apps (customer & rider): https://github.com/codex-xx/FOODDASH-ANDROID.git 📱

**Highlights**

- Modern CodeIgniter 4 PHP backend for admin, restaurants, and API.
- Mobile-first experience with dedicated Android apps for customers and riders.
- Maps & routing using Leaflet + OpenStreetMap for both mobile and web (admin/restaurant views) 🗺️
- Database migrations supported (preferred) — avoid importing raw SQL dumps; use the framework migrations instead for portability and versioning 🔁

**Tech stack**

- Backend: PHP (CodeIgniter 4)
- Database: MySQL (we recommend running MySQL via XAMPP for local development) 🐘
- Mobile: Android (Android Studio) — see linked Android repo
- Maps: Leaflet + OpenStreetMap

Requirements

- PHP 8.2+ with intl and mbstring extensions
- MySQL (XAMPP recommended for local dev)
- Composer
- Android Studio for mobile app development

Quick setup (local)

1. Clone the repository:

```bash
git clone https://github.com/powerrangerblue/FoodDash.git
cd FoodDash
```

2. Start XAMPP and ensure MySQL is running. Create an empty database for FoodDash (e.g., `fooddash_dev`).

3. Copy and update configuration files as needed (example files are in `app/Config/`).

4. Install PHP dependencies with Composer:

```bash
composer install
```

5. Configure database credentials in `app/Config/Database.php` (or via environment variables).

6. Run framework migrations (preferred over importing SQL):

```bash
php spark migrate
```

Notes on migration vs SQL import

- Use `php spark migrate` to run CodeIgniter migrations so schema changes are versioned and reproducible across environments. This repository includes migration scripts under the `database/` folder — do not rely on manual SQL dumps for production migration workflows.

Android mobile apps

- Customer and Rider apps are maintained in a separate repository: https://github.com/codex-xx/FOODDASH-ANDROID.git
- The Android apps use Leaflet/OpenStreetMap for maps and integrate with the backend APIs exposed by this project.

Maps & geolocation

- Web and mobile maps are implemented using Leaflet and OpenStreetMap tiles. Map-related frontend code lives in `public/js/` and mobile map code is in the Android repo.

Folder overview

- `app/` — application code: controllers, models, views, config and helpers.
- `public/` — web root: `index.php`, assets, JS/CSS, uploads and `api/` endpoints.
- `system/` — CodeIgniter core files (framework).
- `database/` — migration scripts and SQL schemas (for reference). Use migrations under `database/`.
- `writable/` — runtime writable storage: `logs/`, `cache/`, `uploads/`, `session/`.
- `PHPMailer/` — included mail library used by the project.
- `tests/` — automated tests and test helpers.

Deployment & production notes

- Point your webserver (Apache/nginx) to the `public/` folder — never the project root.
- Secure environment variables and keep credentials out of source control.
- Use the built-in `spark` CLI for maintenance tasks (migrations, seeds, etc.).

Contributing

We welcome contributions. Please open issues or PRs describing bug fixes, improvements, or feature proposals. Follow standard GitHub workflows and add tests where appropriate.

License

This project includes the CodeIgniter 4 distribution and other components; see the `LICENSE` file for details.

Contact

For questions or support, open an issue in this repository or reach out to the maintainers.

Thank you for using FoodDash! 🍽️
