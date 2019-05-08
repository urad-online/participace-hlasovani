<?php
/**
 * Template Name: Stranka Seznam hlasovani
 * The template for displaying archive pages
 *
 */
function pb_items_archive_imc_issues_body($pb_issues_view_filters)
{
global $wp_query, $post, $voting_ids ;

$filter_category_taxo = 'imccategory';
$filter_status_taxo   = 'imcstatus';
$filter_params_view   = array();
$control_pages = new PbVote_ControlPages( $voting_ids);

$insertpage = $control_pages->gen_url_page_add();
$editpage   = $control_pages->gen_url_page_edit( 0 );
$parameter_pass = "&issueid=";

if ( get_option('permalink_structure') ) { $perma_structure = true; } else {$perma_structure = false;}
if (!empty($voting_ids)) {
	$pb_issues_view_filters->set_value_param('svoting', $voting_ids);
}

wp_enqueue_script( 'imc-gmap' );
wp_enqueue_script( 'mapsV3_infobubble' ); // Insert addon lib for Google Maps V3 -> to style infowindows
wp_enqueue_script( 'mapsV3_richmarker' ); // Insert addon lib for Google Maps V3 -> to style marker

/********************************************* ISSUES PER PAGE ********************************************************/

$user_id = get_current_user_id();
$issues_pp_counter = 0;

// $pb_issues_view_filters = new  PbVote_ArchiveDisplayOptions( 'imc'); //todo

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
                            <option class="imc-CustomOptionDisabledStyle" value="<?php echo esc_attr($pb_issues_view_filters->get_value('ppage')); ?>" selected disabled><?php echo __('Display: ', 'pb-voting'); ?> <?php echo esc_html($pb_issues_view_filters->get_label('ppage', $pb_issues_view_filters->get_value('ppage'))); ?></option>

							<?php echo $pb_issues_view_filters->generate_options_long_ppage($my_permalink)?>
                        </select>
                    </label>
                </li>

                <li>
                    <label class="imc-NaviSelectStyle">
                        <select id="pbvSelectOrderingComponent" title="Order by" onchange="pbvFireNavigation('pbvSelectOrderingComponent')">
                            <option class="imc-CustomOptionDisabledStyle" value="<?php echo esc_attr($pb_issues_view_filters->get_value('sorder')); ?>" selected disabled><?php echo __('Order: ', 'pb-voting'); ?>  <?php echo esc_html($pb_issues_view_filters->get_label('sorder', $pb_issues_view_filters->get_value('sorder'))); ?></option>

							<?php echo $pb_issues_view_filters->generate_options_long_sorder($my_permalink)?>
                        </select>
                    </label>
                </li>

				<?php echo $pb_issues_view_filters->generate_lists_long_view($my_permalink)?>
				<?php
					if ($control_pages->can_vote()) {
						echo $control_pages->gen_button_vote(true, "36");
					}
					if ($control_pages->can_add_project()) {
						echo $control_pages->gen_button_add(true, "36");
					}
				?>
            </ul>
        </nav>
    </div>

    <div class="imc-OverviewFilteringBarStyle">

        <div class="ac-container">
            <input class="imc-DrawerCheckbox" id="ac-1" name="accordion-1" type="checkbox" />
            <div class="imc-SingleHeaderStyle imc-BGColorWhite">
                <label for="ac-1" class="imc-OverviewFilteringPanelLabelStyle">
                    <span><?php echo __('Search &amp; Filtering', 'pb-voting'); ?></span>
                    <span style="display: none;" id="pbItemCatFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Category', 'pb-voting');?></span>
                    <span style="display: none;" id="pbItemStatFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Status', 'pb-voting');?></span>
                    <span style="display: none;" id="pbItemKeywordFilteringLabel" class="u-pull-right imc-OverviewFilteringLabelStyle"><?php echo __('Key words', 'pb-voting');?></span>
                    <i class="material-icons md-24 u-pull-right" id="pbItemFilteringIndicator">filter_list</i>
                </label>

            </div>

            <article class="ac-small imc-DropShadow">

                <div class="imc-row imc-DrawerContentsStyle">

					<div class="imc-row">
						<h3 class="imc-SectionTitleTextStyle"><?php echo __('Search', 'pb-voting'); ?></h3>

						<input name="searchKeyword" autocomplete="off" placeholder="<?php
							echo __('Search key words','pb-voting'); ?>" id="pbItemSearchKeywordInput" type="search" class="imc-InputStyle"/>
					</div>
                    <div class="imc-DrawerFirstCol">

                        <input checked="checked" class="imc-CheckboxToggleStyle" id="pbItemToggleStatusCheckbox" type="checkbox" name="pbItemToggleStatusCheckbox" value="all">
                        <label class="imc-SectionTitleTextStyle" for="pbItemToggleStatusCheckbox"><?php echo __('Voting status', 'pb-voting'); ?></label>
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
                        <label class="imc-SectionTitleTextStyle" for="pbItemToggleCatsCheckbox"><?php echo __('Category', 'pb-voting'); ?></label>
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
                    <button class="imc-button imc-button-primary u-pull-right" onclick="pbItemOverviewGetFilteringData();"><?php echo __('Show selected', 'pb-voting'); ?></button>
                    <button class="imc-button u-pull-right" onclick="pbItemOverviewResetFilters();"><?php echo __('Show all', 'pb-voting'); ?></button>
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
				$paged = $pb_items->get_paged();
				$custom_query = $pb_items->get_query_data();

				// Pagination fix
				$temp_query = $wp_query;
				$wp_query = NULL;
				$wp_query = $custom_query;

				// Output custom query loop
				if ($custom_query->have_posts()) :
					$pbvote_current_view = $pb_issues_view_filters->get_value('view');
					while ($custom_query->have_posts()) :

						$custom_query->the_post();
						$issue_id = get_the_ID();
						$myIssue = (get_current_user_id() == get_the_author_meta('ID') ? true : false);

						$pendingColorClass = 'imc-ColorRed';
						$issues_pp_counter = $issues_pp_counter + 1;

						if ($pbvote_current_view == '1') {
							//LIST VIEW
							pb_item_archive_show_list($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $pb_issues_view_filters->get_plugin_base_url());
						} else {
							//GRID VIEW
							pb_item_archive_show_grid($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $pb_issues_view_filters->get_plugin_base_url());
						}

						$pb_item_category_currentterm = get_the_terms($post->ID, $filter_category_taxo);
						if ($pb_item_category_currentterm) {
							$current_category_id = $pb_item_category_currentterm[0]->term_id;
							$term_thumb = get_term_by('id', $current_category_id, $filter_category_taxo);
							$cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
							$pb_item_cat_name =  $pb_item_category_currentterm[0]->name;
							$cat_thumb_icon = $cat_thumb_arr[0];
						} else {
							$cat_thumb_icon = "";
							$pb_item_cat_name =  "";
						}

						$jsonIssuesArr[] = array (
							'title' => get_the_title(),
							'lat' => get_post_meta($post->ID, "imc_lat", true),
							'lng' => get_post_meta($post->ID, "imc_lng", true),
							'id' => get_the_ID(),
							'url' => get_permalink(),
							'photo' => get_the_post_thumbnail($post->ID, 'post-thumbnail'),
							'imc_url' => plugins_url(),
							'cat' => $pb_item_cat_name,
							'catIcon' => $cat_thumb_icon,
							'votes' => intval(get_post_meta($post->ID, 'imc_likes', true), 10),
							'myIssue' => $myIssue
						);
					endwhile;
				else :
					$map_options = get_option('gmap_settings');
					$jsonIssuesArr[] = array (
						'lat' => $map_options["gmap_initial_lat"],
						'lng' => $map_options["gmap_initial_lng"]
					);

					?>

                    <div class="imc-Separator"></div>

                    <div class="imc-row imc-CenterContents imc-GiveWhitespaceStyle">

                        <i class="material-icons md-huge imc-TextColorHint">local_offer</i>

                        <div class="imc-Separator"></div>

                        <h1 class="imc-FontRoboto imc-Text-XL imc-TextColorSecondary imc-TextItalic imc-TextMedium imc-CenterContents"><?php echo __('No record available','pb-voting'); ?></h1>

                        <div class="imc-Separator"></div>

                        <span class="imc-CenterContents imc-TextMedium imc-Text-LG imc-FontRoboto">
							<a href="<?php echo $insertpage ; ?>" class="imc-LinkStyle"><?php echo __('Report an issue','pb-voting'); ?></a>
							<?php if($pb_issues_view_filters->is_filtering_active()	) { ?>
                                <span class="imc-TextColorSecondary ">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                                <a href="javascript:void(0);" onclick="pbItemOverviewResetFilters();" class="imc-LinkStyle"><?php echo __('Reset filter','pb-voting'); ?></a>
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
				$start_indicator = (($paged - 1) * $pb_issues_view_filters->get_value('ppage')) + 1;
				if ($total_issues === 0) {$start_indicator = 0;}
				$end_indicator = (($paged - 1) * $pb_issues_view_filters->get_value('ppage')) + $issues_pp_counter; ?>

                <p class="img-PaginationLabelStyle imc-TextColorSecondary"><?php echo __('Showing','pb-voting'); ?> <b><?php echo esc_html($start_indicator); ?></b> - <b><?php echo esc_html($end_indicator) ?></b> <?php echo __('of','pb-voting'); ?> <b><?php echo esc_html($total_issues) ?></b> <?php echo __('issues','pb-voting'); ?></p>

				<?php imc_paginate($custom_query, $paged, $pb_issues_view_filters->get_value('ppage'), $pb_issues_view_filters->get_value('sorder'), $pb_issues_view_filters->get_value('view'), $pb_issues_view_filters->get_value('sstatus'), $pb_issues_view_filters->get_value('scategory'), $pb_issues_view_filters->get_value('keyword')); ?>
            </div>

        </div>

        <div class="imc-OverviewMapContainerStyle">
            <div id="imcOverviewMap" class="imc-OverviewMapStyle"></div>
        </div>

    </div>

    <!-- Initialize Map Scripts -->
    <script>
        /*setOverviewLayout();*/
		document.onload = imcInitOverviewMap(<?php echo json_encode($jsonIssuesArr) ?>, <?php echo json_encode( $pb_issues_view_filters->get_plugin_base_url()) ?>);

        jQuery( document ).ready(function() {

            var imported_cat = <?php echo json_encode( $pb_issues_view_filters->get_value_array('scategory')); ?>;

            var imported_status = <?php echo json_encode ($pb_issues_view_filters->get_value_array('sstatus')); ?>;
            var imported_keyword = <?php echo json_encode($pb_issues_view_filters->get_value_array('keyword')); ?>;
            var i;
						var countOfFilterParams = imported_status.length + imported_cat.length + imported_keyword.length;

            if (countOfFilterParams > 0) {
                jQuery('#pbItemFilteringIndicator').css('color', '#1ABC9C');
								jQuery('#pbItemStatusCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });
								jQuery('#pbItemCatCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });

                if ( imported_status.length > 0) {
                    jQuery('#pbItemStatFilteringLabel').show();

                    jQuery('#pbItemToggleStatusCheckbox').prop('checked', false);
                    jQuery('#pbItemToggleStatusCheckbox').prop('checked', true);

                    for (i=0;i<imported_status.length;i++) {
                        jQuery('#pbItem-stat-checkbox-'+imported_status[i]).prop('checked', true);
                    }
                }

                if (imported_cat.length > 0) {
                    jQuery('#pbItemCatFilteringLabel').show();

                    jQuery('#pbItemToggleCatsCheckbox').prop('checked', false);

                    for (i=0;i<imported_cat.length;i++) {
                        jQuery('#pbItem-cat-checkbox-'+imported_cat[i]).prop('checked', true);
                    }
                }

                if (imported_keyword.length > 0) {
                    jQuery('#pbItemKeywordFilteringLabel').show();

                    jQuery('#pbItemSearchKeywordInput').val(imported_keyword);
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
				jQuery(this).siblings('#pbItemStatusCheckboxes')
				.find("input[type='checkbox']")
				.prop('checked', this.checked);

                jQuery(this).siblings('#pbItemCatCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

                jQuery(this).siblings('#pbItemCatChildCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

                jQuery(this).siblings('#pbItemCatGrandChildCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

            });
        });

        function pbItemOverviewGetFilteringData() {
            var selectedStatus = '';
            var notSelectedStatus = '';
            var selectedCats = '';
            var notSelectedCats = '';
            var keywordString = '';

			jQuery('#pbItemStatusCheckboxes input:checkbox:checked').each(function() { selectedStatus = selectedStatus + jQuery(this).attr('value') +','; });
			selectedStatus = selectedStatus.slice(0, -1);

			jQuery('#pbItemStatusCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedStatus = notSelectedStatus + jQuery(this).attr('value') +','; });
			notSelectedStatus = notSelectedStatus.slice(0, -1);

			// if ( notSelectedStatus.length === 0) {
			// 	selectedStatus = "all";
			// }

			jQuery('#pbItemCatCheckboxes input:checkbox:checked').each(function() { selectedCats = selectedCats + jQuery(this).attr('value') +','; });
			selectedCats = selectedCats.slice(0, -1);

			jQuery('#pbItemCatCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedCats = notSelectedCats + jQuery(this).attr('value') +','; });
			notSelectedCats = notSelectedCats.slice(0, -1);

			// if ( notSelectedCats.length === 0) {
			// 	selectedCats = "all";
			// }

            if (jQuery('#pbItemSearchKeywordInput').val() !== '') {
                keywordString = jQuery('#pbItemSearchKeywordInput').val();
            }

            var base = <?php echo json_encode( $my_permalink ) ; ?>;
            var tempfilter1 = <?php echo json_encode(  $pb_issues_view_filters->create_url_variables_short()); ?>;
            var filter1 = decodeURIComponent(tempfilter1);
            var filter2 = '&sstatus=' + selectedStatus;
            var filter3 = '&scategory=' + selectedCats;
            var filter4 = '&keyword=' + keywordString;
            var link = base + filter1 + filter2 + filter3 + filter4;

            window.location = link;
        }

        function pbItemOverviewResetFilters() {
            var i;
            var	checkboxes = document.getElementsByTagName('input');

            for (i = 0; i < checkboxes.length; i++)
            {
                if (checkboxes[i].type === 'checkbox' && checkboxes[i].id !== 'ac-1' )
                {
                    checkboxes[i].checked = true;
                }
            }

            jQuery('#pbItemSearchKeywordInput').val('');

            pbItemOverviewGetFilteringData();
        }
    </script>
<?php } ?>
