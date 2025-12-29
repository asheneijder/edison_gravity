# Swift Engine

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg?style=for-the-badge)
![PHP](https://img.shields.io/badge/php-8.3-777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/laravel-11.0-FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/filament-3.x-F2C94C.svg?style=for-the-badge&logo=filament&logoColor=black)
![Docker](https://img.shields.io/badge/docker-ready-2496ED.svg?style=for-the-badge&logo=docker&logoColor=white)

> **Personal Initiative**  
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
    *   **Edit `.env`** with production credentials. It is CRITICAL to set these values correctly:
        ```ini
        APP_NAME="Swift Engine"
        APP_ENV=production
        # Set to false to hide (DEV) label in admin panel and secure error pages
        APP_DEBUG=false
        APP_URL=https://your-domain.com

        # Custom Production Indicator
        PROD_INDICATOR=true

        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1 (or RDS endpoint)
        DB_DATABASE=swift_engine_prod
        DB_USERNAME=your_db_user
        DB_PASSWORD=your_secure_password

        # CRITICAL: Database driver is required for single-session enforcement
        SESSION_DRIVER=database
        
        # CRITICAL: Queue setup for background logging
        QUEUE_CONNECTION=database
        ```

3.  **Database Setup**:
    *   Ensure your MySQL/MariaDB server is running.
    *   Create the empty database mentioned in `DB_DATABASE`:
        ```sql
        CREATE DATABASE swift_engine_prod;
        ```

---

### 2. Server Deployment Instructions

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

    *Note: The Docker setup includes a supervisor configuration to automatically run the queue worker (`php artisan queue:work`).*

4.  **Final Setup**:
    ```bash
    # install dependencies
    docker-compose exec app composer install --optimize-autoloader --no-dev
    
    # Generate Key
    docker-compose exec app php artisan key:generate

    # Run Migrations (Populates the Database)
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
    *   Install **URL Rewrite** module for IIS.

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

5.  **Database & Key**:
    ```powershell
    php artisan key:generate
    php artisan migrate --force
    ```

6.  **Queue Worker Setup (CRITICAL)**:
    *   Because `QUEUE_CONNECTION=database`, you **MUST** run a queue worker to process background jobs (like activity logging).
    *   **Quick Test (Keep window open)**:
        ```powershell
        php artisan queue:work
        ```
    *   **Production Setup**: Use **NSSM** (Non-Sucking Service Manager) or Windows Task Scheduler to run `php artisan queue:work` as a persistent background service.
    
7.  **IIS Configuration**:
    *   Create a new Website in IIS Manager.
    *   Set **Physical Path** to `C:\inetpub\wwwroot\swift-engine\public`.
    *   Ensure `web.config` exists in the `public` folder.

8.  **Finalize**:
    ```powershell
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

---

## 🛠 Troubleshooting

*   **Logs not appearing?**
    *   Check your `.env`: Ensure `QUEUE_CONNECTION=database`.
    *   **Crucial**: Check if the queue worker is running (`php artisan queue:work`). Without this, logs wait in the `jobs` table forever.
    *   Check the `jobs` table in your database to see if pending jobs exist.
*   **"DEV" still showing in header?**
    *   Ensure `APP_DEBUG=false` in your `.env`.
    *   Run `php artisan config:clear` to refresh the configuration.

---
*© 2025 Ashraf. All Rights Reserved.*
