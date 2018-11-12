<?php

// require_once PB_VOTE_PATH_INC .'/pb_voting_post_types.php';
require_once PB_VOTE_PATH_INC .'/smssluzbacz/apixml30.php';
require_once PB_VOTE_PATH_INC. '/pb_voting_functions.php';
include_once( PB_VOTE_PATH_TEMPL . '/pbvote-part-archive-list.php' );

global $votes_mtbx;

spl_autoload_register('pbvote_class_autoloader', true);

function pb_voting_register_actions_filters_after_init()
{

    // pbvote_register_post_type();
    // add_action( 'add_meta_boxes',      'pbvoting_metabox' );
    // add_action( 'wp_ajax_pbvote_save',        array( 'PbVote_Save', 'save_votes' ));
    add_action( 'wp_ajax_pbvote_save',        'pbvote_save_votes' );
    add_action( 'wp_ajax_nopriv_pbvote_save', 'pbvote_save_votes' );
    add_action( 'wp_ajax_pbvote_getcode',     'pbvote_get_code' );
    add_action( 'wp_ajax_nopriv_pbvote_getcode', 'pbvote_get_code' );

    pb_voting_enqueue_extension();

    add_shortcode( 'pb_vote_reg_widget', 'pb_vote_get_registration_widget');

}

function pb_voting_enqueue_extension()
{
    wp_register_style('pbvote-style', PB_VOTE_URL . '/assets/css/pbvote_style.css', array(),'1.0', "all");
    wp_register_script('pb-voting',   PB_VOTE_URL . '/assets/js/pb-voting.js', array('jquery'),'1.1', false);
    wp_register_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?hl=cs', array('jquery'),'1.1', false);

    wp_enqueue_style('pbvote-style');
    wp_enqueue_script('pb-voting');
    wp_enqueue_script('google-recaptcha');

    wp_localize_script('pb-voting', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

}

function pbvote_class_autoloader( $class_name ) {

    /**
     * If the class being requested does not start with our prefix,
     * we know it's not one in our project
     */
    if ( 0 !== strpos( $class_name, 'PbVote_' ) ) {
        return;
    }

    $file_name = str_replace(
        array( 'PbVote_', '_' ),      // Prefix | Underscores
        array( '', '-' ),         // Remove | Replace with hyphens
        strtolower( $class_name ) // lowercase
    );

    // Compile our path from the current location
    $file =  PB_VOTE_PATH_INC . '/classes/'. $file_name .'.php';

    // If a file is found
    if ( file_exists( $file ) ) {
        // Then load it up!
        require( $file );
    }
}
function add_pbvote_template( $templates )
{
    $templates = array_merge( $templates, array(
        '/archive-hlasovani.php'   => 'Přehled hlasování', ));
    return $templates;
}
function register_pbvote_templates( $atts )
{
    // Create the key used for the themes cache
    $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

    // Retrieve the cache list.
    // If it doesn't exist, or it's empty prepare an array
    $templates = wp_get_theme()->get_page_templates();
    if ( empty( $templates ) ) {
        $templates = array();
    }

    // New cache, therefore remove the old one
    wp_cache_delete( $cache_key , 'themes');

    // Now add our template to the list of templates by merging our templates
    // with the existing templates array from the cache.
    $templates = array_merge( $templates, $this->templates );

    // Add the modified cache to allow WordPress to pick it up for listing
    // available templates
    wp_cache_add( $cache_key, $templates, 'themes', 1800 );

    return $atts;

}
function pb_voting_view_template( $template)
{
    // Get global post
    global $post;

    // Return template if post is empty
    if ( ! $post ) {
        return $template;
    }

    // Return default template if we don't have a custom one defined
    $file = get_post_meta( $post->ID, '_wp_page_template', true );
    if ( $file ) {
        $file = PB_VOTE_PATH_TEMPL . $file;
        if ( file_exists( $file ) ) {
            return $file;
        }
    }
    return $template;
}
add_filter( 'single_template',      'pb_voting_set_single_template' );
add_filter( 'archive_template',     'pb_voting_set_archive_template' );
add_filter( 'theme_page_templates', 'add_pbvote_template' );
add_filter( 'template_include',     'pb_voting_view_template');
