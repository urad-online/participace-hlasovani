<?php
global $votes_mtbx;

spl_autoload_register('pbvote_class_autoloader', true);
add_action( 'after_setup_theme', array( 'PbVote_Templates', 'get_instance' ) );

// require_once PB_VOTE_PATH_INC .'/pb_voting_post_types.php';
require_once PB_VOTE_PATH_INC .'/smssluzbacz/apixml30.php';
require_once PB_VOTE_PATH_INC. '/pb_voting_functions.php';
include_once( PB_VOTE_PATH_TEMPL . '/pbvote-part-archive-list.php' );
include_once( PB_VOTE_PATH_TEMPL . '/pbvote-part-archive-grid.php' );
include_once( PB_VOTE_PATH_TEMPL . '/pb-item-part-archive-list.php' );
include_once( PB_VOTE_PATH_TEMPL . '/pb-item-part-archive-grid.php' );

include_once( PB_VOTE_PATH_INC . '/pb_field_definition.php' );
include_once( PB_VOTE_PATH_INC . '/pb-additional-fields.php' );
include_once( PB_VOTE_PATH_INC . '/pb-add-terms-fields.php' );

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

    wp_register_script('pb-project-edit',   PB_VOTE_URL . '/assets/js/pb-project-edit.js', array('jquery'),'1.1', true);
    wp_enqueue_script('pb-project-edit');
		wp_localize_script('pb-project-edit', 'pbFormInitialData', array(
			        'completed_off' => 'Uložit si pro budoucí editaci',
			        'completed_on'  => 'Odeslat návrh ke schválení',
            ));
}


<<<<<<< HEAD
    // If a file is found
    if ( file_exists( $file ) ) {
        // Then load it up!
        require( $file );
    }
}
function add_pbvote_template( $templates )
{
    $templates = array_merge( $templates, array(
        '/archive-hlasovani.php'   => 'Přehled hlasování',
        '/edit-project_issues.php' => "Oprava projektu",
        '/insert-project_issues.php' => "Přidat nový projekt",
       ));
    return $templates;
}
function register_pbvote_templates( $atts )
=======
function pb_enqueue_scripts( )
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
{
    global $post;

	wp_enqueue_style( 'pb-project', PB_VOTE_URL . '/assets/css/pb-styles.css' );

	if ( is_object( $post)) {
        if (is_page( $post->ID)) {
            switch ( $post->post_name) {
                case 'imc-edit-issue':
                case 'novy-navrh-projektu':
                    $edit_show = 'edit';
					// $map_options = get_option('gmap_settings');
					// $mapOptions = array(
					// 	'initial_lat'  	  => $map_options["gmap_initial_lat"],
					// 	'initial_lng'  	  => $map_options["gmap_initial_lng"],
					// 	'initial_zoom'    => $map_options["gmap_initial_zoom"],
					// 	'initial_mscroll' => $map_options["gmap_mscroll"],
					// 	'initial_bound'   => $map_options["gmap_boundaries"],
					// );

					wp_register_script('pb-project-edit',  PB_VOTE_URL . '/assets/js/pb-project-edit.js', array('jquery'),'1.1', true);
			        wp_enqueue_script('pb-project-edit');
					wp_localize_script('pb-project-edit', 'pbFormInitialData', array(
						        'completed_off' => 'Uložit si pro budoucí editaci',
						        'completed_on'  => 'Odeslat návrh ke schválení',
								// 'mapOptions'	=> $mapOptions ,
		            ));
                    break;

                default:
                    $edit_show = 'unknown page';
                    break;
            }
        } elseif ( is_single($post->ID) and ($post->post_type = 'imc-issues')) {
            $edit_show = 'show_single';
        } else {
            $edit_show = 'other';
        }
    }

}
add_action( 'wp_enqueue_scripts',  'pb_enqueue_scripts');

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
<<<<<<< HEAD
add_filter( 'single_template',      'pb_voting_set_single_template', 20 );
add_filter( 'archive_template',     'pb_voting_set_archive_template' );
add_filter( 'theme_page_templates', 'add_pbvote_template' );
add_filter( 'template_include',     'pb_voting_view_template');
=======
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
