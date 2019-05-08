<?php
function pb_vote_get_registration_widget( $atts, $content, $tag)
{
	$new_widget = new PbVote_GenWidget( $atts);

	return $new_widget->render_widget() ;
}

function pbvote_get_code()
{
    $request = $_POST;
	$user_ip = get_the_user_ip();
	$google_cap = new PbVote_ValidateCaptcha;
	$captcha_res = $google_cap->send_request( $request['captcha_response'], $user_ip );

	if ($captcha_res) {
		// $get_code = new PbVote_GetCode( DELIVERY_MSG_TYPE);
		if ( (!empty( $request['voter_id'])) && (! empty($request['voting_id']))) {
			$get_code = new PbVote_LimeSurveyTokens( $request);
			$output = $get_code->get_code();
		} else {
			$output = array( 'result' => 'error', 'message' => 'Neni zadán identifikátor hlasujícího', );
		}
	} else {
		$output = $google_cap->get_error();
	}

	echo json_encode( $output, JSON_UNESCAPED_UNICODE ) ;

    wp_die(); // this is required to terminate immediately and return a proper response
}

function get_the_user_ip()
{
	global $_SERVER;

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return apply_filters('wpb_get_ip', $ip);
}
function hlasovani_query_arg( $list = array(), $status_list = array()) //todelete
{
	if ( is_string( $list)) {
		$list = explode( ";" , $list);
	}
	$args = array(
		'post_type'   => 'imc_issues',
		'post_status' => array( 'draft', 'publish', 'pending'),
		'posts_per_page' => -1,
        'post__in' => $list,
	);

	return $args;
}

function list_projects($list)
{
    echo implode( ',', 	$list);
	include_once( PB_VOTE_PATH_TEMPL . '/archive-hlasovani.php');
}
function get_all_pbvote_taxo( $term_name = "", $hierarchical = false )
{
 	if (empty($term_name)) {
 		return false;
 	}
    // no default values. using these as examples
    $taxonomies = array(
        $term_name,
    );

    $args = array(
        'orderby'                => 'id',
        'order'                  => 'ASC',
        'hide_empty'             => false,
        'fields'                 => 'all',
    );

	if ($hierarchical) {
		$args = array_merge(
			$args,
			array(
			    'hierarchical' => true,
			    'parent' => 0,
		    )
		);
	}
    $terms = get_terms($taxonomies, $args);

    return $terms;
}

function pbvote_get_current_status_name($mypostID){

    $status_currentterm = get_the_terms($mypostID , PB_VOTING_STATUS_TAXO );

    if ($status_currentterm) {
        $current_step = $status_currentterm[0]->name;
    } else {
		$current_step = "";
	}

    return 	$current_step;

}

function pbvote_get_current_status_color($mypostID)
{

	$color = "";
	$vote_taxo = wp_get_object_terms( $mypostID, array( PB_VOTING_STATUS_TAXO,) );
	if (is_wp_error($vote_taxo)) {
		return false;
	}

	if (! empty( $vote_taxo[0]->term_id)) {
		$temp_term = get_term_meta( $vote_taxo[0]->term_id);
		if (!empty( $temp_term['voting_status_color'][0]) ) {
			$color =  $temp_term['voting_status_color'][0];
		}
	}

	$color = str_replace("#", "", $color);
	return $color;

}

function pom_fun( $input)
{
	$pom = new PbVote_ArchiveDisplayFilterDataImcIssues( $input->get_filter_params());
	$pom1 = $pom->get_query_data();
	return $input;

}
function pbvote_upload_img($file = array(), $parent_post_id, $issue_title, $orientation = null) {

		require_once( ABSPATH . 'wp-admin/includes/admin.php' );

		add_filter( 'sanitize_file_name', 'pbvote_filename_rename_to_hash', 10 );
		$file_return = wp_handle_upload( $file, array(
			'test_form' => false,
			'unique_filename_callback' => 'imc_rename_attachment' // Use this to rename photo
		) );
		remove_filter( 'sanitize_file_name', 'pbvote_filename_rename_to_hash', 10 );

		if( isset( $file_return['error'] ) || isset( $file_return['upload_error_handler'] ) ) {
			return false;
		} else {

		$filename = $file_return['file'];

		if ($orientation) {
			imc_fix_img_orientation( $filename, $file_return['type'], $orientation );
		}

		$attachment = array(
			'post_mime_type' => $file_return['type'],
			'post_title' => mb_convert_encoding(preg_replace( '/\.[^.]+$/', '', basename( $filename ) ), "UTF-8"),
			'post_content' => '',
			'post_status' => 'inherit',
			'guid' => $file_return['url']
		);

		$attachment_id = wp_insert_attachment( $attachment, $file_return['url'], $parent_post_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );

		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		if( 0 < intval( $attachment_id, 10 ) ) {
			return $attachment_id;
		}

	}
	return false;
}

function pbvote_filename_rename_to_hash( $filename )
{
		$info = pathinfo( $filename );
		$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
		$name = basename( $filename, $ext );
		return md5( $name ) . $ext;
}
