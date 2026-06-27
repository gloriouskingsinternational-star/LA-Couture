# L.A. Couture — Full Deployment Guide

Frontend → Netlify | Backend → Railway | Database → Supabase

---

## STEP 1 — Create GitHub Repository

1. Go to https://github.com/new
2. Repository name: `la-couture`
3. Set to **Private** (recommended) or Public
4. Do NOT tick "Add README" (we already have one)
5. Click **Create repository**
6. Copy the repo URL shown (e.g. `https://github.com/YOUR_USERNAME/la-couture.git`)

Then run in your terminal (from project root):
```bash
git remote set-url origin https://github.com/YOUR_USERNAME/la-couture.git
git push -u origin main
```

---

## STEP 2 — Set Up Supabase (Database)

1. Go to https://supabase.com → **New Project**
2. Choose a name: `la-couture-db`, pick a region close to Nigeria (e.g. `eu-west-2` London)
3. Set a **strong database password** — save it somewhere safe
4. Wait ~2 minutes for the project to be ready
5. Go to **Project Settings → Database**
6. Copy these values (you will need them in Step 3):

| Setting | Where to find it |
|---------|-----------------|
| `DB_HOST` | "Host" field — looks like `db.xxxx.supabase.co` |
| `DB_PASSWORD` | The password you set when creating the project |
| `DB_PORT` | Always `5432` |
| `DB_DATABASE` | Always `postgres` |
| `DB_USERNAME` | Always `postgres` |

---

## STEP 3 — Deploy Backend to Railway

1. Go to https://railway.app → **New Project → Deploy from GitHub repo**
2. Select your `la-couture` repo
3. Railway will detect `backend/` — if it doesn't, set **Root Directory** to `backend`
4. Click **Add Variables** and add ALL of these:

```
APP_NAME=L.A. Couture
APP_ENV=production
APP_KEY=                    ← Run "php artisan key:generate --show" locally and paste here
APP_DEBUG=false
APP_URL=                    ← Leave blank for now; fill in after first deploy

DB_CONNECTION=pgsql
DB_HOST=db.YOUR_REF.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=YOUR_SUPABASE_PASSWORD
DB_SSLMODE=require

ADMIN_PASSWORD=YourStrongAdminPassword@2026!
FRONTEND_URL=https://YOUR_SITE.netlify.app
SANCTUM_STATEFUL_DOMAINS=YOUR_SITE.netlify.app

SESSION_DRIVER=cookie
SESSION_LIFETIME=480
SESSION_ENCRYPT=true
SESSION_SAME_SITE=none
CACHE_STORE=file
QUEUE_CONNECTION=sync

LOG_CHANNEL=stack
LOG_LEVEL=error
BCRYPT_ROUNDS=12
```

5. Click **Deploy** — Railway will build using `nixpacks.toml` (installs PHP, runs migrations, seeds DB)
6. After deploy, copy the Railway URL (e.g. `https://la-couture-backend.up.railway.app`)
7. Go back to Variables and set:
   - `APP_URL` = your Railway URL

---

## STEP 4 — Update Frontend API URL

Open `js/api.js` and replace the hardcoded Railway URL:
```js
: (_metaApi || 'https://la-couture-backend.up.railway.app/api');
```
Replace `la-couture-backend.up.railway.app` with your actual Railway domain.

Then commit and push:
```bash
git add js/api.js
git commit -m "Update production API URL"
git push
```

---

## STEP 5 — Deploy Frontend to Netlify

1. Go to https://netlify.com → **Add new site → Import an existing project**
2. Connect to GitHub → select `la-couture` repo
3. Settings:
   - **Base directory**: *(leave empty — root of repo)*
   - **Build command**: *(leave empty — static site)*
   - **Publish directory**: `.`
4. Click **Deploy site**
5. After deploy, copy your Netlify URL (e.g. `https://la-couture.netlify.app`)
6. Go to **Site Settings → Domain → Add custom domain** if you have one

---

## STEP 6 — Update Railway CORS with Netlify URL

Go back to Railway → Variables and update:
```
FRONTEND_URL=https://la-couture.netlify.app
SANCTUM_STATEFUL_DOMAINS=la-couture.netlify.app
```
Click **Redeploy**.

---

## STEP 7 — Test the Full Stack

1. Open your Netlify URL in a browser
2. Try: **Admin Login** → `admin@lacouture.com` / `YourStrongAdminPassword@2026!`
3. Try: **Register** a client account → Admin approves it → Client logs in
4. Try: **Place an order** from the Collections page
5. Try: **Submit a contact form**

---

## Local Development

```bash
# Start backend (from project root)
start-backend.bat          # Windows: double-click or run in terminal

# OR manually
cd backend
php artisan serve

# Frontend: open with VS Code Live Server (port 5501)
# OR open via XAMPP: http://localhost/L.A%20COUTURE/
```

Admin credentials (local): `admin@lacouture.com` / `LaCouture@Admin2026!`

---

## Architecture

```
Browser (Netlify CDN)
    │
    ├── index.html, collections.html, etc.  ← static files
    ├── js/api.js                            ← detects env, calls correct backend
    │
    └── HTTPS → Railway (Laravel API)
                    │
                    └── PostgreSQL (Supabase)
```
