<?php
/**
 * The admin-specific functionality of the plugin.
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
 * Main class file.
 */
class Admin {

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
		if ( defined( 'MD_GOVERNANCE_VERSION' ) ) {
			$this->version = MD_GOVERNANCE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->setup_admin_hooks();
	}

	/**
	 * Function is used to define admin hooks.
	 *
	 * @return void
	 * @since   1.0.0
	 */
	public function setup_admin_hooks(): void {
		add_action( 'admin_menu', array( $this, 'md_governance_add_plugin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @return void
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style( 'md-governance', MD_GOVERNANCE_URL . 'assets/build/admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @return void
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script( 'md-governance', MD_GOVERNANCE_URL . 'assets/build/admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script(
			'md-governance',
			'mdGositeConfig',
			array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( 'md_governance_loadmore_post_nonce' ),
			)
		);
	}

	/**
	 * Function is used to create plugin page.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function md_governance_add_plugin_page(): void {
		add_menu_page(
			__( 'MD Governance', 'md-governance' ),
			__( 'MD Governance', 'md-governance' ),
			'manage_options',
			'md-governance',
			array( $this, 'md_governance_create_admin_page' ),
			'dashicons-admin-generic',
			2
		);
	}

	/**
	 * Function is used to create admin page.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function md_governance_create_admin_page(): void {
		?>
		<div class="wrap mdgv_main_wrapper">
			<h2 class="main_title"><?php esc_html_e( 'MD Governance', 'md-governance' ); ?></h2>
			<p class="description"><?php esc_html_e( 'This plugin allows you to enable or disable blocks and their settings within the Gutenberg editor. Customize block access and configuration based on user roles.', 'md-governance' ); ?></p>
			<div class="mdgv-feature-box-wrapper">
				<div class="mdgv-feature-box">
					<h3><?php esc_html_e( 'Block Governance', 'md-governance' ); ?></h3>
					<div class="mdgv-feature-description">
						<?php esc_html_e( 'Manage which user roles can access specific blocks, including default and custom blocks, with full control over block visibility and settings access.', 'md-governance' ); ?>
					</div>
					<div class="mdgv-feature-setting">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=md-block-governance' ) ); ?>" class="mdgv-page-link"><?php esc_html_e( 'Block Governance', 'md-governance' ); ?></a>
					</div>
				</div>

				<div class="mdgv-feature-box">
					<h3><?php esc_html_e( 'Block Settings Governance', 'md-governance' ); ?><span class="coming_soon"><?php esc_html_e( 'Coming Soon', 'md-governance' ); ?></span></h3>
					<div class="mdgv-feature-description">
						<?php esc_html_e( 'Control access to block settings (color, typography, etc.) based on user roles, restricting settings modifications to authorized users.', 'md-governance' ); ?>
					</div>
					<div class="mdgv-feature-setting">
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=md-settings-governance' ) ); ?>" class="mdgv-page-link"><?php esc_html_e( 'Block Settings Governance', 'md-governance' ); ?></a>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
