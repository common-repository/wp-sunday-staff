<?php
/**
 * Create WP Sunday Staff custom post type.
 *
 * @since 0.9.0
 */
add_action( 'init', 'wp_sunday_staff_post_type', 0 );
function wp_sunday_staff_post_type() {

	$labels = array(
		'name'                => __( 'Staff', 'wp-sunday' ),
		'singular_name'       => __( 'Staff Member', 'wp-sunday' ),
		'menu_name'           => __( 'Staff', 'wp-sunday' ),
		'parent_item_colon'   => __( 'Staff:', 'wp-sunday' ),
	    'all_items'           => __( 'All Staff', 'wp-sunday' ),
    	'view_item'           => __( 'View Staff Member', 'wp-sunday' ),
    	'add_new_item'        => __( 'Add New Staff Member', 'wp-sunday' ),
    	'add_new'             => __( 'Add New', 'wp-sunday' ),
    	'edit_item'           => __( 'Edit Staff Member', 'wp-sunday' ),
    	'update_item'         => __( 'Update Staff Member', 'wp-sunday' ),
    	'search_items'        => __( 'Search Staff', 'wp-sunday' ),
    	'not_found'           => __( 'Not found', 'wp-sunday' ),
    	'not_found_in_trash'  => __( 'Not found in Trash', 'wp-sunday' ),
	);

	$args = array(
		'label'               => __( 'staff', 'wp-sunday' ),
		'description'         => __( 'Staff Description', 'wp-sunday' ),
    	'labels'              => $labels,
    	'hierarchical'        => false,
    	'public'              => true,
    	'show_ui'             => true,
    	'show_in_menu'        => true,
    	'show_in_nav_menus'   => false,
    	'show_in_admin_bar'   => true,
    	'menu_position'       => 22,
    	'menu_icon'           => 'dashicons-id-alt',
    	'can_export'          => true,
    	'has_archive'         => true,
    	'exclude_from_search' => false,
    	'publicly_queryable'  => true,
    	'capability_type'     => 'post',
    	'rewrite'             => array( 'slug' => 'staff-member' ),
    	'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'genesis-seo', 'genesis-scripts' ),
	);

	register_post_type( 'wp_sunday_staff', $args );
}

/**
 * Add Staff Categories taxonomy to the WP Sunday Staff post type.
 *
 * @since 0.9.0
 */
add_action( 'init', 'wp_sunday_staff_taxonomies', 0 );
function wp_sunday_staff_taxonomies() {

	$labels = array(
		'name'                => __( 'Staff Categories' ),
		'singular_name'       => __( 'Staff Category' ),
    	'search_items'        => __( 'Search Staff Categories' ),
    	'all_items'           => __( 'All Staff Categories' ),
    	'parent_item'         => __( 'Parent Staff Category' ),
    	'parent_item_colon'   => __( 'Parent Staff Category:' ),
    	'edit_item'           => __( 'Edit Staff Category' ), 
    	'update_item'         => __( 'Update Staff Category' ),
    	'add_new_item'        => __( 'Add New Staff Category' ),
    	'new_item_name'       => __( 'New Staff Category' ),
    	'menu_name'           => __( 'Staff Categories' ),
	);

	$args = array(
		'labels'              => $labels,
		'hierarchical'        => true,
    	'show_admin_column'   => true,
    	'rewrite'             => array( 'slug' => 'staff-category' ),
	);

	register_taxonomy( 'wp_sunday_staff_category', array( 'wp_sunday_staff' ), $args );

}

/**
 * Create custom meta box for WP Sunday Staff post type.
 *
 * @since 0.9.0
 */
add_action( 'add_meta_boxes', 'wp_sunday_staff_meta_boxes' );
function wp_sunday_staff_meta_boxes() {

	add_meta_box(
		'wp_sunday_staff_box',
		__( 'WP Sunday Staff Settings', 'wp-sunday' ),
		'wp_sunday_staff_box',
		'wp_sunday_staff',
		'normal',
		'high' );

}

/**
 * Callback for WP Sunday Staff meta box.
 *
 * @since 0.9.0
 *
 * @uses genesis_get_custom_field() Get custom field value.
 */
function wp_sunday_staff_box() {

	wp_nonce_field( plugin_basename( __FILE__ ), 'wp_sunday_staff_content_box_nonce' );
	?>

	<table class="form-table">
	<tbody>

		<tr valign="top">
			<th scope="row"><label for="_wp_sunday_staff_position"><?php _e( 'Staff Position', 'wp-sunday' ); ?></label></th>
			<td>
				<p><input class="large-text" type="text" name="_wp_sunday_staff_position" id="_wp_sunday_staff_position" value="<?php echo esc_attr( genesis_get_custom_field( '_wp_sunday_staff_position' ) ); ?>" /></p>
				<p><span class="description"><?php _e( 'Enter the position title of this staff member. Example: Teaching Pastor', 'wp-sunday' ); ?></span></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="_wp_sunday_staff_email"><?php _e( 'Staff Email Address', 'wp-sunday' ); ?></label></th>
			<td>
				<p><input class="large-text" type="text" name="_wp_sunday_staff_email" id="_wp_sunday_staff_email" value="<?php echo esc_attr( genesis_get_custom_field( '_wp_sunday_staff_email' ) ); ?>" /></p>
				<p><span class="description"><?php _e( 'Enter the email address of this staff member. Example: email.address@yourchurch.com', 'wp-sunday' ); ?></span></p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="_wp_sunday_staff_phone"><?php _e( 'Staff Phone Number', 'wp-sunday' ); ?></label></th>
			<td>
				<p><input class="large-text" type="text" name="_wp_sunday_staff_phone" id="_wp_sunday_staff_phone" value="<?php echo esc_attr( genesis_get_custom_field( '_wp_sunday_staff_phone' ) ); ?>" /></p>
				<p><span class="description"><?php _e( 'Enter the phone number of this staff member. Example: (206) 555-1234', 'wp-sunday' ); ?></span></p>
			</td>
		</tr>

	</tbody>
	</table>
	<?php

}

/**
 * Save custom meta box content for WP Sunday Staff post type.
 *
 * @since 0.9.0
 */
add_action( 'save_post', 'wp_sunday_staff_content_box_save' );
function wp_sunday_staff_content_box_save( $post_id ) {

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if (
		!isset( $_POST['wp_sunday_staff_content_box_nonce'] )
		|| !wp_verify_nonce( $_POST['wp_sunday_staff_content_box_nonce'], plugin_basename( __FILE__ ) ) )
	return;

	if ( 'page' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( !current_user_can( 'edit_post', $post_id ) )
		return;
	}

	$staff_position = sanitize_text_field( $_POST['_wp_sunday_staff_position'] );
	$staff_email = sanitize_text_field( $_POST['_wp_sunday_staff_email'] );
	$staff_phone = sanitize_text_field( $_POST['_wp_sunday_staff_phone'] );

	update_post_meta( $post_id, '_wp_sunday_staff_position', $staff_position );
	update_post_meta( $post_id, '_wp_sunday_staff_email', $staff_email );
	update_post_meta( $post_id, '_wp_sunday_staff_phone', $staff_phone );

}