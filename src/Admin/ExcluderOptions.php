<?php

namespace AwaisWP\Excluder\Admin;

use AwaisWP\Excluder\Excluder;
use AwaisWP\Excluder\TemplateLoader;

defined( 'ABSPATH' ) || exit;

/**
 * Class ExcluderOptions
 * @package AwaisWP\Excluder
 */

class ExcluderOptions {

	/**
	 * The content fields.
	 *
	 * @var FIELD_CONTENT
	 */
	public const FIELD_CONTENT = 'content';

	/**
	 * The media fields.
	 *
	 * @var FIELD_MEDIA
	 */
	public const FIELD_MEDIA = 'media';

	/**
	 * The plugins fields.
	 *
	 * @var FIELD_PLUGINS
	 */
	public const FIELD_PLUGINS = 'plugins';

	/**
	 * The themes fields.
	 *
	 * @var FIELD_THEMES
	 */
	public const FIELD_THEMES = 'themes';

	/**
	 * The page slug.
	 *
	 * @var PAGE_SLUG
	 */
	public const PAGE_SLUG = 'aio-wp-migration-tools';

	/**
	 * The template loader.
	 *
	 * @var loader
	 */
	private $loader = null;

	/**
	 * Contains settings.
	 *
	 * @var EXCLUDER_SETTINGS
	 */
	private const EXCLUDER_SETTINGS = 'awp_aio_excluder_settings';


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
		if ( isset( $_POST['submit'] ) && $_POST['submit'] === 'Save Settings' ) {
			if ( ! isset( $_POST['excluder'] ) || ! wp_verify_nonce( $_POST['excluder'], 'excluder-nonce' ) ) {
				wp_die( __( 'Security check failed.', 'ff_excluder-customization' ) );
				exit;
			} else {
				$new_values = array_map( 'sanitize_textarea_field', $_POST['awp_aio_excluder'] );
				update_option( self::EXCLUDER_SETTINGS, $new_values );
			}
		}
	}

	/**
	 * Save settings.
	 * @param string $type
	 *
	 * @return array
	 */
	public static function get_settings( $type = null ) {
		$settings = get_option( self::EXCLUDER_SETTINGS );
		if ( empty( $type ) ) {
			return $settings;
		} elseif ( isset( $settings[ $type ] ) ) {
			$arr = explode( "\n", $settings[ $type ] );
			return array_map( 'sanitize_text_field', $arr );
		} else {
			throw new \Exception( __( 'Missing type', 'ff_excluder-customization' ) );
		}
	}

	/**
	 * Registers a new settings page under Settings.
	 */
	function admin_menu() {
		add_menu_page( 'AIO WP Migration Tools', 'AIO WP Migration Tools', 'manage_options', ExcluderOptions::PAGE_SLUG );
		add_submenu_page(
			ExcluderOptions::PAGE_SLUG,
			'AIO WP Migration Excluder',
			'AIO WP Migration Excluder',
			'manage_options',
			ExcluderOptions::PAGE_SLUG,
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Settings page display callback.
	 */
	function settings_page() {
		$settings = get_option( self::EXCLUDER_SETTINGS );
		$data     = array(
			'settings' => $settings,
		);

		$this->loader->get_template(
			'excluder-options.php',
			$data,
			FF_EXCLUDER_CUST_PLUGIN_DIR_PATH . '/templates/admin/',
			true
		);
	}
}
