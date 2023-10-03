<?php

/**
 * Plugin Name: Exclude Files & Folders for AIO WP Migration with GDrive addon.
 * Description: This add-on will allow admin users to add exclude paths with-in wp-content, uploads, plugins and themes directores for All-in-One WP Migration plugin.
 * Author: Muhammad Awais
 * Author URI: https://awaiswp.com
 * Version: 1.0.0
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace AwaisWP\Excluder;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'FF_EXCLUDER_CUST_PLUGIN_FILE' ) ) {
	define( 'FF_EXCLUDER_CUST_PLUGIN_FILE', __FILE__ );
}

require_once 'vendor/autoload.php';

Bootstrap::get_instance();
