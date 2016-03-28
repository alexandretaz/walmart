<?php
/**
 * Created by PhpStorm.
 * User: alexandre
 * Date: 27/03/16
 * Time: 00:17
 */

if(!defined("ABSPATH")) {
    header("HTTP/1.0 500 Internal Server Error");
}

/**
 * Function that install the database
 */

function sections_install() {
    global $wpdb;

    $walmart_section_table_name = $wpdb->prefix .  "route_section";

    $charset_collate = $wpdb->get_charset_collate();


    $sql = "CREATE TABLE IF NOT EXISTS $walmart_section_table_name (id int(11) unsigned NOT NULL AUTO_INCREMENT, origin char(5) NOT NULL,
		destination char(5) NOT NULL,
		distance int(11) NOT NULL,
		PRIMARY KEY id (id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    add_option("routes_section", "1.0");

}

/**
 * function that installs the data provided on the interview documentation
 */
function sections_install_data() {

    global $wpdb;

    $walmart_section_table_name = $wpdb->prefix .  "route_section";
    // I'm doing the data input from the test pdf

    $first_data = array(
        array('A', 'B' , 10),
        array('B', 'D' , 15),
        array('A', 'C' , 20),
        array('C', 'D' , 30),
        array('B', 'E' , 50),
        array('D', 'E' , 30),
    );
    foreach ($first_data as $row) {
        $wpdb->insert(
            $walmart_section_table_name,
            array(
                'origin' => $row['0'],
                'destination' => $row['1'],
                'distance' => $row['2'],
            )
        );
    }
    sections_install_api_page();
}

function sections_install_api_page() {
    global $wpdb;

    $querystr = "
    SELECT $wpdb->posts.* 
    FROM $wpdb->posts
    WHERE $wpdb->posts.post_name = 'api-routes'
    AND $wpdb->posts.post_type = 'page'
 ";
    $pages = $wpdb->get_results($querystr);

    if(count($pages)>0) {
        return;
    }

    $page = array(
        'post_author' => 1,
        'post_date' => date('Y-m-d H:i:s'),
        'post_content' => '[json-display]',
        'post_title' => 'Routes Api Page',
        'post_name' => 'api-routes',
        'post_status' => 'publish',
        'comment_status' => 'open',
        'ping_status' => 'open',
        'post_modified' => date('Y-m-d H:i:s'),
        'post_modified_gmt' => date('Y-m-d H:i:s'),
        'post_parent' => 0,
        'post_type' => 'page',
        'comment_count' => 0
    );

    wp_insert_post($page);
}