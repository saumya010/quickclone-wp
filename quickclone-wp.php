<?php
/**
 * Plugin Name: QuickClone WP
 * Plugin URI:  https://github.com/saumya010/quickclone-wp
 * Description: Duplicate any post, page, or custom post type with a single click.
 * Version:     1.0.1
 * Author:      Saumya Sharma
 * Author URI:  https://iamsaumya.com
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quickclone-wp
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Include dependencies.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-qcw-duplicator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-qcw-admin.php';

/**
 * Initialize plugin.
 */
add_action( 'plugins_loaded', ['QCW_Admin', 'init'] );
