# Hostinger Deployment Guide for ATOZ GADGETZ

This guide walks you through deploying the **ATOZ GADGETZ** luxury e-commerce platform onto **Hostinger Shared / Cloud Hosting** running PHP 8.2+.

---

## 1. Hostinger Prerequisites & Configuration

### A. Set PHP Version in hPanel
1. Log in to your **Hostinger hPanel**.
2. Navigate to **Websites** &rarr; Select your domain &rarr; **Advanced** &rarr; **PHP Configuration**.
3. Select **PHP 8.2** (or **PHP 8.3**).
4. In the **PHP Extensions** tab, ensure the following are enabled:
   - `curl`
   - `fileinfo`
   - `gd`
   - `intl`
   - `mbstring`
   - `mysqli`
   - `openssl`
   - `pdo_mysql`
   - `zip`
5. Click **Save**.

### B. Create MySQL Database
1. In hPanel, go to **Databases** &rarr; **Management**.
2. Create a new database:
   - **Database Name**: e.g. `u123456789_atoz`
   - **Username**: e.g. `u123456789_admin`
   - **Password**: *Generate a secure password*
3. Record these credentials for step 3.

---

## 2. Code Upload & File Hierarchy

### Recommended Setup (Custom Document Root):
1. In hPanel, access the **File Manager** or connect via **SSH / SFTP**.
2. Create a folder outside `public_html`, for example:  
   `/home/u123456789/domains/atozgadgetz.com/atozindia`
3. Upload the project code into this folder.
4. In hPanel &rarr; **Websites** &rarr; **Change Document Root**, point your website's document root to:  
   `domains/atozgadgetz.com/atozindia/public`

### Alternative Setup (Direct public_html):
If your plan does not allow changing the document root:
- Upload all project files directly into `public_html/`.
- The repository includes a pre-configured root [`.htaccess`](file:///E:/Projects/atozindia/.htaccess) that automatically routes all web traffic into `public/` while securing `.env`, `storage`, and `vendor` from public access.

---

## 3. Environment Configuration (`.env`)

In the project root, create or edit your `.env` file:

```env
APP_NAME="ATOZ GADGETZ"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u123456789_atoz
DB_USERNAME=u123456789_admin
DB_PASSWORD=YourDatabasePasswordHere

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=concierge@yourdomain.com
MAIL_PASSWORD=YourEmailPasswordHere
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="concierge@yourdomain.com"
MAIL_FROM_NAME="ATOZ GADGETZ"
```

---

## 4. Production Provisioning Commands (via SSH)

Connect to your Hostinger server via SSH and run:

```bash
cd /home/u123456789/domains/atozgadgetz.com/atozindia

# 1. Install production PHP dependencies
composer install --no-dev --optimize-autoloader

# 2. Generate application key if not set
php artisan key:generate --force

# 3. Execute database migrations
php artisan migrate --force

# 4. Seed database with initial catalogue, admin roles, and settings
php artisan db:seed --force

# 5. Link public storage disk for product media
php artisan storage:link

# 6. Optimize and cache routes, configuration, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 5. Automated Hostinger Cron Job Setup

To handle inventory reservation timeouts, order fulfillment notifications, and periodic log rotations without needing background daemons:

1. In hPanel, search for **Cron Jobs**.
2. Select **Custom** type.
3. Set the interval to **Every minute** (`* * * * *`).
4. Set Command to:
```bash
/usr/bin/php /home/u123456789/domains/atozgadgetz.com/atozindia/artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Default Admin & Test Accounts

Once seeded, you can log in immediately to the admin console:

- **Admin URL**: `https://yourdomain.com/admin/login`
- **Super Administrator**: `admin@atozgadgetz.com` | Password: `Admin@12345`
- **Store Manager**: `manager@atozgadgetz.com` | Password: `Manager@12345`
- **Test Customer**: `customer@example.com` | Password: `Customer@12345`
