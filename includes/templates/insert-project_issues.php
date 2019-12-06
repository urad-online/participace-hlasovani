<?php

/**

 * Template Name: Insert Issue Page

 *

 */


$project_single = null;

wp_enqueue_script('imc-gmap');

if (isset($_GET['votingid'])) {
	$voting_id = $_GET['votingid'];
	$return_url = get_permalink( $voting_id );
} else {
	$voting_id = 0;
	$return_url = get_home_url();
}

$postTitleError = '';

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$project_save = new PbVote_ProjectSaveData;

	$post_id = $project_save->project_insert( $voting_id );

	if($post_id){
		wp_redirect($return_url);
		exit;
	}
}

$project_single = new PbVote_ProjectEdit;

get_header();





/************************** GMAP SETTINGS *************************************/

$map_options = get_option('gmap_settings');

$map_options_initial_lat   		= $map_options["gmap_initial_lat"];
$map_options_initial_lng 			= $map_options["gmap_initial_lng"];
$map_options_initial_zoom 		= $map_options["gmap_initial_zoom"];
$map_options_initial_mscroll 	= $map_options["gmap_mscroll"];
$map_options_initial_bound 		= $map_options["gmap_boundaries"];



/*****************************************************************************/



$plugin_path_url = pbvote_calculate_plugin_base_url();

// checks if the current user has the ability to post anything

$user = wp_get_current_user();



if( is_user_logged_in() ) {

	?>



    <div class="imc-BGColorGray">



        <div class="imc-SingleHeaderStyle imc-BGColorWhite">

            <a href="<?php echo esc_url($return_url); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">

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



                        <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary"><?php echo __('Report a new issue','pb-voting'); ?></h2>

                        <div class="imc-Separator"></div>

						<?php echo $project_single->template_project_edit( array(
								'lat' => $map_options_initial_lat,
								'lon' => $map_options_initial_lng,
							)) ;?>

                        <!-- Issue's Image -->



						<div class="imc-row">

							<span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>

						</div>

                    </div>



                    <div class="imc-row">



						<?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>

                        <input type="hidden" name="submitted" id="submitted" value="true" />

                        <input id="imcInsertIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn" type="submit" value="Odeslat" />

                    </div>

                </form>

            </div> <!-- Form end -->

        </div>

    </div>







<?php } else { ?>



    <div class="imc-BGColorGray">



        <div class="imc-SingleHeaderStyle imc-BGColorWhite">

            <a href="<?php echo esc_url($return_url); ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">

                <i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>

                <span><?php echo __('Return to overview','pb-voting'); ?></span>

            </a>

        </div>



        <div class="imc-Separator"></div>



        <div class="imc-container">



            <div class="imc-CardLayoutStyle imc-ContainerEmptyStyle">

                <img src="<?php echo esc_url($plugin_path_url);?>/img/img_banner.jpg" class="u-full-width">



                <div class="imc-Separator"></div>



                <div class="imc-row imc-CenterContents imc-GiveWhitespaceStyle">



                    <i class="imc-EmptyStateIconStyle material-icons md-huge">vpn_lock</i>



                    <div class="imc-Separator"></div>



                    <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo __('You are not authorised to report an issue','pb-voting'); ?></h3>



                    <div class="imc-Separator"></div>



                    <a href="<?php echo esc_url(wp_login_url()); ?>" class="imc-Text-XL imc-TextMedium imc-LinkStyle"><?php echo __('Please login!','pb-voting'); ?></a>



                    <div class="imc-Separator"></div>

                </div>

            </div>

        </div>

    </div>



<?php } ?>



    <!-- Form validation rules -->

    <script>

        "use strict";
        (function(){

            /*Google Maps API*/
            google.maps.event.addDomListener(window, 'load', imcInitMap);

            jQuery( document ).ready(function() {
                var validator = new FormValidator('report_an_issue_form',
					<?PHP echo $project_single->render_fields_js_validation(); ?>,
				function(errors, events) {
					jQuery('label.imc-ReportFormErrorLabelStyle').html("");
                    if (errors.length > 0) {
                        var i, j;
                        var errorLength;
                        jQuery("#imcReportFormSubmitErrors").html("");
                        jQuery('#postTitleLabel').html();

                        for (i = 0, errorLength = errors.length; i < errorLength; i++) {
                            if (errors[i].name === "featured_image") {
								imcDeleteAttachedImage('imcReportAddImgInput');
								jQuery("#imcReportFormSubmitErrors").html(errors[i].message);
                            } else {
								for(j=0; j < Math.min(1, errors[i].messages.length); j++) {
									/* zobrazuje se jen prvni chyba, validator vraci stejnou chybu pokud je vice praidel */
									jQuery('#'+errors[i].id+'Label').html(errors[i].messages[j]);
									jQuery("#imcReportFormSubmitErrors").append("<p>"+errors[i].message+"</p>");
								}
                            }
                        }
                    } else {
                        jQuery('#imcInsertIssueSubmitBtn').attr('disabled', 'disabled');
                        jQuery('label.imc-ReportFormErrorLabelStyle').html();
                    }
                });
				validator.registerConditional( 'pb_project_js_validate_required', function(field){
					/* povinna pole se validuji pouze pokud narhovatel zaskrtne odeslat k vyhodnoceni
					 plati pro pole s pravidlem "depends" */
					return jQuery('#pb_project_edit_completed').prop('checked');
				});
            });
        })();

        function imcInitMap() {
            "use strict";
            var mapId = "imcReportIssueMapCanvas";
            // Checking the saved latlng on settings
            var lat = parseFloat('<?php echo floatval($map_options_initial_lat); ?>');
            var lng = parseFloat('<?php echo floatval($map_options_initial_lng); ?>');
            if (lat === '' || lng === '' ) { lat = 40.1349854; lng = 22.0264538; }

            // Options casting if empty
            var zoom = parseInt('<?php echo intval($map_options_initial_zoom, 10); ?>', 10);

            if(!zoom){ zoom = 7; }
            var allowScroll;
            '<?php echo intval($map_options_initial_mscroll, 10); ?>' === '1' ? allowScroll = true : allowScroll = false;
            var boundaries = <?php echo json_encode($map_options_initial_bound);?> ?
				<?php echo json_encode($map_options_initial_bound);?>: null;

            imcInitializeMap(lat, lng, mapId, 'imcAddress', true, zoom, allowScroll, JSON.parse(boundaries));
            imcFindAddress('imcAddress', false, lat, lng);
        }

        document.getElementById('imcReportAddImgInput').onchange = function (e) {
            var file = jQuery("#imcReportAddImgInput")[0].files[0];
            // Delete image if "Cancel"
            if (document.getElementById("imcReportAttachedImageThumb")) {
                imcDeleteAttachedImage("imcReportAttachedImageThumb");
            }
            // If image is too big
            // Get filesize
            var maxFileSize = '<?php echo imc_file_upload_max_size(); ?>';
            if(file && file.size < maxFileSize) {
                loadImage.parseMetaData(file, function(data) { //read image metadata to get orientation info
                    var orientation = 0;
                    if (data.exif) {
                        orientation = data.exif.get('Orientation');
                        console.log(orientation);
                    }
                    document.getElementById('imcPhotoOri').value = parseInt(orientation, 10);
                    var loadingImage =	loadImage (
                        file,
                        function (img) {
                            if(img.type === "error") {
                                console.log("Error loading image ");
                                jQuery("#imcReportFormSubmitErrors").html("The Photo field must contain only gif, png, jpg files.").show();
                                if (document.getElementById("imcReportAttachedImageThumb")) {
                                    imcDeleteAttachedImage("imcReportAttachedImageThumb");
                                }
                            } else {
                                if (document.getElementById("imcReportAttachedImageThumb")) {
                                    imcDeleteAttachedImage("imcReportAttachedImageThumb");
                                }
                                img.setAttribute("id", "imcReportAttachedImageThumb");
                                img.setAttribute("alt", "Attached photo");
                                img.setAttribute("class", "imc-ReportAttachedImgStyle u-cf");
                                document.getElementById('imcImageSection').appendChild(img);
                                jQuery("#imcReportFormSubmitErrors").html("");
                                jQuery("#imcNoPhotoAttachedLabel").hide();
                                jQuery("#imcLargePhotoAttachedLabel").hide();
                                jQuery("#imcPhotoAttachedFilename").html(" " + file.name);
                                jQuery("#imcPhotoAttachedLabel").show();
                            }
                        },
                        {
                            maxHeight: 200,
                            orientation: orientation,
                            canvas: true
                        }
                    );
                });
            } else {
                imcDeleteAttachedImage('imcReportAddImgInput');
                e.preventDefault();
                jQuery("#imcNoPhotoAttachedLabel").hide();
                jQuery("#imcPhotoAttachedLabel").hide();
                jQuery("#imcLargePhotoAttachedLabel").show();
            }
        };
    </script>

<?php get_footer(); ?>
