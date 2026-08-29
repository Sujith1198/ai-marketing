# Production Deployment Guide: AI Marketing Team

Domain: **https://ai-marketing.gridaisync.com/**  
Database Host: **127.0.0.1** (or `217.21.90.174` internally)  
Database Name: **`u376492188_aimarketing`**  
Database User: **`u376492188_aimarketing`**  

---

## Step 1: Upload Project Files to Hostinger

### Option A: Using Git (Recommended)
In your Hostinger hPanel SSH Terminal or File Manager:
```bash
git clone https://github.com/Sujith1198/ai-marketing.git
```

### Option B: Upload Zip File
Upload project `.zip` file via Hostinger File Manager to your domain directory `ai-marketing.gridaisync.com` and extract it.

---

## Step 2: Configure Production `.env`

Copy `.env.production` to `.env` on the server:
```bash
cp .env.production .env
```

Ensure `.env` contains:
```env
APP_NAME="AI Marketing Team"
APP_ENV=production
APP_KEY=base64:xxZJn1DNCbq+2Fu01fKopOxGejHDMyEKdojmBoWzwec=
APP_DEBUG=false
APP_URL=https://ai-marketing.gridaisync.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u376492188_aimarketing
DB_USERNAME=u376492188_aimarketing
DB_PASSWORD=Sujith@0911@9099
```

---

## Step 3: Run Database Migrations & Seeders

Run the migration & seeder command in Hostinger SSH Terminal or Web Terminal:
```bash
php artisan migrate:fresh --seed --force
```

This creates all 40+ tables in `u376492188_aimarketing` and seeds default credentials:
- **Default CEO Login**: `ceo@aimarketing.test`
- **Default Password**: `password`

---

## Step 4: Storage Link & Directory Permissions

Ensure storage permissions are writable:
```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

---

## Step 5: Configure Hostinger Cron Job (1 Minute Interval)

Go to **Hostinger hPanel -> Advanced -> Cron Jobs** and add:

**Command**:
```bash
* * * * * cd /home/u376492188/domains/gridaisync.com/public_html && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 6: Public Folder & Document Root Setup

If your domain points directly to `public_html`, ensure the `.htaccess` file inside `public/` redirects clean URLs to `index.php`.

Your application is live at: **https://ai-marketing.gridaisync.com/**
