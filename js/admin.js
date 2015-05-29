

/*
 * Add the selected post as a form.
 */

jQuery(document).ready(function($) {

	$('.featured-post-select').change(function() {
		var select = $(this),
				container = $('#featured-post'),
				id = select.val(),
				title = this.options[this.options.selectedIndex].text;

		if ( $('#featured-post-' + id).length == 0 && featured_post == 0 ) {
			container.prepend('<div class="featured-post" id="featured-post-' +
								id +
								'"><input type="hidden" name="featured-post" value="' +
								id +
								'"><span class="featured-post-title">' +
								title +
								'</span><a href="#" onClick="featured_delete( this )">Delete</a></div>'
							);
			featured_post = 1;
		}
	});

	$('.featured-post a').on('click', function() {
		featured_delete( this );
		return false;
	});

});

/*
 * featured_delete
 * Function te remove the selected post
 */

function featured_delete( a_el ) {
	var div = jQuery( a_el ).parent();

	div.css('background-color', '#ff0000').fadeOut('normal', function() {
		div.remove();
	});
	featured_post = 0;
	return false;
}