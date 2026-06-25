# Meditrust Nepal

Full-stack medical equipment eCommerce platform.

## Stack

| Layer | Tech |
|---|---|
| Backend | Laravel 10 + MySQL |
| Frontend | Vite + React 18 + Tailwind CSS + React Router |
| Images | Cloudinary (no local storage) |
| Auth | JWT via httpOnly cookies + refresh tokens |
| Payments | Khalti webhook integration |
| Deploy | Laravel backend + Vite React frontend |

## Folder Structure

```
meditrust-nepal/
├── frontend/
│   ├── src/
│   │   ├── main.jsx                # Vite entry point
│   │   ├── App.jsx                 # Routes & layout
│   │   ├── api.js                  # Axios client (httpOnly cookies)
│   │   ├── contexts/
│   │   │   ├── AuthContext.jsx
│   │   │   ├── CartContext.jsx
│   │   │   ├── CompareContext.jsx
│   │   │   ├── ThemeContext.jsx
│   │   │   └── WhatsAppContext.jsx
│   │   ├── pages/
│   │   │   ├── Home.jsx
│   │   │   ├── Products.jsx
│   │   │   ├── ProductDetail.jsx
│   │   │   ├── Cart.jsx
│   │   │   ├── Checkout.jsx
│   │   │   ├── Services.jsx
│   │   │   ├── About.jsx
│   │   │   ├── Contact.jsx
│   │   │   ├── Login.jsx
│   │   │   ├── Register.jsx
│   │   │   └── admin/
│   │   │       ├── AdminLogin.jsx
│   │   │       └── AdminDashboard.jsx
│   │   ├── components/
│   │   ├── hooks/
│   │   ├── utils/
│   │   └── styles/
│   ├── index.html
│   ├── vite.config.js
│   ├── tailwind.config.js
│   ├── postcss.config.js
│   ├── vercel.json
│   ├── .env.example
│   └── package.json
├── laravel-backend/
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── scripts/
│   ├── tests/
│   ├── artisan
│   ├── composer.json
│   ├── .env.example
│   └── deploy-nginx.conf
└── package.json
```

## Local Setup

### 1. Install dependencies
```bash
# Install Laravel backend deps
cd laravel-backend && composer install

# Install frontend deps
cd ../frontend && npm install
```

### 2. Configure Laravel backend
```bash
cp laravel-backend/.env.example laravel-backend/.env
# Fill in DB connection values for MySQL
# - DB_DATABASE=medi_db
# - DB_USERNAME=root
# - DB_PASSWORD=
# Fill in JWT_SECRET, CLOUDINARY_*, KHALTI_SECRET_KEY, TELEGRAM_BOT_TOKEN and TELEGRAM_CHAT_ID as needed.
```

### 3. Run database migrations
```bash
cd laravel-backend
php artisan migrate
```

### 4. Configure frontend
```bash
cp frontend/.env.example frontend/.env
# VITE_API_URL is already configured for Laravel on http://127.0.0.1:8001/api/v1
```

### 5. Run backend (port 8001)
```bash
cd laravel-backend
php artisan serve --host=127.0.0.1 --port=8001
```

### 6. Run frontend (port 5003) in another terminal
```bash
cd frontend
npm run dev
```
# Visit http://127.0.0.1:5003
```

## Deployment

### Backend
1. Deploy `laravel-backend/` to any Laravel-compatible PHP host.
2. Ensure your host supports Laravel 10 and MySQL.
3. Set environment variables:
   - `APP_KEY` — Laravel app key
   - `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
   - `JWT_SECRET` — Secure random string
   - `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`
   - `KHALTI_SECRET_KEY`, `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`
4. Run migrations and seed any initial data as needed.

### Frontend
1. Deploy `frontend/` as a static Vite + React app to your preferred host.
2. Set `VITE_API_URL` to your production Laravel API base URL, e.g. `https://your-backend.example.com/api/v1`.
3. If using a static host, ensure SPA fallback routing is configured correctly.

## API Reference

| Method | Path | Auth | Description |
|---|---|---|---|
| POST | `/api/auth/register` | — | Register user |
| POST | `/api/auth/login` | — | Login |
| POST | `/api/auth/logout` | — | Logout |
| GET | `/api/auth/me` | JWT | Current user |
| GET | `/api/products` | — | List products |
| GET | `/api/products/:slug` | — | Product detail |
| POST | `/api/products` | Admin | Create product |
| PUT | `/api/products/:id` | Admin | Update product |
| DELETE | `/api/products/:id` | Admin | Delete product |
| POST | `/api/orders` | JWT | Place order |
| GET | `/api/orders/my` | JWT | My orders |
| GET | `/api/orders/:id` | JWT | Order detail |
| GET | `/api/orders` | Admin | All orders |
| PATCH | `/api/orders/:id/status` | Admin | Update status |
