<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://levent-sali.gr
 * @since      1.0.0
 *
 * @package    Ls_Advanced_Notification_Bar
 * @subpackage Ls_Advanced_Notification_Bar/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ls_Advanced_Notification_Bar
 * @subpackage Ls_Advanced_Notification_Bar/includes
 * @author     Levent Sali <salilevent@hotmail.com>
 */
class Ls_Advanced_Notification_Bar_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ls-advanced-notification-bar',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
