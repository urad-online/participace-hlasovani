<?php
/**
 * Template Name: Stranka Seznam hlasovani
 * The template for displaying archive pages
 *
 */
function pb_items_archive_imc_issues_export($pb_issues_view_filters)
{
		global $wp_query, $post, $voting_ids ;
		include_once( PB_VOTE_PATH_TEMPL . '/pb-item-part-archive-export.php' );

		$filter_category_taxo = 'imccategory';
		$filter_status_taxo   = 'imcstatus';
		$filter_params_view   = array();

		if ( get_option('permalink_structure') ) { $perma_structure = true; } else {$perma_structure = false;}
		if (!empty($voting_ids)) {
			$pb_issues_view_filters->set_value_param('svoting', $voting_ids);
		}

		/********************************************* ISSUES PER PAGE ********************************************************/

		$user_id = get_current_user_id();

		// $pb_issues_view_filters = new  PbVote_ArchiveDisplayOptions( 'imc'); //todo

		if ( is_front_page() || is_home() ) {
			$front_page_id = get_option('page_on_front');
			$my_permalink = _get_page_link($front_page_id);
		}else{
			$my_permalink = get_the_permalink();
		}

		?>
    <div class="imc-OverviewFilteringBarStyle">

        <div class="ac-container">
            <input class="imc-DrawerCheckbox" id="ac-1" name="accordion-1" type="checkbox" />
            <div class="imc-SingleHeaderStyle imc-BGColorWhite">
                <label for="ac-1" class="imc-OverviewFilteringPanelLabelStyle">
                    <span><?php echo __('Search &amp; Filtering', 'pb-voting'); ?></span>
                    <span style="display: none;" id="pbItemCatFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Kategorie', 'pb-voting');?></span>
                    <span style="display: none;" id="pbItemStatFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Stav', 'pb-voting');?></span>
                    <span style="display: none;" id="pbItemKeywordFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Klíčová slova', 'pb-voting');?></span>
                    <i class="material-icons md-24 u-pull-right" id="pbItemFilteringIndicator">filter_list</i>
                </label>

            </div>

            <article class="ac-small imc-DropShadow">

                <div class="imc-row imc-DrawerContentsStyle">

										<div class="imc-row">
											<h3 class="imc-SectionTitleTextStyle"><?php echo __('Klíčové slovo', 'pb-voting'); ?></h3>

											<input name="searchKeyword" autocomplete="off" placeholder="<?php
												echo __('Vyhledat klíčové slovo','pb-voting'); ?>" id="pbItemSearchKeywordInput" type="search" class="imc-InputStyle"/>
										</div>
                    <div class="imc-DrawerFirstCol">

                        <input checked="checked" class="imc-CheckboxToggleStyle" id="pbItemToggleStatusCheckbox" type="checkbox" name="pbItemToggleStatusCheckbox" value="all">
                        <label class="imc-SectionTitleTextStyle" for="pbItemToggleStatusCheckbox"><?php echo __('Stav hlasování', 'pb-voting'); ?></label>
                        <br>
                        <div id="pbItemStatusCheckboxes" class="imc-row">
														<?php $all_pb_item_statuses = get_all_pbvote_taxo( $filter_status_taxo);
														if ($all_pb_item_statuses) { ?>

															<?php foreach( $all_pb_item_statuses as $pb_item_status ) { ?>
                                  <input checked="checked" class="imc-CheckboxStyle" id="pbItem-stat-checkbox-<?php echo esc_html($pb_item_status->term_id); ?>" type="checkbox" name="<?php echo esc_attr($pb_item_status->name); ?>" value="<?php echo esc_attr($pb_item_status->term_id); ?>">
                                  <label for="pbItem-stat-checkbox-<?php echo esc_html($pb_item_status->term_id); ?>"><?php echo esc_html($pb_item_status->name); ?></label>
                                  <br>
															<?php }
														} ?>
                        </div>
                    </div>

                    <div class="imc-DrawerSecondCol">

                        <input checked="checked" class="imc-CheckboxToggleStyle" id="pbItemToggleCatsCheckbox" type="checkbox"
														name="pbItemToggleCatsCheckbox" value="all">
                        <label class="imc-SectionTitleTextStyle" for="pbItemToggleCatsCheckbox"><?php echo __('Kategorie', 'pb-voting'); ?></label>
                        <br>

                        <div id="pbItemCatCheckboxes" class="imc-row">
														<?php $all_pb_item_category = get_all_pbvote_taxo( $filter_category_taxo , true);
														$count = count($all_pb_item_category);
														$numItemsPerRow = ceil($count / 2);
														$index  = 0;

														echo '<div class="imc-grid-6 imc-columns">';
														foreach( $all_pb_item_category as $pb_item_category ) {
															if ($index > 0 and $index % $numItemsPerRow == 0) {
																echo '</div><div class="imc-grid-6 imc-columns">';
															} ?>

                                <div class="imc-row">

                                    <input checked class="imc-CheckboxStyle" id="pbItem-cat-checkbox-<?php echo esc_html($pb_item_category->term_id); ?>" type="checkbox" name="<?php echo esc_attr($pb_item_category->name); ?>" value="<?php echo esc_attr($pb_item_category->term_id); ?>">
                                    <label for="pbItem-cat-checkbox-<?php echo esc_html($pb_item_category->term_id); ?>"><?php echo esc_html($pb_item_category->name); ?></label>

																		<?php $args = array(
																			'hide_empty'    => false,
																			'hierarchical'  => true,
																			'parent'        => $pb_item_category->term_id
																		);
																		$childterms = get_terms( $filter_category_taxo, $args);

																		if (!empty($childterms)) { ?>
                                        <div id="pbItemCatChildCheckboxes">
																						<?php foreach ( $childterms as $childterm ) { ?>
		                                            <input checked="checked" class="imc-CheckboxStyle imc-CheckboxChildStyle" id="pbItem-cat-checkbox-<?php echo esc_html($childterm->term_id); ?>" type="checkbox" name="<?php echo esc_attr($childterm->name); ?>" value="<?php echo esc_attr($childterm->term_id); ?>">
		                                            <label for="pbItem-cat-checkbox-<?php echo esc_html($childterm->term_id); ?>"><?php echo esc_html($childterm->name); ?></label>
																								<?php $args = array('hide_empty' => false, 'hierarchical'  => true, 'parent' => $childterm->term_id);
																								$grandchildterms = get_terms( $filter_category_taxo, $args);
																								if (!empty($childterms)) { ?>
		                                                <div id="pbItemCatGrandChildCheckboxes">
																												<?php foreach ($grandchildterms as $grandchild ) { ?>
		                                                        <input checked="checked" class="imc-CheckboxStyle imc-CheckboxGrandChildStyle"
																																	id="pbItem-cat-checkbox-<?php echo esc_html($grandchild->term_id); ?>" type="checkbox"
																																	name="<?php echo esc_attr($grandchild->name); ?>" value="<?php echo esc_attr($grandchild->term_id); ?>">
		                                                        <label for="pbItem-cat-checkbox-<?php echo esc_html($grandchild->term_id); ?>"><?php echo esc_html($grandchild->name); ?></label>
																												<?php } ?>
		                                                </div>
																								<?php } ?>
																						<?php } ?>
                                        </div>
																		<?php }?>
                                </div>
																<?php $index++;
														}
														echo '</div>'; ?>
                        </div>
                    </div>
                </div>

                <div class="imc-row-no-margin imc-DrawerButtonRowStyle">
                    <button class="imc-button imc-button-primary u-pull-right" onclick="pbItemOverviewGetFilteringData();"><?php echo __('Zobrazit vybrané', 'pb-voting'); ?></button>
                    <button class="imc-button u-pull-right" onclick="pbItemOverviewResetFilters();"><?php echo __('Zobrazit vše', 'pb-voting'); ?></button>
                </div>
            </article>
        </div>
    </div>


    <div class="imc-BGColorGray imc-OverviewWrapperStyle">

        <div class="imc-OverviewContentStyle">

            <div class="imc-OverviewIssuesContainerStyle" id="pbItemOverviewContainer">

				<?php
				// Get current page and append to custom query parameters array
				$pb_items = new PbVote_ArchiveDisplayFilterDataImcIssues( $pb_issues_view_filters->get_filter_params());
				$custom_query = $pb_items->get_query_data();

				// Pagination fix
				$temp_query = $wp_query;
				$wp_query = NULL;
				$wp_query = $custom_query;
				$paged = $pb_items->get_paged();
				// Output custom query loop
				if ($custom_query->have_posts()) {
					$pbvote_current_view = $pb_issues_view_filters->get_value('view');
					while ($custom_query->have_posts()) :

						$custom_query->the_post();
						$issue_id = get_the_ID();

						$pendingColorClass = 'imc-ColorRed';

						pb_item_archive_show_export($issue_id);

						
					endwhile;

				} else {

					?>
					<div class="imc-Separator"></div>
					<div class="imc-row imc-CenterContents imc-GiveWhitespaceStyle">
						<i class="material-icons md-huge imc-TextColorHint">local_offer</i>
						<div class="imc-Separator"></div>
						<h1 class="imc-FontRoboto imc-Text-XL imc-TextColorSecondary imc-TextItalic imc-TextMedium imc-CenterContents"><?php echo __('Nejsou dostupné záznamy','pb-voting'); ?></h1>
						<div class="imc-Separator"></div>
						<span class="imc-CenterContents imc-TextMedium imc-Text-LG imc-FontRoboto">
							<?php if($pb_issues_view_filters->is_filtering_active()	) { ?>
								<span class="imc-TextColorSecondary ">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
								<a href="javascript:void(0);" onclick="pbItemOverviewResetFilters();" class="imc-LinkStyle"><?php echo __('Zrušit filtr','pb-voting'); ?></a>
							<?php } ?>
						</span>

						<div class="imc-Separator"></div>
					</div>
				<?php
				}

				// Reset postdata & query
				wp_reset_postdata();
				// $wp_query = NULL;
				// $wp_query = $temp_query;
				?>

            </div>

        </div>

    </div>

		<?php
}
