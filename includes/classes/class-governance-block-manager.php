<?php
/**
 * Filters GB blocks by user role class.
 *
 * @since      1.0.0
 * @package    md-governance
 * @subpackage md-governance/includes
 * @author     Multidots <info@multidots.com>
 */

namespace MD_Governance\Includes;

use MD_Governance\Includes\Traits\Singleton;

/**
 * Governance_Block_Manager class File.
 */
class Governance_Block_Manager {

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
		add_action( 'admin_menu', array( $this, 'md_governance_add_sub_menu' ) );
		add_action( 'admin_post_save_md_governance_blocks', array( $this, 'md_governance_save_blocks' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_gutenberg_hide_panel_script' ) );
		add_filter( 'block_editor_settings_all', array( $this, 'md_governance_update_blocks_list' ), 9999 );
		add_action( 'wp_ajax_save_block_governance', array( $this, 'save_block_governance_ajax_callback' ) );
	}

	/**
	 * Adds a submenu page for managing block governance in the WordPress admin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function md_governance_add_sub_menu() {
		add_submenu_page(
			'md-governance',
			__( 'Block Governance', 'md-governance' ),
			__( 'Block Governance', 'md-governance' ),
			'manage_options',
			'md-block-governance',
			array( $this, 'md_governance_create_admin_page' )
		);
	}

	/**
	 * Enqueues JavaScript to hide certain block panels in the Gutenberg editor based on user roles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_gutenberg_hide_panel_script() {
		wp_enqueue_script(
			'md-governance-hide-panel',
			MD_GOVERNANCE_URL . 'assets/build/hidepanel.js',
			array( 'wp-data', 'wp-plugins', 'wp-edit-post', 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-compose' ),
			$this->version,
			true
		);

		wp_localize_script(
			'md-governance-hide-panel',
			'mdGovernanceSettings',
			array(
				'restrictedBlocks' => $this->md_governance_get_restricted_blocks_for_user(),
				'isBlockEditor'    => get_current_screen()->is_block_editor(),
			)
		);
	}

	/**
	 * Function is used to Create the admin page.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function md_governance_create_admin_page() {
		// Retrieve all blocks.
		$blocks_gvernance_data = Governance_Utils::md_governance_get_users_and_blocks();
		$grouped_blocks        = $blocks_gvernance_data['blocks_by_category'];
		$block_categories      = get_block_categories( null, array() );
		?>
		<div class="wrap mdgv_block_wrap">
			<h2 class="main_title"><?php esc_html_e( 'Block Governance', 'md-governance' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Manage Gutenberg block access with Block Governance. Select the user roles to restrict specific blocks. If no roles are selected, blocks will be available to all users.', 'md-governance' ); ?></p>
			<div id="blockGovernanceNotice"></div>
			<form method="POST" class="mdgv_blocks_form" id="blockGovernanceForm">
				<input type="hidden" name="page" value="md-block-governance">
				<?php wp_nonce_field( 'save_md_governance_blocks', 'md_governance_nonce' ); ?>
	
				<div class="mdgv_all_blocks_wrapper">
					<div class="mdgv_all_blocks_list">
						<p class="mdgv_all_blocks_list_description"><?php esc_html_e( 'Do you want to disable all the available blocks for any specific user role?', 'md-governance' ); ?></p>
						
						<div class="mdgv_all_blocks_list_user_roles_wrapper">
							<ul class="mdgv_all_blocks_list_user_roles">
								<?php
								$selected_roles = get_option( 'md_governance_disable_all_blocks', array() );
								foreach ( Governance_Utils::md_governance_get_users_and_blocks()['roles'] as $role => $role_info ) {
									?>
									<li>
										<div class="switch">
											<input type="checkbox" name="disable-all-blocks[]" class="mdgv_all_blocks_list_user_role_select" value="<?php echo esc_attr( $role ); ?>" id="save-selected-users-block-all-<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $selected_roles, true ) ); ?> />
											<span class="slider"></span>
										</div>
										<label for="save-selected-users-block-all-<?php echo esc_attr( $role ); ?>" class="switch-label"><?php echo esc_html( $role_info['name'] ); ?></label>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
	
					<div class="mdgv_block_table_wrap">
						<div class="mdgv_block_table_header">
							<p class="mdgv_block_table_description"><?php esc_html_e( 'Select User Roles to Disable Specific Block', 'md-governance' ); ?></p>
							<div class="mdgv_block_table_header_actions">
								<input type="search" class="blocks-search-input mdgv_block_search_input" placeholder="Search block...">
								<select id="searchByCategory">
									<option value=""><?php esc_html_e( 'Select catgeory', 'md-governance' ); ?></option>
									<?php
									foreach ( $block_categories as $block_category ) {
										?>
										<option value="<?php echo esc_attr( $block_category['slug'] ); ?>"><?php echo esc_html( $block_category['title'] ); ?></option>
										<?php
									}
									?>
								</select>
								<button type="button" id="blockGovernanceSubmitButton" class="button button-primary"><?php esc_html_e( 'Save Changes', 'md-governance' ); ?></button>
							</div>
						</div>
						<div class="mdgv_block_users_table-main">
							<table class="mdgv_block_users_table">
								<thead>
									<tr>
										<th width="25%"><?php esc_html_e( 'Block', 'md-governance' ); ?></th>
										<?php
										foreach ( Governance_Utils::md_governance_get_users_and_blocks()['roles'] as $user_role => $role_info ) {
											?>
											<th><?php echo esc_html( $role_info['name'] ); ?></th>
											<?php
										}
										?>
									</tr>
								</thead>
								<tbody>
									<?php
									if ( ! empty( $grouped_blocks ) ) {
										foreach ( $grouped_blocks as $category => $category_blocks ) {
											$block_count = count( $category_blocks );
											?>
											<tr class="mdgv_block_table_item category-header" data-block-category="<?php echo esc_attr( $category ); ?>">
												<td colspan="100%" class="mdgv_block_table_title category-name"><?php echo esc_html( ucfirst( $category ) . ' ( ' . $block_count . ' )' ); ?></td>
											</tr>
											<?php
											// Sort blocks alphabatically.
											uasort(
												$category_blocks,
												function ( $a, $b ) {
													return strcasecmp( $a['title'], $b['title'] );
												}
											);
											foreach ( $category_blocks as $block ) {
												?>
												<tr class="mdgv_block_table_item" data-block-title="<?php echo esc_attr( $block['title'] ); ?>" data-block-category="<?php echo esc_attr( $category ); ?>">
													<td class="mdgv_block_table_title"><?php echo esc_html( $block['title'] ) . ' (' . esc_html( $block['name'] ) . ')'; ?></td>
													<?php
													foreach ( Governance_Utils::md_governance_get_users_and_blocks()['roles'] as $role => $role_info ) {
														?>
														<td>
															<label class="switch">
																<input type="checkbox" name="save-selected-users-block-<?php echo esc_attr( $block['name'] ); ?>[]" value="<?php echo esc_attr( $role ); ?>" <?php checked( in_array( $role, $block['users'], true ) ); ?> />
																<span class="slider"></span>
															</label>
														</td>
														<?php
													}
													?>
												</tr>
												<?php
											}
										}
									}
									?>
								</tbody>
							</table>
						</div>
						<div class="pagination-container"></div>
					</div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Handles AJAX requests for saving block governance settings.
	 */
	public function save_block_governance_ajax_callback() {
		$governance_nonce = filter_input( INPUT_POST, 'md_governance_nonce', FILTER_SANITIZE_SPECIAL_CHARS );
		$block_by_categopry = filter_input( INPUT_POST, 'blocksByCategory', FILTER_SANITIZE_SPECIAL_CHARS );
		$response         = array();

		if (
			! isset( $governance_nonce ) ||
			! wp_verify_nonce( $governance_nonce, 'save_md_governance_blocks' ) ||
			! current_user_can('manage_options' )
		) {
			wp_send_json_error('Unauthorized', 403);
		}
		

		// Retrive the form data.
		$blocks_gvernance_data = Governance_Utils::md_governance_get_users_and_blocks( $block_by_categopry );
		$disable_all_blocks    = isset( $_POST['disable-all-blocks'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['disable-all-blocks'] ) ) : array();
		$blocks                = $blocks_gvernance_data['blocks'];

		foreach ( $blocks as $block_name => $block ) {
			$selected_users_block = isset( $_POST[ 'save-selected-users-block-' . $block_name ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'save-selected-users-block-' . $block_name ] ) ) : array();

			if ( ! empty( $selected_users_block ) ) {
				update_option( "md_governance_block_{$block_name}", $selected_users_block );
			} else {
				delete_option( "md_governance_block_{$block_name}" );
			}
		}

		// Save disable all blocks for current user.
		if ( ! empty( $disable_all_blocks ) ) {
			update_option( 'md_governance_disable_all_blocks', $disable_all_blocks );
		} else {
			delete_option( 'md_governance_disable_all_blocks' );
		}

		// Prepare success response.
		$response['success'] = true;
		$response['message'] = __( 'Settings saved successfully.', 'md-governance' );

		wp_send_json( $response );
	}

	/**
	 * This function Retrieves restricted blocks for the current user.
	 *
	 * @return array $restricted_blocks_for_user Restricted list of blocks.
	 */
	public function md_governance_get_restricted_blocks_for_user() {
		$restricted_blocks_for_user = array();

		$blocks = Governance_Utils::md_governance_get_users_and_blocks()['blocks'];

		$current_user    = wp_get_current_user();
		$current_user_id = $current_user->ID;
		$user_roles      = (array) $current_user->roles;

		$disable_all_blocks_for_roles = get_option( 'md_governance_disable_all_blocks', array() );

		// If the current user's role is restricted for all available blocks, return all blocks.
		if ( ! empty( $disable_all_blocks_for_roles ) && array_intersect( $user_roles, $disable_all_blocks_for_roles ) ) {
			$all_block_types = array_keys( $blocks );

			return $all_block_types;
		}

		// Loop through each block and check if the current user has access for selected blocks only.
		foreach ( $blocks as $block_type => $block ) {
			$restricted_users = get_option( 'md_governance_block_' . sanitize_text_field( $block_type ), array() );

			foreach ( $restricted_users as $entry ) {
				if ( ( is_numeric( $entry ) && intval( $entry ) === $current_user_id ) || ( is_string( $entry ) && in_array( $entry, $user_roles, true ) ) ) {
					$restricted_blocks_for_user[] = $block_type;
					break;
				}
			}
		}

		return $restricted_blocks_for_user;
	}

	/**
	 * This function is used to Update the blocks list & Replaces block editor settings to allow/disallow certain blocks based on current user's role.
	 *
	 * @param array $editor_settings Block editor settings.
	 * @return array $editor_settings Modified block editor settings.
	 */
	public function md_governance_update_blocks_list( $editor_settings ) {
		$current_user = wp_get_current_user();
		$user_roles   = (array) $current_user->roles;

		$restricted_blocks = $this->md_governance_get_restricted_blocks_for_user();

		if ( isset( $editor_settings['allowedBlockTypes'] ) && is_array( $editor_settings['allowedBlockTypes'] ) ) {
			$editor_settings['allowedBlockTypes'] = array_diff( $editor_settings['allowedBlockTypes'], $restricted_blocks );
		}

		return $editor_settings;
	}
}
