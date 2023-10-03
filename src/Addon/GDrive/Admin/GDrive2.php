<?php

namespace AwaisWP\Excluder\Addon\GDrive\Admin;

use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveSettings;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveToken;
use AwaisWP\Excluder\Singleton;

defined( 'ABSPATH' ) || exit;

/**
 * Class GDrive2
 * @package AwaisWP\Excluder\Addon\GDrive
 */

class GDrive2 extends Singleton {

	private $clientId     = null;
	private $clientSecret = null;
	private $redirectUri  = null;

	public $fileRequest;
	public $folderId;

	private $mimeType;
	private $fileName;
	private $path;
	private $client;

	public function __construct() {
		$settings = get_option( GDriveSettings::GDRIVE_SETTINGS );

		$this->clientId     = $settings['client_id'];
		$this->clientSecret = $settings['secret_key'];
		$this->redirectUri  = get_admin_url( get_current_blog_id(), 'admin.php/' . GDriveToken::PAGE_SLUG );

		$this->client = new \Google_Client();
	}

	/**
	 * Set Google API. Get Token and init the upload process.
	 *
	 **/
	function initialize( $status = 0, $file_seek = 0 ) {
		//echo 'Initializing uploading...' . '<br/>';

		$client = $this->client;

		$client->setClientId( $this->clientId );
		$client->setClientSecret( $this->clientSecret );
		$client->setRedirectUri( $this->redirectUri );

		$refreshToken = file_get_contents( FF_EXCLUDER_CUST_PLUGIN_DIR_PATH . '/token/token.txt' );
		$client->refreshToken( $refreshToken );
		$tokens = $client->getAccessToken();
		$client->setAccessToken( $tokens );

		$client->setDefer( true );
		//$this->processFile();
		return $this->upload( $status, $file_seek );
	}

	/**
	 * Process file and display the mime type.
	 *
	 **/
	public function processFile() {
		$fileRequest = $this->fileRequest;
		//echo "Process File: $fileRequest" . '<br/>';

		$path_parts     = pathinfo( $fileRequest );
		$this->path     = $path_parts['dirname'];
		$this->fileName = $path_parts['basename'];

		$finfo          = finfo_open( FILEINFO_MIME_TYPE );
		$this->mimeType = finfo_file( $finfo, $fileRequest );
		finfo_close( $finfo );

		//echo 'Mime type is: ' . $this->mimeType . '<br/>';
		//$this->upload();
	}

	/**
	 * Upload the file in chunks.
	 * Uploading in chunks allows to upload a large file.
	 **/
	public function upload( $status, $file_seek ) {
		$ret      = array();
		$client   = $this->client;
		$folderId = $this->folderId;
		$filePath = $this->fileRequest;

		$driveService = new \Google_Service_Drive( $client );

		$fileMetadata = new \Google_Service_Drive_DriveFile(
			array(
				'name'    => basename( $filePath ),
				'parents' => array( $folderId ),
			)
		);

		$chunkSizeBytes = 50 * 1024 * 1024; // 10MB chunk size (adjust as needed).
		$client->setDefer( true );

		$request = $driveService->files->create( $fileMetadata );
		$media   = new \Google_Http_MediaFileUpload(
			$client,
			$request,
			'application/octet-stream', // Set the appropriate MIME type for your file.
			null,
			true,
			$chunkSizeBytes
		);

		$media->setFileSize( filesize( $filePath ) );

		// Start uploading.
		//echo 'Uploading...: ' . $this->fileName . '<br/>';

		// Upload the various chunks. $status will be false until the process is complete.
		//$status = false;
		$handle = fopen( $filePath, 'rb' );

		$data['stream_meta'] = stream_get_meta_data( $handle );
		if ( ! $status && ! feof( $handle ) ) {
			$seek_status = fseek( $handle, $file_seek, SEEK_CUR );
			if ( $seek_status == 0 ) {
				$chunk         = fread( $handle, $chunkSizeBytes );
				$new_file_seek = ftell( $handle );
				$status        = $media->nextChunk( $chunk );
			} else {
				$data['debug'] = 'indebug';
				fclose( $handle );
				$client->setDefer( false );
			}
		}

		/*if($seek_status !=0){
			$result = false;
			if ($status != false) {
				$result = $status;
			}
			fclose($handle);
			$client->setDefer(false);
		}*/

		$data['status']      = $status;
		$data['file_seek']   = $new_file_seek;
		$data['seek_status'] = $seek_status;
		return $data;

		// The final value of $status will be the data from the API for the object that has been uploaded.
		/*$result = false;
		if ($status != false) {
			$result = $status;
		}
		fclose($handle);*/

		// Reset to the client to execute requests immediately in the future.
		//$client->setDefer(false);
		//dd($result);
	}
}
