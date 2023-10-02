<?php

namespace AwaisWP\Excluder;

defined( 'ABSPATH' ) || exit;

use AwaisWP\Excluder\Admin\ExcluderOptions;

/**
 * Class Excluder
 * @package AwaisWP\Excluder
 */

class Excluder {

	/**
	 * Construct the Excluder class.
	 */
	public function __construct() {
		add_filter( 'ai1wm_exclude_content_from_export', array( $this, 'exclude_content_from_export' ) );
		add_filter( 'ai1wm_exclude_media_from_export', array( $this, 'exclude_media_from_export' ) );
		add_filter( 'ai1wm_exclude_plugins_from_export', array( $this, 'exclude_plugins_from_export' ) );
		add_filter( 'ai1wm_exclude_themes_from_export', array( $this, 'exclude_themes_from_export' ) );
	}

	/**
	 * Exclude content files.
	 * @param  array $exclude_filters
	 * @return array
	 */
	public function exclude_content_from_export( $exclude_filters ) {
		$lines = array_filter( ExcluderOptions::get_settings( ExcluderOptions::FIELD_CONTENT ) );

		if ( ! empty( $lines ) ) {
			foreach ( $lines as $line ) {
				$exclude_filters[] = $line;
			}
		}

		return $exclude_filters;
	}


	/**
	 * Exclude media files.
	 * @param  array $exclude_filters
	 * @return array
	 */
	public function exclude_media_from_export( $exclude_filters ) {
		 $lines = array_filter( ExcluderOptions::get_settings( ExcluderOptions::FIELD_MEDIA ) );

		if ( ! empty( $lines ) ) {
			foreach ( $lines as $line ) {
				$exclude_filters[] = $line;
			}
		}

		return $exclude_filters;
	}


	/**
	 * Exclude plugins files.
	 * @param  array $exclude_filters
	 * @return array
	 */
	public function exclude_plugins_from_export( $exclude_filters ) {
		$lines = array_filter( ExcluderOptions::get_settings( ExcluderOptions::FIELD_PLUGINS ) );

		if ( ! empty( $lines ) ) {
			foreach ( $lines as $line ) {
				$exclude_filters[] = $line;
			}
		}

		return $exclude_filters;
	}


	/**
	 * Exclude themes files.
	 * @param  array $exclude_filters
	 * @return array
	 */
	public function exclude_themes_from_export( $exclude_filters ) {
		$lines = array_filter( ExcluderOptions::get_settings( ExcluderOptions::FIELD_THEMES ) );

		if ( ! empty( $lines ) ) {
			foreach ( $lines as $line ) {
				$exclude_filters[] = $line;
			}
		}

		return $exclude_filters;
	}
}
