<?php

// SETTING UP THE PLUGIN MENU...
add_action('admin_menu', 'social_menu_pages');
function social_menu_pages(){
	
    add_menu_page('Social Reviews', 'Social Reviews', 'manage_options', SOCIALRV_MAIN_MENU_SLUG, 'all_social_reviews', SOCIALRV_MENU_ICON);
	
    add_submenu_page(SOCIALRV_MAIN_MENU_SLUG, 'Settings', 'Settings', 'manage_options', 'social-reviews-setting','social_setting_page' );
	add_submenu_page(SOCIALRV_MAIN_MENU_SLUG, 'Manage Google Reviews', 'Manage Google Reviews', 'manage_options', 'social-reviews-manage','social_reviews_manage' );
	add_submenu_page(SOCIALRV_MAIN_MENU_SLUG, 'Manage Yelp Reviews', 'Manage Yelp Reviews', 'manage_options', 'yelp-reviews-manage','yelp_reviews_manage' );
	
}


?>