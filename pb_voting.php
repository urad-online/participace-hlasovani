<?php
/*
Plugin Name: Participativní projekty - hlasování
Plugin URI: https://urad.online
Description: AJAX search in hierarchical pb_voting structure.
Author: Miloslav Stastny
Version: 0.1
Author URI: https://urad.online
Text Domain: pb-voting
Domain Path: /languages/
*/

define( 'PB_VOTE_PATH',       dirname(__FILE__));
define( 'PB_VOTE_PATH_INC',   PB_VOTE_PATH.'/includes');
define( 'PB_VOTE_URL',        plugins_url('pb_voting'));
define( 'PB_VOTE_PATH_TEMPL', PB_VOTE_PATH_INC.'/templates');
define( 'PB_VOTING_POST_TYPE', 'pb-voting');

define( 'PB_VOTE_TABLE_NAMES', array(
    'register' => 'pb_register',
    'votes'    => 'pb_votes',
));

register_activation_hook( __FILE__, 'pb_vote_activation' );
register_uninstall_hook( __FILE__, 'pb_vote_uninstall');

pb_voting_register_plugin_actions();

global $pb_voting, $pb_voting_db, $pb_vote;

function pb_voting_register_plugin_actions()
{
    add_action( 'init',           'pb_vote_on_init' );
    add_action( 'admin_init',     'pb_vote_on_admnin_init');
    add_action( 'plugins_loaded', 'pb_vote_plugin_loaded');
}

function pb_vote_activation()
{
    global $pb_vote_table_name;

    require_once PB_VOTE_PATH_INC. '/pb_voting_create_tables.php';

    pb_voting_create_tables();

    $message = "Activated at: ". date('Y-m-d H:i:s');
    file_put_contents( PB_VOTE_PATH . '/pb_vote_plugin_activate.log', $message);
    flush_rewrite_rules();
}

function pb_vote_on_init()
{
    pb_voting_register_actions_filters_after_init();

    global $pb_vote;
    if (class_exists('VotingInfoMetabox')) {
        $pb_vote = new VotingInfoMetabox();
    };


    // $pb_vote = pb_voting_Select_Items_form::get_instance();
    // pb_voting_Service_Edit::get_instance();
    // $pb_vote = pb_voting_Item_Edit::get_instance();

    // $pb_vote->create_json_file();
    // add_action('init', array( 'pb_voting_Select_Items_form', 'init'));
}

function pb_vote_on_admnin_init( )
{
    // add_action( 'save_post', 'pbvoting_metabox_save', 20, 3 );
}

function pb_vote_plugin_loaded()
{
    register_deactivation_hook( __FILE__, 'pb_vote_deactivate' );
    require_once PB_VOTE_PATH_INC .'/pb_voting_load.php';


}
function pb_vote_uninstall()
{
    require_once PB_VOTE_PATH_INC. '/pb_voting_create_table.php';

    pb_voting_drop_tables( );
}
function pb_vote_deactivate()
{
    $message = "Deactivated at: ". date('Y-m-d H:i:s');
    file_put_contents(PB_VOTE_PATH.'/pb_vote_plugin_deactivate.log', $message);

}
