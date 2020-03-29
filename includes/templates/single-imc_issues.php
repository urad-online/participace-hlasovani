<?php
/**
 * The template for displaying all single issues and attachments
 *
 */
get_header();
global $comments_enabled ;
wp_enqueue_script('imc-gmap');

$issue_id = $post->ID;
$project_single = new PbVote_ProjectSingle();
$control_pages = new PbVote_ControlPages( $issue_id);
$issue_rating = new PbVote_ThumbRating();

if(isset($_POST['submitted']) && isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {

	$old_likes = intval(get_post_meta($issue_id, "imc_likes", true), 10);
	$new_likes = $old_likes + 1;

	$current_user = get_current_user_id();
	add_post_meta($issue_id, "imc_allvoters", $current_user, false);

	global $wpdb;
	$imc_votes_table_name = $wpdb->prefix . 'imc_votes';
	$wpdb->insert(
		$imc_votes_table_name,
		array(
			'issueid' => $issue_id,
			'created' => gmdate("Y-m-d H:i:s",time()),
			'created_by' => $current_user,
		)
	);

	$update = update_post_meta($issue_id, "imc_likes", $new_likes, $old_likes);

	if ($update) {
		wp_redirect(get_permalink($issue_id));
		exit;
	}
}

$editpage = $control_pages->get_url_edit();
$listpage = get_parent_url_by_taxo($issue_id);

if ( get_option('permalink_structure') ) { $perma_structure = true; } else {$perma_structure = false;}

if( $perma_structure){$parameter_pass = '/?edit_id=';} else{$parameter_pass = '&edit_id=';}

$plugin_path_url = imc_calculate_plugin_base_url();

// $pb_project_single = new pbProjectSingle;

?>

    <div class="imc-BGColorGray">

        <!--Start the loop.-->
		<?php
		while ( have_posts() ) : the_post();
				$issue_id = get_the_ID();
				$user_id = get_current_user_id();
				$user = wp_get_current_user();
				$issue_status = get_post_status( $issue_id );	?>

        <div class="imc-SingleHeaderStyle imc-BGColorWhite">

            <a href="<?php echo $listpage ; ?>" class="u-pull-left imc-SingleHeaderLinkStyle ">
                <i class="material-icons md-36 imc-SingleHeaderIconStyle">keyboard_arrow_left</i>
                <span><?php echo __('Return to overview','pb-voting');  ?></span>
            </a>
						<?php if(pbvote_user_can_edit(get_the_ID(), $user_id)) { ?>
                <a href="<?php echo esc_url( $editpage . $parameter_pass . $issue_id ); ?>" class="u-pull-right imc-SingleHeaderLinkStyle">
                    <i class="material-icons md-36 imc-SingleHeaderIconStyle">mode_edit</i>
                    <span class="imc-hidden-xs imc-hidden-sm"><?php echo __('Edit issue','pb-voting');  ?></span>
                </a>
						<?php } ?>
        </div>

				<?php if ($issue_status !== 'publish') { ?>
	            <div class="imc-SingleHeaderStyle imc-BGColorRed">
	                <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary imc-CenterContents" style="line-height: 60px;">
									<?php echo __('Under Moderation','pb-voting');  ?></h2>
	            </div>
				<?php } ?>

            <div class="imc-Separator"></div>

            <div class="imc-container">

                <div id="issue-<?php echo esc_attr($issue_id); ?>" class="issue-<?php echo esc_attr($issue_id); ?> imc_issues type-imc_issues status-publish" >

                    <div class="imc-row">
                        <div class="imc-grid-8 imc-columns">
                            <div class="imc-CardLayoutStyle">
                                <div class="imc-row">
																		<?php $imccategory_currentterm = get_the_terms($issue_id , 'imccategory' );
																		if ($imccategory_currentterm) {
																			$current_category_name = $imccategory_currentterm[0]->name;
																			$current_category_id = $imccategory_currentterm[0]->term_id;
																			$term_thumb = get_term_by('id', $current_category_id, 'imccategory');
																			$cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
																		}?>
								                    <div class="imc-grid-2 imc-columns" hidden>
																			<?php if ( $cat_thumb_arr ) { ?>
									                            <img src="<?php echo esc_url($cat_thumb_arr[0]); ?>" class="imc-SingleCategoryIcon">
																			<?php }	else { ?>
									                            <img src="<?php echo esc_url(esc_url($plugin_path_url));?>/img/ic_default_cat.png" class="imc-SingleCategoryIcon">
																			<?php } ?>
							                        <div class="imc-row-no-margin imc-CenterContents">
							                            <span class="imc-Text-SM imc-TextColorSecondary imc-TextBold imc-FontRoboto">#</span>
							                            <span class="imc-Text-SM imc-TextColorSecondary imc-TextMedium imc-FontRoboto"><?php echo esc_html(the_ID()); ?></span>
							                        </div>
								                    </div>
								                    <div class="imc-grid-10 imc-columns">
																		<?php the_title( '<h2 class="imc-PageTitleTextStyle imc-TextColorPrimary">', '</h2>' );?>
                                    <p class="imc-SingleCategoryTextStyle imc-Text-LG imc-TextColorSecondary"><?php echo esc_html($current_category_name); ?> </p>
								                    </div>
                                </div>

                                <div class="imc-row">
                                    <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">access_time</i>
                                    <span class="imc-SingleInformationTextStyle imc-TextColorSecondary imc-FontRoboto imc-TextMedium imc-Text-SM">
																			<?php the_date(get_option('date_format')); ?>
																		</span>

                                    <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">person</i>
                                    <span class="imc-SingleInformationTextStyle imc-TextColorSecondary imc-FontRoboto imc-TextMedium imc-Text-SM">
																			<?php the_author(); ?>
																		</span>
																		<?php echo $issue_rating->show_rating_number( $issue_id, $issue_status) ?>
                                </div>

																<?php if ($issue_status == 'publish') { ?>

		                                <div class="imc-row">
																				<?php $imcstatus_currentterm = get_the_terms($issue_id , 'imcstatus' );
																				if ($imcstatus_currentterm) {
																					$current_step_name = $imcstatus_currentterm[0]->name;
																					$current_order_step_id = get_term_meta( $imcstatus_currentterm[0]->term_id, 'imc_term_order');

																					$term_color_data = get_option('tax_imcstatus_color_' . $imcstatus_currentterm[0]->term_id);
																					$step_color = $term_color_data;
																				} ?>

																				<ul class="imc-progress-indicator">
																						<?php // Calculate grid based on number of Statuses
																						$all_steps = get_terms( 'imcstatus', 'order=ASC&hide_empty=0' );

																						if ( ! empty( $all_steps ) && ! is_wp_error( $all_steps ) ) {
																							foreach ( $all_steps as $step ) {
																								$color = get_option('tax_imcstatus_color_' . $step->term_id);
																								$step_order_id = get_term_meta( $step->term_id, 'imc_term_order');
																								if ($step_order_id[0] <= $current_order_step_id[0] ) { ?>
			                                              <style>
			                                                  .imc-progress-indicator > li .bubble:before, .imc-progress-indicator > li.imc-stepId-<?php echo esc_attr($step->term_id); ?> .bubble:after{
			                                                      background-color: #<?php echo esc_attr($color);?>;
			                                                      border-color: #<?php echo esc_attr($color);?>;
			                                                  }
			                                              </style>

											                              <li class="imc-stepId-<?php echo esc_attr($step->term_id); ?> imc-FontRobotoSlab imc-Text-XS imc-TextBold imc-TextBold" style="color: rgba(0, 0, 0, 0.87);">
																												<span class="bubble" style="background-color: #<?php echo esc_attr($color);?>; color: #<?php echo esc_attr($color);?>; "></span>
																												<?php echo esc_html($step->name); ?>
																										</li>

																								<?php }	else { ?>
			                                              <style>
			                                                  .imc-progress-indicator > li .bubble:before, .imc-progress-indicator > li.imc-stepId-<?php echo esc_attr($step->term_id); ?> .bubble:after {
			                                                      background-color: #dddddd;
			                                                      border-color: #dddddd;
			                                                  }
			                                              </style>
			                                              <li class="imc-stepId-<?php echo esc_attr($step->term_id); ?> imc-FontRobotoSlab imc-Text-XS imc-TextBold imc-TextBold" style="color: rgba(0, 0, 0, 0.3);">
																												<span class="bubble" style="background-color: #dddddd; color: #dddddd;"></span>
																												<?php echo esc_html($step->name); ?>
																										</li>
																								<?php }
																							}
																						} ?>
			                                  </ul>
		                                </div>
																<?php } ?>

																<?php
																if (get_the_content()) { ?>
                                    <div class="imc-row">
                                        <h3 class="imc-SectionTitleTextStyle"><?php echo __('Popis projektu','pb-voting'); ?></h3>
                                        <div class="imc-SingleDescriptionStyle imc-TextColorSecondary imc-JustifyText"><?php the_content(); ?></div>
                                    </div>
																<?php } ?>
                                <div class="imc-row-no-margin">
																		<?php $img_url = wp_get_attachment_url( get_post_thumbnail_id($issue_id) ); ?>
                                    <h3 class="imc-SectionTitleTextStyle"><?php echo __('Ilustrační obrázek','pb-voting'); ?></h3>

																		<?php if ($img_url) { ?>
                                        <a href="<?php echo esc_url($img_url); ?>" target="_blank"> <?php the_post_thumbnail('thumbnail'); ?> </a> <!--thumbnail medium large full-->
																		<?php } else { ?>
                                        <div class="imc-row imc-CenterContents">
                                            <i class="material-icons md-huge imc-TextColorHint">landscape</i>
                                            <span class="imc-NotAvailableTextStyle imc-TextColorHint imc-DisplayBlock"><?php echo __('No photos submitted', 'pb-voting'); ?></span>
                                        </div>
																		<?php }?>
                                </div>
																<hr class="imc-HorizontalWhitespaceSeparator" style="padding-top:10px">
																<?php
																$project_meta = get_post_meta( $issue_id );
																echo $project_single->template_part_pb_project( $project_meta );
																?>
                            </div> <!--End Card-->

														<?php if (($issue_status == 'publish') && ($comments_enabled)) { ?>
                                <div class="imc-CardLayoutStyle">
                                    <h3 class="imc-SectionTitleTextStyle"><?php echo __('Comments','pb-voting'); ?></h3>
																		<?php if ( comments_open() || get_comments_number() ) {
																				$comments = get_comments(array( 'post_id' => $issue_id)); ?>
																				<?php if ( is_user_logged_in() ) { ?>
                                            <div class="imc-CommentsFormWrapperStyle imc-row">

																								<?php
																								$comments_number = get_comments_number();
																								if ( $comments_number == 0 ) {
																									$comments_string = __('No Comments', 'pb-voting');
																								} elseif ( $comments_number > 1 ) {
																									$comments_string = $comments_number . __(' Comments', 'pb-voting');
																								} else {
																									$comments_string = __('1 Comment', 'pb-voting');
																								}

																								/* Customizing comments form */
																								$comment_args = array(
																									'id_form'           => 'commentform_custom',
																									'class_form'      	=> 'imc-CommentFormStyle',
																									'id_submit'         => 'imc-submit',
																									'class_submit'      => 'imc-button imc-button-primary u-pull-right',
																									'name_submit'       => 'imc-submit',
																									'label_submit'      => __( 'Post Comment', 'pb-voting' ),
																									'format'            => 'xhtml',
																									'comment_field' =>  '<div class="imc-row-no-margin"><p class="comment-form-comment"><label for="comment"></label>'.
																									                    '<textarea placeholder="'. __('Add a comment','pb-voting') . '" class="imc-InputStyle imc-CommentTextArea" id="comment_custom" name="comment" rows="2" aria-required="true">' .
																									                    '</textarea></p></div>',
																									'must_log_in' => '<p class="must-log-in">' .
																									                 sprintf(
																										                 __( 'You must be <a class="imc-LinkStyle" href="%s">logged in</a> to post a comment.', 'pb-voting' ),
																										                 wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
																									                 ) . '</p>',
																									'logged_in_as' => '<div class="imc-row-no-margin"><span class="comment-count">'.esc_html($comments_string).'</span><p class="logged-in-as">' .
																									                  sprintf(
																										                  __( 'Logged in as <span class="imc-TextColorPrimary">%2$s</span>. <a class="imc-LinkStyle" href="%3$s" title="Log out of this account">Log out?</a>','pb-voting' ),
																										                  admin_url( 'profile.php' ),
																										                  $user_identity,
																										                  wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
																									                  ) . '</p></div>',
																									'comment_notes_before' => '',
																									'comment_notes_after' => '',
																								);

																								comment_form($comment_args); ?>

                                            </div>
																				<?php } else {
																						if (empty($comments)) { ?>
                                                <div class="imc-row imc-CenterContents">
                                                    <i class="material-icons md-huge imc-TextColorHint">comment</i>
                                                    <span class="imc-NotAvailableTextStyle imc-TextColorHint imc-DisplayBlock"><?php echo __('No comments submitted', 'pb-voting'); ?></span>
                                                </div>
																						<?php } else {  ?>
                                                <div class="imc-row imc-CommentsFormWrapperStyle imc-CommentsCounterStyle"><span class="comment-count"><?php echo intval($comments[0]->comment_count).'&nbsp;'.__('comments','pb-voting'); ?></span></div>
																						<?php }
																				} ?>

                                        <div class="imc-CommentsWrapperStyle imc-row">
																						<?php foreach($comments as $comment) {

																								$comment_id = $comment->comment_ID;
																								$comment_message = $comment->comment_content;
																								$comment_author = $comment->comment_author;

																								$user_object = new WP_User($comment->user_id);
																								$roles = $user_object->roles;
																								$role = array_shift($roles);

																								if ($role === 'administrator') {
																									$args = array( 'class' => 'imc-CommentAuthorIconStyle imc-AdminIconStyle');
																								} else {
																									$args = array( 'class' => 'imc-CommentAuthorIconStyle imc-PlainUserIconStyle');
																								}

																								/* Check if comment author is the logged in user */
																								$author_is_me = false;
																								if ( intval ($comment->user_id, 10) === intval($current_user->ID, 10) )  {
																									$author_is_me = true;
																								}

																								$approved_comment = $comment->comment_approved;
																								if($approved_comment) {
																									$comment_class = '';
																									$comment_pending_string = '';
																								} else {
																									$comment_class = 'imc-CommentPending';
																									$comment_pending_string = __('Pending','pb-voting');
																								}

																								if (intval ($approved_comment) === 0) {

																									if ( !is_user_logged_in() ) {
																										continue;
																									}
																									if(!current_user_can( 'administrator' ) && !$author_is_me){
																										continue;
																									}

																								} ?>
                                                <div class="<?php echo esc_attr($comment_class); ?> imc-CommentStyle imc-row">

                                                    <div class="imc-grid-1 imc-column imc-hidden-sm">
																												<?php $commenter_role = "";

																												if ($role === 'administrator') {
																													$commenter_role = "&nbsp;&bull;&nbsp;&nbsp;" .__('Administrator','pb-voting');
																													$comment_avatar = "ic_avatar_admin.png";
																												}	else if ( intval ($comment->user_id, 10) === intval(get_the_author_meta('ID')) ) {

																													$commenter_role = "&nbsp;&bull;&nbsp;&nbsp;" .__('Issue author','pb-voting');

																													if($author_is_me) {
																														$comment_avatar = "ic_avatar_author_me.png";
																													} else {
																														$comment_avatar = "ic_avatar_author.png";
																													}
																												} else {
																													if($author_is_me) {
																														$comment_avatar = "ic_avatar_user_me.png";
																													} else {
																														$comment_avatar = "ic_avatar_user.png";
																													}
																												} ?>

                                                        <img src="<?php echo esc_url($plugin_path_url);?>/img/<?php echo $comment_avatar; ?>" class="imc-CommentIconStyle">

                                                    </div>

                                                    <div class="imc-grid-11 imc-columns">
                                                        <div class="imc-row-no-margin">
                                                            <span class="imc-CommentAuthorStyle"><?php echo esc_html($comment_author);?></span>

                                                            <span class="imc-CommentMetaStyle"><?php echo esc_html($commenter_role); ?> </span>
                                                            <span class="imc-CommentMetaStyle">&nbsp;&bull;&nbsp;&nbsp;<?php echo get_comment_date( get_option( 'date_format' ), $comment_id); ?> - <?php echo get_comment_time( "G:i", $gmt = false, $translate = true );?> </span>
                                                            <span class="imc-ColorRed imc-CommentPendingLabelStyle u-pull-right">&nbsp;&nbsp;&nbsp;<?php echo esc_html($comment_pending_string); ?> </span>
                                                        </div>

                                                        <div class="imc-row">
                                                            <div class="imc-CommentDetailsStyle"> <?php echo esc_html($comment_message); ?></div>
                                                        </div>
                                                    </div>

                                                </div>
																						<?php } ?>

                                        </div>

																		<?php } else { ?>
                                        <h3 class="imc-NotAvailableTextStyle imc-TextColorSecondary"><?php echo __('Comments are disabled','pb-voting'); ?></h3>
																		<?php } ?>

                                </div> <!--End Card-->

														<?php } ?>

                        </div>

                        <!-- Start Column 2 -->
                        <div class="imc-grid-4 imc-columns">

							<?php $adminMsgs = imc_show_issue_message(get_the_ID(), get_current_user_id());
							if ($adminMsgs) { ?>
                                <div class="imc-CardLayoutStyle">
                                    <h3 class="imc-SectionTitleTextStyle">
                                        <i class="material-icons md-24 imc-ColorRed imc-AlignIconToButton">error</i>&nbsp;<?php echo __('Messages','pb-voting'); ?></h3>
                                    <span class="imc-SingleTimelineItemDescStyle imc-TextColorPrimary"><?php echo esc_html($adminMsgs); ?></span>
                                </div>
							<?php } ?>

                            <!--Map-->
                            <div class="imc-CardLayoutStyle">
                                <h3 class="imc-SectionTitleTextStyle"><?php echo __('Location','pb-voting'); ?></h3>
                                <div id="imcSingleIssueMapCanvas" class="imc-SingleMapCanvasStyle"></div>
                                <div class="imc-row-no-margin">
                                    <i class="material-icons md-24 imc-TextColorSecondary imc-VerticalAlignMiddle">place</i>
                                    <span class="imc-FontRoboto imc-TextBold imc-Text-XS imc-TextColorSecondary"> <?php echo esc_html(get_post_meta($issue_id, "imc_address", true)); ?></span>
                                </div>
                            </div>

							<?php
							if (($issue_status == 'publish') ){

								// Check if user can vote
								$voterslist = get_post_meta($issue_id, "imc_allvoters", false);

								if ( PB_RATING_ENABLED ) {
										if ( is_user_logged_in() ) { ?>
                        <form action="" id="increaseBtn" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="submitted" id="submitted" value="true"/>
														<?php
														wp_nonce_field('post_nonce', 'post_nonce_field');

														if ($user_id === intval(get_the_author_meta('ID'), 10) ) { ?>
				                        <div class="imc-CardLayoutStyle imc-CenterContents">
				                            <img alt="My Issue icon" class="imc-VerticalAlignMiddle"
				                                 title="<?php echo __('My Issue', 'pb-voting'); ?>"
				                                 src="<?php echo esc_url($plugin_path_url);?>/img/ic_my_issue_grid.png')); ?>">
				                            <span
				                                    class="imc-Text-MD imc-TextMedium imc-TextColorSecondary imc-FontRoboto"><?php echo __('My issue', 'pb-voting'); ?></span>
				                        </div>
														<?php } else {
																$hasVoted = false;
																if ($voterslist) {
																	foreach ($voterslist as $voter) {
																		if (intval($voter, 10) === intval($user_id, 10)) {
																			$hasVoted = true;
																		}
																	}
																}
																if ($hasVoted) { ?>
					                          <button type="submit"
					                                  class="imc-button imc-button-primary-disabled imc-button-block"
					                                  disabled>
					                              <i class="material-icons md-18 imc-VerticalAlignMiddle">thumb_up</i>
					                              <span
					                                      class="imc-Text-MD imc-TextRegular imc-FontRoboto">&nbsp; <?php echo __('Voted', 'pb-voting'); ?></span>
					                          </button>
																<?php } else { ?>
						                        <button type="submit"
						                                class="u-full-width imc-button imc-button-primary imc-button-block">
						                            <i class="material-icons md-18 imc-VerticalAlignMiddle">thumb_up</i>
						                            <span class="imc-Text-MD imc-TextRegular imc-FontRoboto">&nbsp; <?php echo __('Vote', 'pb-voting'); ?></span>
						                        </button>
																<?php }
														} ?>

		                    </form>
										<?php } else { ?>
												<div class="imc-CardLayoutStyle imc-CenterContents">
													 <i class="material-icons md-18 imc-AlignIconToLabel">thumb_up</i>
													 <?PHP echo render_rating_help_link("", "24", ""); ?>
											 </div>
										<?PHP }
								}?>

                <!-- Start Issue Timeline -->
		                <div class="imc-CardLayoutStyle">
		                    <h3 class="imc-SectionTitleTextStyle"><?php echo __('Timeline','pb-voting'); ?></h3>

									<?php

									$timeline = imc_get_issue_timeline($issue_id);

									// If there is only one item, show it
									if (count($timeline) == 1) { ?>

                      <div class="imc-row-no-margin">
                          <span class="imc-SingleTimelineStepCircleStyle imc-circle" style="background-color: #<?php echo esc_attr($timeline[0]->color);?> "></span>
                          <span class="imc-SingleTimelineStepTitleStyle imc-TextColorPrimary"><?php echo esc_html($timeline[0]->title); ?></span>
                      </div>

                      <div class="imc-row-no-margin imc-SingleTimelineItemStyle imc-SingleTimelineLastItem">
                          <span class="imc-SingleTimelineItemDescStyle imc-TextColorPrimary"><?php echo esc_html($timeline[0]->description); ?></span>

                          <span class="imc-SingleTimelineItemFooterTextStyle imc-TextColorPrimary"><?php echo imc_calculate_relative_date($timeline[0]->dateTimestamp), ' ', __('by','pb-voting'), ' ', esc_html($timeline[0]->name); ?></span>
                      </div>

									<?php  } else {

										// Pop last element of array to show it last with small styling changes
										$last_tml_item = array_pop($timeline);

										?>

                      <div class="imc-row-no-margin">
                          <span class="imc-SingleTimelineStepCircleStyle imc-circle" style="background-color: #<?php echo esc_attr($timeline[0]->color);?> "></span>
                          <span class="imc-SingleTimelineStepTitleStyle imc-TextColorPrimary"><?php echo esc_html($timeline[0]->title); ?></span>
                      </div>

                      <div class="imc-row imc-SingleTimelineItemStyle" style="border-left: 3px solid rgba(0,0,0,0.23);">
                          <span class="imc-SingleTimelineItemDescStyle imc-TextColorPrimary"><?php echo esc_html($timeline[0]->description); ?></span>
                          <span class="imc-SingleTimelineItemFooterTextStyle imc-TextColorPrimary"><?php echo esc_html(imc_calculate_relative_date($timeline[0]->dateTimestamp)), ' ', __('by','pb-voting'), ' ', esc_html($timeline[0]->name); ?></span>
                      </div>

										<?php // Loop through the rest
										$rest_tml_items = array_slice($timeline, 1);

										foreach ($rest_tml_items as $val) {	?>

                          <div class="imc-row-no-margin">
                              <span class="imc-SingleTimelineStepCircleStyle imc-circle" style="background-color: #<?php echo esc_attr($val->color);?> "></span>
                              <span class="imc-SingleTimelineStepTitleStyle imc-TextColorPrimary"><?php echo esc_html($val->title); ?></span>
                          </div>

                          <div class="imc-row imc-SingleTimelineItemStyle" style="border-left: 3px solid rgba(0,0,0,0.12);">
                              <span class="imc-SingleTimelineItemDescStyle imc-TextColorSecondary"><?php echo esc_html($val->description); ?></span>
                              <span class="imc-SingleTimelineItemFooterTextStyle imc-TextColorSecondary"><?php echo esc_html(imc_calculate_relative_date($val->dateTimestamp)), ' ', __('by','pb-voting'), ' ',  esc_html($val->name); ?></span>
                          </div>

										<?php } ?>

                        <div class="imc-row-no-margin">
                            <span class="imc-SingleTimelineStepCircleStyle imc-circle" style="background-color: #<?php echo esc_attr($last_tml_item->color);?> "></span>
                            <span class="imc-SingleTimelineStepTitleStyle imc-TextColorPrimary"><?php echo esc_html($last_tml_item->title); ?></span>
                        </div>

                        <div class="imc-row-no-margin imc-SingleTimelineItemStyle imc-SingleTimelineLastItem">
                            <span class="imc-SingleTimelineItemDescStyle imc-TextColorSecondary"><?php echo esc_html($last_tml_item->description); ?></span>
                            <span class="imc-SingleTimelineItemFooterTextStyle imc-TextColorSecondary"><?php echo esc_html(imc_calculate_relative_date($last_tml_item->dateTimestamp)), ' ', __('by','pb-voting'), ' ',  esc_html($last_tml_item->name); ?></span>
                        </div>

									<?php } ?>

                                </div>
                                <!-- End Issue Timeline -->

							<?php }?>

                        </div>
                    </div>
                </div>
            </div>

		<?php endwhile; ?>
        <!--End the loop.-->
			</div><!-- .site-main -->

    <!-- Scripts -->
    <script>
        var lat = parseFloat("<?php echo floatval(get_post_meta($issue_id, "imc_lat", true)); ?>");
        var lng = parseFloat("<?php echo floatval(get_post_meta($issue_id, "imc_lng", true)); ?>");
        document.onload = imcInitializeMap(lat, lng, 'imcSingleIssueMapCanvas', '', false, 15, false);

        if(jQuery(".imc-CommentTextArea").length !== 0) {
            jQuery("#imc-submit").attr('disabled','disabled');
        }

        jQuery(".imc-CommentTextArea").keydown(function() {

            var textarea = jQuery.trim(jQuery('.imc-CommentTextArea').val());

            if (textarea.length > 1) {
                jQuery("#imc-submit").removeAttr('disabled');
            }
        });

        jQuery(".imc-CommentTextArea").keyup(function() {

            var textarea = jQuery.trim(jQuery('.imc-CommentTextArea').val());

            if (textarea.length < 3) {
                jQuery("#imc-submit").attr('disabled','disabled');
            }
        });

    </script>
<?php get_footer(); ?>
