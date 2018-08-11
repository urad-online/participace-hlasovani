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
		$get_code = new PbVote_LimeSurveyTokens( DELIVERY_MSG_TYPE);
		if ( (!empty( $request['voter_id'])) && (! empty($request['voting_id']))) {
			$output = $get_code->get_code( $request);
		} else {
			$output = array( 'result' => 'error', 'message' => 'Neni zadán identifikátor hlasujícího',);
		}
	} else {
		$output = $google_cap->get_error();
	}

    echo json_encode( $output ) ;

    wp_die(); // this is required to terminate immediately and return a proper response
}

function pb_votimg_set_single_template($single_template) {
    global $post;

    $single_template = dirname( __FILE__ ) . '/templates/single-'.$post->post_type.'.php';

    return $single_template;
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
