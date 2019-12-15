<?php

/**
 * 14.01
 * IMC-Archive part for grid-option
 *
 */

function pbvote_archive_show_list($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url) {
global $voting_enabled, $comments_enabled;

	$issue_id = intval($post->ID, 10);

	$imccategory_currentterm = get_the_terms($post->ID , 'voting_category' );
	$current_category_name = ""; // Reset
	$cat_thumb_arr = "";

	if ($imccategory_currentterm) {
		$current_category_name = $imccategory_currentterm[0]->name;
		$current_category_id = $imccategory_currentterm[0]->term_id;
		$term_thumb = get_term_by('id', $current_category_id, 'voting_category');
		$cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
	} ?>

    <div class="imc-ListLayoutStyle imc-OverviewListStyle" id="issue-<?php echo esc_html($issue_id);?>">

        <div class="imc-row-no-margin">

            <div class="imc-grid-1 imc-column imc-CenterContents imc-hidden-sm">

                <div class="imc-row-no-margin">
                    <span class="imc-Text-XS-R imc-FontRoboto imc-TextMedium imc-TextColorSecondary"><?php echo esc_html($issue_id); ?></span>
                </div>

                <div class="imc-row imc-CenterContents">
					<?php if ( $cat_thumb_arr ) { ?>

                        <img src="<?php echo esc_url($cat_thumb_arr[0]); ?>" class="imc-OverviewListCategoryIcon">

					<?php }	else { ?>

                        <img src="<?php echo esc_url($plugin_path_url);?>/img/ic_default_cat.png" class="imc-OverviewListCategoryIcon">

					<?php } ?>
                </div>

            </div>

            <div class="imc-grid-9 imc-columns">

                <div class="imc-ListItemMainInfoStyle">

                    <div class="imc-row-no-margin">
                        <a class="imc-OverviewListTitleStyle imc-LinkStyle" href="<?php echo esc_url(get_permalink());?>"><?php echo esc_html(get_the_title());?></a>
                    </div>

                    <div class="imc-row-no-margin">
                        <span class="imc-OverviewListCatNameStyle imc-OverviewListTextNoWrapStyle"><?php echo esc_html($current_category_name); ?></span>
                    </div>

                    <div class="imc-row-no-margin">
                        <!--Watch out when trying to escape the excerpt, because it has a url inside-->
                        <span class="imc-OverviewListDescriptionStyle imc-OverviewListTextNoWrapStyle"><?php printf(get_the_excerpt());  ?></span>
                    </div>

                    <!-- <div class="imc-row-no-margin imc-OverviewListTextNoWrapStyle">
                        <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">place</i>
                        <span class="imc-OverviewListStepLabelStyle  imc-TextColorSecondary"><?php //echo esc_html(get_post_meta($post->ID, 'imc_address', true)); ?></span>
                    </div> -->

                </div>

                <hr class="imc-HorizontalSeparator">

                <div class="imc-row-no-margin">

                    <div class="imc-DisplayInlineBlock">
                        <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">access_time</i>

											<?php $time_create = get_post_time('U', false);
											if ($time_create < 0 || !$time_create ) {
												$timeString = __('Under moderation','pb-voting');
											}
											else {
												$timeString = imc_calculate_relative_date($time_create);
											} ?>

                        <span class="imc-OverviewListStepLabelStyle imc-TextColorSecondary imc-hidden-xs"><?php echo esc_html($timeString); ?></span>
                    </div>

                    <div class="imc-DisplayInlineBlock">
                        <span class="imc-OverviewListStepCircleStyle imc-circle imc-AlignIconToLabel" style="background-color: #<?php echo esc_attr(pbvote_get_current_status_color($post->ID));?>"></span>
                        <span class="imc-OverviewListStepLabelStyle imc-TextColorSecondary"><?php echo esc_html(pbvote_get_current_status_name($post->ID));?></span>
                    </div>
										<?php if ($comments_enabled) { ?>
											<div class="imc-DisplayInlineBlock">
												<i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">comment</i>
												<span class="imc-OverviewListStepLabelStyle imc-TextColorSecondary"><?php
												comments_number( 'No comments', '1 comment', '% comments' );
												printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments number', 'pb-voting' ), number_format_i18n( get_comments_number() ) );
												?></span>
											</div>
										<?PHP } ?>
                </div>
            </div>

            <div class="imc-grid-2 imc-columns imc-CenterContents">

				<?php if ( has_post_thumbnail() ) { ?>

                    <a href="<?php echo esc_url(get_permalink());?>" class="imc-BlockLevelLinkStyle imc-OverviewListImageStyle">
						<?php echo esc_html(the_post_thumbnail( "thumbnail", array( "class"=>"pbvote-OverviewListImageStyle")  )); ?>
                    </a>

				<?php } else { ?>

	              <a href="<?php echo esc_url(get_permalink());?>" class="imc-BlockLevelLinkStyle">
	                  <div class="imc-OverviewListNoPhotoWrapperStyle">
	                      <i class="imc-EmptyStateIconStyle material-icons md-48">landscape</i>
	                      <span class="imc-DisplayBlock imc-ReportFormErrorLabelStyle imc-TextColorHint"><?php echo __('No photo submitted','pb-voting'); ?></span>
	                  </div>
	              </a>

				<?php } ?>

            </div>
        </div>
    </div>


<?php } ?>
