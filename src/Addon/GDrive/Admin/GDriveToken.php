<?php

namespace AwaisWP\Excluder\Addon\GDrive\Admin;

use AwaisWP\Excluder\TemplateLoader;
use AwaisWP\Excluder\Admin\ExcluderOptions;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveSettings;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDrive;

defined( 'ABSPATH' ) || exit;

/**
 * Class GDriveToken
 * @package AwaisWP\Excluder\Addon\GDrive
 */

class GDriveToken {




	/**
	 * The page slug.
	 *
	 * @var PAGE_SLUG
	 */
	public const PAGE_SLUG = 'aio-wp-gdrive-upload';


	/**
	 * The template loader.
	 *
	 * @var loader
	 */
	private $loader = null;


	/**
	 * The GDrive instace.
	 *
	 * @var gdrive
	 */
	private $gdrive = null;

	/**
	 * Construct the Excluder class.
	 */
	public function __construct() {
		 $this->loader = TemplateLoader::get_instance();
		$this->gdrive  = GDrive::get_instance();
		add_action( 'init', array( $this, 'register_session' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Register PHP session.
	 **/
	public function register_session() {
		if ( php_sapi_name() != 'cli' ) {
			ob_start();
		}

		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
	}

	/**
	 * Registers a new settings page under Settings.
	 */
	public function admin_menu() {
		add_submenu_page(
			ExcluderOptions::PAGE_SLUG,
			'GDrive Upload',
			'GDrive Upload',
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Init the Gogole Client to Oauth user.
	 **/
	public function init_google_client() {
		$result   = array();
		$settings = get_option( GDriveSettings::GDRIVE_SETTINGS );

		$clientId     = $settings['client_id'] ?? '';
		$clientSecret = $settings['secret_key'] ?? '';

		$redirectUri = get_admin_url( get_current_blog_id(), 'admin.php?page=' . self::PAGE_SLUG );

		if ( empty( $clientId ) || empty( $clientSecret ) ) {
			$result['error']  = __( 'Client ID or Client Secret is missing. Please go to GDrive Settings Page.', 'ff_excluder-customization' );
			$result['client'] = null;
			return $result;
		}

		try {

			$client = new \Google_Client();
			$client->setApplicationName( 'Get Token' );
			$client->setClientId( $clientId );
			$client->setClientSecret( $clientSecret );
			$client->setRedirectUri( $redirectUri );
			$client->setScopes( array( 'https://www.googleapis.com/auth/drive.file' ) );
			$client->setAccessType( 'offline' );
			//$client->setApprovalPrompt('force');
			$client->setPrompt( 'consent' );

			if ( isset( $_GET['code'] ) ) {
				$client->authenticate( $_GET['code'] );
				$_SESSION['token'] = $client->getAccessToken();
				$client->getAccessToken( array( 'refreshToken' ) );
				//$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				header( 'Location: ' . filter_var( $redirectUri, FILTER_SANITIZE_URL ) );
				exit;
			}

			if ( isset( $_SESSION['token'] ) ) {
				$client->setAccessToken( $_SESSION['token'] );
			}

			if ( isset( $_REQUEST['logout'] ) ) {
				$client->revokeToken( $_SESSION['token'] );
				unset( $_SESSION['token'] );
				unset( $_SESSION['save_token'] );
			}

			if ( isset( $_SESSION['token'] ) && $client->getAccessToken() ) {
				$token = $_SESSION['token'];

				//echo "Access Token = " . $token['access_token'] . '<br/>';
				//echo "Refresh Token = " . $token['refresh_token'] . '<br/>';

				// Saving the refresh token in a text file.
				$saveToken = file_put_contents( FF_EXCLUDER_CUST_PLUGIN_DIR_PATH . '/token/token.txt', $token['refresh_token'] );
				if ( $saveToken ) {
					$_SESSION['save_token'] = $saveToken;
				}
			}
		} catch ( \Exception $e ) {

			$result['error']  = $e->getMessage();
			$result['client'] = null;
			return $result;
		}

		$result['error']  = null;
		$result['client'] = $client;
		return $result;
	}

	/**
	 * Settings page display callback.
	 */
	function settings_page() {
		ini_set( 'display_errors', 1 );
		ini_set( 'display_startup_errors', 1 );
		error_reporting( E_ALL );

		set_time_limit( 0 );

		if ( ! isset( $_GET['upload'] ) ) {
			$result = $this->init_google_client();

			//dd($result);
			if ( empty( $result['error'] ) ) {
				$client           = $result['client'];
				$data['auth_url'] = $client->createAuthUrl();
				$data['error']    = '';
			} else {
				$data['error'] = $result['error'];
			}
		} else {
			$data['gdrive']   = $this->gdrive;
			$data['auth_url'] = '';
			$data['error']    = '';
		}

		$data['backup_list'] = GDriveSettings::get_aio_backup_list();

		$this->loader->get_template(
			'gdrive-file-upload.php',
			$data,
			FF_EXCLUDER_CUST_PLUGIN_DIR_PATH . '/templates/admin/addon/',
			true
		);
	}
}

function dd( $mix ) {
	echo '<pre>';
	print_r( $mix );
	echo '</pre>';
}
