<?php
/**
 * The activation functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    md-governance
 * @subpackage md-governance/admin
 * @author     Multidots <info@multidots.com>
 */

declare( strict_types = 1 );

namespace MD_GOVERNANCE\Includes;

use MD_GOVERNANCE\Includes\Traits\Singleton;

/**
 * Activator class file.
 */
class Activator {

	use Singleton;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Constructor currently has no properties to initialize.
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @return mixed
	 * @since    1.0.0
	 */
	public static function activate(): bool {
		$version = '7';
		$theme   = wp_get_theme();
		if ( version_compare( $version, get_bloginfo( 'version' ), '>=' ) ) {
			return true;
		} else {
			wp_die(
				esc_html_e( 'Please activate Twenty two theme', 'md-governance' ),
				'Theme dependency check',
				array( 'back_link' => true )
			);
		}
	}
}
