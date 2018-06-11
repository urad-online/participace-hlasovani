<?php

require_once PB_VOTE_PATH_INC .'/pb_voting_post_types.php';
global $votes_mtbx;

spl_autoload_register('pbvote_class_autoloader', true);

function pb_voting_register_actions_filters_after_init()
{

    pbvote_register_post_type();
    // add_action( 'add_meta_boxes',      'pbvoting_metabox' );
    // add_action( 'wp_ajax_pbvote_save',        array( 'PbVote_Save', 'save_votes' ));
    add_action( 'wp_ajax_pbvote_save',        'pbvote_save_votes' );
    add_action( 'wp_ajax_nopriv_pbvote_save', 'pbvote_save_votes' );
    add_action( 'wp_ajax_pbvote_getcode',     'pbvote_get_code' );
    add_action( 'wp_ajax_nopriv_pbvote_getcode', 'pbvote_get_code' );

    pb_voting_enqueue_extension();

}

function pb_voting_enqueue_extension()
{
    wp_register_style('pbvote-style', PB_VOTE_URL . '/assets/css/pbvote_style.css', array(),'1.0', "all");
    wp_register_script('pb-voting',   PB_VOTE_URL . '/assets/js/pb-voting.js', array('jquery'),'1.1', true);

    wp_enqueue_style('pbvote-style');
    wp_enqueue_script('pb-voting');

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
function pbvote_get_code()
{
    $request = $_POST;
    $get_code = new PbVote_GetCode();
    if ( (!empty( $request['voter_id'])) && (! empty($request['voting_id']))) {
        $output = $get_code->get_code( $request);
    } else {
        $output = array( 'result' => 'error', 'message' => 'Neni zadán identifikátor hlasujícího',);
    }
    $output =  json_encode( $output );
    echo $output ;

    wp_die(); // this is required to terminate immediately and return a proper response
}
function pb_votimg_set_single_template($single_template) {
    global $post;

    if ($post->post_type == 'pb-voting') {
        $single_template = dirname( __FILE__ ) . '/templates/single-pb_voting.php';
    } elseif ($post->post_type == 'hlasovani') {
        $single_template = dirname( __FILE__ ) . '/templates/single-hlasovani.php';
    }
    return $single_template;
}

add_filter( 'single_template', 'pb_votimg_set_single_template' );
