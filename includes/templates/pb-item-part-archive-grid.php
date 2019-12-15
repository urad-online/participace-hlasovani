<?php

/**
 * 14.01
 * IMC-Archive part for grid-option
 *
 */

function pb_item_archive_show_grid($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url) {
global $voting_enabled;
    $issue_id = intval($post->ID, 10); ?>

    <div class="imc-CardLayoutStyle imc-OverviewTileStyle" id="issue-<?php echo esc_html($issue_id);?>">

        <div class="imc-OverviewTileImageStyle imc-CenterContents">

            <?php $author_id = intval(get_the_author_meta('ID'));
            if ( intval($author_id, 10) === intval($user_id, 10) && !pbvote_user_can_edit(get_the_ID(), $user_id)  )  { ?>

                <img alt="My Issue icon" title="<?php echo __('My Issue','pb-voting'); ?>" src="<?php echo esc_url($plugin_path_url);?>/img/ic_my_issue_grid.png" class="imc-OverviewGridMyIssueIconStyle">

            <?php } else if(pbvote_user_can_edit(get_the_ID(), $user_id)) { ?>

                <a class="imc-button-primary imc-button imc-OverviewTileEditButtonStyle" href="<?php echo esc_url( $editpage . $parameter_pass . $issue_id ) ; ?>" target="_self"><?php echo __('Edit','pb-voting'); ?></a>

            <?php } ?>

            <?php if ( has_post_thumbnail() ) { ?>

                <a href="<?php echo esc_url(get_permalink());?>" class="imc-BlockLevelLinkStyle ">
                    <?php echo esc_html(the_post_thumbnail( "thumbnail", array( "class"=>"pbvote-OverviewTileImageStyle"))); ?>
                </a>

            <?php } else { ?>

                <a href="<?php echo esc_url(get_permalink());?>" class="imc-BlockLevelLinkStyle">
                    <div class="imc-OverviewGridNoPhotoWrapperStyle">
                        <i class="imc-EmptyStateIconStyle material-icons md-huge">landscape</i>
                        <span class="imc-DisplayBlock imc-ReportGenericLabelStyle imc-TextColorHint"><?php echo __('No photo submitted','pb-voting'); ?></span>
                    </div>
                </a>

            <?php } ?>

            <div class="imc-OverviewTileIdStyle"><span class="imc-Text-SM">#</span> <?php echo esc_html($issue_id); ?></div>
            <?php if ( $voting_enabled) {
                $total_likes = intval (get_post_meta($post->ID, 'imc_likes', true), 10); ?>
                <div class="imc-OverviewTileVotesStyle">
                    <div class="my-issue-votes">
                        <i class="material-icons md-18">thumb_up</i> <?php echo esc_html($total_likes); ?>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="imc-OverviewTileDetailsStyle">

            <a class="imc-OverviewTileTitleStyle imc-LinkStyle" href="<?php echo esc_url(get_permalink());?>"><?php echo esc_html(get_the_title());?></a>

            <div class="imc-OverviewTileSectionStyle">
                <?php $imccategory_currentterm = get_the_terms($post->ID , 'imccategory' );

                // Reset
                $current_category_name = "";

                if ($imccategory_currentterm) {
                    $current_category_name = $imccategory_currentterm[0]->name;
                    $current_category_id = $imccategory_currentterm[0]->term_id;
                    $term_thumb = get_term_by('id', $current_category_id, 'imccategory');
                    $cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
                }?>

                <?php if ( $cat_thumb_arr ) { ?>

                    <img src="<?php echo esc_url($cat_thumb_arr[0]); ?>" class="imc-OverviewTileCategoryIcon u-pull-left">

                <?php }	else { ?>

                    <img src="<?php echo esc_url($plugin_path_url);?>/img/ic_default_cat.png" class="imc-OverviewTileCategoryIcon u-pull-left">

                <?php } ?>

                <span class="u-pull-left imc-OverviewCatNameStyle imc-OverviewGridCatNameStyle"><?php echo esc_html($current_category_name); ?></span>

                <?php $adminMsgs = imc_show_issue_message(get_the_ID(), get_current_user_id());
                if ($adminMsgs) { ?>
                    <div class="imc-AdminMsgsStyle">
                        <i class="material-icons md-24 u-pull-right <?php echo esc_html($pendingColorClass); ?>">error</i>
                        <span class="imc-AdminMsgsTooltipStyle imc-AdminMsgsGridTooltipStyle"><?php echo esc_html($adminMsgs); ?></span>
                    </div>
                <?php } ?>
            </div>

            <hr class="imc-HorizontalWhitespaceSeparator">

            <div class="imc-OverviewTileSectionStyle imc-FlexParent">

                <div class="imc-FlexChild imc-CenterContents">
                    <i class="material-icons md-24 imc-TextColorSecondary">access_time</i>
                    <?php $time_create = get_post_time('U', false);

                    if ($time_create < 0 || !$time_create ) {
                        $timeString = __('Under moderation','pb-voting');
                    }
                    else {
                        $timeString = imc_calculate_relative_date($time_create);

                    } ?>

                    <span class="imc-DisplayBlock imc-OverviewGridStepLabelStyle imc-TextColorSecondary"><?php echo esc_html($timeString);?></span>
                </div>

                <div class="imc-FlexChild imc-CenterContents">
                    <span class="imc-OverviewGridStepCircleStyle imc-circle" style="background-color: #<?php echo esc_attr(getCurrentImcStatusColor($post->ID));?>"></span>
                    <span class="imc-DisplayBlock imc-OverviewGridStepLabelStyle imc-TextColorSecondary"><?php echo esc_html(getCurrentImcStatusName($post->ID));?></span>
                </div>

                <!-- <div class="imc-FlexChild imc-CenterContents">
                    <i class="material-icons md-24 imc-TextColorSecondary">comment</i>

                    <span class="imc-DisplayBlock imc-OverviewGridStepLabelStyle imc-TextColorSecondary">
                        <?php
                        //printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments number', 'pb-voting' ), number_format_i18n( get_comments_number() ) );
                        //comments_number( 'No comments', '1 comment', '% comments' );
                        ?>

                    </span>

                </div> -->

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
