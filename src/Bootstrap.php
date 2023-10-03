<?php

namespace AwaisWP\Excluder;

use AwaisWP\Excluder\Admin\ExcluderOptions;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveSettings;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveToken;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveAjax;

defined( 'ABSPATH' ) || exit;

/**
 * Class Bootstrap
 * @package AwaisWP\Excluder
 */

class Bootstrap {
	/**
	 * The plugin version.
	 *
	 * @var version
	 */
	private $version = '1.0.0';

	/**
	 * Instance to call certain functions globally within the plugin.
	 *
	 * @var instance
	 */
	protected static $instance = null;

	/**
	 * Construct the plugin.
	 */
	public function __construct() {
		 add_action( 'init', array( $this, 'load_plugin' ), 0 );
	}

	/**
	 * Main Bootstrap instance.
	 *
	 * Ensures only one instance is loaded or can be loaded.
	 *
	 * @static
	 * @return self Main instance.
	 */
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Determine which plugin to load.
	 */
	public function load_plugin() {
		 $this->define_constants();
		$this->init_hooks();
	}

	/**
	 * Define WC Constants.
	 */
	private function define_constants() {
		// Path related defines
		$this->define( 'FF_EXCLUDER_CUST_PLUGIN_FILE', FF_EXCLUDER_CUST_PLUGIN_FILE );
		$this->define( 'FF_EXCLUDER_CUST_PLUGIN_BASENAME', plugin_basename( FF_EXCLUDER_CUST_PLUGIN_FILE ) );
		$this->define( 'FF_EXCLUDER_CUST_PLUGIN_DIR_PATH', untrailingslashit( plugin_dir_path( FF_EXCLUDER_CUST_PLUGIN_FILE ) ) );
		$this->define( 'FF_EXCLUDER_CUST_PLUGIN_DIR_URL', untrailingslashit( plugins_url( '/', FF_EXCLUDER_CUST_PLUGIN_FILE ) ) );
	}

	/**
	 * Collection of hooks.
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'init' ), 1 );
		add_action( 'admin_init', array( $this, 'is_parent_plugin_present' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Localisation.
	 */
	public function load_textdomain() {
		 load_plugin_textdomain( 'ff_excluder-customization', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		new ExcluderOptions();
		new Excluder();
		new GDriveSettings();
		new GDriveToken();
		//new GDriveAjax();
	}

	/**
	 * Check if parent plugin present.
	 */
	public function is_parent_plugin_present() {
		if ( is_plugin_active( 'all-in-one-wp-migration/all-in-one-wp-migration.php' ) ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'add_notice' ) );
	}

	/**
	 * Add notice for parent plugin.
	 */
	public function add_notice() {
		$screen = get_current_screen();
		if ( 'settings_page_aio-wp-migration-excluder' === $screen->id ) {
			?>
			<div class="notice error is-dismissible">
				<p>
					<?php _e( 'Please install All-in-One WP Migration plugin in order to use below features.', 'ff_excluder-customization' ); ?>

				</p>
			</div>
			<?php
		}
	}

	/**
	 * Enqueue all styles.
	 */
	public function enqueue_styles() {
		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $this->app_pages() ) ) {
			wp_enqueue_style( 'ff_excluder-customization-backend', FF_EXCLUDER_CUST_PLUGIN_DIR_URL . '/assets/css/ff_excluder-customization-backend.css', array(), null, 'all' );
		}
	}


	/**
	 * Enqueue all scripts.
	 */
	public function enqueue_scripts() {
		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $this->app_pages() ) ) {
			wp_enqueue_script( 'ff_excluder-customization-backend', FF_EXCLUDER_CUST_PLUGIN_DIR_URL . '/assets/js/ff_excluder-customization-backend.js', array( 'jquery' ) );

			$wp_localize_data = array(
				'ajax_url'         => admin_url( 'admin-ajax.php' ),
				'_ajax_nonce'      => wp_create_nonce( 'ajax' ),
				'aio_file_path'    => untrailingslashit( WP_CONTENT_DIR ) . '/ai1wm-backups',
				'custom_file_path' => untrailingslashit( ABSPATH ),
			);
			wp_localize_script( 'ff_excluder-customization-backend', 'LOCAL', $wp_localize_data );
		}
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string $name
	 * @param  string|bool $value
	 */
	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Enqueue all scripts.
	 */
	public function app_pages() {
		return array(
			ExcluderOptions::PAGE_SLUG,
			GDriveSettings::PAGE_SLUG,
			GDriveToken::PAGE_SLUG,
		);
	}
}
