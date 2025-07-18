<?php
/**
 * The core plugin class.
 *
 * @since      1.0.0
 * @package    md-governance
 * @subpackage md-governance/includes
 * @author     Multidots <info@multidots.com>
 */

declare( strict_types = 1 );

namespace MD_GOVERNANCE\Includes;

use MD_GOVERNANCE\Includes\Blocks;
use MD_GOVERNANCE\Includes\Governance_Block_Manager;
use MD_GOVERNANCE\Includes\Governance_Block_Settings_Manager;
use MD_GOVERNANCE\Includes\Traits\Singleton;

/**
 * Main class File.
 */
class MD_Governance {

	use Singleton;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      MD_Governance_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'MD_GOVERNANCE_VERSION' ) ) {
			$this->version = MD_GOVERNANCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'md-governance';

		Front::get_instance();
		Admin::get_instance();
		Activator::get_instance();
		Deactivator::get_instance();
		I18::get_instance();
		Governance_Block_Manager::get_instance();
		Governance_Block_Settings_Manager::get_instance();
		Governance_Utils::get_instance();
		Blocks::get_instance();
	}
}
