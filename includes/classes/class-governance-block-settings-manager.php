<?php
/**
 * Filters GB block's settings by user role class.
 *
 * @since      1.0.0
 * @package    md-governance
 * @subpackage md-governance/includes
 * @author     Multidots <info@multidots.com>
 */

namespace MD_Governance\Includes;

use MD_Governance\Includes\Traits\Singleton;
use WP_Block_Type_Registry;
use WP_Block_Editor_Context;

/**
 * Governance_Block_Settings_Manager class File.
 */
class Governance_Block_Settings_Manager {


	use Singleton;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      md_Governance    $loader    Maintains and registers all hooks for the plugin.
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
		$this->setup_hooks();
	}

	/**
	 * Register action/filter hooks.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_hooks() {
		add_action( 'admin_menu', array( $this, 'md_governance_add_sub_plugin_page' ) );
	}

	/**
	 * Adds a new sub menu page to the WordPress admin dashboard for the MD Governance plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function md_governance_add_sub_plugin_page() {
		add_submenu_page(
			'md-governance',
			__( 'Settings Governance', 'md-governance' ),
			__( 'Settings Governance', 'md-governance' ),
			'manage_options',
			'md-settings-governance',
			array( $this, 'md_governance_create_admin_page' )
		);
	}

	/**
	 * Creates the admin page and its HTML.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function md_governance_create_admin_page() {
		?>
		<div class="wrap mdgv_block_wrap">
			<h2 class="main_title"><?php esc_html_e( 'Block Settings Governance', 'md-governance' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Select the user roles for which you want to disable the settings of specific blocks. If no roles are selected for a block, it will be available to all users.', 'md-governance' ); ?></p>

			<div class="mdgv_all_blocks_wrapper">
				<span class="mdgv_coming_soon_page">
					<?php echo wp_kses_post( 'This feature is currently <b class="mdgv_bold">under development &nbsp;</b> &amp; will be available soon in a short while. <strong class="mdgv_stay_tuned">Stay tuned!</strong>' ); ?>
				</span>
			</div>
		</div>
		<?php
	}
}
