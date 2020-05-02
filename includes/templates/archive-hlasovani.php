<?php
/**
 * Template Name: Stranka Seznam hlasovani
 * The template for displaying archive pages
 *
 */

// $insertpage = getIMCInsertPage();
// $editpage = getIMCEditPage();
// $listpage = getIMCArchivePage();
// $voting_page = get_first_pbvoting_post();
global $wp_query, $post;
$insertpage = "";
$editpage = "";
$listpage = "";
$voting_page = "";
$voting_category_taxo = 'voting_category';
$voting_status_taxo   = 'voting_status';
$filter_params_view   = array();

if ( get_option('permalink_structure') ) { $perma_structure = true; } else {$perma_structure = false;}

/********************************************* ISSUES PER PAGE ********************************************************/

$user_id = get_current_user_id();
$issues_pp_counter = 0;

$voting_view_filters = new  PbVote_ArchiveDisplayOptions();
get_header();

if ( is_front_page() || is_home() ) {
	$front_page_id = get_option('page_on_front');
	$my_permalink = _get_page_link($front_page_id);
}else{
	$my_permalink = get_the_permalink();
}

?>
    <div class="imc-SingleHeaderStyle imc-BGColorWhite">

        <nav class="imc-OverviewHeaderNavStyle">
            <ul class="imc-OverviewNavUlStyle">
                <li>
                    <label class="imc-NaviSelectStyle">
                        <select id="pbvSelectDisplayComponent" title="Issues to display" onchange="pbvFireNavigation('pbvSelectDisplayComponent')">
                            <option class="imc-CustomOptionDisabledStyle" value="<?php echo esc_attr($voting_view_filters->get_value('ppage')); ?>" selected disabled><?php echo __('Display: ', 'pb-voting'); ?> <?php echo esc_html($voting_view_filters->get_label('ppage', $voting_view_filters->get_value('ppage'))); ?></option>

							<?php echo $voting_view_filters->generate_options_long_ppage($my_permalink)?>
                        </select>
                    </label>
                </li>

                <li>
                    <label class="imc-NaviSelectStyle">
                        <select id="pbvSelectOrderingComponent" title="Order by" onchange="pbvFireNavigation('pbvSelectOrderingComponent')">
                            <option class="imc-CustomOptionDisabledStyle" value="<?php echo esc_attr($voting_view_filters->get_value('sorder')); ?>" selected disabled><?php echo __('Order: ', 'pb-voting'); ?>  <?php echo esc_html($voting_view_filters->get_label('sorder', $voting_view_filters->get_value('sorder'))); ?></option>

							<?php echo $voting_view_filters->generate_options_long_sorder($my_permalink)?>
                        </select>
                    </label>
                </li>

				<?php echo $voting_view_filters->generate_lists_long_view($my_permalink)?>
            </ul>
        </nav>
    </div>

    <div class="imc-OverviewFilteringBarStyle">

        <div class="ac-container">
            <input class="imc-DrawerCheckbox" id="ac-1" name="accordion-1" type="checkbox" />
            <div class="imc-SingleHeaderStyle imc-BGColorWhite">
                <label for="ac-1" class="imc-OverviewFilteringPanelLabelStyle">
                    <span><?php echo __('Search &amp; Filtering', 'pb-voting'); ?></span>
                    <span style="display: none;" id="imcCatFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Kategorie', 'pb-voting');?></span>
                    <span style="display: none;" id="imcStatFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Stav', 'pb-voting');?></span>
                    <span style="display: none;" id="imcKeywordFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Klíčová slova', 'pb-voting');?></span>
                    <i class="material-icons md-24 u-pull-right" id="pbvFilteringIndicator">filter_list</i>
                </label>

            </div>

            <article class="ac-small imc-DropShadow">

                <div class="imc-row imc-DrawerContentsStyle">

					<div class="imc-row">
						<h3 class="imc-SectionTitleTextStyle"><?php echo __('Vyhledat', 'pb-voting'); ?></h3>

						<input name="searchKeyword" autocomplete="off" placeholder="<?php
							echo __('Vyhledat klíčové slovo','pb-voting'); ?>" id="pbvSearchKeywordInput" type="search" class="imc-InputStyle"/>
					</div>
                    <div class="imc-DrawerFirstCol">

                        <input checked="checked" class="imc-CheckboxToggleStyle" id="pbvToggleStatusCheckbox" type="checkbox" name="pbvToggleStatusCheckbox" value="all">
                        <label class="imc-SectionTitleTextStyle" for="pbvToggleStatusCheckbox"><?php echo __('Stav hlasování', 'pb-voting'); ?></label>
                        <br>
                        <div id="pbvStatusCheckboxes" class="imc-row">
							<?php $all_pbvstatus = get_all_pbvote_taxo( $voting_status_taxo);
							if ($all_pbvstatus) { ?>

								<?php foreach( $all_pbvstatus as $pbvstatus ) { ?>

                                    <input checked="checked" class="imc-CheckboxStyle" id="pbv-stat-checkbox-<?php echo esc_html($pbvstatus->term_id); ?>" type="checkbox" name="<?php echo esc_attr($pbvstatus->name); ?>" value="<?php echo esc_attr($pbvstatus->term_id); ?>">
                                    <label for="pbv-stat-checkbox-<?php echo esc_html($pbvstatus->term_id); ?>"><?php echo esc_html($pbvstatus->name); ?></label>
                                    <br>

								<?php }
							} ?>
                        </div>
                    </div>

                    <div class="imc-DrawerSecondCol">

                        <input checked="checked" class="imc-CheckboxToggleStyle" id="pbvToggleCatsCheckbox" type="checkbox"
																name="pbvToggleCatsCheckbox" value="all">
                        <label class="imc-SectionTitleTextStyle" for="pbvToggleCatsCheckbox"><?php echo __('Kategorie', 'pb-voting'); ?></label>
                        <br>

                        <div id="pbvCatCheckboxes" class="imc-row">

														<?php $all_pbvcategory = get_all_pbvote_taxo( $voting_category_taxo , true);

														$count = count($all_pbvcategory);
														$numItemsPerRow = ceil($count / 2);
														$index  = 0;

														echo '<div class="imc-grid-6 imc-columns">';
														foreach( $all_pbvcategory as $pbvcategory ) {
															if ($index > 0 and $index % $numItemsPerRow == 0) {
																echo '</div><div class="imc-grid-6 imc-columns">';
															} ?>

                                <div class="imc-row">

                                    <input checked class="imc-CheckboxStyle" id="pbv-cat-checkbox-<?php echo esc_html($pbvcategory->term_id); ?>" type="checkbox" name="<?php echo esc_attr($pbvcategory->name); ?>" value="<?php echo esc_attr($pbvcategory->term_id); ?>">
                                    <label for="pbv-cat-checkbox-<?php echo esc_html($pbvcategory->term_id); ?>"><?php echo esc_html($pbvcategory->name); ?></label>

																		<?php $args = array(
																			'hide_empty'    => false,
																			'hierarchical'  => true,
																			'parent'        => $pbvcategory->term_id
																		);
																		$childterms = get_terms( $voting_category_taxo, $args);

																		if (!empty($childterms)) { ?>

                                        <div id="pbvCatChildCheckboxes">

																						<?php foreach ( $childterms as $childterm ) { ?>

                                                <input checked="checked" class="imc-CheckboxStyle imc-CheckboxChildStyle" id="pbv-cat-checkbox-<?php echo esc_html($childterm->term_id); ?>" type="checkbox" name="<?php echo esc_attr($childterm->name); ?>" value="<?php echo esc_attr($childterm->term_id); ?>">
                                                <label for="pbv-cat-checkbox-<?php echo esc_html($childterm->term_id); ?>"><?php echo esc_html($childterm->name); ?></label>

																								<?php $args = array('hide_empty' => false, 'hierarchical'  => true, 'parent' => $childterm->term_id);
																								$grandchildterms = get_terms( $voting_category_taxo, $args);

																								if (!empty($childterms)) { ?>

                                                  <div id="pbvCatGrandChildCheckboxes">

																											<?php foreach ($grandchildterms as $grandchild ) { ?>
													                                                            <input checked="checked" class="imc-CheckboxStyle imc-CheckboxGrandChildStyle"
																													id="pbv-cat-checkbox-<?php echo esc_html($grandchild->term_id); ?>" type="checkbox"
																													name="<?php echo esc_attr($grandchild->name); ?>" value="<?php echo esc_attr($grandchild->term_id); ?>">
													                                                            <label for="pbv-cat-checkbox-<?php echo esc_html($grandchild->term_id); ?>"><?php echo esc_html($grandchild->name); ?></label>
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
                    <button class="imc-button imc-button-primary u-pull-right" onclick="pbvOverviewGetFilteringData();"><?php echo __('Zobrazit vybrané', 'pb-voting'); ?></button>
                    <button class="imc-button u-pull-right" onclick="pbvOverviewResetFilters();"><?php echo __('Zobrazit vše', 'pb-voting'); ?></button>
                </div>

            </article>

        </div>
    </div>


    <div class="imc-BGColorGray imc-OverviewWrapperStyle">

        <div class="imc-OverviewContentStyle">

            <div class="imc-OverviewIssuesContainerStyle" id="imcOverviewContainer">

				<?php
				// Get current page and append to custom query parameters array
				$voting_items = new PbVote_ArchiveDisplayFilterData( $voting_view_filters->get_filter_params());
				$paged = $voting_items->get_paged();
				$custom_query = $voting_items->get_query_data();

				// Pagination fix
				$temp_query = $wp_query;
				$wp_query = NULL;
				$wp_query = $custom_query;

				// Output custom query loop
				if ($custom_query->have_posts()) :
					$pbvote_current_view = $voting_view_filters->get_value('view');
					while ($custom_query->have_posts()) :

						$custom_query->the_post();
						$issue_id = get_the_ID();
						$myIssue = (get_current_user_id() == get_the_author_meta('ID') ? true : false);

						$pendingColorClass = 'imc-ColorRed';
						$issues_pp_counter = $issues_pp_counter + 1;

						if ($pbvote_current_view == '1') {
							//LIST VIEW
							pbvote_archive_show_list($post, $editpage, $voting_view_filters->parameter_pass, $user_id, $pendingColorClass, $voting_view_filters->get_plugin_base_url());
						} else {
							//GRID VIEW
							pbvote_archive_show_grid($post, $editpage, $voting_view_filters->parameter_pass, $user_id, $pendingColorClass, $voting_view_filters->get_plugin_base_url());
						}

						$pbvcategory_currentterm = get_the_terms($post->ID, $voting_category_taxo);
						if ($pbvcategory_currentterm) {
							$current_category_id = $pbvcategory_currentterm[0]->term_id;
							$term_thumb = get_term_by('id', $current_category_id, $voting_category_taxo);
							$cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
						}

					endwhile;
				else :

					?>

                    <div class="imc-Separator"></div>

                    <div class="imc-row imc-CenterContents imc-GiveWhitespaceStyle">

                        <i class="material-icons md-huge imc-TextColorHint">local_offer</i>

                        <div class="imc-Separator"></div>

                        <h1 class="imc-FontRoboto imc-Text-XL imc-TextColorSecondary imc-TextItalic imc-TextMedium imc-CenterContents"><?php echo __('Nejsou dostupné záznamy','pb-voting'); ?></h1>

                        <div class="imc-Separator"></div>

                        <span class="imc-CenterContents imc-TextMedium imc-Text-LG imc-FontRoboto">
							<?php if($voting_view_filters->is_filtering_active()	) { ?>
                                <span class="imc-TextColorSecondary ">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                                <a href="javascript:void(0);" onclick="pbvOverviewResetFilters();" class="imc-LinkStyle"><?php echo __('Zrušit filtr','pb-voting'); ?></a>
							<?php } ?>
						</span>

                        <div class="imc-Separator"></div>
                    </div>

				<?php endif;
				// Reset postdata & query
				wp_reset_postdata();
				$wp_query = NULL;
				$wp_query = $temp_query;
				?>

            </div>
            <div class="imc-OverviewPaginationContainerStyle">

				<?php $total_issues = $custom_query->found_posts;
				$start_indicator = (($paged - 1) * $voting_view_filters->get_value('ppage')) + 1;
				if ($total_issues === 0) {$start_indicator = 0;}
				$end_indicator = (($paged - 1) * $voting_view_filters->get_value('ppage')) + $issues_pp_counter; ?>

                <p class="img-PaginationLabelStyle imc-TextColorSecondary"><?php echo __('Showing','pb-voting'); ?> <b><?php echo esc_html($start_indicator); ?></b> - <b><?php echo esc_html($end_indicator) ?></b> <?php echo __('of','pb-voting'); ?> <b><?php echo esc_html($total_issues) ?></b> <?php echo __('issues','pb-voting'); ?></p>

				<?php pbvote_paginate($custom_query, $paged, $voting_view_filters->get_value('ppage'), $voting_view_filters->get_value('sorder'), $voting_view_filters->get_value('view'), $voting_view_filters->get_value('sstatus'), $voting_view_filters->get_value('scategory'), $voting_view_filters->get_value('keyword')); ?>
            </div>

        </div>

        <!-- <div class="imc-OverviewMapContainerStyle">
            <div id="imcOverviewMap" class="imc-OverviewMapStyle"></div>
        </div> -->

    </div>

    <!-- Initialize Map Scripts -->
    <script>
        /*setOverviewLayout();*/

        jQuery( document ).ready(function() {

            var imported_cat = <?php echo json_encode( $voting_view_filters->get_value_array('scategory')); ?>;

            var imported_status = <?php echo json_encode($voting_view_filters->get_value_array('sstatus')); ?>;
            var imported_keyword = <?php echo json_encode($voting_view_filters->get_value_array('keyword')); ?>;
            var i;


            if (imported_status || imported_cat || imported_keyword) {
                jQuery('#pbvFilteringIndicator').css('color', '#1ABC9C');
				jQuery('#pbvStatusCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });
				jQuery('#pbvCatCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });

                if (imported_status) {
                    jQuery('#imcStatFilteringLabel').show();

                    jQuery('#pbvToggleStatusCheckbox').prop('checked', false);

                    for (i=0;i<imported_status.length;i++) {
                        jQuery('#pbv-stat-checkbox-'+imported_status[i]).prop('checked', true);
                    }
                }

                if (imported_cat) {
                    jQuery('#imcCatFilteringLabel').show();

                    jQuery('#pbvToggleCatsCheckbox').prop('checked', false);

                    for (i=0;i<imported_cat.length;i++) {
                        jQuery('#pbv-cat-checkbox-'+imported_cat[i]).prop('checked', true);
                    }
                }

                if (imported_keyword) {
                    jQuery('#imcKeywordFilteringLabel').show();

                    jQuery('#pbvSearchKeywordInput').val(imported_keyword);
                }
            }
        });

        function pbvFireNavigation(id) {
            location.href = jQuery('#'+id)[0].value;
            jQuery( id +" option:disabled" ).prop('selected', true);
        }

        // Checkbox select propagation
        jQuery(function () {
            jQuery("input[type='checkbox']").change(function () {
				jQuery(this).siblings('#pbvStatusCheckboxes')
				.find("input[type='checkbox']")
				.prop('checked', this.checked);

                jQuery(this).siblings('#pbvCatCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

                jQuery(this).siblings('#pbvCatChildCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

                jQuery(this).siblings('#pbvCatGrandChildCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

            });
        });

        function pbvOverviewGetFilteringData() {
            var selectedStatus = '';
            var notSelectedStatus = '';
            var selectedCats = '';
            var notSelectedCats = '';
            var keywordString = '';

			jQuery('#pbvStatusCheckboxes input:checkbox:checked').each(function() { selectedStatus = selectedStatus + jQuery(this).attr('value') +','; });
			selectedStatus = selectedStatus.slice(0, -1);

			jQuery('#pbvStatusCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedStatus = notSelectedStatus + jQuery(this).attr('value') +','; });
			notSelectedStatus = notSelectedStatus.slice(0, -1);

			// if ( notSelectedStatus.length === 0) {
			// 	selectedStatus = "all";
			// }

			jQuery('#pbvCatCheckboxes input:checkbox:checked').each(function() { selectedCats = selectedCats + jQuery(this).attr('value') +','; });
			selectedCats = selectedCats.slice(0, -1);

			jQuery('#pbvCatCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedCats = notSelectedCats + jQuery(this).attr('value') +','; });
			notSelectedCats = notSelectedCats.slice(0, -1);

			// if ( notSelectedCats.length === 0) {
			// 	selectedCats = "all";
			// }

            if (jQuery('#pbvSearchKeywordInput').val() !== '') {
                keywordString = jQuery('#pbvSearchKeywordInput').val();
            }

            var base = <?php echo json_encode( $my_permalink ) ; ?>;
            var tempfilter1 = <?php echo json_encode(  $voting_view_filters->create_url_variables_short()); ?>;
            var filter1 = decodeURIComponent(tempfilter1);
            var filter2 = '&sstatus=' + selectedStatus;
            var filter3 = '&scategory=' + selectedCats;
            var filter4 = '&keyword=' + keywordString;
            var link = base + filter1 + filter2 + filter3 + filter4;

            window.location = link;
        }

        function pbvOverviewResetFilters() {
            var i;
            var	checkboxes = document.getElementsByTagName('input');

            for (i = 0; i < checkboxes.length; i++)
            {
                if (checkboxes[i].type === 'checkbox' && checkboxes[i].id !== 'ac-1' )
                {
                    checkboxes[i].checked = true;
                }
            }

            jQuery('#pbvSearchKeywordInput').val('');

            pbvOverviewGetFilteringData();
        }
    </script>

<?php get_footer(); ?>
