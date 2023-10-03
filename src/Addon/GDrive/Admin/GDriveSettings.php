<?php

namespace AwaisWP\Excluder\Addon\GDrive\Admin;

use AwaisWP\Excluder\TemplateLoader;
use AwaisWP\Excluder\Admin\ExcluderOptions;

defined( 'ABSPATH' ) || exit;

/**
 * Class GDriveSettings
 * @package AwaisWP\Excluder\Addon\GDrive
 */

class GDriveSettings {


	/**
	 * The page slug.
	 *
	 * @var PAGE_SLUG
	 */
	public const PAGE_SLUG = 'aio-wp-gdrive-settings';


	/**
	 * The template loader.
	 *
	 * @var loader
	 */
	private $loader = null;

	/**
	 * Contains settings.
	 *
	 * @var GDRIVE_SETTINGS
	 */
	public const GDRIVE_SETTINGS = 'awp_aio_gdrive_settings';


	/**
	 * Construct the Excluder class.
	 */
	public function __construct() {
		$this->loader = TemplateLoader::get_instance();
		add_action( 'admin_init', array( $this, 'on_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Save settings.
	 */
	public function on_admin_init() {
		if ( isset( $_POST['submit'] ) && $_POST['submit'] === 'Save' ) {
			if ( ! isset( $_POST['gdrive'] ) || ! wp_verify_nonce( $_POST['gdrive'], 'gdrive-nonce' ) ) {
				wp_die( __( 'Security check failed.', 'ff_excluder-customization' ) );
				exit;
			} else {
				$new_values = array_map( 'sanitize_text_field', $_POST['awp_aio_gdrive'] );
				update_option( self::GDRIVE_SETTINGS, $new_values );
			}
		}
	}

	/**
	 * Registers a new settings page under Settings.
	 */
	function admin_menu() {
		add_submenu_page(
			ExcluderOptions::PAGE_SLUG,
			'GDrive Settings',
			'GDrive Settings',
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Settings page display callback.
	 */
	public function settings_page() {
		$settings = get_option( self::GDRIVE_SETTINGS );
		$data     = array(
			'settings' => $settings,
		);

		$this->loader->get_template(
			'gdrive-settings.php',
			$data,
			FF_EXCLUDER_CUST_PLUGIN_DIR_PATH . '/templates/admin/addon/',
			true
		);
	}

	/**
	 * Get list of backup files.
	 **/
	public static function get_aio_backup_list() {
		return glob( WP_CONTENT_DIR . '/ai1wm-backups/*.wpress' );
	}

	/**
	 * Get human readable size of the file.
	 * @param  int  $size
	 * @param  int $precision
	 * @return string
	 */
	public static function readable_size( $size, $precision = 2 ) {
		 $base    = log( $size, 1024 );
		$suffixes = array( 'B', 'KB', 'MB', 'GB', 'TB' );
		return round( pow( 1024, $base - floor( $base ) ), $precision ) . ' ' . $suffixes[ floor( $base ) ];
	}
}
