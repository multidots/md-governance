<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.multidots.com/
 * @since             1.0.0
 * @package           md-governance
 *
 * @wordpress-plugin
 * Plugin Name:       MD Governance
 * Plugin URI:        https://www.multidots.com/
 * Description:       Enhance your WordPress site with this plugin to restrict access to gutenberg blocks and its settings depending on user roles.
 * Version:           1.0.0
 * Author:            Multidots
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       md-governance
 * Domain Path:       /languages
 */

namespace MD_GOVERNANCE;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'MD_GOVERNANCE_VERSION', '1.0.0' );
define( 'MD_GOVERNANCE_URL', plugin_dir_url( __FILE__ ) );
define( 'MD_GOVERNANCE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MD_GOVERNANCE_BASEPATH', plugin_basename( __FILE__ ) );
define( 'MD_GOVERNANCE_SRC_BLOCK_DIR_PATH', untrailingslashit( MD_GOVERNANCE_DIR . 'assets/build/blocks' ) );
define( 'MD_GOVERNANCE_LANGUAGE_DIR_PATH', untrailingslashit( MD_GOVERNANCE_DIR . '/languages' ) );

if ( ! defined( 'MD_GOVERNANCE_PATH' ) ) {
	define( 'MD_GOVERNANCE_PATH', __DIR__ );
}

// Load the autoloader.
require_once plugin_dir_path( __FILE__ ) . '/includes/helpers/autoloader.php';


register_activation_hook( __FILE__, array( \MD_GOVERNANCE\Includes\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( \MD_GOVERNANCE\Includes\Deactivator::class, 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * @return void
 * @since    1.0.0
 */
function run_md_scaffold(): void {
	new \MD_GOVERNANCE\Includes\MD_Governance();
}
run_md_scaffold();
