<?php

namespace AwaisWP\Excluder;

defined( 'ABSPATH' ) || exit;

/**
 * Class TemplateLoader
 * @package AwaisWP\Excluder
 */

class TemplateLoader extends Singleton {
	/**
	 * Loads a template.
	 *
	 * @param  string $template_name
	 * @param  array $args
	 * @param  string $template_path
	 * @param  bool $echo
	 *
	 */
	public function get_template( $template_name = null, $args = array(), $template_path = null, $echo = false ) {
		$output = null;

		$template_path = $template_path . $template_name;

		if ( file_exists( $template_path ) ) {
			extract($args); // @codingStandardsIgnoreLine required for template.

			ob_start();
			include $template_path;
			$output = ob_get_clean();
		} else {
			throw new \Exception( __( 'Specified path does not exist', 'ff_excluder-customization' ) );
		}

		if ( $echo ) {
			print $output;
		} else {
			return $output;
		}
	}
}
