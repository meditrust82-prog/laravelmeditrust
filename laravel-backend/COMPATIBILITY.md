Compatibility notes

- Routes are defined under `/routes/api.php` with prefix `/api/v1` to match the frontend.
- JWT flows use `firebase/php-jwt`; set `JWT_SECRET` and `JWT_TTL_MINUTES` in `.env` as needed.
- Cloudinary service uses `cloudinary/cloudinary_php`; configure credentials from `.env`.
- Khalti webhook endpoint performs IP allow-list checking; add HMAC validation to match Node behavior if required.
- GBP integration is a placeholder service; implement exact API calls modeled from `backend/src/services/gbp.service.js`.
- Render route currently returns a placeholder; configure to serve the frontend's `index.html` or implement server-side rendering as needed.

Database, models, and migrations are present — run `php artisan migrate --seed` after installing dependencies.

Required Composer packages are already declared in `composer.json`.
