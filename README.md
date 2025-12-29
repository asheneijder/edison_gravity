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

## 🔐 Environment Security (.env Encryption)

To securely manage production credentials, Laravel allows you to encrypt your `.env` file.

**1. Encrypting (On Local Machine):**
Run this command to encrypt your production `.env` file. It will generate a `.env.encrypted` file.
```bash
php artisan env:encrypt --env=production
```
*Save the decryption key (e.g., `base64:xyz...`) securely. Do not commit it to git.*

**2. Decrypting (On Server):**
Restore your `.env` file on the server using the key:
```bash
php artisan env:decrypt --key=base64:xyz...
```

---

## 📦 Deployment Guide

### Option 1: Linux (AlmaLinux / Ubuntu) - 🐳 Docker Method (Recommended)

This method ensures consistency across environments using containers.

#### **Phase 1: Server Preparation**
1.  **Install Docker & Compose**:
    *   **AlmaLinux**:
        ```bash
        sudo dnf config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
        sudo dnf install docker-ce docker-ce-cli containerd.io
        sudo systemctl start docker && sudo systemctl enable docker
        ```
    *   **Ubuntu**:
        ```bash
        sudo apt-get update
        sudo apt-get install docker-ce docker-ce-cli containerd.io
        ```

#### **Phase 2: Code Setup**
1.  **Clone Repository**:
    ```bash
    mkdir -p /var/www
    cd /var/www
    git clone https://gitlab.com/your-repo/swift-engine.git swift-engine
    cd swift-engine
    ```

2.  **Environment Setup**:
    *   **Method A (Direct)**: Copy example and edit.
        ```bash
        cp .env.example .env
        nano .env
        ```
    *   **Method B (Encrypted)**: Upload `.env.encrypted` and decrypt.
        ```bash
        # (Requires php installed on host, OR run inside container after build)
        # We will use Method A typically for Docker initial setup.
        ```
    *   **Critical Prod Settings in `.env`**:
        ```ini
        APP_ENV=production
        APP_DEBUG=false
        PROD_INDICATOR=true
        QUEUE_CONNECTION=database
        DB_DATABASE=swift_engine_prod
        ```

#### **Phase 3: Build & Launch**
1.  **Configure Docker Files**:
    *   Edit `docker-compose.yml`: Ensure ports `80:80` and `443:443`.
    *   Edit `docker/nginx/conf.d/app.conf`: Set `server_name your-domain.com`.

2.  **Start Containers**:
    ```bash
    docker-compose build app
    docker-compose up -d
    ```

3.  **Initialization**:
    ```bash
    # Install dependencies (Production optimized)
    docker-compose exec app composer install --optimize-autoloader --no-dev
    
    # Generate Key
    docker-compose exec app php artisan key:generate

    # Run Migrations
    # NOTE: The database is created automatically by the container.
    docker-compose exec app php artisan migrate --force
    
    # Cache Configuration
    docker-compose exec app php artisan optimize
    ```

---

### Option 2: Windows Server - 🪟 IIS Method

#### **Phase 1: Prerequisites**
*   **PHP 8.3** (Right click -> Extract to `C:\php`). Add to System PATH.
*   **Composer** (Install globally).
*   **MySQL 8.0** or MariaDB (Install and run service).
*   **IIS Web Server** (Enable CGI role).
*   **URL Rewrite Module** (Download from Microsoft/IIS.net).

#### **Phase 2: Database Setup**
1.  Open MySQL Workbench or Command Line.
2.  Create the database:
    ```sql
    CREATE DATABASE swift_engine_prod;
    ```

#### **Phase 3: Application Setup**
1.  **Import Code**:
    *   Clone or unzip files to `C:\inetpub\wwwroot\swift-engine`.

2.  **Environment**:
    *   Copy `.env.example` to `.env`.
    *   Edit `.env`:
        ```ini
        APP_ENV=production
        APP_DEBUG=false
        DB_DATABASE=swift_engine_prod
        QUEUE_CONNECTION=database
        SESSION_DRIVER=database
        ```
    *   *If utilizing encryption*: Run `php artisan env:decrypt --key=...` in PowerShell.

3.  **Install Dependencies**:
    ```powershell
    cd C:\inetpub\wwwroot\swift-engine
    composer install --optimize-autoloader --no-dev
    npm install
    npm run build
    ```

4.  **Finalize Laravel**:
    ```powershell
    php artisan key:generate
    php artisan migrate --force
    php artisan storage:link
    php artisan optimize
    ```

#### **Phase 4: IIS & Worker Configuration**
1.  **IIS Site**:
    *   Add Website -> Name: "SwiftEngine".
    *   Physical Path: `C:\inetpub\wwwroot\swift-engine\public`.
    *   Binding: Port 80 (or 443 with SSL).

2.  **Permissions**:
    *   Right-click `storage` folders -> Properties -> Security.
    *   Grant **Full Control** to `IUSR` and `IIS_IUSRS`.

3.  **Queue Worker (CRITICAL)**:
    *   You **must** keep a background process running for logs to appear.
    *   **Use NSSM (Recommended)**:
        ```powershell
        nssm install SwiftEngineQueue "C:\php\php.exe"
        # Arguments: "artisan queue:work --tries=3"
        # Directory: "C:\inetpub\wwwroot\swift-engine"
        nssm start SwiftEngineQueue
        ```

---

## 🛠 Troubleshooting

*   **Logs not appearing?**
    *   **Check Queue**: Ensure `php artisan queue:work` is running (Docker handles this via supervisor; Windows needs NSSM).
*   **"500 Server Error" on Windows?**
    *   Check `storage/logs/laravel.log`.
    *   Verify `IUSR` permissions on `storage` folder.
*   **Database Connection Error?**
    *   Docker: Ensure `DB_HOST=db` (service name) or `127.0.0.1` depending on network mode.
    *   Windows: Ensure `DB_HOST=127.0.0.1`.

---

## 🌐 Advanced: Deploying on a "Busy" Server

If your production server **already has other Docker containers** running (e.g., on ports 80/443), you cannot simply bind `80:80` again. You have two main options:

### Option A: Reverse Proxy (Traefik / Nginx Proxy Manager) - **Recommended**
If you use a central proxy to manage domains:
1.  **Do NOT** map ports `80:80` or `443:443` in `docker-compose.yml`.
2.  **Connect to the Proxy Network**:
    ```yaml
    networks:
      swift-engine-net:
        external: true
        name: your_proxy_network_name
    ```
3.  **Remove Ports**: Comment out the `ports` section in `docker-compose.yml`. The proxy will talk to the container internally via the network.

### Option B: Unique Port Mapping (Standalone)
If you just want to access it via a specific port or map it manually through a host Nginx:
1.  **Choose a unique port** (e.g., 8085).
2.  **Update `docker-compose.yml`**:
    ```yaml
    ports:
      - "8085:80"
    ```
3.  **Access**: You can now access the site at `http://your-server-ip:8085`.

---
*© 2025 Ashraf. All Rights Reserved.*
