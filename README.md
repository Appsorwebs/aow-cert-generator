AOW Certificate Generator

Quick setup

1. Copy plugin file to WordPress plugins folder:
   - wp-content/plugins/aow-cert-generator/aow-cert-generator.php
2. Activate plugin in WP admin.
3. Add shortcode [aow_cert_generator] to a page to use the UI.

Developer checks
# AppsOrWebs Certificate Generator

WordPress plugin that provides a certificate generation and verification UI via the `[aow_cert_generator]` shortcode.

Key points
- Frontend generator + verification UI
- REST endpoints for persistence, upload, export and email
- Admin tools for managing certificates and background jobs

Building assets

1. Install Node deps: `npm ci`
2. Build: `npm run build`
3. Dev: `npm run dev` (watch)

Server-side rendering note

PDF/PNG server-side rendering requires a headless browser (Puppeteer/Chromium) or an external service. If not available, use client-side exports for previews.

Testing

- Run PHP lint and PHPUnit in a local WP development environment.

License: GPL-2.0
composer require spatie/browsershot
