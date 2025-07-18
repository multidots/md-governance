<?php
/**
 * Utility functions for the Governance plugin.
 *
 * This class contains helper methods and common utility functions
 * that are used throughout the Governance plugin to simplify
 * and centralize commonly used logic.
 *
 * @since      1.0.0
 * @package    md-governance
 * @subpackage md-governance/includes
 * @author     Multidots <info@multidots.com>
 */

namespace MD_Governance\Includes;

use MD_Governance\Includes\Traits\Singleton;
use WP_Block_Type_Registry;

/**
 * Governance_Block_Manager class File.
 */
class Governance_Utils {

	use Singleton;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		$this->setup_utils_hooks();
	}

	/**
	 * Register action/filter hooks.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function setup_utils_hooks() {
		// Governance utils hooks here.
	}

	/**
	 * Function is used to Retrieve all users with their roles and associated blocks.
	 *
	 * This function fetches all users, their roles, and the block types registered in WordPress.
	 *
	 * @return array An associative array containing two elements:
	 *               - 'users': An array of users with their IDs, usernames, and roles.
	 *               - 'blocks': An array of block types with their attributes and associated users.
	 */
	public static function md_governance_get_users_and_blocks( $block_by_category = '' ) {
		$users              = get_users();
		$users_with_roles   = array();
		$blocks             = array();
		$blocks_by_category = array();

		// Process each user to extract ID, username, and role(s).
		foreach ( $users as $user ) {
			$users_with_roles[ $user->ID ] = array(
				'ID'       => $user->ID,
				'username' => $user->user_login,
				'role'     => ( count( $user->roles ) > 1 ? 'Multiple Roles' : $user->roles[0] ),
			);
		}

		// Get all available roles in WordPress.
		$wp_roles  = wp_roles()->roles;
		$all_roles = array();

		// Process each role to extract role name and slug.
		foreach ( $wp_roles as $role_slug => $role_data ) {
			$all_roles[ $role_slug ] = array(
				'slug' => $role_slug,
				'name' => $role_data['name'],
			);
		}

		// Get all registered block types.
		$block_registry = WP_Block_Type_Registry::get_instance();
		foreach ( $block_registry->get_all_registered() as $block_type ) {
			$category = $block_type->category ?: 'Uncategorized';

			// Collect block type attributes and associated users.
			$block_data = array(
				'name'        => $block_type->name,
				'title'       => $block_type->title,
				'description' => $block_type->description,
				'category'    => $category,
				'icon'        => $block_type->icon,
				'keywords'    => $block_type->keywords,
				'supports'    => $block_type->supports,
				'users'       => get_option( "md_governance_block_{$block_type->name}", array() ),
			);
	
			$blocks_by_category[ $category ][] = $block_data;
	
			// Apply category filter if provided
			if ( empty( $block_by_category ) || $category === $block_by_category ) {
				$blocks[ $block_type->name ] = $block_data;
			}
		}
	
		return array(
			'users'              => $users_with_roles,
			'blocks'             => $blocks,
			'roles'              => $all_roles,
			'blocks_by_category' => $blocks_by_category,
		);
	}

	/**
	* Governance pagination function.
	*
	* @param int $page max_num_pages.
	* @param int $current current page.
	*/
	public static function md_governance_pagination( $page, $current = 1, $query_args = array() ) {
		// Bail if there is only one page.
		if ( $page <= 1 ) {
			return;
		}

		// Ensure $query_args is an array.
		$query_args = is_array( $query_args ) ? $query_args : array();

		// Merge query arguments with pagination.
		$base_url = add_query_arg( array_merge( array( 'paged' => '%#%' ), $query_args ), remove_query_arg( 'paged' ) );

		$allowed_tags = array(
			'span' => array(
				'class' => array(),
			),
			'a'    => array(
				'class' => array(),
				'href'  => array(),
			),
		);

		$args = array(
			'base'      => $base_url,
			'format'    => '?paged=%#%',
			'current'   => $current,
			'total'     => $page,
			'prev_text' => sprintf( '%1$s', __( '« Previous', 'md-governance' ) ),
			'next_text' => sprintf( '%1$s', __( 'Next »', 'md-governance' ) ),
			'mid_size'  => 2,
		);

		printf( '<div class="mdgv_pagination">%s</div>', wp_kses( paginate_links( $args ), $allowed_tags ) );
	}
}
