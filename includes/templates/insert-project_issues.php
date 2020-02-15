<?php

/**

 * Template Name: Insert Issue Page

 *

 */
$page_content = $post->post_content;

if (isset($_GET['votingid'])) {
	$atts = array(
		'voting_id' => $_GET['votingid'],
	);
	$voting_id = $_GET['votingid'];
}

$project_new = new PbVote_ProjectInsert( $atts);
if ( $project_new->is_submitted() ) {
	$result = $project_new->save_data() ;
	if ( $result) {
		wp_redirect($project_new->return_url);
		exit;
	}
} else {
	get_header();
	?>
	<div>
	<div class="imc-BGColorGray">
		<div class="imc-SingleHeaderStyle imc-BGColorWhite">
			<div class="imc-container">
				<?php echo apply_filters( 'the_content', $page_content ); ?>
			</div>
		</div>
	</div>
	<div class="imc-SingleHeaderStyle imc-BGColorWhite">

	<a href="<?php echo esc_url($project_new->return_url); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">

	<i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>

	<span><?php echo __('Return to overview','pb-voting'); ?></span>

	</a>

	</div>
	<?PHP
	echo $project_new->render_form() ;
	get_footer();
}
