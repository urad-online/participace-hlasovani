<?php

/**
 * 14.01
 * IMC-Archive part for grid-option
 *
 */

function pb_item_archive_show_export($post) {

	$issue_id = intval($post->ID, 10);
	$project_meta = get_post_meta( $issue_id );
	$project_single = new PbVote_ProjectSingle();

	$imccategory_currentterm = get_the_terms($post->ID , 'imccategory' );
	$current_category_name = ""; // Reset

	if ($imccategory_currentterm) {
		$current_category_name = $imccategory_currentterm[0]->name;
	} ?>

	<div>
		<div class="uvod imc-ListLayoutStyle imc-OverviewListStyle" id="issue-<?php echo esc_html($issue_id);?>">
        <div class="imc-row-no-margin">
            <div class="imc-grid-10 imc-columns">
                <div class="imc-ListItemMainInfoStyle">
                    <div class="imc-row-no-margin">
                        <span class="imc-OverviewListTitleStyle imc-LinkStyle"><?php echo esc_html(get_the_title());?></span>
                    </div>
                    <div class="imc-row-no-margin">
                        <span class="imc-OverviewListCatNameStyle imc-OverviewListTextNoWrapStyle"><?php echo esc_html($current_category_name); ?></span>
                    </div>
                    <div class="imc-row-no-margin">
                        <span class="imc-OverviewListDescriptionStyle imc-OverviewListTextNoWrapStyle"><?php printf(get_the_excerpt());  ?></span>
                    </div>
                    <div class="imc-row-no-margin imc-OverviewListTextNoWrapStyle">
                        <i class="material-icons md-18 imc-TextColorSecondary imc-AlignIconToLabel">place</i>
                        <span class="imc-OverviewListStepLabelStyle  imc-TextColorSecondary"><?php echo esc_html(get_post_meta($post->ID, 'imc_address', true)); ?></span>
                    </div>
                </div>
                <hr class="imc-HorizontalSeparator">
            </div>
            <div class="imc-grid-2 imc-columns imc-CenterContents">
							<?php if ( has_post_thumbnail() ) { ?>
			                <div class="imc-BlockLevelLinkStyle ">
												<?php echo esc_html(the_post_thumbnail( "thumbnail", array( "class"=>"pbvote-OverviewListImageStyle") )); ?>
			                </div>
							<?php } else { ?>
			                <div class="imc-BlockLevelLinkStyle">
			                  <div class="imc-OverviewListNoPhotoWrapperStyle">
			                      <i class="imc-EmptyStateIconStyle material-icons md-48">landscape</i>
			                      <span class="imc-DisplayBlock imc-ReportFormErrorLabelStyle imc-TextColorHint"><?php echo __('No photo submitted','pb-voting'); ?></span>
			                  </div>
			                </div>
							<?php } ?>
            </div>
        </div>
    </div>
<!-- detailni posis -->
		<div class="imc-CardLayoutStyle">
				<div class="imc-row">
						<div class="imc-grid-10 imc-columns">
						<?php the_title( '<h2 class="imc-PageTitleTextStyle imc-TextColorPrimary">', '</h2>' );?>
						</div>
				</div>

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
								<div > <?php the_post_thumbnail('thumbnail'); ?> </div> <!--thumbnail medium large full-->
						<?php } else { ?>
								<div class="imc-row imc-CenterContents">
										<i class="material-icons md-huge imc-TextColorHint">landscape</i>
										<span class="imc-NotAvailableTextStyle imc-TextColorHint imc-DisplayBlock"><?php echo __('No photos submitted', 'pb-voting'); ?></span>
								</div>
						<?php }?>
				</div>
				<hr class="imc-HorizontalWhitespaceSeparator" style="padding-top:10px">
				<?php
				echo $project_single->template_part_pb_project( $project_meta );
				?>
		</div> <!--End Card-->
</div>
<?php } ?>
