# Swift Engine (Edison Gravity)

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg?style=for-the-badge)
![PHP](https://img.shields.io/badge/php-8.3-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-11.0-FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/filament-3.x-F2C94C.svg?style=for-the-badge&logo=filament&logoColor=black)
![Docker](https://img.shields.io/badge/docker-ready-2496ED.svg?style=for-the-badge&logo=docker&logoColor=white)

> **AmanahRaya Trustees Berhad Initiative**  
> *Developed by Ashraf*

---

## 🚀 Overview

**Swift Engine** is a specialized financial processing system designed to streamline SWIFT message handling and audit trails. Built with robustness and security in mind, it serves as a central engine for processing, monitoring, and analyzing financial communication data.

### ✨ Key Features

*   **SWIFT Message Processing**: Automated ingestion and parsing of MT/MX messages.
*   **Comprehensive Activity Logging**:
    *   Logs **every** user and admin action.
    *   **Geo-Location Tracking**: Automatically resolves IP addresses to physical locations (City, Country).
    *   **CSV Exports**: Downloadable activity logs for audit compliance.
*   **Secure Access**:
    *   **Single Session Enforcement**: Prevents concurrent logins; older sessions are automatically revoked.
    *   **Role-Based Access Control**: Granular permissions for Admins and Users.
*   **Modern Dashboard**:
    *   Visual Charts: Message frequency and volume by BIC.
    *   Real-time "Recent Logs" widget.

---

## 📦 Deployment Guide

Follow these steps to migrate **Swift Engine** to your production environment.

### 1. Code Migration & Setup

**Recommended Directory:**
*   **Linux**: `/var/www/swift-engine`
*   **Windows**: `C:\inetpub\wwwroot\swift-engine` or `C:\Apps\swift-engine`

**Step-by-Step Import:**

1.  **Clone the Repository** (Best method):
    ```bash
    cd /var/www
    git clone https://gitlab.com/your-repo/swift-engine.git swift-engine
    cd swift-engine
    ```
    *Alternatively, SFTP the project files if direct git access is restricted.*

2.  **Environment Configuration (.env)**:
    *   **Never** commit your `.env` file.
    *   Copy the example file on the server:
        ```bash
        cp .env.example .env
        ```
    *   **Edit `.env`** with production credentials:
        ```ini
        APP_NAME="Swift Engine"
        APP_ENV=production
        APP_DEBUG=false
        APP_URL=https://swift-engine.amanahraya.my

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1 (or RDS endpoint)
        DB_DATABASE=swift_engine_prod
        DB_USERNAME=your_db_user
        DB_PASSWORD=your_secure_password

        # Critical for Single Session & Logging
        SESSION_DRIVER=database
        QUEUE_CONNECTION=database (ensure queue worker is running)
        ```

---

### 2. server Deployment Instructions

#### 🐧 AlmaLinux / Ubuntu (Docker Method - Recommended)

1.  **Install Docker & Docker Compose**:
    *   *AlmaLinux*: `sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo && sudo dnf install docker-ce docker-ce-cli containerd.io`
    *   *Ubuntu*: `sudo apt-get update && sudo apt-get install docker-ce docker-ce-cli containerd.io`

2.  **Prepare Docker Files**:
    *   Open `docker-compose.yml` and check comments tagged with `ashraf29122025`.
    *   Ensure port mappings are set to `80:80` and `443:443`.
    *   Set `APP_ENV=production` in the `environment` section.

3.  **Build & Run**:
    ```bash
    # Build container with production assets
    docker-compose build app

    # Start services in background
    docker-compose up -d
    ```

4.  **Final Setup**:
    ```bash
    # install dependencies
    docker-compose exec app composer install --optimize-autoloader --no-dev
    
    # Generate Key
    docker-compose exec app php artisan key:generate

    # Run Migrations
    docker-compose exec app php artisan migrate --force
    
    # Optimize
    docker-compose exec app php artisan optimize
    ```

#### 🪟 Windows Server (IIS Method)

1.  **Prerequisites**:
    *   Install **PHP 8.3** for Windows (VS16 x64 Thread Safe).
    *   Install **Composer**.
    *   Install **MySQL** or **MariaDB**.
    *   Install **IIS** with CGI module.

2.  **Directory Setup**:
    *   Place source code in `C:\inetpub\wwwroot\swift-engine`.

3.  **Permissions**:
    *   Grant `Modify` permission to `IUSR` and `IIS_IUSRS` for:
        *   `storage/` directory.
        *   `bootstrap/cache/` directory.

4.  **Install Dependencies**:
    ```powershell
    cd C:\inetpub\wwwroot\swift-engine
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    ```

5.  **IIS Configuration**:
    *   Create a new Website in IIS Manager.
    *   Set **Physical Path** to `C:\inetpub\wwwroot\swift-engine\public`.
    *   Add `web.config` to the `public` folder (Laravel includes this by default) to handle URL rewriting.

6.  **Finalize**:
    ```powershell
    php artisan key:generate
    php artisan migrate --force
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

---

## 🛠 Troubleshooting

*   **Logs not appearing?**
    *   Ensure `QUEUE_CONNECTION` is set correctly. If `database`, run `php artisan queue:work`. If `sync`, logs appear instantly (slower performance).
*   **Permission Denied?**
    *   Linux: `chown -R www-data:www-data storage bootstrap/cache`
    *   Windows: Check `IUSR` permissions on `storage` folder.

---
*© 2025 AmanahRaya Trustees Berhad. All Rights Reserved.*
