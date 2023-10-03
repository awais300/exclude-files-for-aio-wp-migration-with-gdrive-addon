<?php

use AwaisWP\Excluder\Admin\ExcluderOptions;
?>
<!-- Tab links -->
<div class="wrap">
	<div class="tab">
		<button class="tablinks" id="default-open" onclick="openTab(event, 'tab-1')"><?php echo __( 'Settings', 'ff_excluder-customization' ); ?></button>
		<button class="tablinks" onclick="openTab(event, 'tab-2')">More</button>
	</div>
	<!-- Tab content -->
	<div id="tab-1" class="tabcontent">
		<h1><?php echo __( 'Settings', 'ff_excluder-customization' ); ?></h1>
		<?php if ( isset( $_POST['submit'] ) && $_POST['submit'] === 'Save Settings' ) : ?>
			<div class="notification">
				<div class="notice notice-success is-dismissible">
					<p><?php echo __( 'Settings Saved.', 'ff_excluder-customization' ); ?></p>
				</div>
			</div>
		<?php endif; ?>
		<form name="form1" id="form1" method="post" action="">
			<div id="ex-content">
				<?php wp_nonce_field( 'excluder-nonce', 'excluder' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="content"><?php echo __( 'Exclude Content Files', 'ff_excluder-customization' ); ?></label>
							<i><?php echo __( 'Enter file path one per line.', 'ff_excluder-customization' ); ?></i>
							<i><?php echo __( 'For content files, the path is already wp-content/', 'ff_excluder-customization' ); ?></i>
						</th>
						<td>
							<textarea name="awp_aio_excluder[<?php echo ExcluderOptions::FIELD_CONTENT; ?>]" id="content" rows="5" cols="50"><?php echo esc_textarea( $settings['content'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="ex-media">
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="content"><?php echo __( 'Exclude Media Files', 'ff_excluder-customization' ); ?></label>
							<i><?php echo __( 'Enter file path one per line.', 'ff_excluder-customization' ); ?></i>
							<i><?php echo __( 'For media files, the path is already wp-content/uploads/', 'ff_excluder-customization' ); ?></i>
						</th>
						<td>
							<textarea name="awp_aio_excluder[<?php echo ExcluderOptions::FIELD_MEDIA; ?>]" id="content" rows="5" cols="50"><?php echo esc_textarea( $settings['media'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="ex-plugins">
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="content"><?php echo __( 'Exclude Plugin Files', 'ff_excluder-customization' ); ?></label>
							<i><?php echo __( 'Enter file path one per line.', 'ff_excluder-customization' ); ?></i>
							<i><?php echo __( 'For plugin files, the path is already wp-content/plugins/', 'ff_excluder-customization' ); ?></i>
						</th>
						<td>
							<textarea name="awp_aio_excluder[<?php echo ExcluderOptions::FIELD_PLUGINS; ?>]" id="content" rows="5" cols="50"><?php echo esc_textarea( $settings['plugins'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>

			<div id="ex-themes">
				<table class="form-table">
					<tr>
						<th scope="row">
							<label for="content"><?php echo __( 'Exclude Themes Files', 'ff_excluder-customization' ); ?></label>
							<i><?php echo __( 'Enter file path one per line.', 'ff_excluder-customization' ); ?></i>
							<i><?php echo __( 'For theme files, the path is already wp-content/themes/', 'ff_excluder-customization' ); ?></i>
						</th>
						<td>
							<textarea name="awp_aio_excluder[<?php echo ExcluderOptions::FIELD_THEMES; ?>]" id="content" rows="5" cols="50"><?php echo esc_textarea( $settings['themes'] ); ?></textarea>
						</td>
					</tr>
				</table>
			</div>
			<?php submit_button( 'Save Settings' ); ?>
		</form>
	</div>

	<div id="tab-2" class="tabcontent morehelp">
		<h1><?php echo __( 'Need more help?', 'ff_excluder-customization' ); ?></h1>

		<p>
			<?php echo __( 'Are you struggling to keep your WordPress website running smoothly? I offer a <b>monthly WordPress maintenance</b> service at an <b>incredibly reasonable price</b>.', 'ff_excluder-customization' ); ?>
			<br />

			<?php echo __( 'Whether you need <i>regular updates, bug fixes, or simply want to ensure your website is always in top shape</i>, I\'ve got you covered.', 'ff_excluder-customization' ); ?><br />

			<?php echo __( 'Don\'t let website issues hold you back.', 'ff_excluder-customization' ); ?> <a target="_blank" href="https://awaiswp.com/wordpress-maintenance-services/" class="button button-secondary"><?php echo __( 'Click Here</a> to get in touch with me and take the hassle out of WordPress maintenance today!', 'ff_excluder-customization' ); ?>
		</p>
	</div>
</div>
