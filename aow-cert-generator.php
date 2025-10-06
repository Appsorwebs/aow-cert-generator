<?php
/**
 * Plugin Name: AppsOrWebs Certificate Generator
 * Description: Registers the [aow_cert_generator] shortcode and outputs the custom, secure, dark-themed certificate generation and verification application.
 * Version: 1.0.2
 * Author: AppsOrWebs Limited
 * Author URI: https://appsorwebs.com
 * License: GPL2
 *
 * Copyright (c) 2025 AppsOrWebs Limited
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main shortcode output function for the certificate generator UI
 */
function appsorwebs_certificate_generator_output() {
    ob_start();
    ?>
    <script>
    (function(){
        // Admin REST config for this page
        var AOW_REST = window.AOW_REST || {};
        AOW_REST.root = AOW_REST.root || '<?php echo esc_url( rest_url( "aow-cert/v1/" ) ); ?>';
        AOW_REST.nonce = AOW_REST.nonce || '<?php echo wp_create_nonce( "wp_rest" ); ?>';
        function el(id){return document.getElementById(id)}
        // Simple HTML-escaping helper for modal messages
        function escHtml(s){ if (s === null || s === undefined) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }        // If the frontend modal library hasn't loaded yet (enqueued asset), provide safe fallbacks
        if (typeof window.aow_showModal === 'undefined') {
            window.aow_showModal = function(title, htmlBody){ try { alert((title||'') + "\n\n" + (htmlBody||'').replace(/<[^>]+>/g,'')); } catch(e) { console.log(title, htmlBody); } };
            window.aow_confirm = function(message, cbYes, cbNo){ if (confirm(message)) { if (typeof cbYes === 'function') cbYes(); } else { if (typeof cbNo === 'function') cbNo(); } };
            window.aow_prompt = function(message, defaultValue, cb){ var v = prompt(message, defaultValue||''); if (typeof cb === 'function') cb(v); };
        }

        var sendBtn = el('aow_send_email_btn');
        if(sendBtn){
            sendBtn.addEventListener('click', function(){
                var checkboxes = document.querySelectorAll('input[name="selected_certs[]"]:checked');
                if(!checkboxes.length){
                    aow_showModal('No Certificates Selected', '<div>Please select one or more certificates to email.</div>');
                    return;
                }
                var certIds = Array.prototype.map.call(checkboxes, function(c){return c.value});
                var to = el('aow_email_to').value.trim();
                if(!to){ aow_showModal('Missing Recipient', '<div>Please provide recipient email(s).</div>'); return; }
                var subject = el('aow_email_subject').value;
                var message = el('aow_email_message').value;

                var recipients = to.split(',').map(function(s){return s.trim()}).filter(Boolean);
                if(!recipients.length){ aow_showModal('Invalid Recipients', '<div>No valid recipient emails found.</div>'); return; }

                sendBtn.disabled = true;
                sendBtn.textContent = 'Sending...';

                fetch(AOW_REST.root + 'export-and-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': AOW_REST.nonce
                    },
                    body: JSON.stringify({
                        cert_ids: certIds,
                        recipients: recipients,
                        subject: subject,
                        message: message,
                        format: (document.getElementById('aow_email_format') ? document.getElementById('aow_email_format').value : 'pdf')
                    })
                }).then(function(resp){
                    return resp.json();
                }).then(function(data){
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Send Email for Selected';
                    if(data && data.success && data.results){
                        var ok = [], fail = [];
                        Object.keys(data.results).forEach(function(k){
                            if(data.results[k].success) ok.push(k); else fail.push(k + ': ' + (data.results[k].error || 'failed'));
                        });
                        var msg = '<div><strong>Email results</strong></div><div>Sent: ' + escHtml(ok.join(', ')) + '</div>';
                        if(fail.length) msg += '<div class="mt-2 text-red-600">Failed: ' + escHtml(fail.join('; ')) + '</div>';
                        aow_showModal('Email Results', msg);
                    } else {
                        var err = (data && data.data && data.data.message) ? data.data.message : JSON.stringify(data);
                        aow_showModal('Email Error', '<div>Failed to send emails: '+escHtml(err)+'</div>');
                    }
                }).catch(function(err){
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Send Email for Selected';
                    aow_showModal('Network Error', '<div>Request failed: ' + escHtml(err.message) + '</div>');
                });
            });
        }
    })();
    background: linear-gradient(45deg, var(--aow-primary-color), #1E2A3A 50%, var(--aow-secondary-color));
        opacity: 0.8;
        filter: blur(5px);
        transition: all 0.5s ease;
    }
    .illuminated-card:hover::before {
        opacity: 1;
        filter: blur(8px);
    }
</style>

<!-- Preconnect to CDNs for faster loading -->
<link rel="preconnect" href="https://cdn.tailwindcss.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preconnect" href="https://cdnjs.cloudflare.com">

<!-- Load external scripts required for the application -->
<!-- Tailwind must load first but can be deferred slightly -->
<script src="https://cdn.tailwindcss.com" defer></script>
<!-- Fonts load asynchronously -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<!-- QRCode.js can load asynchronously since it's only needed for certificate display -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" defer></script>

<div id="app" class="flex flex-col items-center p-4 min-h-screen">

    <!-- Navigation/Header - Focused on Brand Prominence -->
    <header class="w-full max-w-5xl flex justify-between items-center py-8 border-b border-aow-primary/50 mb-8">
        <!-- Header uses Teal and a STRONG glow -->
        <h1 class="text-4xl font-black text-aow-primary flex items-center tracking-widest aow-header-glow">
            <!-- Icon uses Vibrant Coral/Orange -->
            <svg class="w-10 h-10 mr-3 text-aow-secondary" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L14 11.586V8a6 6 0 00-6-6zM8 16h4a2 2 0 11-4 0z"></path></svg>
            AppsOrWebs Certificate Generator
        </h1>
        <nav>
            <!-- Buttons use primary teal and secondary coral glow for interaction -->
            <button id="nav-generator" class="px-5 py-2 text-sm font-semibold rounded-full bg-aow-primary text-aow-dark-bg hover:bg-aow-secondary hover:shadow-secondary-glow transition duration-300">Certificate Generator</button>
            <button id="nav-verifier" class="px-5 py-2 text-sm font-semibold rounded-full text-aow-primary border border-aow-primary hover:bg-aow-card-bg/70 ml-3 transition duration-300">Verification Portal</button>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main id="content-area" class="w-full max-w-5xl">
        <!-- Content will be injected here by JavaScript -->
        <p class="text-center text-gray-500">Loading application...</p>
    </main>

    <!-- Message Box for Alerts (No alert() allowed) -->
    <div id="message-box" class="fixed top-0 right-0 m-6 p-4 rounded-lg shadow-xl text-white transition-opacity duration-300 opacity-0 z-50 pointer-events-none" style="min-width: 250px;"></div>

    
    <script>
        // Ensure legacy inline code can access AOW_REST; the full app logic has been moved to an enqueued file.
        window.AOW_REST = window.AOW_REST || {};
        window.AOW_REST.root = window.AOW_REST.root || '<?php echo esc_url( rest_url( "aow-cert/v1/" ) ); ?>';
        window.AOW_REST.nonce = window.AOW_REST.nonce || '<?php echo wp_create_nonce( "wp_rest" ); ?>';
    </script>
    <?php
    // Enqueue the main frontend application script (extracted from inline markup)
    // The script is registered/enqueued by aow_enqueue_frontend_assets() which runs on wp_enqueue_scripts.
    // Stop output buffering and return the content
    return ob_get_clean();
}

// Register the shortcode with WordPress
add_shortcode('aow_cert_generator', 'appsorwebs_certificate_generator_output');

/**
 * Register a custom post type for storing certificates securely.
 */
function aow_register_certificate_cpt() {
    $labels = array(
        'name' => 'Certificates',
        'singular_name' => 'Certificate',
    );

    $args = array(
        'labels' => $labels,
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'rest_base' => 'aow-certificates',
        'capability_type' => 'post',
        'supports' => array('title'),
    );

    register_post_type('aow_certificate', $args);
}
add_action('init', 'aow_register_certificate_cpt');

/**
 * Activation check: ensure built assets exist for production usage.
 */
// Previously we blocked activation if dist assets were missing. Prefer a softer approach:
// show an admin notice advising the administrator to run the build if assets are missing.
function aow_check_built_assets_notice() {
    // Only show to users who can manage options (admins)
    if ( ! current_user_can( 'manage_options' ) ) return;

    $dist_file = plugin_dir_path(__FILE__) . 'assets/dist/aow-frontend-app.js';
    if ( ! file_exists( $dist_file ) ) {
        // Display a prominent admin notice
        echo '<div class="notice notice-warning is-dismissible"><p><strong>AOW Certificate Generator:</strong> built frontend assets are missing. Please run <code>npm run build</code> in the plugin directory and rebuild assets so the plugin can serve optimized bundles (<code>assets/dist/</code>).</p></div>';
    }
}
add_action( 'admin_notices', 'aow_check_built_assets_notice' );

/**
 * Activation and deactivation hooks to add/remove custom capability.
 */
function aow_plugin_activate() {
    $role = get_role('administrator');
    $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
    if ($role && ! $role->has_cap($cap)) {
        $role->add_cap($cap);
    }
    // Create custom table for certificates with unique constraint
    global $wpdb;
    $table_name = $wpdb->prefix . 'aow_certificates';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        certificate_id VARCHAR(191) NOT NULL,
        post_id BIGINT UNSIGNED DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY certificate_id_unique (certificate_id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    // Create jobs table for background exports/emails
    $jobs_table = $wpdb->prefix . 'aow_jobs';
    $sql2 = "CREATE TABLE IF NOT EXISTS $jobs_table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        job_hash VARCHAR(191) NOT NULL,
        job_data LONGTEXT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'pending',
        results LONGTEXT NULL,
        retries INT NOT NULL DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        started_at DATETIME DEFAULT NULL,
        finished_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY job_hash_unique (job_hash)
    ) $charset_collate;";
    dbDelta( $sql2 );
}
register_activation_hook(__FILE__, 'aow_plugin_activate');

function aow_plugin_deactivate() {
    $role = get_role('administrator');
    if ($role && $role->has_cap('manage_aow_certificates')) {
        $role->remove_cap('manage_aow_certificates');
    }
}
register_deactivation_hook(__FILE__, 'aow_plugin_deactivate');

/**
 * Register REST API routes for certificate CRUD operations.
 */
function aow_register_rest_routes() {
    register_rest_route('aow-cert/v1', '/create', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_create_certificate',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));

    register_rest_route('aow-cert/v1', '/list', array(
        'methods' => 'GET',
        'callback' => 'aow_rest_list_certificates',
        'permission_callback' => function() {
            return true; // public listing for verification (returns minimal data)
        }
    ));

    register_rest_route('aow-cert/v1', '/verify/(?P<id>[A-Za-z0-9\-]+)', array(
        'methods' => 'GET',
        'callback' => 'aow_rest_verify_certificate',
        'permission_callback' => function() {
            return true;
        }
    ));

    register_rest_route('aow-cert/v1', '/upload/(?P<type>logo|signature)', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_upload_media',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));

    register_rest_route('aow-cert/v1', '/export', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_export_certificate',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));

    // Enqueue export+email to run in background (schedules a cron event)
    register_rest_route('aow-cert/v1', '/enqueue', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_enqueue_export_and_email',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));

    register_rest_route('aow-cert/v1', '/retry-job', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_retry_job',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));
    register_rest_route('aow-cert/v1', '/purge-job', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_purge_job',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));

    register_rest_route('aow-cert/v1', '/export-and-email', array(
        'methods' => 'POST',
        'callback' => 'aow_rest_export_and_email',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));

    register_rest_route('aow-cert/v1', '/delete/(?P<id>[A-Za-z0-9\-]+)', array(
        'methods' => 'DELETE',
        'callback' => 'aow_rest_delete_certificate',
        'permission_callback' => function() {
            $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
            return current_user_can($cap);
        }
    ));
}
add_action('rest_api_init', 'aow_register_rest_routes');

/**
 * Add Tools -> Certificates admin page for management (list, bulk delete, export)
 */
function aow_admin_menu() {
    add_management_page(
        'Certificates',
        'Certificates',
        'manage_aow_certificates',
        'aow-certificates',
        'aow_admin_certificates_page'
    );
    add_management_page(
        'Certificate Jobs',
        'Certificate Jobs',
        'manage_aow_certificates',
        'aow-cert-jobs',
        'aow_admin_jobs_page'
    );
}
add_action('admin_menu', 'aow_admin_menu');

/**
 * Settings page for brand colors and capability mapping
 */
function aow_register_settings() {
    register_setting('aow_settings_group', 'aow_primary_color');
    register_setting('aow_settings_group', 'aow_secondary_color');
    register_setting('aow_settings_group', 'aow_capability_slug');
}
add_action('admin_init', 'aow_register_settings');

function aow_settings_page() {
    add_options_page('AOW Certificates', 'AOW Certificates', 'manage_options', 'aow-cert-settings', 'aow_settings_page_render');
}
add_action('admin_menu', 'aow_settings_page');

function aow_settings_page_render() {
    if (! current_user_can('manage_options') ) wp_die('Insufficient permissions');
    if ( isset($_POST['submit']) && check_admin_referer('aow_settings_save', 'aow_settings_nonce') ) {
        update_option('aow_primary_color', sanitize_hex_color($_POST['aow_primary_color'] ?? ''));
        update_option('aow_secondary_color', sanitize_hex_color($_POST['aow_secondary_color'] ?? ''));
        $newcap = sanitize_text_field($_POST['aow_capability_slug'] ?? 'manage_aow_certificates');
        update_option('aow_capability_slug', $newcap);
        // Ensure administrator role has this capability
        $role = get_role('administrator');
        if ($role && ! $role->has_cap($newcap)) $role->add_cap($newcap);
        echo '<div class="updated notice"><p>Settings saved.</p></div>';
    }
    $primary = get_option('aow_primary_color', '#00C2B2');
    $secondary = get_option('aow_secondary_color', '#FF8859');
    $cap = get_option('aow_capability_slug', 'manage_aow_certificates');
    ?>
    <div class="wrap">
        <h1>AOW Certificate Settings</h1>
        <form method="post">
            <?php wp_nonce_field('aow_settings_save','aow_settings_nonce'); ?>
            <table class="form-table">
                <tr><th><label for="aow_primary_color">Primary Color</label></th><td><input type="text" id="aow_primary_color" name="aow_primary_color" value="<?php echo esc_attr($primary); ?>"></td></tr>
                <tr><th><label for="aow_secondary_color">Secondary Color</label></th><td><input type="text" id="aow_secondary_color" name="aow_secondary_color" value="<?php echo esc_attr($secondary); ?>"></td></tr>
                <tr><th><label for="aow_capability_slug">Capability Slug</label></th><td><input type="text" id="aow_capability_slug" name="aow_capability_slug" value="<?php echo esc_attr($cap); ?>"> <p class="description">Set capability required to create/delete certificates. Default: manage_aow_certificates</p></td></tr>
            </table>
            <p class="submit"><input type="submit" name="submit" class="button button-primary" value="Save Changes"></p>
        </form>
    </div>
    <?php
}

function aow_rest_retry_job( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $job_id = intval( $params['job_id'] ?? 0 );
    if ( ! $job_id ) return new WP_Error('missing_id','job_id required', array('status'=>400));
    global $wpdb;
    $jobs_table = $wpdb->prefix . 'aow_jobs';
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $jobs_table WHERE id = %d", $job_id), ARRAY_A);
    if ( ! $row ) return new WP_Error('not_found','Job not found', array('status'=>404));
    $job_data = json_decode($row['job_data'], true);
    if ( ! is_array($job_data) ) return new WP_Error('bad_data','Job data invalid', array('status'=>500));

    // increment retry count
    $wpdb->update($jobs_table, array('retries' => intval($row['retries']) + 1, 'status'=>'pending'), array('id'=>$job_id), array('%d','%s'), array('%d'));

    // Use Action Scheduler or wp-cron to re-enqueue
    if ( function_exists('as_enqueue_async_action') ) {
        as_enqueue_async_action('aow_run_enqueued_export_email', array('job'=>$job_data));
    } elseif ( function_exists('as_schedule_single_action') ) {
        as_schedule_single_action(time()+5, 'aow_run_enqueued_export_email', array('job'=>$job_data));
    } else {
        wp_schedule_single_event(time()+5, 'aow_run_enqueued_export_email', array($job_data));
    }

    return rest_ensure_response(array('success'=>true,'message'=>'Job re-enqueued'));
}

// Inline admin scripts consolidated into external file aow-admin.js
// They are enqueued via aow_enqueue_admin_assets() below.

function aow_rest_purge_job( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $job_id = intval( $params['job_id'] ?? 0 );
    if ( ! $job_id ) return new WP_Error('missing_id','job_id required', array('status'=>400));
    global $wpdb;
    $jobs_table = $wpdb->prefix . 'aow_jobs';
    $deleted = $wpdb->delete($jobs_table, array('id'=>$job_id), array('%d'));
    if ( $deleted === false ) return new WP_Error('db_error','Failed to purge job', array('status'=>500));
    return rest_ensure_response(array('success'=>true,'message'=>'Job purged'));
}

// Inline admin scripts consolidated into external file aow-admin.js
// They are enqueued via aow_enqueue_admin_assets() below.

function aow_enqueue_admin_assets(){
    // Only enqueue on our plugin admin pages
    $screen = get_current_screen();
    // Conservative approach: enqueue on post type and tools pages where plugin outputs admin UI
    wp_register_script('aow-admin-js', plugins_url('assets/js/aow-admin.js', __FILE__), array(), '1.0.0', true);
    wp_enqueue_script('aow-admin-js');
    wp_localize_script('aow-admin-js', 'AOW_REST', array(
        'root' => esc_url( rest_url('aow-cert/v1/') ),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}
add_action('admin_enqueue_scripts', 'aow_enqueue_admin_assets');

/**
 * Enqueue frontend assets (modal, styles) for the public shortcode output.
 */
function aow_enqueue_frontend_assets() {
    // Register frontend modal script and styles
    $dist_dir = plugin_dir_path(__FILE__) . 'assets/dist/';
    $use_dist = false;

    // Prefer built dist files if they exist
    if ( file_exists( $dist_dir . 'aow-frontend-app.js' ) ) {
        $use_dist = true;
    }

    if ( $use_dist ) {
        $ver_frontend = filemtime( $dist_dir . 'aow-frontend.js' );
        $ver_admin = filemtime( $dist_dir . 'aow-admin.js' );
        // Register dist scripts
        wp_register_script('aow-frontend-js', plugins_url('assets/dist/aow-frontend.js', __FILE__), array(), $ver_frontend, true);
        wp_register_script('aow-frontend-app', plugins_url('assets/dist/aow-frontend-app.js', __FILE__), array('aow-frontend-js'), $ver_admin, true);
        wp_register_script('aow-admin-js', plugins_url('assets/dist/aow-admin.js', __FILE__), array(), $ver_admin, true);
        wp_register_style('aow-frontend-css', plugins_url('assets/css/aow-frontend.css', __FILE__), array(), filemtime(plugin_dir_path(__FILE__) . 'assets/css/aow-frontend.css'));

        wp_enqueue_script('aow-frontend-js');
        wp_enqueue_script('aow-frontend-app');
        wp_enqueue_style('aow-frontend-css');
    } else {
        // Fallback to source files for development
        wp_register_script('aow-frontend-js', plugins_url('assets/js/aow-frontend.js', __FILE__), array(), '1.0.0', true);
        wp_register_script('aow-frontend-app', plugins_url('assets/js/aow-frontend-app.js', __FILE__), array('aow-frontend-js'), '1.0.0', true);
        wp_register_style('aow-frontend-css', plugins_url('assets/css/aow-frontend.css', __FILE__), array(), '1.0.0');

        wp_enqueue_script('aow-frontend-js');
        wp_enqueue_script('aow-frontend-app');
        wp_enqueue_style('aow-frontend-css');
    }

    // Localize AOW_REST on the frontend modal script so data is available before the main app runs
    wp_localize_script('aow-frontend-js', 'AOW_REST', array(
        'root' => esc_url( rest_url('aow-cert/v1/') ),
        'nonce' => wp_create_nonce('wp_rest'),
        'isAdmin' => current_user_can('manage_options') ? true : false,
        'primaryColor' => get_option('aow_primary_color', '#00C2B2'),
        'secondaryColor' => get_option('aow_secondary_color', '#FF8859')
    ));
}
add_action('wp_enqueue_scripts', 'aow_enqueue_frontend_assets');

function aow_admin_certificates_page() {
    if ( ! current_user_can('manage_aow_certificates') ) {
        wp_die('Insufficient permissions');
    }

    // Handle bulk actions
    if ( isset($_POST['aow_bulk_action']) && check_admin_referer('aow_bulk_action', 'aow_bulk_nonce') ) {
        $action = sanitize_text_field($_POST['aow_bulk_action']);
        $selected = array_map('sanitize_text_field', (array)($_POST['selected_certs'] ?? array()));
        if ($action === 'delete' && ! empty($selected)) {
            foreach ($selected as $id) {
                $posts = get_posts(array('post_type' => 'aow_certificate', 'title' => $id, 'numberposts' => 1));
                if (! empty($posts)) wp_delete_post($posts[0]->ID, true);
            }
            echo '<div class="updated notice"><p>Selected certificates deleted.</p></div>';
        }
        if ($action === 'export' && ! empty($selected)) {
            $format = sanitize_text_field($_POST['aow_export_format'] ?? 'json');
            $export = array();
            foreach ($selected as $id) {
                $p = get_posts(array('post_type' => 'aow_certificate', 'title' => $id, 'numberposts' => 1));
                if (! empty($p)) {
                    $post = $p[0];
                    $export[] = array(
                        'certificateId' => $post->post_title,
                        'studentName' => get_post_meta($post->ID, 'studentName', true),
                        'courseTitle' => get_post_meta($post->ID, 'courseTitle', true),
                        'completionDate' => get_post_meta($post->ID, 'completionDate', true),
                        'instructorName' => get_post_meta($post->ID, 'instructorName', true),
                    );
                }
            }
            if ($format === 'csv') {
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="aow-certificates-export.csv"');
                $out = fopen('php://output', 'w');
                fputcsv($out, array('certificateId','studentName','courseTitle','completionDate','instructorName'));
                foreach ($export as $row) fputcsv($out, $row);
                fclose($out);
                exit;
            }
            // server-side export enqueue for PDF/PNG
            if ( in_array($format, array('pdf','png')) ) {
                // If small batch, attempt synchronous export and zip download
                if ( count($selected) <= 5 && class_exists('ZipArchive') ) {
                    $uploaded_files = array();
                    foreach ( $selected as $cid ) {
                        $req = new WP_REST_Request();
                        $req->set_method('POST');
                        $req->set_body_params(array('certificateId'=>$cid,'format'=>$format));
                        $resp = aow_rest_export_certificate($req);
                        if ( is_wp_error($resp) ) continue;
                        $data = rest_get_server()->response_to_data($resp);
                        if ( ! empty($data['id']) ) {
                            $file = get_attached_file($data['id']);
                            if ( file_exists($file) ) $uploaded_files[] = $file;
                        }
                    }
                    if ( empty($uploaded_files) ) {
                        echo '<div class="error notice"><p>No files could be exported synchronously. Jobs have been scheduled instead.</p></div>';
                        // fallback to enqueue
                    } else {
                        $zip = new ZipArchive();
                        $upload_dir = wp_upload_dir();
                        $zip_path = $upload_dir['path'] . '/aow-export-' . time() . '.zip';
                        if ( $zip->open($zip_path, ZipArchive::CREATE) === TRUE ) {
                            foreach ( $uploaded_files as $fpath ) {
                                $zip->addFile($fpath, basename($fpath));
                            }
                            $zip->close();
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="aow-export-' . time() . '.zip"');
                            header('Content-Length: ' . filesize($zip_path));
                            readfile($zip_path);
                            // optionally delete file after download
                            @unlink($zip_path);
                            exit;
                        } else {
                            echo '<div class="error notice"><p>Failed to create zip archive. Jobs scheduled instead.</p></div>';
                        }
                    }
                }
                // schedule job via Action Scheduler if present or wp-cron
                $job = array('cert_ids'=>$selected, 'recipients'=>array(), 'subject'=>'Bulk Export', 'message'=>'Bulk export scheduled by admin', 'format'=>$format, 'requested_by'=>get_current_user_id());
                // schedule via Action Scheduler if available
                if ( function_exists('as_enqueue_async_action') ) {
                    as_enqueue_async_action('aow_run_enqueued_export_email', array('job'=>$job));
                } elseif ( class_exists('ActionScheduler') || function_exists('as_schedule_single_action') ) {
                    if ( function_exists('as_schedule_single_action') ) as_schedule_single_action(time()+10, 'aow_run_enqueued_export_email', array('job'=>$job));
                } else {
                    // fallback to wp-cron
                    wp_schedule_single_event( time() + 10, 'aow_run_enqueued_export_email', array( $job ) );
                }
                echo '<div class="updated notice"><p>Export jobs scheduled for selected certificates. Generated files will appear in Media Library when ready.</p></div>';
            } else {
                // default JSON
                header('Content-type: application/json');
                header('Content-Disposition: attachment; filename="aow-certificates-export.json"');
                echo wp_json_encode($export);
                exit;
            }
        }
    }

    $query = new WP_Query(array('post_type' => 'aow_certificate', 'posts_per_page' => -1));
    $certs = $query->posts;
    ?>
    <div class="wrap">
        <h1>Certificates</h1>
        <form method="post">
            <?php wp_nonce_field('aow_bulk_action', 'aow_bulk_nonce'); ?>
            <div style="margin-bottom:12px; display:flex; gap:8px; align-items:center;">
                <select name="aow_bulk_action">
                    <option value="">Bulk Actions</option>
                    <option value="delete">Delete Selected</option>
                    <option value="export">Export Selected (JSON)</option>
                </select>
                <label style="display:inline-flex;align-items:center;gap:6px;">
                    Format:
                    <select name="aow_export_format">
                        <option value="json">JSON</option>
                        <option value="csv">CSV</option>
                        <option value="pdf">PDF (server-side)</option>
                        <option value="png">PNG (server-side)</option>
                    </select>
                </label>
                <input type="submit" class="button" value="Apply">
            </div>
            <div style="margin-bottom:12px;">
                <h2>Email selected certificates</h2>
                <label>To (comma-separated): <input type="text" id="aow_email_to" name="aow_email_to" style="width:40%"></label>
                <label>Subject: <input type="text" id="aow_email_subject" name="aow_email_subject" style="width:40%" value="Your AppsOrWebs Certificate"></label>
                <label style="margin-left:12px;">Format: <select id="aow_email_format" name="aow_email_format"><option value="pdf">PDF</option><option value="png">PNG</option></select></label>
                <br>
                <label>Message:</label>
                <textarea id="aow_email_message" name="aow_email_message" rows="4" cols="80">Please find attached your certificate.</textarea>
                <button type="button" id="aow_send_email_btn" class="button button-primary">Send Email for Selected</button>
            </div>
            <table class="widefat fixed striped">
                <thead><tr><th style="width:2%"><input type="checkbox" id="aow_select_all"></th><th>Certificate ID</th><th>Student</th><th>Course</th><th>Completion</th><th>Attachments</th><th>Actions</th></tr></thead>
                <tbody>
                <?php if ( empty($certs) ) : ?>
                    <tr><td colspan="5">No certificates found.</td></tr>
                <?php else: foreach ($certs as $p): ?>
                    <tr>
                        <td><input type="checkbox" name="selected_certs[]" value="<?php echo esc_attr($p->post_title); ?>"></td>
                        <td><?php echo esc_html($p->post_title); ?></td>
                        <td><?php echo esc_html(get_post_meta($p->ID, 'studentName', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($p->ID, 'courseTitle', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($p->ID, 'completionDate', true)); ?></td>
                        <td>
                            <?php
                            // Find latest attachment for this certificate from jobs table
                            global $wpdb;
                            $jobs_table = $wpdb->prefix . 'aow_jobs';
                            $attach_html = '&mdash;';
                            $rows = $wpdb->get_results($wpdb->prepare("SELECT results FROM $jobs_table WHERE results LIKE %s ORDER BY finished_at DESC LIMIT 10", '%' . $wpdb->esc_like($p->post_title) . '%'), ARRAY_A);
                            if ( ! empty($rows) ) {
                                foreach ( $rows as $jr ) {
                                    $res = json_decode($jr['results'], true);
                                    if ( is_array($res) && isset($res[$p->post_title]) ) {
                                        $info = $res[$p->post_title];
                                        if ( ! empty($info['attachment_id']) ) {
                                            $url = wp_get_attachment_url($info['attachment_id']);
                                            $attach_html = '<a href="' . esc_url($url) . '" target="_blank">Download</a>';
                                            break;
                                        } elseif ( ! empty($info['file']) ) {
                                            $attach_html = esc_html($info['file']);
                                            break;
                                        }
                                    }
                                }
                            }
                            echo $attach_html;
                            ?>
                        </td>
                        <td>
                            <button type="button" class="button aow-export-btn" data-cert="<?php echo esc_attr($p->post_title); ?>">Export</button>
                            <button type="button" class="button aow-email-btn" data-cert="<?php echo esc_attr($p->post_title); ?>">Email</button>
                            <button type="button" class="button aow-enqueue-btn" data-cert="<?php echo esc_attr($p->post_title); ?>">Enqueue</button>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </form>
    </div>
    <script>
    document.getElementById('aow_select_all')?.addEventListener('change', function(e){
        document.querySelectorAll('input[name="selected_certs[]"]').forEach(function(cb){ cb.checked = e.target.checked; });
    });
    </script>
    <script>
    (function(){
        var root = '<?php echo esc_url( rest_url( "aow-cert/v1/" ) ); ?>';
        var nonce = '<?php echo wp_create_nonce( "wp_rest" ); ?>';

        function doExport(certId){
            return fetch(root + 'export', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-WP-Nonce':nonce},
                body: JSON.stringify({certificateId: certId, format: 'pdf'})
            }).then(r=>r.json());
        }

        function doEmail(certId, to){
            var fmt = (document.getElementById('aow_email_format') ? document.getElementById('aow_email_format').value : 'pdf');
            return fetch(root + 'export-and-email', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-WP-Nonce':nonce},
                body: JSON.stringify({cert_ids:[certId], recipients: to, subject: 'Your Certificate', message: 'Please find attached your certificate.', format: fmt})
            }).then(r=>r.json());
        }

        function doEnqueue(certId, to){
            var fmt = (document.getElementById('aow_email_format') ? document.getElementById('aow_email_format').value : 'pdf');
            return fetch(root + 'enqueue', {
                method: 'POST',
                headers: {'Content-Type':'application/json','X-WP-Nonce':nonce},
                body: JSON.stringify({cert_ids:[certId], recipients: to, subject: 'Your Certificate', message: 'Please find attached your certificate.', format: fmt})
            }).then(r=>r.json());
        }

        document.querySelectorAll('.aow-export-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                var cert = this.getAttribute('data-cert');
                var theBtn = this;
                theBtn.disabled = true; theBtn.textContent='Exporting...';
                doExport(cert).then(function(res){
                    theBtn.disabled = false; theBtn.textContent='Export';
                    var url = (res && res.url) ? res.url : (res && res.data && res.data.url ? res.data.url : null);
                    if(url) {
                        if(window.aow_showModal) aow_showModal('Exported', '<div>File available: <a href="'+url+'" target="_blank">'+url+'</a></div>', '<button id="aow-modal-ok" class="px-4 py-2 bg-aow-primary text-white rounded">OK</button>');
                    } else {
                        if(window.aow_showModal) aow_showModal('Export Failed', '<div>Export failed: '+escHtml(JSON.stringify(res))+'</div>');
                    }
                }).catch(function(e){ theBtn.disabled=false; theBtn.textContent='Export'; if(window.aow_showModal) aow_showModal('Network Error', '<div>Request failed: '+escHtml(e.message)+'</div>'); });
            });
        });

        document.querySelectorAll('.aow-email-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                var cert = this.getAttribute('data-cert');
                var theBtn = this;
                aow_prompt('Enter recipient email(s), comma-separated', '', function(to){
                    if(!to) return;
                    theBtn.disabled = true; theBtn.textContent='Sending...';
                    doEmail(cert, to).then(function(res){
                        theBtn.disabled = false; theBtn.textContent='Email';
                        if(res && res.success) {
                            if(window.aow_showModal) aow_showModal('Email', '<div>Email queued/sent.</div>');
                        } else {
                            if(window.aow_showModal) aow_showModal('Email Failed', '<div>Email failed: '+escHtml(JSON.stringify(res))+'</div>');
                        }
                    }).catch(function(e){ theBtn.disabled=false; theBtn.textContent='Email'; if(window.aow_showModal) aow_showModal('Network Error', '<div>Request failed: '+escHtml(e.message)+'</div>'); });
                });
            });
        });

        document.querySelectorAll('.aow-enqueue-btn').forEach(function(btn){
            btn.addEventListener('click', function(){
                var cert = this.getAttribute('data-cert');
                var theBtn = this;
                aow_prompt('Enter recipient email(s), comma-separated (leave empty to only export)', '', function(to){
                    if(to === null) return;
                    theBtn.disabled = true; theBtn.textContent='Enqueuing...';
                    doEnqueue(cert, to).then(function(res){
                        theBtn.disabled = false; theBtn.textContent='Enqueue';
                        if(res && res.success) {
                            if(window.aow_showModal) aow_showModal('Job Scheduled', '<div>Job scheduled successfully.</div>');
                        } else {
                            if(window.aow_showModal) aow_showModal('Enqueue Failed', '<div>Enqueue failed: '+escHtml(JSON.stringify(res))+'</div>');
                        }
                    }).catch(function(e){ theBtn.disabled=false; theBtn.textContent='Enqueue'; if(window.aow_showModal) aow_showModal('Network Error', '<div>Request failed: '+escHtml(e.message)+'</div>'); });
                });
            });
        });

    })();
    </script>
    <?php
}

function aow_admin_jobs_page() {
    if ( ! current_user_can('manage_aow_certificates') ) wp_die('Insufficient permissions');
    global $wpdb;
    $jobs_table = $wpdb->prefix . 'aow_jobs';
    $rows = $wpdb->get_results("SELECT * FROM $jobs_table ORDER BY created_at DESC LIMIT 100", ARRAY_A);
    ?>
    <div class="wrap">
        <h1>Certificate Jobs</h1>
        <p class="description">Recent background export/email jobs. This table is created during plugin activation.</p>
        <table class="widefat fixed striped">
            <thead><tr><th>ID</th><th>Hash</th><th>Status</th><th>Created</th><th>Started</th><th>Finished</th><th>Results</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if ( empty($rows) ) : ?>
                <tr><td colspan="7">No jobs found.</td></tr>
            <?php else: foreach ( $rows as $r ): ?>
                <tr>
                    <td><?php echo esc_html($r['id']); ?></td>
                    <td><?php echo esc_html($r['job_hash']); ?></td>
                    <td><?php echo esc_html($r['status']); ?></td>
                    <td><?php echo esc_html($r['created_at']); ?></td>
                    <td><?php echo esc_html($r['started_at']); ?></td>
                    <td><?php echo esc_html($r['finished_at']); ?></td>
                    <td>
                        <?php if ( ! empty($r['results']) ) {
                            $res = json_decode($r['results'], true);
                            if ( is_array($res) ) {
                                foreach ( $res as $cid => $info ) {
                                    if ( ! empty($info['attachment_id']) ) {
                                        $url = wp_get_attachment_url($info['attachment_id']);
                                        echo '<div><strong>' . esc_html($cid) . ':</strong> <a href="' . esc_url($url) . '" target="_blank">Download</a></div>';
                                    } elseif ( ! empty($info['file']) ) {
                                        echo '<div><strong>' . esc_html($cid) . ':</strong> ' . esc_html($info['file']) . '</div>';
                                    } else {
                                        echo '<div><strong>' . esc_html($cid) . ':</strong> ' . esc_html($info['error'] ?? 'failed') . '</div>';
                                    }
                                }
                            } else {
                                echo esc_html($r['results']);
                            }
                        } else { echo '&mdash;'; } ?>
                    </td>
                    <td>
                        <?php if ( $r['status'] !== 'completed' ) : ?>
                            <button class="button aow-retry-job" data-jobid="<?php echo esc_attr($r['id']); ?>">Retry</button>
                        <?php endif; ?>
                        <button class="button aow-details-job" data-jobid="<?php echo esc_attr($r['id']); ?>">Details</button>
                        <button class="button aow-purge-job" data-jobid="<?php echo esc_attr($r['id']); ?>">Purge</button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}



function aow_rest_create_certificate( WP_REST_Request $request ) {
    $params = $request->get_json_params();

    // Basic sanitization and validation
    $studentName = sanitize_text_field( $params['studentName'] ?? '' );
    $courseTitle = sanitize_text_field( $params['courseTitle'] ?? '' );
    $completionDate = sanitize_text_field( $params['completionDate'] ?? '' );
    $instructorName = sanitize_text_field( $params['instructorName'] ?? '' );
    $logoUrl = esc_url_raw( $params['logoUrl'] ?? '' );
    $signatureUrl = esc_url_raw( $params['signatureUrl'] ?? '' );
    $certificateId = sanitize_text_field( $params['certificateId'] ?? '' );

    if ( empty( $studentName ) || empty( $courseTitle ) || empty( $certificateId ) ) {
        return new WP_Error( 'missing_fields', 'Required fields are missing', array( 'status' => 400 ) );
    }

    // Enforce uniqueness: check for existing certificate with same title (certificateId)
    $existing = get_posts(array(
        'post_type' => 'aow_certificate',
        'title' => $certificateId,
        'post_status' => 'publish',
        'numberposts' => 1,
    ));
    $force = ! empty( $params['force'] );
    if ( ! empty( $existing ) && ! $force ) {
        return new WP_Error( 'duplicate_id', 'A certificate with this ID already exists. Pass {"force":true} to overwrite.', array( 'status' => 409 ) );
    }
    if ( ! empty( $existing ) && $force ) {
        wp_delete_post( $existing[0]->ID, true );
    }

    $post_id = wp_insert_post(array(
        'post_type' => 'aow_certificate',
        'post_title' => $certificateId,
        'post_status' => 'publish',
        'meta_input' => array(
            'studentName' => $studentName,
            'courseTitle' => $courseTitle,
            'completionDate' => $completionDate,
            'instructorName' => $instructorName,
            'logoUrl' => $logoUrl,
            'signatureUrl' => $signatureUrl,
        )
    ));

    if ( is_wp_error( $post_id ) || $post_id === 0 ) {
        return new WP_Error( 'create_failed', 'Failed to create certificate', array( 'status' => 500 ) );
    }

    // Record into custom table for uniqueness mapping
    global $wpdb;
    $table_name = $wpdb->prefix . 'aow_certificates';
    $inserted = $wpdb->insert(
        $table_name,
        array('certificate_id' => $certificateId, 'post_id' => $post_id),
        array('%s','%d')
    );

    if ($inserted === false) {
        // Clean up created post
        wp_delete_post( $post_id, true );
        return new WP_Error( 'create_failed', 'Failed to record certificate (possible duplicate).', array( 'status' => 500 ) );
    }

    return rest_ensure_response( array( 'success' => true, 'id' => $certificateId ) );
}

function aow_rest_list_certificates( WP_REST_Request $request ) {
    $query = new WP_Query(array(
        'post_type' => 'aow_certificate',
        'post_status' => 'publish',
        'posts_per_page' => -1,
    ));

    $items = array();
    foreach ( $query->posts as $p ) {
        $items[] = array(
            'certificateId' => $p->post_title,
            'studentName' => get_post_meta( $p->ID, 'studentName', true ),
            'courseTitle' => get_post_meta( $p->ID, 'courseTitle', true ),
            'completionDate' => get_post_meta( $p->ID, 'completionDate', true ),
            'instructorName' => get_post_meta( $p->ID, 'instructorName', true ),
            'logoUrl' => get_post_meta( $p->ID, 'logoUrl', true ),
            'signatureUrl' => get_post_meta( $p->ID, 'signatureUrl', true ),
        );
    }

    return rest_ensure_response( $items );
}

function aow_rest_verify_certificate( WP_REST_Request $request ) {
    $id = sanitize_text_field( $request['id'] );
    $posts = get_posts(array(
        'post_type' => 'aow_certificate',
        'title' => $id,
        'post_status' => 'publish',
        'numberposts' => 1,
    ));

    if ( empty( $posts ) ) {
        return new WP_Error( 'not_found', 'Certificate not found', array( 'status' => 404 ) );
    }

    $p = $posts[0];
    $data = array(
        'certificateId' => $p->post_title,
        'studentName' => get_post_meta( $p->ID, 'studentName', true ),
        'courseTitle' => get_post_meta( $p->ID, 'courseTitle', true ),
        'completionDate' => get_post_meta( $p->ID, 'completionDate', true ),
        'instructorName' => get_post_meta( $p->ID, 'instructorName', true ),
        'logoUrl' => get_post_meta( $p->ID, 'logoUrl', true ),
        'signatureUrl' => get_post_meta( $p->ID, 'signatureUrl', true ),
    );

    return rest_ensure_response( $data );
}

function aow_rest_delete_certificate( WP_REST_Request $request ) {
    $id = sanitize_text_field( $request['id'] );
    $posts = get_posts(array(
        'post_type' => 'aow_certificate',
        'title' => $id,
        'post_status' => 'publish',
        'numberposts' => 1,
    ));

    if ( empty( $posts ) ) {
        return new WP_Error( 'not_found', 'Certificate not found', array( 'status' => 404 ) );
    }

    $deleted = wp_delete_post( $posts[0]->ID, true );
    if ( ! $deleted ) {
        return new WP_Error( 'delete_failed', 'Failed to delete certificate', array( 'status' => 500 ) );
    }

    // Remove from custom mapping table as well
    global $wpdb;
    $table_name = $wpdb->prefix . 'aow_certificates';
    $wpdb->delete( $table_name, array( 'certificate_id' => $id ), array( '%s' ) );

    return rest_ensure_response( array( 'success' => true ) );
}

function aow_rest_upload_media( WP_REST_Request $request ) {
    // Accept either a file (multipart) under 'file' or a base64 string in 'data'
    if ( ! function_exists( 'wp_handle_sideload' ) ) require_once ABSPATH . 'wp-admin/includes/file.php';

    $type = $request['type'];
    $files = $request->get_file_params();
    $params = $request->get_json_params();

    if ( ! empty( $files['file'] ) ) {
        $file = $files['file'];
        $overrides = array( 'test_form' => false );
        $movefile = wp_handle_sideload( $file, $overrides );
        if ( isset( $movefile['error'] ) ) {
            return new WP_Error( 'upload_failed', $movefile['error'], array( 'status' => 500 ) );
        }
        $file_path = $movefile['file'];
        $file_url = $movefile['url'];
    } elseif ( ! empty( $params['data'] ) ) {
        // base64 data
        $data = $params['data'];
        if ( preg_match('/^data:(image\/[^;]+);base64,(.*)$/', $data, $m) ) {
            $mime = $m[1];
            $b64 = base64_decode($m[2]);
            $ext = explode('/', $mime)[1];
            $upload = wp_upload_bits( uniqid('aow_') . '.' . $ext, null, $b64 );
            if ( $upload['error'] ) return new WP_Error( 'upload_failed', $upload['error'], array( 'status' => 500 ) );
            $file_path = $upload['file'];
            $file_url = $upload['url'];
        } else {
            return new WP_Error( 'bad_data', 'Invalid data URI', array( 'status' => 400 ) );
        }
    } else {
        return new WP_Error( 'no_file', 'No file provided', array( 'status' => 400 ) );
    }

    // Insert into Media Library
    $wp_filetype = wp_check_filetype( basename( $file_path ), null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name( basename( $file_path ) ),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id = wp_insert_attachment( $attachment, $file_path );
    if ( ! is_wp_error( $attach_id ) ) {
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $meta = wp_generate_attachment_metadata( $attach_id, $file_path );
        wp_update_attachment_metadata( $attach_id, $meta );
        return rest_ensure_response( array( 'url' => wp_get_attachment_url( $attach_id ), 'id' => $attach_id ) );
    }

    return new WP_Error( 'attach_failed', 'Failed to insert attachment', array( 'status' => 500 ) );
}

function aow_get_certificate_post_by_id( $certificateId ) {
    $posts = get_posts(array(
        'post_type' => 'aow_certificate',
        'title' => $certificateId,
        'post_status' => 'publish',
        'numberposts' => 1,
    ));
    return ! empty( $posts ) ? $posts[0] : null;
}

function aow_rest_export_certificate( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $id = sanitize_text_field( $params['certificateId'] ?? '' );
    $format = sanitize_text_field( $params['format'] ?? 'pdf' );
    if ( empty( $id ) ) return new WP_Error( 'missing_id', 'certificateId required', array( 'status' => 400 ) );

    // Rate limiting per IP (simple)
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $transient_key = 'aow_export_rl_' . md5( $ip );
    $count = intval( get_transient( $transient_key ) );
    if ( $count > 20 ) return new WP_Error( 'rate_limited', 'Too many export requests, please try later', array( 'status' => 429 ) );
    set_transient( $transient_key, $count + 1, HOUR_IN_SECONDS );

    $post = aow_get_certificate_post_by_id( $id );
    if ( ! $post ) return new WP_Error( 'not_found', 'Certificate not found', array( 'status' => 404 ) );

    // Build a local preview URL for the certificate; it should be accessible and renderable.
    // We'll build a URL to the page that contains the shortcode and add query params to render only the certificate.
    $page_url = home_url( '/?aow_export=1&cert_id=' . rawurlencode( $id ) );

    // Use Browsershot (Spatie) if available
    if ( class_exists('\Spatie\Browsershot\Browsershot') ) {
        try {
            $upload_dir = wp_upload_dir();
            $out_path = $upload_dir['path'] . '/' . $id . '.' . ($format === 'png' ? 'png' : 'pdf');
            $bs = new \Spatie\Browsershot\Browsershot();
            $bs->noSandbox();
            $bs->setNodeBinary( '/usr/bin/node' );
            $bs->setNpmBinary( '/usr/bin/npm' );
            if ( $format === 'png' ) {
                $bs->setScreenshotType('png');
                $bs->timeout(120)->save($out_path);
            } else {
                $bs->format('A4')->timeout(120)->savePdf($out_path);
            }

            // Insert into media library
            $filetype = wp_check_filetype( basename( $out_path ), null );
            $attachment = array(
                'post_mime_type' => $filetype['type'],
                'post_title' => $id,
                'post_content' => '',
                'post_status' => 'inherit'
            );
            $attach_id = wp_insert_attachment( $attachment, $out_path );
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $meta = wp_generate_attachment_metadata( $attach_id, $out_path );
            wp_update_attachment_metadata( $attach_id, $meta );

            return rest_ensure_response( array( 'url' => wp_get_attachment_url( $attach_id ), 'id' => $attach_id ) );
        } catch ( Exception $e ) {
            return new WP_Error( 'export_failed', 'Export failed: ' . $e->getMessage(), array( 'status' => 500 ) );
        }
    }

    return new WP_Error( 'no_renderer', 'Server-side renderer (Browsershot) is not available. Please ensure composer dependencies are installed and Node + Puppeteer are present.', array( 'status' => 500 ) );
}

function aow_rest_export_and_email( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $cert_ids = $params['cert_ids'] ?? $params['certificateIds'] ?? $params['certificateId'] ?? null;
    $format = sanitize_text_field( $params['format'] ?? 'pdf' );
    $recipients = $params['recipients'] ?? $params['to'] ?? $params['emails'] ?? null;
    $cc = sanitize_text_field( $params['cc'] ?? '' );
    $bcc = sanitize_text_field( $params['bcc'] ?? '' );
    $subject = sanitize_text_field( $params['subject'] ?? "Your AppsOrWebs Certificate" );
    $message = wp_kses_post( $params['message'] ?? 'Please find attached your certificate.' );

    if ( empty( $cert_ids ) || empty( $recipients ) ) return new WP_Error( 'missing_params', 'cert_ids and recipients are required', array( 'status' => 400 ) );

    // normalize cert ids to array
    if ( ! is_array( $cert_ids ) ) {
        $cert_ids = array( $cert_ids );
    }

    // normalize recipients to array
    if ( is_string( $recipients ) ) {
        $recipients = array_map('trim', explode(',', $recipients));
    }
    if ( ! is_array( $recipients ) ) {
        return new WP_Error( 'invalid_recipients', 'Recipients must be a string or array', array( 'status' => 400 ) );
    }

    $headers = array('Content-Type: text/html; charset=UTF-8');
    if ( ! empty( $cc ) ) $headers[] = 'Cc: ' . sanitize_text_field($cc);
    if ( ! empty( $bcc ) ) $headers[] = 'Bcc: ' . sanitize_text_field($bcc);

    $results = array();
    foreach ( $cert_ids as $cid ) {
        $cid = sanitize_text_field( $cid );
        if ( empty( $cid ) ) {
            $results[$cid] = array('success'=>false,'error'=>'Empty certificate id');
            continue;
        }

        // Build export request
        $export_req = new WP_REST_Request();
        $export_req->set_method('POST');
        $export_req->set_body_params(array('certificateId' => $cid, 'format' => $format));
        $export_resp = aow_rest_export_certificate( $export_req );
        if ( is_wp_error( $export_resp ) ) {
            $results[$cid] = array('success'=>false,'error'=>$export_resp->get_error_message());
            continue;
        }
        $data = rest_get_server()->response_to_data( $export_resp );
        $attach_id = $data['id'] ?? 0;
        if ( ! $attach_id ) {
            $results[$cid] = array('success'=>false,'error'=>'Export did not return attachment id');
            continue;
        }
        $file_path = get_attached_file( $attach_id );
        if ( ! file_exists( $file_path ) ) {
            $results[$cid] = array('success'=>false,'error'=>'Attachment file missing');
            continue;
        }

        $sent = wp_mail( $recipients, $subject, $message, $headers, array( $file_path ) );
        if ( ! $sent ) {
            $results[$cid] = array('success'=>false,'error'=>'wp_mail failed');
        } else {
            $results[$cid] = array('success'=>true,'file'=>get_attached_file($attach_id),'attachment_id'=>$attach_id);
        }
    }

    return rest_ensure_response( array( 'success' => true, 'results' => $results ) );
}

/**
 * Enqueue export+email to run in background via WP Cron
 */
function aow_rest_enqueue_export_and_email( WP_REST_Request $request ) {
    $params = $request->get_json_params();
    $cert_ids = $params['cert_ids'] ?? null;
    $recipients = $params['recipients'] ?? $params['to'] ?? null;
    $subject = sanitize_text_field( $params['subject'] ?? 'Your Certificate' );
    $message = wp_kses_post( $params['message'] ?? 'Please find attached your certificate.' );
    $format = sanitize_text_field( $params['format'] ?? 'pdf' );

    if ( empty( $cert_ids ) || empty( $recipients ) ) {
        return new WP_Error('missing_params','cert_ids and recipients required', array('status'=>400));
    }
    if ( ! is_array($cert_ids) ) $cert_ids = array($cert_ids);
    if ( is_string($recipients) ) $recipients = array_map('trim', explode(',', $recipients));

    $job = array(
        'cert_ids' => $cert_ids,
        'recipients' => $recipients,
        'subject' => $subject,
        'message' => $message,
        'format' => $format,
        'requested_by' => get_current_user_id(),
    );

    $hash = 'aow_enqueue_' . md5( wp_json_encode($job) );
    if ( get_transient( $hash ) ) {
        return rest_ensure_response(array('success'=>false,'message'=>'An identical job is already queued.'));
    }

    // Persist job record in jobs table for tracking
    global $wpdb;
    $jobs_table = $wpdb->prefix . 'aow_jobs';
    $job_data_json = wp_json_encode($job);
    $inserted = $wpdb->insert($jobs_table, array('job_hash'=>$hash,'job_data'=>$job_data_json,'status'=>'pending','created_at'=>current_time('mysql')),
        array('%s','%s','%s','%s'));
    $job_record_id = $inserted ? $wpdb->insert_id : 0;
    // Use Action Scheduler if available for robustness
    if ( class_exists('\ActionScheduler') || function_exists('as_enqueue_async_action') ) {
        if ( function_exists('as_enqueue_async_action') ) {
            as_enqueue_async_action('aow_run_enqueued_export_email', array('job' => $job));
        } elseif ( class_exists('ActionScheduler') ) {
            // fallback to scheduling via the Action Scheduler API
            if ( function_exists('as_next_scheduled_action') && ! as_next_scheduled_action('aow_run_enqueued_export_email', array('job'=>$job)) ) {
                as_schedule_single_action(time() + 10, 'aow_run_enqueued_export_email', array('job'=>$job));
            }
        }
        set_transient( $hash, 1, MINUTE_IN_SECONDS * 10 );
        error_log('AOW: scheduled job via Action Scheduler');
        return rest_ensure_response(array('success'=>true,'message'=>'Job scheduled (Action Scheduler)','job_id'=>$job_record_id));
    }

    // schedule to run in 10 seconds via wp-cron as a fallback
    // schedule via wp-cron fallback
    wp_schedule_single_event( time() + 10, 'aow_run_enqueued_export_email', array( $job ) );
    set_transient( $hash, 1, MINUTE_IN_SECONDS * 10 );

    return rest_ensure_response(array('success'=>true,'message'=>'Job scheduled','job_id'=>$job_record_id));
}

/**
 * Background worker: runs exports and emails for a job array
 */
function aow_run_enqueued_export_email( $job ) {
    global $wpdb;
    $jobs_table = $wpdb->prefix . 'aow_jobs';

    $job_record_id = 0;
    // If called via Action Scheduler with wrapper, $job may be ['job'=>...]
    if ( is_array($job) && isset($job['job']) ) $job = $job['job'];

    // If it's an array with job_id, try to load persisted job data
    if ( is_array($job) && isset($job['job_id']) && intval($job['job_id']) ) {
        $job_record_id = intval($job['job_id']);
        $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $jobs_table WHERE id = %d", $job_record_id), ARRAY_A);
        if ( $row && ! empty($row['job_data']) ) {
            $loaded = json_decode($row['job_data'], true);
            if ( is_array($loaded) ) $job = $loaded;
        }
    }

    if ( empty($job['cert_ids']) || empty($job['recipients']) ) return;

    // update job record to running
    if ( $job_record_id ) {
        $wpdb->update($jobs_table, array('status'=>'running','started_at'=>current_time('mysql')), array('id'=>$job_record_id), array('%s','%s'), array('%d'));
    }

    $aggregate_results = array();
    foreach( (array)$job['cert_ids'] as $cid ) {
        // Build export req
        $export_req = new WP_REST_Request();
        $export_req->set_method('POST');
        $export_req->set_body_params(array('certificateId'=>$cid,'format'=>$job['format'] ?? 'pdf'));
        $export_resp = aow_rest_export_certificate( $export_req );
        if ( is_wp_error($export_resp) ) {
            $aggregate_results[$cid] = array('success'=>false,'error'=>$export_resp->get_error_message());
            error_log('AOW export failed for ' . $cid . ': ' . $export_resp->get_error_message());
            continue;
        }
        $data = rest_get_server()->response_to_data( $export_resp );
        $attach_id = $data['id'] ?? 0;
        if ( ! $attach_id ) { error_log('AOW export produced no attachment for ' . $cid); continue; }
        $file_path = get_attached_file( $attach_id );
        if ( ! file_exists($file_path) ) { error_log('AOW attachment missing: ' . $file_path); continue; }
        // send mail
        $headers = array('Content-Type: text/html; charset=UTF-8');
        wp_mail( $job['recipients'], $job['subject'], $job['message'], $headers, array($file_path) );
        $aggregate_results[$cid] = array('success'=>true,'attachment_id'=>$attach_id,'file'=>$file_path);
    }

    // determine failures
    $failed = array_filter($aggregate_results, function($r){ return empty($r['success']); });

    if ( $job_record_id ) {
        if ( empty($failed) ) {
            $wpdb->update($jobs_table, array('status'=>'completed','results'=>wp_json_encode($aggregate_results),'finished_at'=>current_time('mysql')), array('id'=>$job_record_id), array('%s','%s','%s'), array('%d'));
        } else {
            // increment retries and decide whether to reschedule
            $row = $wpdb->get_row($wpdb->prepare("SELECT retries FROM $jobs_table WHERE id = %d", $job_record_id), ARRAY_A);
            $retries = intval($row['retries'] ?? 0) + 1;
            $wpdb->update($jobs_table, array('retries'=>$retries,'results'=>wp_json_encode($aggregate_results)), array('id'=>$job_record_id), array('%d','%s'), array('%d'));
            if ( $retries <= 3 ) {
                // exponential backoff in seconds
                $delay = 30 * pow(2, $retries - 1);
                if ( function_exists('as_enqueue_async_action') ) {
                    as_enqueue_async_action('aow_run_enqueued_export_email', array('job'=>array_merge($job, array('job_id'=>$job_record_id))));
                } elseif ( function_exists('as_schedule_single_action') ) {
                    as_schedule_single_action(time() + $delay, 'aow_run_enqueued_export_email', array('job'=>array_merge($job, array('job_id'=>$job_record_id))));
                } else {
                    wp_schedule_single_event(time() + $delay, 'aow_run_enqueued_export_email', array(array_merge($job, array('job_id'=>$job_record_id))));
                }
                $wpdb->update($jobs_table, array('status'=>'retrying'), array('id'=>$job_record_id), array('%s'), array('%d'));
                error_log('AOW: job ' . $job_record_id . ' retrying in ' . $delay . ' seconds (attempt ' . $retries . ')');
            } else {
                // mark failed
                $wpdb->update($jobs_table, array('status'=>'failed','finished_at'=>current_time('mysql')), array('id'=>$job_record_id), array('%s','%s'), array('%d'));
                error_log('AOW: job ' . $job_record_id . ' failed after ' . $retries . ' attempts');
            }
        }
    }
}
// Register worker for wp-cron
add_action('aow_run_enqueued_export_email', 'aow_run_enqueued_export_email');

// Action Scheduler compatibility: wrapper receives args array with 'job' key
function aow_as_run_wrapper( $arg ) {
    if ( is_array( $arg ) && isset( $arg['job'] ) ) {
        aow_run_enqueued_export_email( $arg['job'] );
    } elseif ( is_array( $arg ) && isset( $arg[0]['job'] ) ) {
        aow_run_enqueued_export_email( $arg[0]['job'] );
    }
}
add_action('aow_run_enqueued_export_email_action_scheduler', 'aow_as_run_wrapper');
?>
