<?php
/*
 * Plugin Name: Social Reviews
 * Version: 1.0
 * Plugin URI: acutweb.com
 * Description: Easily display Testimonials on any page, post.
 * Author: Acutweb
 * Author URI: acutweb.com
*/

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

define( 'SOCIALRV_VERSION', '1.0' );
define( 'SOCIALRV_DIR', dirname( __FILE__ ) );
define( 'SOCIALRV_URI', plugins_url( '', __FILE__ ) );
define( 'SOCIALRV_BASENAME', plugin_basename( __FILE__ ) );
define('SOCIALRV_MAIN_MENU_SLUG', 'social-reviews');
define('SOCIALRV_MENU_ICON', 'dashicons-lock');

add_action( 'wp_enqueue_scripts', 'frontend_scripts' );

include_once( SOCIALRV_DIR . '/includes/menu.php' );
include_once( SOCIALRV_DIR . '/includes/all_social_reviews.php' );
include_once( SOCIALRV_DIR . '/includes/setting.php' );


	
	function setup() {

		// Admin Only Pages
		if( is_admin() ) {
			// Libraries
			
			// Admin Help Page
			include_once( SOCIALRV_DIR . '/includes/help/help.php' );
		}
		// Settings
		include_once( SOCIALRV_DIR . '/includes/settings.php' );
		// API
		include_once( SOCIALRV_DIR . '/includes/api.php' );
		// Support
		include_once( SOCIALRV_DIR . '/includes/form-functions.php' );
		// CPT
		include_once( SOCIALRV_DIR . '/includes/lib/bne_cpt-class.php' );
		include_once( SOCIALRV_DIR . '/includes/cpt-main.php' );
		include_once( SOCIALRV_DIR . '/includes/cpt-generator.php' );
		// Shortcodes
		include_once( SOCIALRV_DIR . '/includes/shortcode-display.php' );
		include_once( SOCIALRV_DIR . '/includes/shortcode-display-api.php' );
		include_once( SOCIALRV_DIR . '/includes/shortcode-form.php' );
		include_once( SOCIALRV_DIR . '/includes/shortcode-badge.php' );
		include_once( SOCIALRV_DIR . '/includes/shortcode-stats.php' );
		// Updater
		/*require( SOCIALRV_DIR . '/includes/lib/plugin-update-checker.php' );
		$SOCIALRV_Pro_Update_Checker = PucFactory::buildUpdateChecker(
			'http://updates.bnecreative.com/?action=get_metadata&slug=bne-testimonials-pro',
			__FILE__,
			'bne-testimonials-pro'
		);*/
		/*
		 *	Thumbnail Support
		 *
		 *	Because some themes will selectively choose what post types
		 *	can use post-thumbnails, we will first remove support to
		 *	basically reset the option, then we will add it back.
		 *
		 *	This may seem link backwards thinking, but works.
		 *	
		*/
		remove_theme_support( 'post-thumbnails' );
		if( !current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		
		include_once( SOCIALRV_DIR . '/includes/legacy/testimonial-output.php' );
		include_once( SOCIALRV_DIR . '/includes/legacy/shortcode-list.php' );
		include_once( SOCIALRV_DIR . '/includes/legacy/shortcode-masonry.php' );
		
		include_once( SOCIALRV_DIR . '/includes/legacy/shortcode-slider.php' );	
	}

/*
	 *	Register frontend CSS and JS
	 *
	 *	@since 		v1.0
	 *	@updated 	v2.4.2
	 *
	*/
	function frontend_scripts() {
		$min = ( defined('WP_DEBUG') && true === WP_DEBUG ) ? '' : '.min';
		// CSS
		wp_register_style( 'social-reviews-css', SOCIALRV_URI . '/assets/css/social-reviews'.$min.'.css', '', SOCIALRV_VERSION, 'all' );
		
		// Curtail JS
		wp_register_script( 'curtail', SOCIALRV_URI . '/assets/js/jquery.curtail.min.js', array('jquery'), '1.1.2', true );
		// Load the plugin CSS
		wp_enqueue_style( 'social-reviews-css');
	}
	

	
/*
 * 	Template Parts
 *
 *	Returns the template part needed to display in the testimonial.
 *
 *	$part		string			The filename of the template part.
 *	$atts		string|array	Any needed attributes to pass.
 *	$return		string			Either return or echo the part.
 *	$wrapper	string			The wrapping container - div, h1, h3, h4, p, etc.
 *
 *	@since 		v2.3
*/
function SOCIALRV_get_template( $part, $atts, $api = null, $return = 'return', $wrapper = null ) {
	return include( SOCIALRV_DIR . "/includes/templates/$part.php" );
}



/*
 * 	Get Testimonial Gravatar
 *
 *	Queries Gravatar and checks if an image is set for the email. If it is, get the URL.
 *
 *	$email		string			Email address to check against
 *	$size		int				Gravatar returned sized
 *	$gravatar	string			Default/fallback Image
 *
 *	@since v2.5
*/
function SOCIALRV_get_gravatar( $email = null, $size = '150', $gravatar = null ) {
	$options = get_option('SOCIALRV_settings');
	if( isset( $options['enable_gravatars'] ) ) {
		if( $options['enable_gravatars'] == 'on' && $email ) {
			$hash = md5( stripslashes( sanitize_email( $email ) ) );
			$resp = wp_remote_head( "https://www.gravatar.com/avatar/{$hash}?d=404" );
			if( !is_wp_error( $resp ) && $resp['response']['code'] == 200 ) {
				$gravatar = get_avatar_url( $email, array( 'size' => $size ) );
			}
		}
	}
	
	return $gravatar;

}