<?php
/*
Plugin Name: Participativní projekty - hlasování
Plugin URI: https://urad.online
Description: Registrace, správa a hlasování o návrzích participativního rozpočtu. Integrace s Limesurvey a https://sms.sluzba.cz
Author: Miloslav Stastny
Version: 1.1.10
Author URI: https://urad.online
Text Domain: pb-voting
Domain Path: /languages
*/
// define( 'DELIVERY_MSG_TYPE',  'Email'); // values Email, Sms
global $metabox_pbvote;

define( 'PB_VOTE_PATH',       dirname(__FILE__));
define( 'PB_VOTE_PATH_INC',   PB_VOTE_PATH.'/includes');
define( 'PB_VOTE_URL',        plugins_url(basename(PB_VOTE_PATH)));
define( 'PB_VOTE_PATH_TEMPL', PB_VOTE_PATH_INC.'/templates');
define( 'PB_VOTING_POST_TYPE',      'hlasovani');
define( 'PB_VOTING_STATUS_TAXO',    'voting_status');
define( 'PB_OPTION_NAME',    'pbvoting' );


if (! defined('PBVOTE_DEBUG')) {
    define( 'PBVOTE_DEBUG',   false );
}

define( 'PB_VOTE_TABLE_NAMES', array(
    'register'     => 'pb_register',
    'register_log' => 'pb_register_log',
    'votes'        => 'pb_votes',
));

$options = get_option(PB_OPTION_NAME);
if (! empty($options)) {
  define( 'PB_HELP_SLUG',  $options['pb_help_slug'] );
  define( 'PB_HELP_SLUG_RATING_SECTION', "#".$options['pb_help_rating_section_id']  );
  define( 'PB_RATING_ENABLED', ($options['pb_enable_rating']) == "1" ? true : false);
} else {
  define( 'PB_HELP_SLUG',  "otazky-a-odpovedi" );
  define( 'PB_HELP_SLUG_RATING_SECTION', "#podportenavrh"  );
  define( 'PB_RATING_ENABLED', true);
}

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
    // require_once PB_VOTE_PATH_INC. '/pb_voting_create_pages.php';
    // pbvote_create_pages();

    $message = "Activated at: ". date('Y-m-d H:i:s');
    file_put_contents( PB_VOTE_PATH . '/pb_vote_plugin_activate.log', $message);
    flush_rewrite_rules();
}

function pb_vote_on_init()
{
    pb_voting_register_actions_filters_after_init();

    global $pb_vote;
    $pb_vote = null;

    if (class_exists('VotingInfoMetabox')) {
        $pb_vote = new VotingInfoMetabox();
    };


    $setting_menu = new PbVote_Setting;
    // $pb_vote = pb_voting_Select_Items_form::get_instance();
    // pb_voting_Service_Edit::get_instance();
    // $pb_vote = pb_voting_Item_Edit::get_instance();

    // $pb_vote->create_json_file();
    // add_action('init', array( 'pb_voting_Select_Items_form', 'init'));
}

function pb_vote_on_admnin_init( )
{
  global $wp_query;
  global $metabox_pbvote;
  if ( empty($metabox_pbvote) ) {
    $metabox_pbvote = new PbVote_ImcIssueDetailMetabox;
  }
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
function pbvote_create_terms_pom()
{
  require_once PB_VOTE_PATH_INC. '/pb_voting_create_terms.php';
  pbvote_create_terms();
}
