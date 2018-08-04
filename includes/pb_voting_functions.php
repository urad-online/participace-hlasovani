<?php
function pb_vote_get_registration_widget( $atts, $content, $tag)
{
	$new_widget = new PbVote_GenWidget( $atts);

	return $new_widget->render_widget() ;
}

function pbvote_get_code()
{
    $request = $_POST;
    // $get_code = new PbVote_GetCode( 'Sms');
    // $get_code = new PbVote_GetCode( 'Email');
    $get_code = new PbVote_LimeSurveyTokens( 'Email');
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
