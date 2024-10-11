<?php
/**
 * Plugin Name: Yunus Admin Data Viewer Vue App
 * Description: A Vue-powered plugin with data, settings, and caching functionalities.
 * Author: Yunus Emre Özdiyar
 * Version: 1.0.0
 * Text Domain: yunus-admin-data-viewer
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Enqueue scripts and styles conditionally
function yunus_admin_view_enqueue_scripts($hook_suffix) {
    if ($hook_suffix === 'toplevel_page_yunus-admin-view-app') {
        wp_enqueue_script(
            'yunus_vue_app',
            plugins_url('/dist/main.js', __FILE__),
            [],
            filemtime(plugin_dir_path(__FILE__) . 'dist/main.js'),
            true
        );

        wp_localize_script('yunus_vue_app', 'yunusPluginData', [
            'root_url' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ]);

        // Check for style.css before enqueueing
        $style_path = plugin_dir_path(__FILE__) . 'dist/style.css';
        if (file_exists($style_path)) {
            wp_enqueue_style(
                'yunus_vue_styles',
                plugins_url('/dist/style.css', __FILE__),
                [],
                filemtime($style_path)
            );
        } else {
            error_log('Style.css file not found at ' . $style_path);
        }
    }
}
add_action('admin_enqueue_scripts', 'yunus_admin_view_enqueue_scripts');

// Register admin menu
function yunus_admin_view_admin_menu() {
    add_menu_page(
        __('Admin View Vue App', 'yunus-admin-view'),
        __('Admin View App', 'yunus-admin-view'),
        'manage_options',
        'yunus-admin-view-app',
        'yunus_admin_view_render_app',
        'dashicons-admin-generic',
        3
    );
}
add_action('admin_menu', 'yunus_admin_view_admin_menu');

// Render Vue App
function yunus_admin_view_render_app() {
    echo "<div id='yunus-vue-app'></div>";
    echo "<!-- Debug: Rendered the Vue app container -->";
}

// Register REST API endpoints
function yunus_register_rest_endpoints() {
    register_rest_route('yunus/v1', '/update-setting', [
        'methods' => 'POST',
        'callback' => 'yunus_update_setting_endpoint',
        'permission_callback' => 'yunus_verify_admin_permission',
    ]);

    // Update Setting Endpoint
    register_rest_route('yunus/v1', '/update-setting', [
        'methods' => 'POST',
        'callback' => 'yunus_update_setting_endpoint',
        'permission_callback' => 'yunus_verify_admin_permission',
    ]);

    // Get All Settings Endpoint
    register_rest_route('yunus/v1', '/get-settings', [
        'methods' => 'GET',
        'callback' => 'yunus_get_settings_endpoint',
        'permission_callback' => 'yunus_verify_admin_permission',
    ]);
}
add_action('rest_api_init', 'yunus_register_rest_endpoints');

// Verify Admin Permission
function yunus_verify_admin_permission() {
    return current_user_can('manage_options');
}

// Data Endpoint Callback
function yunus_get_data_endpoint() {
    $cache_key = 'yunus_data_cache';
    $cached_data = get_option($cache_key);

    if ($cached_data && isset($cached_data['timestamp']) && time() - $cached_data['timestamp'] < HOUR_IN_SECONDS) {
        return rest_ensure_response($cached_data['data']);
    }

    // Fetch data from the external API
    $response = wp_remote_get('https://miusage.com/v1/challenge/2/static/');
    if (is_wp_error($response)) {
        return new WP_Error('api_error', __('Unable to fetch data from the API', 'yunus-admin-view'));
    }

    $data = json_decode(wp_remote_retrieve_body($response), true);
    if (!isset($data['data'])) {
        return new WP_Error('invalid_data', __('Invalid data structure received', 'yunus-admin-view'));
    }

    // Cache the data
    update_option($cache_key, [
        'timestamp' => time(),
        'data' => $data
    ]);

    return rest_ensure_response($data);
}

// Update Setting Endpoint Callback
function yunus_update_setting_endpoint($request) {
    // Verify nonce
    $nonce = $request->get_header('X-WP-Nonce');
    if (!wp_verify_nonce($nonce, 'wp_rest')) {
        return new WP_Error('rest_nonce_invalid', __('Nonce verification failed', 'yunus-admin-view'), ['status' => 403]);
    }

    // Get and sanitize input
    $settings_data = $request->get_json_params();
    $num_rows = isset($settings_data['setting_value']['numRows']) ? intval($settings_data['setting_value']['numRows']) : 5;
    $date_format = isset($settings_data['setting_value']['dateFormat']) ? sanitize_text_field($settings_data['setting_value']['dateFormat']) : 'human';

    // Validate input
    if ($num_rows < 1 || $num_rows > 10) {
        return new WP_Error('rest_invalid_param', __('Number of rows must be between 1 and 10', 'yunus-admin-view'), ['status' => 400]);
    }
    if (!in_array($date_format, ['human', 'timestamp'])) {
        return new WP_Error('rest_invalid_param', __('Invalid date format', 'yunus-admin-view'), ['status' => 400]);
    }

    // Update the settings in the database
    update_option('yunus_num_rows', $num_rows);
    update_option('yunus_date_format', $date_format);

    return rest_ensure_response(['success' => true]);
}

// Get All Settings Endpoint Callback
function yunus_get_settings_endpoint() {
    $settings = [
        'numRows' => get_option('yunus_num_rows', 5),
        'dateFormat' => get_option('yunus_date_format', 'human')
    ];

    return rest_ensure_response($settings);
}
