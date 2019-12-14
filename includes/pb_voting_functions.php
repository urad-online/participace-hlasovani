<?php
$generaloptions     = get_option( 'general_settings' );
$voting_enabled     = ( empty($generaloptions["imc_ratings"]))  ? false : $generaloptions["imc_ratings"];
$comments_enabled   = ( empty($generaloptions["imc_comments"])) ? false : $generaloptions["imc_comments"];

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

function pb_voting_set_single_template($single_template)
{
    global $post;
	// return $single_template;

  if ($post->post_type == PB_VOTING_POST_TYPE ) {
		$single_template = PB_VOTE_PATH_TEMPL . '/single-'.$post->post_type.'.php';
	}

  if ($post->post_type == 'imc_issues' ) {
		$single_template = PB_VOTE_PATH_TEMPL . '/single-'.$post->post_type.'.php';
	}

	return $single_template;
}
function pb_voting_set_archive_template($archive_template) {
    global $post;
	// return $single_template;

    if ($post->post_type == PB_VOTING_POST_TYPE ) {
		$archive_template = PB_VOTE_PATH_TEMPL . '/archive-'.$post->post_type.'.php';
	}
	return $archive_template;
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

function pbvote_user_can_edit($post_id, $current_user) {

	$status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
    $terms_count = count($status_terms);
    if ( $terms_count > 2 ) {
        $edit_statuses = array($status_terms[0]->term_id, $status_terms[1]->term_id);
    } elseif ($terms_count > 1) {
        $edit_statuses = array($status_terms[0]->term_id, );
    } else {
        $edit_statuses = array();
    }

	// Issue is not current user's
	$my_issue = get_post($post_id);
	$author_id = intval($my_issue ->post_author, 10); // Author's id of current #post

	if($author_id == $current_user) {
		return in_array( getCurrentImcStatusID($post_id), $edit_statuses , true);
	}

	return false;
}

function pbvote_calculate_plugin_base_url() {
	$url_full_path = plugin_basename( __FILE__ );
	$url_pieces = explode("/", $url_full_path);
	return plugin_dir_url( '' ). $url_pieces[0];
}

function pbvote_insert_cat_dropdown( $taxonomy = 'my_custom_taxonomy', $selected_term_id = 0) {

	function create_select_with_grandchildren( $fieldName, $selected_term_id  ) {
		$args = array('hide_empty' => false, 'hierarchical' => true, 'parent' => 0);
		$terms = get_terms('imccategory', $args);

		$html = '';
		$html .= '<select name="' . $fieldName . '" id="'.$fieldName.'"class="' . $fieldName . ' "' . '>';

		$html .= '<option value="" class="imc-CustomOptionDisabledStyle" disabled selected>'.__('Select a category','pb-voting').'</option>';

		foreach ( $terms as $term ) {
			$selected = ((!empty( $selected_term_id)) && ( $selected_term_id == $term->term_id )) ? "selected" : "";
			$html .= '<option class="imc-CustomOptionParentStyle" '.$selected.' value="' . $term->term_id . '" >'.$term->name.'</option>';

			$args = array(
				'hide_empty'    => false,
				'hierarchical'  => true,
				'parent'        => $term->term_id
			);
			$childterms = get_terms('imccategory', $args);

			foreach ( $childterms as $childterm ) {
				$html .= '<option class="imc-CustomOptionChildStyle" value="' . $childterm->term_id . '">&nbsp; ' . $childterm->name . '</option>';

				$args = array('hide_empty' => false, 'hierarchical'  => true, 'parent' => $childterm->term_id);
				$granchildterms = get_terms('imccategory', $args);

				foreach ( $granchildterms as $granchild ) {
					$html .= '<option class="imc-CustomOptionGrandchildStyle" value="' . $granchild->term_id . '">&nbsp;&nbsp; ' . $granchild->name . '</option>';
				}
			}
		}
		$html .=  "</select>";

		return $html;
	}

	$selector = create_select_with_grandchildren( $taxonomy, $selected_term_id);
	return $selector;

}

function pom_fun( $input)
{
	$pom = get_included_files();

	$output = array_search( "D:\web\parti\wp-admin\includes\image.php", $pom);
	$output = array_search( "D:\web\parti\wp-admin\includes\image-edit.php", $pom);
	return $output;
}
function get_parent_url_by_taxo( $issue_id = 0, $taxo = "voting-period")
{
		$term_slug = get_parent_taxo_slug( $issue_id, $taxo );
		if ( empty( $term_slug ) ) {
			return "";
		}

		$query_args = array(
			'post_type' => PB_VOTING_POST_TYPE,
			'posts_per_page' => 1,
			'tax_query' => array(array(
				'taxonomy' => $taxo,
				'field' => 'slug',
				'terms' => $term_slug,
			)),
		);

		$pom = get_posts( $query_args );
		if ( (is_array($pom)) && (count($pom) > 0)) {
			return esc_url( get_permalink($pom[0]->ID));
		}

	return "";

}

function get_parent_taxo_slug( $issue_id = 0, $taxo = "voting-period")
{
	$terms = get_the_terms( $issue_id, $taxo );

	if ( $terms && ! is_wp_error( $terms ) ) {
			return $terms[0]->slug;
	}
	return "";
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

function pbvote_filename_rename_to_hash( $filename ) {
	$info = pathinfo( $filename );
	$ext  = empty( $info['extension'] ) ? '' : '.' . $info['extension'];
	$name = basename( $filename, $ext );
	return md5( $name ) . $ext;
}
function add_link_to_voting( $parent_id, $post_id )
{
		$items =  get_post_meta( $parent_id, "_pods_items", true);
		array_push( $items, $post_id );
		// $new_items =  json_encode( $newitems);
		update_post_meta( $parent_id, "_pods_items", $items);
}
