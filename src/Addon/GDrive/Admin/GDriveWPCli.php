<?php

namespace AwaisWP\Excluder\Addon\GDrive\Admin;

use AwaisWP\Excluder\Admin\ExcluderOptions;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveSettings;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDrive;

defined( 'ABSPATH' ) || exit;

/**
 * Class GDriveWPCli
 * @package AwaisWP\Excluder\Addon\GDrive
 */

class GDriveWPCli {

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
		 $this->gdrive = GDrive::get_instance();
		add_action( 'cli_init', array( $this, 'wp_cli_register_commands' ) );
	}

	/**
	 * Register WP CLI command.
	 **/
	function wp_cli_register_commands() {
		\WP_CLI::add_command( 'gd upload', array( $this, 'upload' ) );
	}

	/**
	 * Start the upload process via WP CLI.
	 * @param  array $args
	 * @param  array $assoc_args
	 */
	public function upload( $args, $assoc_args ) {
		$path         = $assoc_args['file'];
		$gd_folder_id = $assoc_args['gdrive_folder_id'];

		if ( $this->validate_arguments( $path, $gd_folder_id ) === true ) {
			$gdrive              = $this->gdrive;
			$gdrive->fileRequest = $path;
			$gdrive->folderId    = $gd_folder_id;
			$gdrive->initialize();
		}
	}

	/**
	 * Validate WP CLI arguments.
	 * @param string $path.
	 * @param $gd_folder_id
	 * @return bool | Exit()
	 **/
	public function validate_arguments( $path = '', $gd_folder_id = '' ) {
		if ( ! is_file( $path ) || ! file_exists( $path ) ) {
			//if (0) {
			\WP_CLI::line( 'Please provide the correct path of file' );
			$this->help();
		}

		if ( empty( $gd_folder_id ) || is_bool( $gd_folder_id ) ) {
			\WP_CLI::line( 'Please provide the Google Drive folder ID' );
			$this->help();
		}

		return true;
	}

	/**
	 * Display general help in console.
	 */
	public function help() {
		\WP_CLI::line( 'Site root path is: ' . ABSPATH );
		\WP_CLI::line( 'Example: wp gd upload --file=paht/to/file --gdrive_folder_id=folder_id' );
		exit;
	}
}
