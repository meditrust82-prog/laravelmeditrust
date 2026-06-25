Laravel backend scaffold to replace the Node.js backend.

Quick setup (after installing PHP and Composer):

1. Install dependencies

```bash
cd laravel-backend
composer install
```

2. Configure `.env` (copy from `.env.example`) and set MySQL and third-party credentials.

3. Generate app key and JWT secret

```bash
php artisan key:generate
# set JWT_SECRET in .env; the backend uses firebase/php-jwt
```

4. Run migrations and seed admin

```bash
php artisan migrate --seed
php artisan db:seed --class=AdminSeeder
```

Notes:
- This scaffold preserves the `/api/v1/*` routes and JSON contract; the frontend can remain unchanged.
- Composer dependencies already include JWT, Cloudinary, Google API, and Guzzle packages.
- Replace placeholder implementations (AI routing and any incomplete GBP behavior) with production code from the original Node services where noted in `COMPATIBILITY.md`.
