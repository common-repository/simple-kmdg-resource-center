<?php
namespace KMDG\SIMPLERC;
/*
File Name: setup.php
Plugin URI: http://kmdg.com
description: Functions for activating and deactivating KMDG's simple RC
Author: Dugan Dobbs
Author URI: http://kmdg.com
License: GPL2
 */

/**
 * Runs hooks and registers our custom post type.
 */
function create_post_type()
{
    register_post_type('simple-resources',
        array(
            'labels' => array(
                'name' => __('Resources'),
                'singular_name' => __('Resource')
            ),

            'public' => true,
            'has_archive' => true,
            'rewrite' => array(
                'with_front' => false,
                'slug' => 'resources'
            ),
            'supports' => array('title', 'revisions', 'thumbnail'),
            'taxonomies' => array(''),

            'menu_icon' => 'dashicons-media-document',
            'show_in_rest' => true,
        )
    );
}

/**
 * Runs hooks and registers our custom post taxonomy.
 */
function create_custom_tax()
{
    register_taxonomy(
        'resource-category',
        'simple-resources',
        array(
            'label' => __('Resource Category'),
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'show_in_quick_edit' => true,
            'public' => true
        )
    );
}

/**
 * Given the resource type runs a query to get the items and return them.
 * @param $rctype
 * @return \WP_Query
 */
function get_rc_query($rctype)
{
    return new \WP_Query([
        'post_type' => 'simple-resources',
        'post_status' => 'publish',
        'posts_per_page' => '-1',
        'order' => 'ASC',
        'orderby' => 'menu_order',
        'tax_query' => [
            [
                'taxonomy' => 'resource-category',
                'terms' => $rctype,
                'field' => 'slug',
            ]],
    ]);
}

/**
 * @return int[]|\WP_Post[]
 */
function get_rc_posts()
{
    return \get_posts(["post_type"=>"simple-resources","fields"=>"ids"]);
}

/**
 * A gerry-rigged router to ensure our templates are served for resource items.
 * @param $template
 * @return string
 */
function kmdg_simple_rc_template($template){
    global $wp_query;
    $post_type = get_post_type();
    if ($post_type == 'simple-resources'){
        if (is_archive()) {
            return plugin_path_helper('/parts/simple-resources/archive.php');
        }
        else {
            return plugin_path_helper('/parts/simple-resources/single.php');
        }
    }
    elseif (is_tax('resource-category')){
        return plugin_path_helper('/parts/simple-resources/archive.php');
    }
    return $template;
}

/**
 * A function to hook our ACF JSONs to ACF's Load Path
 * @param $paths
 * @return mixed
 */
function load_acf( $paths ) {
    $paths[] = plugin_path_helper("acf-json");
    return $paths;
}

/**
 * A wrapper function to hook our custom ACF Options page
 * @param $url
 * @return string
 */
function my_acf_settings_url( $url ) {
    return MY_ACF_URL;
}

/**
 * A hook to show the ACF settings for admins.
 * @param $show_admin
 * @return false
 */
function my_acf_settings_show_admin( $show_admin ) {
    return false;
}

/**
 * A function to hook into to when plugin is deactivated.
 */
function kmdg_simple_rc_deactivate() {
    unregister_post_type( 'simple-resources' );
    unregister_taxonomy( 'resource-category' );
    flush_rewrite_rules();
}

/**
 * A function to hook into when plugin is activated.
 */
function kmdg_simple_rc_activate() {
    flush_rewrite_rules();
}

/**
 * Adds our option page with ACF.
 */
acf_add_options_page(array(
    'page_title' 	=> 'KMDG Simple RC Options',
    'menu_title'	=> 'Simple RC Options',
    'menu_slug' 	=> 'kmdg_simple_rc_options',
    'capability'	=> 'edit_posts',
    'redirect'		=> false
));



function remove_wp_seo_meta_box() {
    remove_meta_box('wpseo_meta', "simple-resources", 'normal');
}

function sitemap_exclude_post_type( $excluded, $post_type ) {
    return $post_type === 'simple-resources';
}

function sitemap_exclude_post_ids() {
    return get_rc_posts();
}
