<div class="wrap">
	<h1><?php echo __('GDrive Settings'); ?></h1>

	<form name="form1" id="form1" method="post" action="">
		<div class="field">
			<?php wp_nonce_field('gdrive-nonce', 'gdrive'); ?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="content"><?php echo __('Client ID', 'ff_excluder-customization'); ?></label>
					</th>
					<td>
						<input type="text" class="regular-text" name="awp_aio_gdrive[client_id]" id="content" value="<?php echo esc_html($settings['client_id']); ?>" />
					</td>
				</tr>
			</table>
		</div>

		<div class="field">
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="content"><?php echo __('Secret Key', 'ff_excluder-customization'); ?></label>
					</th>
					<td>
						<input type="text" class="regular-text" name="awp_aio_gdrive[secret_key]" id="content" value="<?php echo esc_html($settings['secret_key']); ?>" />
					</td>
				</tr>
			</table>
		</div>
		<?php submit_button( 'Save' ); ?>
	</form>
</div>