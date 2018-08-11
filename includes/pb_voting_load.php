<?php

// require_once PB_VOTE_PATH_INC .'/pb_voting_post_types.php';
require_once PB_VOTE_PATH_INC .'/smssluzbacz/apixml30.php';
require_once PB_VOTE_PATH_INC. '/pb_voting_functions.php';
define('SMSGATE_LOGIN', 'rousar');
define('SMSGATE_PASSWD', 'MJMMJVvdppps1*');

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

add_filter( 'single_template', 'pb_votimg_set_single_template' );
