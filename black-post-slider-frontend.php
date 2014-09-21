<?php
/*
 * Frontend part of the Featured Post plugin.
 */


/*
 * timelord_featured_post
 * The function called in the frontend.
 */
function timelord_featured_post( $term = false ) {

	if ( $term ) {
		$args = array(
		'post_type' => 'featured_post',
		'posts_per_page' => 10,
		'order' => 'ASC',
		'orderby' => 'menu_order date',
		'tax_query' => array(
			array(
				'taxonomy' => 'fp-category',
				'field' => 'slug',
				'terms' => $term
			) )
		);
	} else {
		$args = array(
			'post_type' => 'featured_post',
			'posts_per_page' => 10,
			'order' => 'ASC',
			'orderby' => 'menu_order date'
		);
	}

	$the_query = new WP_Query( $args );
	$fp_counter = 0;

	if ( $the_query->have_posts() ) { ?>
		<div class="featured_posts" id='#cycle'>
			<div class="featured_images"> <?php
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					/* Get real featured post */
					$realpost = get_post_meta( get_the_ID(), 'featured-post', true);

					if ( has_post_thumbnail() ) {
						$thumb_id = get_post_thumbnail_id(get_the_ID());
						$photo = wp_get_attachment_image_src( $thumb_id, 'full' );
						$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						if ($photo[0]) { ?>
							<img src="<?php echo $photo[0]; ?>" 
								alt="<?php
									if ($alt) {
										echo $alt;
									} else {
										the_title();
									} ?>"
								id="img_<?php the_ID(); ?>"
							/>
						<?php }
						//the_post_thumbnail();
						$fp_counter++; // number of real posts
					} else if ( has_post_thumbnail( $realpost ) ) {
						$thumb_id = get_post_thumbnail_id( $realpost );
						$photo = wp_get_attachment_image_src( $thumb_id, 'full' );
						$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
						if ($photo[0]) { ?>
							<img src="<?php echo $photo[0]; ?>" 
								alt="<?php
									if ($alt) {
										echo $alt;
									} else {
										the_title();
									} ?>"
								id="img_<?php the_ID(); ?>"
							/>
						<?php }
						//echo get_the_post_thumbnail( $realpost );
						$fp_counter++; // number of real posts
					}
				} ?>
			</div>
			<div class="featured_titles count_<?php echo floor( 100 / $fp_counter ); ?>"><?php
				$active = 'active';
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					/* Get real featured post */
					$realpost = get_post_meta( get_the_ID(), 'featured-post', true);

					/* No thumbnail, no title */
					if ( !has_post_thumbnail() && !has_post_thumbnail( $realpost ) ) {
						continue;
					} ?>
					<div class="featured_title <?php echo $active; ?>" id="title_img_<?php the_ID(); ?>">
						<div class="featured_title_inside">
							<div class="featured_title_more_inside">
								<h4><?php 
								the_title();
								if ( is_numeric( $realpost ) ) {
									$p = get_post( $realpost );
									if ( $p ) { ?>
										<span>
											<a href="<?php echo $p->guid; ?>" title="<?php _e('Visit the post: ', 'feat_posts'); the_title(); ?>">	
												&nbsp;&raquo;
											</a>
										</span>
										<?php
									}
								} ?>
								</h4>
							</div>
						</div>
					</div><?php
					if ( $active == 'active' ) { $active = ''; }
				} ?>
			</div>
			<div class="clear"></div>
		</div><?php
		// load in the footer, only when active
		add_action('wp_footer', 'feat_post_custom_css');
		add_action('wp_footer', 'feat_post_frontend_js_libs');
		add_action('wp_footer', 'feat_post_frontend_js_code');
	} else {
		// no posts found
	}
	/* Restore original Post Data */
	wp_reset_postdata();
}


/* Load CSS and JavaScript */
function feat_post_frontend_css() {
	wp_enqueue_style('feat_post_frontend_css', plugins_url('css/frontend.css', __FILE__), 'screen');
}
add_action('wp_enqueue_scripts', 'feat_post_frontend_css');


function feat_post_jquery() {
	// load always
	wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'feat_post_jquery');

	
function feat_post_frontend_js_libs() {
	wp_enqueue_script('feat_post_cycle2_scripts', plugins_url('/js/jquery.cycle2.min.js', __FILE__), true);
	wp_enqueue_script('feat_post_frontend_scripts', plugins_url('/js/frontend.js', __FILE__), true);
}


/*
 * Load JavaScript function to start the slider
 * Options are used to configure it 
 */
function feat_post_frontend_js_code() {
	$effect = get_option('feat_post_effect', 'fade');
	$effects = array( 'fade', 'fadeout', 'none', 'scrollHorz' );
	if ( !in_array( $effect, $effects )) {
		$effect = 'fade';
	} 
	$speed = get_option('feat_post_speed', '600');
	$timeout = get_option('feat_post_timeout', '8000');
	?>
	<script>
	jQuery( document ).ready(function() {
		jQuery('.featured_images').cycle({
			speed: <?php echo $speed; ?>,
			timeout: <?php echo $timeout; ?>,
			fx: '<?php echo $effect; ?>',
		});
	});

	</script>
	<?php
}


/*
 * Load optional CSS
 */
function feat_post_custom_css() {
	?>
	<style>
	<?php if ( get_option('feat_post_height') ) { ?>
		div.featured_posts {
			height: <?php echo get_option('feat_post_height'); ?>;
		}
	<?php } ?>
	<?php if ( get_option('feat_post_border_color') ) { ?>
		div.featured_titles { 
			background-color: <?php echo get_option('feat_post_border_color'); ?>;
		}
		div.featured_title_more_inside {
			border-color: <?php echo get_option('feat_post_border_color'); ?>;
		}
	<?php } ?>
	<?php if ( get_option('feat_post_gradient_top') && get_option('feat_post_gradient_bottom') ) { ?>
		div.featured_title_inside {
			background-color: <?php echo get_option('feat_post_gradient_top'); ?>;
			background-image: -moz-linear-gradient(top, <?php echo get_option('feat_post_gradient_top'); ?>, <?php echo get_option('feat_post_gradient_bottom'); ?>);
			background-image: -ms-linear-gradient(top, <?php echo get_option('feat_post_gradient_top'); ?>, <?php echo get_option('feat_post_gradient_bottom'); ?>);
			background-image: -webkit-linear-gradient(top, <?php echo get_option('feat_post_gradient_top'); ?>, <?php echo get_option('feat_post_gradient_bottom'); ?>);
			background-image: -o-linear-gradient(top, <?php echo get_option('feat_post_gradient_top'); ?>, <?php echo get_option('feat_post_gradient_bottom'); ?>);
			background-image: linear-gradient(top, <?php echo get_option('feat_post_gradient_top'); ?>, <?php echo get_option('feat_post_gradient_bottom'); ?>);
		}
	<?php } ?>
	<?php if ( get_option('feat_post_text_color') ) { ?>
		div.featured_title h4 {
			color: <?php echo get_option('feat_post_text_color'); ?>;
		}
		div.featured_title h4 a {
			color: <?php echo get_option('feat_post_text_color'); ?>;
		}
	<?php } ?>
	</style><?php
}

?>