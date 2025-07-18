<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    md-governance
 * @subpackage md-governance/public
 * @author     Multidots <info@multidots.com>
 */

declare( strict_types = 1 );

namespace MD_GOVERNANCE\Includes;

use MD_GOVERNANCE\Includes\Traits\Singleton;

/**
 * Frontend main class.
 */
class Front {


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
		$this->setup_front_hooks();
	}

	/**
	 * All public facing hook will be placed under this function.
	 *
	 * @return void
	 */
	public function setup_front_hooks(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'enqueue_block_assets', array( $this, 'enqueue_editor_assets' ) );
		add_filter( 'should_load_separate_core_block_assets', '__return_true' );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @return void
	 * @since    1.0.0
	 */
	public function enqueue_styles(): void {
		wp_enqueue_style( 'md-governance-front', MD_GOVERNANCE_URL . 'assets/build/main.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @return void
	 * @since    1.0.0
	 */
	public function enqueue_scripts(): void {
		wp_enqueue_script( 'md-governance', MD_GOVERNANCE_URL . 'assets/build/main.js', array( 'jquery' ), $this->version, false );

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
	 * Enqueue editor scripts and styles.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_editor_assets(): void {
		// Change block Priority to head.
		$blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
		foreach ( $blocks as $block ) {
			if ( has_block( $block->name ) ) {
				wp_enqueue_style( $block->style );
			}
		}
	}

}
