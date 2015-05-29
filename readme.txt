
=== Black Post Slider ===

Contributors: mpol
Tags: featured post, slider, post slider, posts slider, black post
Requires at least: 3.3
Tested up to: 4.2
License: GPLv2 or later
Stable tag: 1.0.3

A featured post slider that shows post in a slider.

== Description ==

Black Post Slider is a WordPress plugin that shows selected posts in a slider.
You can easily select posts to show in the slider.
It's possible to just have one slider, but you can also have several sliders.

Features

* Up to 10 slides on each slider
* Fully Responsive
* SEO friendly
* Custom CSS possible
* Use any color you want, not just Black
* Support for categories with which you can make several sliders
* Fully translatable and localizable
* Lightweight, low on resources
* Easy to use admin area
* Uses the WordPress Media Manager

= Languages =

* nl_NL [Marcel Pol](http://zenoweb.nl)
* sr_RS [Ogi Djuraskovic](http://firstsiteguide.com)

== Installation ==

1. Make sure the files are within a folder.
2. Copy the whole folder inside the wp-content/plugins/ folder.
3. In the backend, activate the plugin.
4. You can now add featured posts.
5. For each featured post, select a featured image.
6. Optionally select a real post to link to.

= How to display the slider on your website =

You can place the following code in your template file, like header.php:

	<?php
		if ( function_exists( 'timelord_featured_post' ) ) {
			timelord_featured_post();
	}
	?>

If you want to have multiple sliders, you can make categories for the featured posts.
Then use the slug of the category as a parameter for the functioncall:

	<?php
		if ( function_exists( 'timelord_featured_post' ) ) {
			timelord_featured_post('frontpage');
		}
	?>

This will show all the featured posts from the frontpage category.

== Frequently Asked Questions ==

= How can I order the posts? =

You can use a plugin like [Simple Custom Post Order](http://wordpress.org/plugins/simple-custom-post-order/)
to order the posts. This sets the menu_order field for each post.
If you do not order the posts in this way, they will be sorted by date.

= What options are there for the slide effect? =

You can set the slide effect. The supported slide effects are:

* fade: crossfade to the next slide.
* fadeout: the old slide will fade out, afterwards the new slide will fade in.
* none: it will just switch without any effect.
* scrollHorz: a horizontal slide.

There is also a speed setting. You can set in milliseconds how long the slide effect will take
And there's the timeout setting, which is the timeout for the next slide, also in milliseconds.

= How can I customize the CSS? =

In the Settings page you can set the CSS for the border and the background of the titles.
It accepts webcolors, like "#444" or "white".

= How do I set the height of the slider? =

The height of the slider is determined by the height of the first image.

When loading however, it might have loaded the html, but not yet the javascript. Therefore there might be a short moment that it is displayed in a different height.
To solve this, you can set an initial height is css, like:

div.featured_posts {
	height: 400px; /* start height at loadtime */
}

= How do I set the width of the slider? =

The width of the whole slider is 100%. That means it takes all your space, or the space of
the outside container.
The width of the images is set fixed to 70%, while the titles have 30%.

== Screenshots ==

1. Frontend view of the slider
2. Admin view with the overview of slides
3. Admin view when editing a slide (featured post)

== Changelog ==

= 1.0.3 =
* 2015-05-29
* Fix loading on Firefox the first time.

= 1.0.2 =
* 2014-09-20
* Add Serbian Translation

= 1.0.1 =
* 2014-05-06
* Fix setting the height at first load

= 1.0 =
* 2014-04-30
* Initial version
