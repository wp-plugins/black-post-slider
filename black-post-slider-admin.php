<?php
/*
 * Admin part of the Featured Post plugin.
*/


function feat_post_add_post_type() {
	$labels = array(
		'name'			=> __('Featured Posts', 'feat_posts'),
		'singular_name'	=> __('Featured Posts', 'feat_posts'),
		'add_new'		=> __('New Featured Post', 'feat_posts'),
		'add_new_item'	=> __('New Featured Post', 'feat_posts'),
		'edit_item'		=> __('Edit Featured Post', 'feat_posts'),
		'new_item'		=> __('New Featured Post', 'feat_posts'),
		'view_item'		=> __('View Featured Post', 'feat_posts'),
		'search_items'	=> __('Search Featured Post', 'feat_posts'),
		'not_found'		=> __('No Featured Post found', 'feat_posts'),
		'not_found_in_trash' => __('No Featured Posts found in the thrash', 'feat_posts'),
		'parent_item_colon' => '',
		'menu_name'		=> __('Featured Posts', 'feat_posts')
	);
	register_post_type('Featured_Post',array(
		'public'		=> true,
		'show_in_menu'	=> true,
		'show_ui'		=> true,
		'labels'		=> $labels,
		'hierarchical'	=> false,
		'supports'		=> array('title','page-attributes','thumbnail','custom-fields'),
		'capability_type' => 'post',
		'taxonomies'	=> array('fp-category'),
		'exclude_from_search' => true,
		'rewrite'		=> false,
		'rewrite'		=> array( 'slug' => 'featured_post', 'with_front' => false ),
		)
	);

	$labels = array(
		'name'                          => __('Category', 'feat_posts'),
		'singular_name'                 => __('Category', 'feat_posts'),
		'search_items'                  => __('Search Category', 'feat_posts'),
		'popular_items'                 => __('Popular Categories', 'feat_posts'),
		'all_items'                     => __('All Categories', 'feat_posts'),
		'parent_item'                   => __('Parent Category', 'feat_posts'),
		'edit_item'                     => __('Edit Category', 'feat_posts'),
		'update_item'                   => __('Update Category', 'feat_posts'),
		'add_new_item'                  => __('Add Category', 'feat_posts'),
		'new_item_name'                 => __('New Category', 'feat_posts'),
		'separate_items_with_commas'    => __('Separate Categories with commas', 'feat_posts'),
		'add_or_remove_items'           => __('Add or remove Categories', 'feat_posts'),
		'choose_from_most_used'         => __('Choose from the most used Categories', 'feat_posts'),
		);

	$args = array(
		'label'                         => __('Category', 'feat_posts'),
		'labels'                        => $labels,
		'public'                        => true,
		'hierarchical'                  => true,
		'show_ui'                       => true,
		'show_in_nav_menus'             => false,
		'args'                          => array( 'orderby' => 'term_order' ),
		'rewrite'						=> false,
		'query_var'                     => true
	);
	register_taxonomy( 'fp-category', 'featured_post', $args );

}
add_filter( 'init', 'feat_post_add_post_type' );


/*
 * Filter the request to just give posts for the given taxonomy, if applicable.
 */
function feat_post_taxonomy_dropdown() {
	global $typenow;

	if ( $typenow == 'featured_post') {
		$filters = get_object_taxonomies( $typenow );

		foreach ( $filters as $tax_slug ) {
			$tax_obj = get_taxonomy( $tax_slug );
			if ( isset($_GET[$tax_slug]) ) {
				$selected = $_GET[$tax_slug];
			} else {
				$selected = "";
			}
			wp_dropdown_categories( array(
				'show_option_all' => __('Show all in '.$tax_obj->label ),
				'taxonomy' 	  => $tax_slug,
				'name' 		  => $tax_obj->name,
				'orderby' 	  => 'name',
				'selected' 	  => $selected,
				'hierarchical' 	  => $tax_obj->hierarchical,
				'show_count' 	  => false,
				'hide_empty' 	  => true
			) );
		}
	}
}
add_action( 'restrict_manage_posts', 'feat_post_taxonomy_dropdown' );

function feat_post_taxonomy_filter( $query ) {
	global $pagenow, $typenow;

	if ( 'edit.php' == $pagenow ) {
		$filters = get_object_taxonomies( $typenow );
		foreach ( $filters as $tax_slug ) {
			$var = &$query->query_vars[$tax_slug];
			if ( isset( $var ) ) {
				$term = get_term_by( 'id', $var, $tax_slug );
				if ( isset($term->slug) ) {
					$var = $term->slug;
				}
			}
		}
	}
}
add_filter( 'parse_query', 'feat_post_taxonomy_filter' );


function feat_post_menu() {
	add_submenu_page('edit.php?post_type=featured_post', __('Settings', 'feat_posts'), __('Settings', 'feat_posts'), 'manage_options', 'featured-post-settings', 'admin_featured_post');
	add_meta_box('featured-post-metabox', __('Selected Featured Post', 'feat_posts'), 'feat_post_displayMetaBox', 'Featured_Post', 'normal', 'high');
}
add_action('admin_menu', 'feat_post_menu');


function feat_post_displayMetaBox() {
	global $post;
	$post_id = $post->ID; ?>

	<div id="featured-post">
	<script>var featured_post = 0;</script><?php // count onto max 1 featured_posts

	// Get selected post if existing
	$featured = get_post_meta($post_id, 'featured-post', true); // string

	if ( $featured ) { // display the already selected post
		$p = get_post($featured);
		if ( $p ) { ?>
			<div class="featured-post" id="featured-post-<?php echo $featured; ?>">
				<input type="hidden" name="featured-post" value="<?php echo $featured; ?>">
				<span class="related-post-title"><?php echo $p->post_title . " (" . $featured . ")"; ?></span>
				<a href="#"><?php _e('Delete', 'feat_posts' ); ?></a>
			</div>
			<script>var featured_post = 1;</script><?php
		} else { ?>
			<script>var featured_post = 0;</script><?php
		}
	} ?>
	</div><?php

	echo '
		<p>
			<select class="featured-post-select" name="featured-post-select">
				<option value="0">' . __('Select', 'feat_posts' ) . '</option>';

	$query = array(
		'nopaging' => true,
		'post__not_in' => array($post_id),
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'post_type' => 'post',
		'orderby' => 'title',
		'order' => 'ASC'
	);

	$p = new WP_Query($query);

	$count = count($p->posts);
	$counter = 1;
	foreach ($p->posts as $thePost) {
		if ( is_int( $counter / 50 ) ) {
			echo '
				</select>
			</p>
			<p>
				<select class="featured-post-select" name="featured-post-select">
					<option value="0">' . __('Select', 'feat_posts' ) . '</option>';
		} ?>
		<option value="<?php echo $thePost->ID; ?>">
			<?php echo $thePost->post_title . " (" . $thePost->ID . ")"; ?>
		</option>
		<?php
		$counter++;
	}

	wp_reset_query();
	wp_reset_postdata();

	echo '
			</select>
		</p>
		<p>' .
			__('Select the featured post from the list.', 'feat_posts' )
		. '</p>';
}


/*
 * Function feat_post_save
 * Function to save the custom field when saving the post
 */
function feat_post_save($id) {
	global $pagenow;

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	if ( isset($_POST['featured-post']) ) {
		update_post_meta($id, 'featured-post', $_POST['featured-post']);
	}
	/* Only delete on post.php page, not on Quick Edit. */
	if ( empty($_POST['featured-post']) ) {
		if ( $pagenow == 'post.php' ) {
			delete_post_meta($id, 'featured-post');
		}
	}
}
add_action('save_post', 'feat_post_save');


/* Load CSS and JavaScript */
function feat_post_admin_css() {
	wp_enqueue_style('feat_posts_css', plugins_url('css/admin.css', __FILE__), 'screen');
}
add_action('admin_print_styles', 'feat_post_admin_css');

function feat_post_js_admin_libs() {
	if ( isset($_GET['page']) ) {
		$pos_page = $_GET['page'];
		$pos_args = 'feat_posts';
		$pos = strpos($pos_page,$pos_args);
		if ( $pos === false ) {} else {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-core');
			//wp_enqueue_script('jquery-ui-sortable');
		}
	}
	wp_enqueue_script('feat_post_scripts', plugins_url('/js/admin.js', __FILE__), false);
}
add_action('admin_print_scripts', 'feat_post_js_admin_libs');


/*
 * admin_featured_post
 * Function to fill the settings page in admin.
 */
function admin_featured_post() { ?>
	<div class="wrap">
		<h2><?php _e('Featured Post Slider Settings', 'feat_posts'); ?></h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'feat_post_options' );
			do_settings_sections( 'feat_post_options' ); ?>
	
			<table class="form-table">

				<tr valign="top">
					<th scope="row" colspan="2"><h3><?php _e('Slider Effect Options', 'feat_posts'); ?></h3></th>
				</tr>

				<tr valign="top" class="feat_post_effect">
					<th scope="row"><?php _e('Slide Effect:', 'feat_posts'); ?></th>
					<td>
						<?php
						$effect = get_option('feat_post_effect', 'fade');
						$effects = array( 'fade', 'fadeout', 'none', 'scrollHorz' );
						if ( !in_array( $effect, $effects )) {
							$effect = 'fade';
						}
						?>
					<select name="feat_post_effect" class="feat_post_effect">
						<option value="fade" <?php selected( $effect, 'fade', true ); ?>>fade</option>
						<option value="fadeout" <?php selected( $effect, 'fadeout', true ); ?>>fadeout</option>
						<option value="none" <?php selected( $effect, 'none', true ); ?>>none</option>
						<option value="scrollHorz" <?php selected( $effect, 'scrollHorz', true ); ?>>scrollHorz</option>
					</select> 
					</td>
				</tr>

				<tr valign="top" class="feat_post_speed">
					<th scope="row"><?php _e('Speed:', 'feat_posts'); ?></th>
					<td>
					<input type="text" name="feat_post_speed" value="<?php echo get_option('feat_post_speed', '600'); ?>" />
					</td>
				</tr>
				
				<tr valign="top" class="feat_post_timeout">
					<th scope="row"><?php _e('Timeout:', 'feat_posts'); ?></th>
					<td>
					<input type="text" name="feat_post_timeout" value="<?php echo get_option('feat_post_timeout', '8000'); ?>" />
					</td>
				</tr>

				<tr valign="top">
					<th scope="row" colspan="2"><h3><?php _e('CSS Options', 'feat_posts'); ?></h3></th>
				</tr>

				<tr valign="top" class="feat_post_border_color">
					<th scope="row"><?php _e('Border color:', 'feat_posts'); ?></th>
					<td>
					<input type="text" name="feat_post_border_color" value="<?php echo get_option('feat_post_border_color', '#666'); ?>" />
					</td>
				</tr>

				<tr valign="top" class="feat_post_gradient_top">
					<th scope="row"><?php _e('Gradient top color:', 'feat_posts'); ?></th>
					<td>
					<input type="text" name="feat_post_gradient_top" value="<?php echo get_option('feat_post_gradient_top', '#333'); ?>" />
					</td>
				</tr>

				<tr valign="top" class="feat_post_gradient_bottom">
					<th scope="row"><?php _e('Gradient bottom color:', 'feat_posts'); ?></th>
					<td>
					<input type="text" name="feat_post_gradient_bottom" value="<?php echo get_option('feat_post_gradient_bottom', '#111'); ?>" />
					</td>
				</tr>

				<tr valign="top" class="feat_post_text_color">
					<th scope="row"><?php _e('Text color:', 'feat_posts'); ?></th>
					<td>
					<input type="text" name="feat_post_text_color" value="<?php echo get_option('feat_post_text_color', '#eee'); ?>" />
					</td>
				</tr>

			</table>
			
			<p class="submit">
				<input type="button" name="submit" id="submit" class="button button-primary reset" value="<?php _e('Set to Defaults', 'feat_posts'); ?>"  />
				<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save', 'feat_posts'); ?>" /> 
			</p>
			<script>
				/* Reset form to defaults */
				jQuery('.reset').click(function( event ) {
					jQuery('.form-table input').each(function( index ) {
						jQuery( '.feat_post_effect' ).val('fade');
						jQuery( '.feat_post_speed input' ).val('600');
						jQuery( '.feat_post_timeout input' ).val('8000');
						jQuery( '.feat_post_border_color input' ).val('#666');
						jQuery( '.feat_post_gradient_top input' ).val('#333');
						jQuery( '.feat_post_gradient_bottom input' ).val('#111');
						jQuery( '.feat_post_text_color input' ).val('#eee');
						return false;
					});
				});
			</script>
		</form> 
	</div>
	<?php
}


/*
 * Register settings
 */
function feat_post_register_settings() {
	register_setting( 'feat_post_options', 'feat_post_effect' );
	register_setting( 'feat_post_options', 'feat_post_speed', 'intval' );
	register_setting( 'feat_post_options', 'feat_post_timeout', 'intval' );
	register_setting( 'feat_post_options', 'feat_post_border_color' );
	register_setting( 'feat_post_options', 'feat_post_gradient_top' );
	register_setting( 'feat_post_options', 'feat_post_gradient_bottom' );
	register_setting( 'feat_post_options', 'feat_post_text_color' );
}
add_action( 'admin_init', 'feat_post_register_settings' );


/*
 * feat_post_links
 * Add Settings link to the main plugin page
 *
 */

function feat_post_links( $links, $file ) {
        if ( $file == plugin_basename( dirname(__FILE__).'/featured-post-slider.php' ) ) {
                $links[] = '<a href="' . admin_url( 'edit.php?post_type=featured_post&page=featured-post-settings' ) . '">'.__( 'Settings' ).'</a>';
        }
        return $links;
}
add_filter( 'plugin_action_links', 'feat_post_links', 10, 2 );


/*
 * feat_post_init
 * Function called at initialisation.
 * - Loads language files for frontend and backend
 */
function feat_post_init() {
 	load_plugin_textdomain('feat_posts', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/');
}
add_action('plugins_loaded', 'feat_post_init');


/*
 * feat_post_activate
 * Function called at activation time.
 */
function feat_post_activate() {

}
register_activation_hook(__FILE__, 'feat_post_activate');


?>