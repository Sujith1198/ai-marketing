# Production Deployment Guide: AI Marketing Team

Domain: **https://ai-marketing.gridaisync.com/**  
Database Host: **127.0.0.1** (or `217.21.90.174` internally)  
Database Name: **`u376492188_aimarketing`**  
Database User: **`u376492188_aimarketing`**  

---

## Fixing 403 Forbidden Error on Hostinger

A `403 Forbidden` error on Hostinger shared hosting happens when Apache tries to list files in the root folder because the domain points to `public_html` instead of `public_html/public`.

### Fix 1: Built-in Root `.htaccess` & `index.php` (Automated Fix)
We have added a root `.htaccess` and root `index.php` directly in the project root repository.  
Simply pull or copy the latest code to your server:
```bash
git pull origin main
```
This automatically routes web traffic to `public/index.php` without throwing 403 Forbidden!

---

### Fix 2: Change Hostinger Target Directory (Recommended in hPanel)
1. Log in to **Hostinger hPanel**.
2. Go to **Websites -> Dashboard -> Domain / Directory Configuration**.
3. Change **Target Directory / Document Root** to:
   `/public_html/public`  (or `/public_html/ai-marketing/public`)
4. Click **Save**.

---

## Full Deployment Commands

Run these commands in your Hostinger SSH / Web Terminal:

```bash
# 1. Pull latest code
git pull origin main

# 2. Copy production environment file
cp .env.production .env

# 3. Run database migrations & seed default CEO user
php artisan migrate:fresh --seed --force

# 4. Storage link & permissions
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

### Accessing Your Live System:
- **URL**: [https://ai-marketing.gridaisync.com/](https://ai-marketing.gridaisync.com/)
- **Default CEO Login**: `ceo@aimarketing.test`
- **Default Password**: `password`
