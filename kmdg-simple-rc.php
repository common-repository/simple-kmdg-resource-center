<?php
namespace KMDG\SIMPLERC;

/*
Plugin Name: Simple KMDG Resource Center
Plugin URI:
description: A simple version of KMDG's Resource Center
Version: 1.1
Author: KMDG
Author URI: http://kmdg.com/
License: GPL2
*/

require("helper_lib.php");
require("setup.php");

register_deactivation_hook( __FILE__, 'KMDG\SIMPLERC\kmdg_simple_rc_deactivate' );
register_activation_hook( __FILE__, 'KMDG\SIMPLERC\kmdg_simple_rc_activate' );
add_action( 'init', 'KMDG\SIMPLERC\create_post_type' );
add_action( 'init', 'KMDG\SIMPLERC\create_custom_tax' );
add_action( 'wp_enqueue_scripts', 'KMDG\SIMPLERC\enqueue_rc_sources' );
add_action( 'add_meta_boxes', 'KMDG\SIMPLERC\remove_wp_seo_meta_box', 100 );

add_filter( 'template_include', 'KMDG\SIMPLERC\kmdg_simple_rc_template' );
add_filter( 'acf/settings/load_json', 'KMDG\SIMPLERC\load_acf' );
//add_filter( 'wpseo_sitemap_exclude_post_type', 'KMDG\SIMPLERC\sitemap_exclude_post_type', 10, 2 );
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'KMDG\SIMPLERC\sitemap_exclude_post_ids' );

if (!class_exists('ACF')) {
    add_filter('acf/settings/url', 'KMDG\SIMPLERC\my_acf_settings_url');
    add_filter('acf/settings/show_admin', 'KMDG\SIMPLERC\my_acf_settings_show_admin');
    define('KMDG_RC_ACF_PATH', plugin_path_helper('/includes/advanced-custom-fields/'));
    define('KMDG_RC_ACF_URL', plugin_url_helper('/includes/advanced-custom-fields/'));
    include_once(KMDG_RC_ACF_PATH . 'acf.php');
}


