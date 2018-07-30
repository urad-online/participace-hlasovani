<?php
/**
 * The template for displaying all single issues and attachments
 *
 */

// $pb_project_single = new pbProjectSingle;

get_header(); ?>

    <div class="imc-BGColorGray">

        <!--Start the loop.-->
		<?php
		while ( have_posts() ) : the_post();
			$issue_id = get_the_ID();
			$user_id = get_current_user_id();
			$user = wp_get_current_user(); ?>


			<?php if (get_post_status( $issue_id ) !== 'publish') { ?>

                <div class="imc-SingleHeaderStyle imc-BGColorRed">
                    <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary imc-CenterContents" style="line-height: 60px;">
						<?php echo __('Under Moderation','participace-projekty');  ?></h2>
                </div>

			<?php } ?>

            <div class="imc-Separator"></div>

            <div class="imc-container">

                <div id="issue-<?php echo esc_attr($issue_id); ?>" class="issue-<?php echo esc_attr($issue_id); ?> imc_issues type-imc_issues status-publish" >

                    <div class="imc-row">
                        <div class="imc-grid-8 imc-columns">
                            <div class="imc-CardLayoutStyle">
                                <div class="imc-row">

                                    <div class="imc-grid-2 imc-columns">

                                        <div class="imc-row-no-margin imc-CenterContents">
                                            <span class="imc-Text-SM imc-TextColorSecondary imc-TextBold imc-FontRoboto">#</span>
                                            <span class="imc-Text-SM imc-TextColorSecondary imc-TextMedium imc-FontRoboto"><?php echo esc_html(the_ID()); ?></span>
                                        </div>
                                    </div>

                                    <div class="imc-grid-10 imc-columns">
										<?php the_title( '<h2 class="imc-PageTitleTextStyle imc-TextColorPrimary">', '</h2>' );?>
                                        <p class="imc-SingleCategoryTextStyle imc-Text-LG imc-TextColorSecondary"><?php echo esc_html("current_category_name"); ?> </p>
                                    </div>
                                </div>

								<?php if (get_post_status( $issue_id ) == 'publish') { ?>

								<?php } ?>

								<?php
								if (get_the_content()) { ?>
                                    <div class="imc-row">
                                        <h3 class="imc-SectionTitleTextStyle"><?php echo __('Description','participace-projekty'); ?></h3>
                                        <div class="imc-SingleDescriptionStyle imc-TextColorSecondary imc-JustifyText"><?php the_content(); ?></div>
                                    </div>
								<?php } ?>


                                <div class="imc-row-no-margin">
									<?php $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
                                    <h3 class="imc-SectionTitleTextStyle"><?php echo __('Photos','participace-projekty'); ?></h3>

									<?php if ($img_url) { ?>
                                        <a href="<?php echo esc_url($img_url); ?>" target="_blank"> <?php the_post_thumbnail('thumbnail'); ?> </a> <!--thumbnail medium large full-->
									<?php } else { ?>
                                        <div class="imc-row imc-CenterContents">
                                            <i class="material-icons md-huge imc-TextColorHint">landscape</i>
                                            <span class="imc-NotAvailableTextStyle imc-TextColorHint imc-DisplayBlock"><?php echo __('No photos submitted', 'participace-projekty'); ?></span>
                                        </div>
									<?php }?>

                                </div>
                            </div> <!--End Card-->


                        </div>
						<div class="imc-grid-4 imc-columns">
                            <?PHP  echo do_shortcode('[pb_vote_reg_widget survey_id="mila_pokus"]'); ?>
						</div>

                    </div>
                </div>
            </div>

		<?php endwhile; ?>
        <!--End the loop.-->
    </div><!-- .site-main -->

    <!-- Scripts -->
<?php get_footer(); ?>
