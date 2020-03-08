<?php

/**
 * 14.01
 * IMC-Archive part for grid-option
 *
 */

function pb_item_archive_show_list($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url) {
global $comments_enabled;

	$issue_id = intval($post->ID, 10);

	$imccategory_currentterm = get_the_terms($post->ID , 'imccategory' );
	$current_category_name = ""; // Reset

	if ($imccategory_currentterm) {
		$current_category_name = $imccategory_currentterm[0]->name;
		$current_category_id = $imccategory_currentterm[0]->term_id;
		$term_thumb = get_term_by('id', $current_category_id, 'imccategory');
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

				<?php $adminMsgs = imc_show_issue_message(get_the_ID(), get_current_user_id());
				if ($adminMsgs) { ?>
                    <div class="imc-AdminMsgsStyle imc-row">
                        <i class="material-icons md-24 <?php echo esc_html($pendingColorClass); ?>">error</i>
                        <span class="imc-AdminMsgsTooltipStyle imc-AdminMsgsListTooltipStyle"><?php echo esc_html($adminMsgs); ?></span>
                    </div>
				<?php } ?>
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

                    <div class="imc-row-no-margin imc-OverviewListTextNoWrapStyle">
                        <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">place</i>
                        <span class="imc-OverviewListStepLabelStyle  imc-TextColorSecondary"><?php echo esc_html(get_post_meta($post->ID, 'imc_address', true)); ?></span>
                    </div>

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
                        <span class="imc-OverviewListStepCircleStyle imc-circle imc-AlignIconToLabel" style="background-color: #<?php echo esc_attr(getCurrentImcStatusColor($post->ID));?>"></span>
                        <span class="imc-OverviewListStepLabelStyle imc-TextColorSecondary"><?php echo esc_html(getCurrentImcStatusName($post->ID));?></span>
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
					<?php if (PB_RATING_ENABLED) {
							$total_likes = intval (get_post_meta($post->ID, 'imc_likes', true), 10); ?>
	                    <div class="imc-DisplayInlineBlock">
	                        <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">thumb_up</i>
	                        <span class="imc-OverviewListStepLabelStyle imc-TextColorSecondary"><?php echo esc_html($total_likes).render_rating_help_link(); ?></span>
	                    </div>
					<?PHP } ?>
                </div>
            </div>

            <div class="imc-grid-2 imc-columns imc-CenterContents">

				<?php if ( has_post_thumbnail() ) { ?>

                    <a href="<?php echo esc_url(get_permalink());?>" class="imc-BlockLevelLinkStyle ">
						<?php echo esc_html(the_post_thumbnail( "thumbnail", array( "class"=>"pbvote-OverviewListImageStyle") )); ?>
                    </a>

				<?php } else { ?>

                    <a href="<?php echo esc_url(get_permalink());?>" class="imc-BlockLevelLinkStyle">
                        <div class="imc-OverviewListNoPhotoWrapperStyle">
                            <i class="imc-EmptyStateIconStyle material-icons md-48">landscape</i>
                            <span class="imc-DisplayBlock imc-ReportFormErrorLabelStyle imc-TextColorHint"><?php echo __('No photo submitted','pb-voting'); ?></span>
                        </div>
                    </a>

				<?php } ?>

				<?php $author_id = intval(get_the_author_meta('ID'));
				if ( intval($author_id, 10) === intval($user_id, 10) && !pbvote_user_can_edit(get_the_ID(), $user_id)  )  { ?>

                    <img class="imc-OverviewListMyIssueIconStyle" alt="My Issue icon" title="<?php echo __('My Issue','pb-voting'); ?>" src="<?php echo esc_url($plugin_path_url);?>/img/ic_my_issue_list.png">

				<?php } else if(pbvote_user_can_edit(get_the_ID(), $user_id)) { ?>

                    <a class="imc-button-primary imc-button-small imc-OverviewListEditButtonStyle" href="<?php echo esc_url( $editpage . $parameter_pass . $issue_id ); ?>" target="_self"><?php echo __('Edit','pb-voting'); ?></a>

				<?php } ?>
            </div>
        </div>
    </div>

    <script>
        (function(){
            "use strict";
            var elementId = "issue-<?php echo esc_html($issue_id);?>";
            var postId = <?php echo esc_js($issue_id);?>;
            jQuery( document ).ready(function() {
                loadOverviewMouseEventScripts(elementId, postId);
            });
        })();
    </script>

<?php } ?>
