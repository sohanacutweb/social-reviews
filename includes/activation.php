<?php

//Create database
function socialr_activation($network_wide) {
    global $wpdb;
	
    $charset_collate = $wpdb->get_charset_collate();

    /* $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "yrw_yelp_business (".
           "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
           "business_id VARCHAR(100) NOT NULL,".
           "name VARCHAR(255) NOT NULL,".
           "photo VARCHAR(255),".
           "address VARCHAR(255),".
           "rating DOUBLE PRECISION,".
           "url VARCHAR(255),".
           "website VARCHAR(255),".
           "review_count INTEGER NOT NULL,".
           "PRIMARY KEY (`id`),".
           "UNIQUE INDEX yrw_business_id (`business_id`)".
           ") " . $charset_collate . ";";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    dbDelta($sql); */

    $sql = "CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "socialr_yelp_review (".
           "id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,".
           "yelp_business_id BIGINT(20) UNSIGNED NOT NULL,".
           "review_id VARCHAR(60) NOT NULL,".
           "rating INTEGER NOT NULL,".
           "text VARCHAR(10000),".
           "url VARCHAR(255),".
           "time VARCHAR(20) NOT NULL,".
           "author_name VARCHAR(255),".
           "author_img VARCHAR(255),".
           "PRIMARY KEY (`id`)".
           ") " . $charset_collate . ";";

    dbDelta($sql);
}
register_activation_hook(__FILE__, 'socialr_activation');
?>