<?php

namespace AwaisWP\Excluder\Addon\GDrive\Admin;

use AwaisWP\Excluder\Addon\GDrive\Admin\GDrive2;

defined( 'ABSPATH' ) || exit;

/**
 * Class GDriveAjax
 * @package AwaisWP\Excluder\Addon\GDrive
 */

class GDriveAjax {

	/**
	 * Construct the Excluder class.
	 */
	public function __construct() {
		$callback = 'init_upload';
		add_action( "wp_ajax_{$callback}", array( $this, $callback ) );
		add_action( "wp_ajax_nopriv_{$callback}", array( $this, $callback ) ); //for non-logged in users
	}

	public function init_upload() {
		if ( ! check_ajax_referer( 'ajax', '_ajax_nonce', false ) ) {
			wp_send_json_error();
			wp_die();
		} else {
			$arr = $this->init_google_drive_api();
			echo json_encode( $arr );
		}
		exit();
	}

	public function init_google_drive_api() {
		$fullPath = WP_CONTENT_DIR . '/ai1wm-backups/' . $_POST['backup_file_name'];
		$folderId = isset( $_POST['gdrive_folder_id'] ) ? $_POST['gdrive_folder_id'] : '';

		if ( ! is_file( $fullPath ) || ! file_exists( $fullPath ) ) {
			$response['error']   = true;
			$response['message'] = __( 'File do not exist. Please input correct file path.', 'ff_excluder-customization' );
		} elseif ( empty( $folderId ) ) {
			$response['error']   = true;
			$response['message'] = __( 'Google Drive folder ID is missing.', 'ff_excluder-customization' );
		} elseif ( empty( $_POST['first_run'] ) || 1 ) {

			$file_seek = intval( $_POST['file_seek'] );
			$status    = $_POST['status'];

			$gdrive              = GDrive2::get_instance();
			$gdrive->fileRequest = $fullPath;
			$gdrive->folderId    = $folderId;
			$result              = $gdrive->initialize( $status, $file_seek );

			$status    = $result['status'];
			$file_seek = $result['file_seek'];

			if ( $status == false ) {
				// File uploaded status.
				$response['status'] = 0;
			} else {
				$response['status']  = 1;
				$response['message'] = 'File Uploaded!';
			}
			$response['error']       = false;
			$response['stream_meta'] = $result['stream_meta'];
			$response['seek_status'] = $result['seek_status'];
			return $response;
		}
	}
}
