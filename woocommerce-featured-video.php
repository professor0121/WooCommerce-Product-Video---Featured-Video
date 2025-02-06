<?php
/**
 * Plugin Name: WooCommerce Featured Video
 * Plugin URI: https://yourwebsite.com/
 * Description: Adds a featured video option for WooCommerce products.
 * Version: 1.0.0
 * Author: Professor
 * Author URI: https://yourwebsite.com/
 * License: GPL2
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WCFV_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WCFV_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
require_once WCFV_PLUGIN_DIR . 'includes/admin-settings.php';
require_once WCFV_PLUGIN_DIR . 'includes/video-display.php';

// Activation Hook
function wcfv_activate() {
    // Activation logic here
}
register_activation_hook(__FILE__, 'wcfv_activate');

// Deactivation Hook
function wcfv_deactivate() {
    // Deactivation logic here
}
register_deactivation_hook(__FILE__, 'wcfv_deactivate');

