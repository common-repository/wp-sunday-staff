<?php
/**
 * @package WP Sunday\Widgets
 * @author  WP Sunday
 * @license GPL-2.0+
 * @link    http://www.wpsunday.com/plugins/wp-sunday-staff/
 */

/**
 * WP Sunday Featured Staff widget class.
 *
 * @since 0.9.0
 *
 * @package WP Sunday\Widgets
 */
class WP_Sunday_Featured_Staff extends WP_Widget {

	/**
	 * Holds widget settings defaults, populated in constructor.
	 *
	 * @var array
	 */
	protected $defaults;

	/**
	 * Constructor. Set the default widget options and create widget.
	 *
	 * @since 0.9.0
	 */
	function __construct() {

		$this->defaults = array(
			'title'                   => '',
			'posts_cat'               => '',
			'posts_num'               => 1,
			'show_image'              => 0,
			'image_size'              => 'large',
			'image_alignment'         => 'aligncenter',
			'show_title'              => 0,
			'show_content'            => 'content-limit',
			'content_limit'           => 120,
			'more_text'               => __( 'Learn&nbsp;More&nbsp;&rsaquo;', 'wp-sunday' ),
		);

		$widget_ops = array(
			'classname'   => 'featured-content featured-staff',
			'description' => __( 'Displays featured staff with thumbnails', 'wp-sunday' ),
		);

		$control_ops = array(
			'id_base' => 'featured-staff',
		);

		parent::__construct( 'featured-staff', __( 'WP Sunday - Staff', 'wp-sunday' ), $widget_ops, $control_ops );

	}

	/**
	 * Echo the widget content.
	 *
	 * @since 0.9.0
	 *
	 * @global WP_Query $wp_query                Query object.
	 * @global array    $_genesis_displayed_ids  Array of displayed post IDs.
	 * @global $integer $more
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget( $args, $instance ) {

		global $wp_query, $_genesis_displayed_ids;

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		echo $args['before_widget'];

		//* Set up the widget title
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $args['after_title'];

		$query_args = array(
			'post_type'           => 'wp_sunday_staff',
			'showposts'           => $instance['posts_num'],
		);
		
		//* Filter by custom taxonomy if selected
		if ( ! empty( $instance['posts_cat'] ) )
			$query_args['tax_query'] = array(
        		array(
          			'taxonomy'  => 'wp_sunday_staff_category',
          			'field'     => 'term_id',
        			'terms'     => $instance['posts_cat'],
    			),
    		);

		$wp_query = new WP_Query( $query_args );

		if ( have_posts() ) : while ( have_posts() ) : the_post();

			$_genesis_displayed_ids[] = get_the_ID();

			genesis_markup( array(
				'html5'   => '<article %s>',
				'xhtml'   => sprintf( '<div class="%s">', implode( ' ', get_post_class() ) ),
				'context' => 'entry',
			) );

			$image = genesis_get_image( array(
				'format'  => 'html',
				'size'    => $instance['image_size'],
				'context' => 'featured-post-widget',
				'attr'    => genesis_parse_attr( 'entry-image-widget', array ( 'alt' => get_the_title() ) ),
			) );

			if ( $instance['show_image'] && $image ) {
				$role = empty( $instance['show_title'] ) ? '' : 'aria-hidden="true"';
				printf( '<a href="%s" class="%s" %s>%s</a>', get_permalink(), esc_attr( $instance['image_alignment'] ), $role, $image );
			}

			if ( $instance['show_title'] )
				echo genesis_html5() ? '<header class="entry-header">' : '';

				if ( ! empty( $instance['show_title'] ) ) {

					$title = get_the_title() ? get_the_title() : __( '(no title)', 'wp-sunday' );

					/**
					 * Filter the featured staff widget title.
					 *
					 * @since  0.9.0
					 *
					 * @param string $title    Featured staff title.
					 * @param array  $instance {
					 *     Widget settings for this instance.
					 *
					 *     @type string $title                   Widget title.
					 *     @type int    $posts_cat               ID of the post category.
					 *     @type int    $posts_num               Number of staff to show.
					 *     @type bool   $show_image              True if featured image should be
					 *                                           shown, false otherwise.
					 *     @type string $image_alignment         Image alignment: alignnone,
					 *                                           alignleft, aligncenter or alignright.
					 *     @type string $image_size              Name of the image size.
					 *     @type bool   $show_title              True if featured staff title should
					 *                                           be shown, false otherwise.
					 *     @type bool   $show_content            True if featured staff content
					 *                                           should be shown, false otherwise.
					 *     @type int    $content_limit           Amount of content to show, in
					 *                                           characters.
					 *     @type int    $more_text               Text to use for More link.
					 * }
					 * @param array  $args     {
					 *     Widget display arguments.
					 *
					 *     @type string $before_widget Markup or content to display before the widget.
					 *     @type string $before_title  Markup or content to display before the widget title.
					 *     @type string $after_title   Markup or content to display after the widget title.
					 *     @type string $after_widget  Markup or content to display after the widget.
					 * }
					 */
					$title = apply_filters( 'genesis_featured_post_title', $title, $instance, $args );
					$heading = genesis_a11y( 'headings' ) ? 'h4' : 'h2';

					if ( genesis_html5() )
						printf( '<%s class="entry-title"><a href="%s">%s</a></%s>', $heading, get_permalink(), $title, $heading );
					else
						printf( '<%s><a href="%s">%s</a></%s>', $heading, get_permalink(), $title, $heading );

				}

			if ( $instance['show_title'] )
				echo genesis_html5() ? '</header>' : '';

			if ( ! empty( $instance['show_content'] ) ) {

				echo genesis_html5() ? '<div class="entry-content">' : '';

				if ( 'excerpt' == $instance['show_content'] ) {
					the_excerpt();
				}
				elseif ( 'content-limit' == $instance['show_content'] ) {
					the_content_limit( (int) $instance['content_limit'], genesis_a11y_more_link( esc_html( $instance['more_text'] ) ) );
				}
				else {

					global $more;

					$orig_more = $more;
					$more = 0;

					the_content( genesis_a11y_more_link( esc_html( $instance['more_text'] ) ) );

					$more = $orig_more;

				}

				echo genesis_html5() ? '</div>' : '';

			}

			genesis_markup( array(
				'html5' => '</article>',
				'xhtml' => '</div>',
			) );

		endwhile; endif;

		//* Restore original query
		wp_reset_query();

		echo $args['after_widget'];

	}

	/**
	 * Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @since 0.9.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update( $new_instance, $old_instance ) {

		$new_instance['title']     = strip_tags( $new_instance['title'] );
		$new_instance['more_text'] = strip_tags( $new_instance['more_text'] );
		return $new_instance;

	}

	/**
	 * Echo the settings update form.
	 *
	 * @since 0.9.0
	 *
	 * @param array $instance Current settings
	 */
	function form( $instance ) {

		//* Merge with defaults
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wp-sunday' ); ?>:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'posts_cat' ); ?>"><?php _e( 'Staff Category', 'wp-sunday' ); ?>:</label>
			<?php
			$categories_args = array(
				'taxonomy'			=> 'wp_sunday_staff_category',
				'name'				=> $this->get_field_name( 'posts_cat' ),
				'selected'			=> $instance['posts_cat'],
				'orderby'			=> 'Name',
				'hierarchical'		=> 1,
				'show_option_all'	=> __( 'All Categories', 'wp-sunday' ),
				'hide_empty'		=> 1,
			);
			wp_dropdown_categories( $categories_args ); ?>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'posts_num' ) ); ?>"><?php _e( 'Number of Staff to Show', 'wp-sunday' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'posts_num' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'posts_num' ) ); ?>" value="<?php echo esc_attr( $instance['posts_num'] ); ?>" size="2" />
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_image' ) ); ?>" value="1" <?php checked( $instance['show_image'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_image' ) ); ?>"><?php _e( 'Show Featured Image', 'wp-sunday' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>"><?php _e( 'Image Size', 'wp-sunday' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_size' ) ); ?>">
				<option value="thumbnail" <?php selected( 'thumbnail', $instance['image_size'] ); ?>>thumbnail (<?php echo esc_html( get_option( 'thumbnail_size_w' ) ); ?>x<?php echo esc_html( get_option( 'thumbnail_size_h' ) ); ?>)</option>
				<option value="large" <?php selected( 'large', $instance['image_size'] ); ?>>large (<?php echo esc_html( get_option( 'large_size_w' ) ); ?>x<?php echo esc_html( get_option( 'large_size_h' ) ); ?>)</option>
				<?php
				$sizes = genesis_get_additional_image_sizes();
				foreach( (array) $sizes as $name => $size )
					#echo '<option value="' . esc_attr( $name ) . '" '. selected( $name, $instance['image_size'], FALSE ) . '>' . esc_html( $name ) . ' ( ' . esc_html( $size['width'] ) . 'x' . esc_html( $size['height'] ) . ' )</option>';
					printf( '<option value="%s" %s>%s (%sx%s)</option>', esc_attr( $name ), selected( $name, $instance['image_size'], 0 ), esc_html( $name ), esc_html( $size['width'] ), esc_html( $size['height'] ) );
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'image_alignment' ) ) ; ?>"><?php _e( 'Image Alignment', 'wp-sunday' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'image_alignment' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'image_alignment' ) ); ?>">
				<option value="alignnone">- <?php _e( 'None', 'wp-sunday' ); ?> -</option>
				<option value="alignleft" <?php selected( 'alignleft', $instance['image_alignment'] ); ?>><?php _e( 'Left', 'wp-sunday' ); ?></option>
				<option value="alignright" <?php selected( 'alignright', $instance['image_alignment'] ); ?>><?php _e( 'Right', 'wp-sunday' ); ?></option>
				<option value="aligncenter" <?php selected( 'aligncenter', $instance['image_alignment'] ); ?>><?php _e( 'Center', 'wp-sunday' ); ?></option>
			</select>
		</p>

		<hr class="div" />

		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>" type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>" value="1" <?php checked( $instance['show_title'] ); ?>/>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"><?php _e( 'Show Staff Title', 'wp-sunday' ); ?></label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>"><?php _e( 'Content Type', 'wp-sunday' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'show_content' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_content' ) ); ?>">
				<option value="content" <?php selected( 'content', $instance['show_content'] ); ?>><?php _e( 'Show Content', 'wp-sunday' ); ?></option>
				<option value="content-limit" <?php selected( 'content-limit', $instance['show_content'] ); ?>><?php _e( 'Show Content Limit', 'wp-sunday' ); ?></option>
				<option value="" <?php selected( '', $instance['show_content'] ); ?>><?php _e( 'No Content', 'wp-sunday' ); ?></option>
			</select>
			<br />
			<label for="<?php echo esc_attr( $this->get_field_id( 'content_limit' ) ); ?>"><?php _e( 'Limit content to', 'wp-sunday' ); ?>
				<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'content_limit' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'content_limit' ) ); ?>" value="<?php echo esc_attr( intval( $instance['content_limit'] ) ); ?>" size="3" />
				<?php _e( 'characters', 'wp-sunday' ); ?>
			</label>
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'more_text' ) ); ?>"><?php _e( 'More Text (if applicable)', 'wp-sunday' ); ?>:</label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'more_text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'more_text' ) ); ?>" value="<?php echo esc_attr( $instance['more_text'] ); ?>" />
		</p>

		<?php

	}

}
