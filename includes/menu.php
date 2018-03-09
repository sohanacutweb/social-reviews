<?php

// SETTING UP THE PLUGIN MENU...
add_action('admin_menu', 'social_menu_pages');
function social_menu_pages(){
	
    add_menu_page('Social Reviews', 'Social Reviews', 'manage_options', SOCIALRV_MAIN_MENU_SLUG, 'all_social_reviews', SOCIALRV_MENU_ICON);
	
    add_submenu_page(SOCIALRV_MAIN_MENU_SLUG, 'Settings', 'Settings', 'manage_options', 'social-reviews-setting','social_setting_page' );
	
}


?>