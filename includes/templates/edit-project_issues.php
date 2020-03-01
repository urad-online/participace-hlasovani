pbVoteIssueSubmitBtn<?php
/**
 * Template Name: Edit Issue Page
 *
 */

$project_single = null;
$page_content = $post->post_content;
wp_enqueue_script('imc-gmap');

$user = wp_get_current_user();

$safe_inserted_id = intval( $_GET['edit_id'] );
$safe_inserted_id = sanitize_text_field( $safe_inserted_id );
$given_issue_id   = $safe_inserted_id;

$return_url = get_parent_url_by_taxo( $safe_inserted_id );

$postTitleError = '';
$all_status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$project_save = new PbVote_ProjectSaveData;

	$post_id = $project_save->update_project();
	if (is_wp_error($post_id)) {
		$errors = $post_id->get_error_messages();
		foreach ($errors as $error) {
			echo $error;
		}
		exit;
	}

	if($post_id){
		wp_redirect($return_url);
		exit;
	}

}

/************************** GMAP SETTINGS *************************************/
$map_options = get_option('gmap_settings');
$map_options_initial_lat = $map_options["gmap_initial_lat"];
$map_options_initial_lng = $map_options["gmap_initial_lng"];
/*****************************************************************************/

/* create instance of class for form rendering */
$project_single = new PbVote_RenderFormEdit;


// checks if the current user has the ability to post anything

$issue_for_edit = get_post($given_issue_id);

$pb_project_meta = get_post_meta($safe_inserted_id);
$pb_project_meta[ 'issue_image'][0] = wp_get_attachment_url( get_post_thumbnail_id($given_issue_id) );
$pb_project_meta[ 'postTitle'][0] = get_the_title($given_issue_id);
$pb_project_meta[ 'postContent'][0] = $issue_for_edit->post_content;
$pb_project_meta[ 'postAddress'] = $pb_project_meta[ 'imc_address'];
$imccategory_currentterm = get_the_terms($given_issue_id , 'imccategory' );
if ($imccategory_currentterm) {
	$pb_project_meta[ 'my_custom_taxonomy'][0] = $imccategory_currentterm[0]->term_id;
}


$pb_project_status = wp_get_object_terms($given_issue_id, 'imcstatus');
$pb_project_edit_completed = '0';
foreach ($all_status_terms as $key => $term ) {
	if ( $term->slug == $pb_project_status[0]->slug) {
		$pb_project_edit_completed = $key;
	}
}
if ($pb_project_edit_completed == '0') {
	$pb_project_meta[ 'pb_project_edit_completed'][0] = "0";
} else {
	$pb_project_meta[ 'pb_project_edit_completed'][0] = "1";
}

$issue_address 	= $pb_project_meta[ 'imc_address'][0];
$issue_lat 		= $pb_project_meta[ 'imc_lat'][0];
$issue_lng 		= $pb_project_meta[ 'imc_lng'][0];

$form_map_options['lat']    = $pb_project_meta[ 'imc_lat'][0];;
$form_map_options['lng']    = $pb_project_meta[ 'imc_lng'][0];
$form_map_options['zoom']    = $map_options["gmap_initial_zoom"];
$form_map_options['mscroll'] = $map_options["gmap_mscroll"];
$form_map_options['bound']   = json_decode($map_options["gmap_boundaries"]);

wp_localize_script('pb-formvalidator', 'formValidatorData', array(
	'rules' 	=> $project_single->render_fields_js_validation(),
	'mapData' => $form_map_options,
	'budgetTable' => $project_single->get_field_property( 'cost', 'limit'),
	'fileSize'    => $project_single->get_field_property( 'photo', 'max_size'),
));

get_header();
$plugin_path_url = pbvote_calculate_plugin_base_url();
?>
<div class="imc-BGColorGray">
	<div class="imc-SingleHeaderStyle imc-BGColorWhite">
		<div class="imc-container">
			<?php echo apply_filters( 'the_content', $page_content ); ?>
		</div>
	</div>
</div>
<?php
if(pbvote_user_can_edit($given_issue_id, $user->ID)) { ?>
    <!-- <div class="imc-BGColorGray"> -->
    <div>

        <div class="imc-SingleHeaderStyle imc-BGColorWhite">
            <a href="<?php echo esc_url( $return_url ); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">
                <i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>
                <span><?php echo __('Return to overview','pb-voting'); ?></span>
            </a>
        </div>

        <div class="imc-Separator"></div>

        <div class="imc-container">

            <!-- INSERT FORM BEGIN -->
            <div id="insert_form_wrapper">
                <form name="report_an_issue_form" action="" id="primaryPostForm" method="POST" enctype="multipart/form-data">

                    <div class="imc-CardLayoutStyle">

                        <div class="imc-row-no-margin">

                            <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary u-pull-left"><?php echo __('Edit issue','pb-voting'); ?></h2>
                            <div class="u-pull-right">

                                <span class="imc-Text-MD imc-TextColorSecondary imc-TextBold imc-FontRoboto">#</span>
                                <span class="imc-Text-MD imc-TextColorSecondary imc-TextMedium imc-FontRoboto"><?php echo esc_html($given_issue_id); ?></span>
                            </div>
                        </div>


                        <div class="imc-Separator"></div>

						<?php	echo $project_single->render_form_edit(
										array(
											'lat' => $form_map_options[ 'lat'],
											'lon' => $form_map_options[ 'lng'],
										),
										$pb_project_meta
										) ;
										?>

                        <div class="imc-row">
                            <span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>
                        </div>

                    </div>

                    <div class="imc-row">
						<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                        <input type="hidden" name="submitted" id="submitted" value="true" />
                        <input id="pbVoteIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn"
							type="submit" value="Odeslat" />
                    </div>

                </form>
            </div> <!-- Form end -->
        </div>
    </div>


<?php } else { ?>

    <div class="imc-BGColorGray">

        <div class="imc-SingleHeaderStyle imc-BGColorWhite">
            <a href="<?php echo esc_url( $return_url ); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">
                <i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>
                <span><?php echo __('Return to overview','pb-voting'); ?></span>
            </a>
        </div>

        <div class="imc-Separator"></div>

        <div class="imc-container">

            <div class="imc-CardLayoutStyle imc-ContainerEmptyStyle" style="text-align:center;">
                <img src="<?php echo esc_url($plugin_path_url);?>/img/img_banner.jpg" class="">

                <div class="imc-Separator"></div>

                <div class="imc-row imc-CenterContents imc-GiveWhitespaceStyle">

                    <i class="imc-EmptyStateIconStyle material-icons md-huge">vpn_lock</i>

                    <div class="imc-Separator"></div>

                    <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo __('You are not authorised to edit an issue','pb-voting'); ?></h3>

                    <div class="imc-Separator"></div>

                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="imc-Text-XL imc-TextMedium imc-LinkStyle"><?php echo __('Please login!','pb-voting'); ?></a>

                    <div class="imc-Separator"></div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>

<?php get_footer(); ?>
