<?php
/*
Plugin Name: WP Sunday - Staff
Plugin URI:  http://www.wpsunday.com/plugins/wp-sunday-staff/
Description: Displays featured staff in WP Sunday child themes.
Version:     1.0.2
Author:      WP Sunday
Author URI:  http://www.wpsunday.com/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wp-sunday
*/

include 'wp-sunday-staff-post-type.php';
include 'class-wp-sunday-staff.php';

/**
 * Flush the permalinks to make WP Sunday Staff
 * custom post type URLs work.
 *
 * @since 0.9.0
 */
function wp_sunday_staff_activate() {
	wp_sunday_staff_post_type();
	wp_sunday_staff_taxonomies();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wp_sunday_staff_activate' );

/**
 * Register widget.
 *
 * @since 0.9.0
 */
add_action( 'widgets_init', 'wp_sunday_staff_load_widgets' );
function wp_sunday_staff_load_widgets() {
	register_widget( 'WP_Sunday_Featured_Staff' );
}