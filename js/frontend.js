
/* JavaScript for the frontend of the Featured Post Slider plugin */


/*
 * Resize the height of the slider according to the height of the first image at resizing of the window.
 * Also do the same on document.ready so we have an initial height.
 */

jQuery( window ).resize(function() {
	var height = jQuery( '.featured_images img:first-child' ).height();
	height = (height - 1); // avoid rounding error
	jQuery('.featured_posts').css( 'height', height + "px" );
});
jQuery( document ).ready(function() {
	var height = jQuery( '.featured_images img:first-child' ).height();
	height = (height - 1); // avoid rounding error
	jQuery('.featured_posts').css( 'height', height + "px" );
});


/*
 * Event when the Cycle2 slide moves.
 * Here it will make the corresponding title active. 
 */
jQuery(document).ready(function($) {
	jQuery( '.featured_images' ).on( 'cycle-before', function(e, optionHash, outgoingSlideEl, incomingSlideEl, forwardFlag ) {
    	var id = incomingSlideEl.id;
    	// Remove old styling
    	jQuery( "div.featured_title.active" ).animate({
			opacity: 0.6,
		}, 600 );
    	jQuery( 'div.featured_title:first-child' ).removeClass( 'active' );
    	jQuery( 'div.featured_title' ).removeClass( 'active' );
    	
    	// Add new styling
    	jQuery( '#title_' + id ).addClass( 'active' );
    	jQuery( "div.featured_title.active" ).animate({
			opacity: 1,
		}, 600 );
	});
});

