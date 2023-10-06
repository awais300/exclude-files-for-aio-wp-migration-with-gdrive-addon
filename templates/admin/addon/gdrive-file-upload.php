<?php

use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveToken;
use AwaisWP\Excluder\Addon\GDrive\Admin\GDriveSettings;

?>
<div class="wrap">
	<h1><?php echo __( 'Upload File to Google Drive' ); ?></h1>
	<div class="auth-info">
		<?php if ( isset( $_SESSION['save_token'] ) ) : ?>
			<div class="notice notice-success notice-alt">
				<?php
				$url         = get_admin_url( get_current_blog_id(), 'admin.php?page=' . GDriveToken::PAGE_SLUG );
				$logout_link = "<a class='logout' href='{$url}&logout'>" . __( 'Logout', 'ff_excluder-customization' ) . '</a>';
				?>
				<p><?php echo __( 'Authentication Successful ' . $logout_link, 'ff_excluder-customization' ); ?></p>
			</div>

		<?php elseif ( ! empty( $error ) ) : ?>
			<div class="notice notice-warning notice-alt">
				<p><?php echo $error; ?></p>
			</div>
		<?php else : ?>
			<a class="login" href="<?php echo @$auth_url; ?>">Connect Me!</a>
		<?php endif; ?>
	</div>

	<!-- <a href="#" id="mybtn">Ajax Test</a> -->

	<?php if ( isset( $_SESSION['save_token'] ) && $error == '' ) : ?>
		<div class="field">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="content"><?php echo __( 'GDrive Folder ID:', 'ff_excluder-customization' ); ?></label>
						<i><?php echo __( 'Your file will be uploaded under this folder. <a target="_blank"href="https://i.imgur.com/W8svvyH.png">See example</a>', 'ff_excluder-customization' ); ?></i>
					</th>
					<td>
						<input id="gdrive_folder_id" type="text" class="regular-text" name="gdrive_folder_id" id="content" value="<?php echo $_GET['gdrive_folder_id'] ?? ''; ?>" />
					</td>
				</tr>
			</table>
		</div>
		<div class="field">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="content"><?php echo __( 'Select file', 'ff_excluder-customization' ); ?></label>
						<i><?php echo __( 'Select backup file from the list.', 'ff_excluder-customization' ); ?></i>
					</th>
					<td>
						<select id="backup_file_name" name="backup_file_name">
							<option value="">--<?php echo __( 'Select File', 'ff_excluder-customization' ); ?>--</option>
							<?php foreach ( $backup_list as $backup_name ) : ?>
								<?php
								$size = GDriveSettings::readable_size( filesize( $backup_name ) );
								?>
								<option value="<?php echo basename( $backup_name ); ?>"><?php echo basename( $backup_name ) . ' (' . $size . ')'; ?></option>
							<?php endforeach; ?>
						</select>
						<input type="checkbox" <?php echo ( isset( $_GET['custom_path'] ) ) ? 'checked' : ''; ?> id="check-custom-path" value=""><span>
							<?php
							_e(
								'I want to upload different file',
								'ff_excluder-customization'
							);
							?>
						</span>
					</td>
				</tr>

				<tr id="custom-row" <?php echo ( isset( $_GET['custom_path'] ) ) ? 'style="display: table-row;"' : ''; ?>>
					<th scope="row">
						<label for="content"><?php echo __( 'Input file path', 'ff_excluder-customization' ); ?></label>
						<i><?php echo __( 'The path is already set to sites\'s root: ' . untrailingslashit( ABSPATH ), 'ff_excluder-customization' ); ?></i>
					</th>

					<td>
						<input type="text" id="custom-path" placeholder="<?php _e( 'e.g: /wp-content/somefile.zip', 'ff_excluder-customization' ); ?>" class="regular-text" name="custom_path" value="<?php echo $_GET['custom_path'] ?? ''; ?>" /><br />
					</td>
				</tr>
			</table>
		</div>
		<p class="submit">
			<input data-url="<?php echo wp_nonce_url( get_admin_url( get_current_blog_id(), 'admin.php?page=' . GDriveToken::PAGE_SLUG . '&upload=yes' ) ); ?>" type="button" name="submit" id="upload_btn" class="button button-primary" value="Upload to Google Drive">
		</p>
		<?php
		if ( isset( $_GET['upload'] ) && $_GET['upload'] == 'yes' ) :

			$nonce = $_GET['_wpnonce'];
			if ( ! wp_verify_nonce( $nonce ) ) {
				wp_die( _e( 'Invlid request', 'ff_excluder-customization' ) );
			}
			?>
			<div class="uploading">
				<?php
				$fullPath = @$_GET['backup_file_name'];
				$folderId = isset( $_GET['gdrive_folder_id'] ) ? $_GET['gdrive_folder_id'] : '';
				if ( ! is_file( $fullPath ) || ! file_exists( $fullPath ) ) {
					echo __( 'File (' . $fullPath . ') do not exist. Please input correct file path.', 'ff_excluder-customization' );
				} elseif ( empty( $folderId ) ) {
					echo __( 'Google Drive folder ID is missing.', 'ff_excluder-customization' );
				} else {
					$gdrive->fileRequest = $fullPath;
					$gdrive->folderId    = $folderId;
					$gdrive->initialize();
				}
				?>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>

<?php if ( isset( $_GET['upload'] ) && $_GET['upload'] == 'yes' ) : ?>
	<script>
		if (jQuery('input#check-custom-path').is(':checked')) {
			jQuery('#backup_file_name').prop('disabled', 'disabled');
		} else {
			jQuery('#backup_file_name').prop('disabled', '');
		}
		window.history.replaceState(null, '', '<?php echo get_admin_url( get_current_blog_id(), 'admin.php?page=' . GDriveToken::PAGE_SLUG ); ?>');
	</script>
<?php endif; ?>
