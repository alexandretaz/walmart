<?php

/*
Plugin Name: Walmart
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A Plugin made as a job interview to a position on walmart.
Version: 1.0
Author: alexandre
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

/** Require blocks: Here I put all the required files for the plugin to work */
require_once (__DIR__."/backend/install.php");
require_once (__DIR__."/backend/admin.php");
require_once (__DIR__."/classes/request.class.php");
require_once (__DIR__."/classes/response.class.php");
require_once (__DIR__."/frontend/result.php");


/** Plugin activation section: Here are all the Hooks to activate the plugin */
// Installing a new table on the wordpress database
register_activation_hook(__FILE__, "sections_install");
//Installing the test Data
register_activation_hook(__FILE__, "sections_install_data");

/**administrator menu **/
add_action ("admin_menu", 'routes_menu');

/** frontend shortcode **/
add_action("parse_request", "api_result");

