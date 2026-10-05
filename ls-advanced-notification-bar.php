<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://levent-sali.gr
 * @since             1.0.0
 * @package           Ls_Advanced_Notification_Bar
 *
 * @wordpress-plugin
 * Plugin Name:       Advanced Notification Bar
 * Plugin URI:        https://github.com/Lsali/ls-advanced-notification-bar
 * Description:       Displays a customizable notification bar at the top of your WordPress site with advanced settings
 * Version:           1.0.0
 * Author:            Levent Sali
 * Author URI:        https://levent-sali.gr/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ls-advanced-notification-bar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'LS_ADVANCED_NOTIFICATION_BAR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ls-advanced-notification-bar-activator.php
 */
function activate_ls_advanced_notification_bar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ls-advanced-notification-bar-activator.php';
	Ls_Advanced_Notification_Bar_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ls-advanced-notification-bar-deactivator.php
 */
function deactivate_ls_advanced_notification_bar() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ls-advanced-notification-bar-deactivator.php';
	Ls_Advanced_Notification_Bar_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ls_advanced_notification_bar' );
register_deactivation_hook( __FILE__, 'deactivate_ls_advanced_notification_bar' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ls-advanced-notification-bar.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ls_advanced_notification_bar() {

	$plugin = new Ls_Advanced_Notification_Bar();
	$plugin->run();

}
run_ls_advanced_notification_bar();
