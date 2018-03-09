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


class BNE_Testimonials_Pro {

	
    /*
     * 	Constructor
     *
     *	@since 		v2.3
     *	@updated	v2.5
     *
    */
	function __construct() {
		
		// Set Constants
		define( 'BNE_TESTIMONIALS_VERSION', '2.5' );
		define( 'BNE_TESTIMONIALS_DIR', dirname( __FILE__ ) );
		define( 'BNE_TESTIMONIALS_URI', plugins_url( '', __FILE__ ) );
		define( 'BNE_TESTIMONIALS_BASENAME', plugin_basename( __FILE__ ) );
		
		// Textdomain
		add_action( 'plugins_loaded', array( $this, 'text_domain' ) );
		
		// Setup Includes / Files
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		
		// Scripts 
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 11, 1 );
		
		// Deactivate Legacy Widgets
		add_action( 'widgets_init', array( $this, 'deactivate_widgets_1x' ), 12 );
		
		// Admin Notices
		add_action( 'admin_notices', array( $this, 'notice' ) );

	}



	/*
	 * 	Informational Notice
	 *
	 * 	@since		v2.5
	*/
	function notice() {
		$notice_id = 'bne_testimonials_notice2_5';
		$dismissed = get_option( $notice_id );
		if( $dismissed == 'notified' || !current_user_can('manage_options' ) ) {
			return;
		}
		if( isset( $_GET[$notice_id] ) && $_GET[$notice_id] ) {
			if( $_GET[$notice_id] == 'close_notice' ) {
				update_option( $notice_id, 'notified' );
				return;
			}
		}
		$help_url = 'http://docs.bnecreative.com/articles/plugins/testimonials-pro/#api_docs';
		$close = add_query_arg( $notice_id, 'close_notice' );
		?>
		<div class="notice notice-info is-dismissible">
			<p>
			<?php echo sprintf( 
				'<strong>New in BNE Testimonials:</strong> '.esc_html__( 'You can now customize the email notification for the submission form, enable email gravatars for testimonial images, use columns for list and slider layouts, and hide extra testimonials using list and masonry layouts. %3$s%1$s%5$s | %4$s%2$s%5$s', 'bne-testimonials' ),
				esc_html__( 'Go to Settings', 'bne-testimonials' ),
				esc_html__( 'Close', 'bne-testimonials' ),
				'<a href="'.esc_url( get_admin_url( null, 'edit.php?post_type=bne_testimonials&page=bne_testimonials_settings' ) ).'">',
				'<a href="'.esc_url( $close ).'">',
				'</a>'
				
			); ?>
			</p>
		</div>
		<?php
	}



	/*
	 * 	Textdomain for Localization
	 *
	 * 	@since		v1.8
	 * 	@updated	v2.3
	*/
	function text_domain() {
		load_plugin_textdomain( 'bne-testimonials', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}



	/*
	 *	Plugin Setup
	 *	
	 * 	@since 		v1.0
	 * 	@updated 	v2.5
	 *
	*/
	function setup() {

		// Admin Only Pages
		if( is_admin() ) {
			// Libraries
			include_once( BNE_TESTIMONIALS_DIR . '/includes/lib/cmb2/init.php' );
			
			// Admin Help Page
			include_once( BNE_TESTIMONIALS_DIR . '/includes/help/help.php' );
		}

		// Settings
		include_once( BNE_TESTIMONIALS_DIR . '/includes/settings.php' );

		// API
		include_once( BNE_TESTIMONIALS_DIR . '/includes/api.php' );

		// Support
		include_once( BNE_TESTIMONIALS_DIR . '/includes/locals.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/form-functions.php' );

		// CPT
		include_once( BNE_TESTIMONIALS_DIR . '/includes/lib/bne_cpt-class.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/cpt-main.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/cpt-generator.php' );

		// Shortcodes
		include_once( BNE_TESTIMONIALS_DIR . '/includes/shortcode-display.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/shortcode-display-api.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/shortcode-form.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/shortcode-badge.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/shortcode-stats.php' );

		// Updater
		require( BNE_TESTIMONIALS_DIR . '/includes/lib/plugin-update-checker.php' );
		$BNE_Testimonials_Pro_Update_Checker = PucFactory::buildUpdateChecker(
			'http://updates.bnecreative.com/?action=get_metadata&slug=bne-testimonials-pro',
			__FILE__,
			'bne-testimonials-pro'
		);
		

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


		// v2.0 Widget
		include_once( BNE_TESTIMONIALS_DIR . '/includes/widgets.php' );
		
		
		
		/*
		 *	v1.x legacy Shortcodes and Widgets
		 *
		 *	Prior to v2.0, Testimonials used a customizable filter set
		 *	to output the data. This allowed devs to re-arrange and customize
		 *	how the testimonials are displayed. With v2.0+ we have removed all
		 *	of this in favor of pre-defined themes. To prevent confusion and
		 *	disruption of customizations, we have moved these functions here
		 *	and can only be called using the legacy shortcode varients and widgets.
		 *
		 *	Shortcodes using this structure:
		 *	[bne_testimonials_list]
		 *	[bne_testimonials_slider]
		 *	[bne_testimonials_masonry]
		 *
		*/
		include_once( BNE_TESTIMONIALS_DIR . '/includes/legacy/testimonial-output.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/legacy/shortcode-list.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/legacy/shortcode-masonry.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/legacy/shortcode-slider.php' );	
		include_once( BNE_TESTIMONIALS_DIR . '/includes/legacy/widget-list.php' );
		include_once( BNE_TESTIMONIALS_DIR . '/includes/legacy/widget-slider.php' );

	}




	/*
	 *	Remove v1.x legacy Widgets
	 *
	 *	If the site still has the List Widget or Slider Widget activated,
	 *	let's continue to use them. However, if they do not, then lets
	 *	remove them for future users.
	 *
	 *	@since 		v2.0
	 *	@updated 	v2.3
	 *
	*/
	function deactivate_widgets_1x() {
	
		// List Widget Check
		if( !is_active_widget( false, false, 'bne_testimonials_list_widget') ) {
			unregister_widget( 'bne_testimonials_list_widget' );
		}
		
		// Slider Widget Check
		if( !is_active_widget( false, false, 'bne_testimonials_slider_widget') ) {
			unregister_widget( 'bne_testimonials_slider_widget' );
		}	
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
		wp_register_style( 'bne-testimonials-css', BNE_TESTIMONIALS_URI . '/assets/css/bne-testimonials'.$min.'.css', '', BNE_TESTIMONIALS_VERSION, 'all' );
		
		// Masonry JS
		wp_register_script( 'masonry', BNE_TESTIMONIALS_URI . '/assets/js/masonry.min.js', array('jquery'), '3.1.5', true );
		
		// Curtail JS
		wp_register_script( 'curtail', BNE_TESTIMONIALS_URI . '/assets/js/jquery.curtail.min.js', array('jquery'), '1.1.2', true );
		
		// Check if we're on a BNE WordPress Theme...
		if( !defined('BNE_FRAMEWORK_VERSION') ) {
			// Flexslider
			wp_register_script( 'flexslider', BNE_TESTIMONIALS_URI . '/assets/js/flexslider.min.js', array('jquery'), '2.2.2', true );
		}
	
		// Load the plugin CSS
		wp_enqueue_style( 'bne-testimonials-css');
	
	}



	/*
	 *	
	 *	Register Admin CSS and JS
	 *
	 *	@since 		v1.0
	 *	@updated 	v2.3
	 *
	*/
	function admin_scripts( $hook ) {
		
		global $post;
		
		// Post Types to check against
		$cpt_slugs = array(
			'bne_testimonials',
			'bne_testimonials_sg',
		);
		
		// Check if we're on a post new or edit admin screen.
		if( $hook == 'post-new.php' || $hook == 'post.php' ) {
			
			// Crosscheck with our Post Type list.
			if( in_array( $post->post_type, $cpt_slugs ) ) {     
				
				// Finally, check if we're not on a BNE theme as this is already available from there.
				if( !defined('BNE_FRAMEWORK_VERSION') ) {
					wp_enqueue_style( 'bne-cmb-admin-css', BNE_TESTIMONIALS_URI . '/assets/css/bne-cmb-admin.css', '', BNE_TESTIMONIALS_VERSION, 'all'  );
				}
			
			}
		}
	}


} // END Class

	
// Initiate the Class
$BNE_Testimonials_Pro = new BNE_Testimonials_Pro();



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
function bne_testimonials_get_template( $part, $atts, $api = null, $return = 'return', $wrapper = null ) {
	return include( BNE_TESTIMONIALS_DIR . "/includes/templates/$part.php" );
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
function bne_testimonials_get_gravatar( $email = null, $size = '150', $gravatar = null ) {
	$options = get_option('bne_testimonials_settings');
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