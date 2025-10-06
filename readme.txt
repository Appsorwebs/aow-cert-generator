=== AppsOrWebs Certificate Generator ===
Contributors: appsorwebs
Donate link: https://appsorwebs.com
Tags: certificates, learning, verification, admin
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
Generate and verify certificates from the WordPress admin or public verification portal. Includes REST endpoints, upload handlers, background job support, and optional server-side rendering for PDF/PNG exports.

== Installation ==
1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Add the `[aow_cert_generator]` shortcode to a page to display the UI

== Frequently Asked Questions ==
= Does this plugin render PDFs on the server? =
Server-side PDF/PNG rendering requires a headless browser (Puppeteer/Chromium) or Browsershot. If not available, client-side exports are used for previews.

== Changelog ==
= 1.0.2 =
* Prefer dist bundles in production and add release packaging
* Add README, changelog, uninstall and activation checks

= 1.0.1 =
* Initial public release
