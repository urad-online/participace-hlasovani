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
$insertpage = "";
$editpage = "";
$listpage = "";
$voting_page = "";
$voting_category_taxo = 'voting_category';
$voting_status_taxo   = 'voting_status';
$filter_params_view   = array();

if ( get_option('permalink_structure') ) { $perma_structure = true; } else {$perma_structure = false;}
if( $perma_structure){$parameter_pass = '/?myparam=';} else{$parameter_pass = '&myparam=';}

/********************************************* ISSUES PER PAGE ********************************************************/
////Validating: User Input Data
$pbvote_safe_ppage_values = array( -1, 6, 12, 24 ); //all possible options
$pbvote_safe_ppage = isset($_GET['pbv_ppage']) ? intval( $_GET['pbv_ppage'] ) : '';
$filter_params_view = array_merge( $filter_params_view, array( 'pbv_ppage' => $pbvote_safe_ppage));


if ( ! in_array( $pbvote_safe_ppage, $pbvote_safe_ppage_values, true ) ) {$pbvote_safe_ppage = '';}
$pbvote_safe_ppage = sanitize_text_field( $pbvote_safe_ppage );//Sanitizing: Cleaning User Input
$filter_params_view = array_merge( $filter_params_view, array( 'pbv_ppage' => $pbvote_safe_ppage));

//Pass the safe ppage input to session variable
if($pbvote_safe_ppage!=''){$_SESSION['pbv_ppage_session']= $pbvote_safe_ppage;}
if(isset($_SESSION['pbv_ppage_session'])) { $pbvote_imported_ppage = $_SESSION['pbv_ppage_session'];} else { $pbvote_imported_ppage = '6'; }
$pbvote_imported_ppage_label = $pbvote_imported_ppage;
if($pbvote_imported_ppage=='-1') {$pbvote_imported_ppage_label = 'All'; }

/**********************************************************************************************************************/

/********************************************* ORDER OF OVERVIEW ISSUES ***********************************************/
//Validating: User Input Data
$pbvote_safe_sorder_values = array( 1,2 ); //all possible options, 1=order by date, 2=order by votes
$pbvote_safe_sorder = isset($_GET['pbv_sorder']) ? intval( $_GET['pbv_sorder'] ) : '';

if ( ! in_array( $pbvote_safe_sorder, $pbvote_safe_sorder_values, true ) ) {$pbvote_safe_sorder = '';}
$pbvote_safe_sorder = sanitize_text_field( $pbvote_safe_sorder );//Sanitizing: Cleaning User Input
$filter_params_view = array_merge( $filter_params_view, array( 'pbv_sorder' => $pbvote_safe_sorder));

//Pass the safe order input to session variable
if($pbvote_safe_sorder!=''){$_SESSION['pbv_sorder_session']= $pbvote_safe_sorder;}
if(isset($_SESSION['pbv_sorder_session'])) { $pbvote_imported_order = $_SESSION['pbv_sorder_session'];} else { $pbvote_imported_order = '1'; }
$pbvote_imported_order_label = __('Date', 'pb-voting');
if ($pbvote_imported_order == '2') {$pbvote_imported_order_label = __('Votes', 'pb-voting'); }


/**********************************************************************************************************************/

/********************************************* VIEW OF OVERVIEW ISSUES ************************************************/

//We need default view from Settings
$pbvote_defaultViewOption = '1';

//Validating: User Input Data
$pbvote_safe_view_values = array( 1,2 ); //all possible options, 1=order by date, 2=order by votes
$pbvote_safe_view = isset($_GET['pbv_view']) ? intval( $_GET['pbv_view'] ) : '';

if ( ! in_array( $pbvote_safe_view, $pbvote_safe_view_values, true ) ) {$pbvote_safe_view = '';}
$pbvote_safe_view = sanitize_text_field( $pbvote_safe_view );//Sanitizing: Cleaning User Input
$filter_params_view = array_merge( $filter_params_view, array( 'pbv_view' => $pbvote_safe_view));
//Pass the safe order input to session variable
if($pbvote_safe_view!=''){$_SESSION['pbv_view_session']= $pbvote_safe_view;}
if(isset($_SESSION['pbv_view_session'])) { $pbvote_imported_view = $_SESSION['pbv_view_session'];} else { $pbvote_imported_view = $pbvote_defaultViewOption; }

/**********************************************************************************************************************/

/********************************************* FILTERED IDS OF STATUS *************************************************/
// Sanitizing: Cleaning User Input
$pbvote_safe_status = isset($_GET['pbv_sstatus']) ? sanitize_text_field( $_GET['pbv_sstatus'] ) : '';

// Validating: User Input Data
$pbvote_safe_status = array_map( 'intval', array_filter( explode(',', $pbvote_safe_status), 'is_numeric' ) );
if ( ! $pbvote_safe_status ) {$pbvote_safe_status = '';}
//Pass the safe status_ids input to session variable
if(isset($pbvote_safe_status) && $pbvote_safe_status!='') {
	$_SESSION['pbv_sstatus_session'] = $pbvote_safe_status;
}else{
	$_SESSION['pbv_sstatus_session'] = false;
}

if($_SESSION['pbv_sstatus_session']) {
	$pbvote_imported_sstatus = implode(",", $_SESSION['pbv_sstatus_session']);
	$pbvote_imported_sstatus4checkbox = $_SESSION['pbv_sstatus_session'];
}else{
	$pbvote_imported_sstatus = false;
	$pbvote_imported_sstatus4checkbox = '';
}

/**********************************************************************************************************************/

/********************************************* FILTERED IDS OF CATEGORY ***********************************************/
//Sanitizing: Cleaning User Input
$pbvote_safe_category = isset($_GET['pbv_scategory']) ? sanitize_text_field( $_GET['pbv_scategory'] ) : '';

//Validating: User Input Data
$pbvote_safe_category = array_map( 'intval', array_filter( explode(',', $pbvote_safe_category), 'is_numeric' ) );
if ( ! $pbvote_safe_category ) {$pbvote_safe_category = '';}
//Pass the safe category_ids input to session variable
if(isset($pbvote_safe_category) && $pbvote_safe_category!='') {
	$_SESSION['pbv_scategory_session'] = $pbvote_safe_category;
}else{
	$_SESSION['pbv_scategory_session'] = false;
}

if($_SESSION['pbv_scategory_session']) {
	$pbvote_imported_scategory = implode(",", $_SESSION['pbv_scategory_session']);
	$pbvote_imported_scategory4checkbox = $_SESSION['pbv_scategory_session'];
}else{
	$pbvote_imported_scategory = false;
	$pbvote_imported_scategory4checkbox = '';
}

/**********************************************************************************************************************/

/********************************************* FILTERED KEYWORD *******************************************************/
//Sanitizing: Cleaning User Input
$pbvote_safe_keyword = isset($_GET['pbv_keyword']) ? sanitize_text_field( $_GET['pbv_keyword'] ) : '';


//Validating: User Input Data (if lenght is more than 40 chars)
if ( strlen( $pbvote_safe_keyword ) > 40 ) {$pbvote_safe_keyword = substr( $pbvote_safe_keyword, 0, 40 );}
//Pass the safe keyword input to session variable
if(isset($pbvote_safe_keyword) && $pbvote_safe_keyword!='') {
	$_SESSION['pbv_keyword_session'] = $pbvote_safe_keyword;
}else{
	$_SESSION['pbv_keyword_session'] = false;
}
if($_SESSION['pbv_keyword_session']) {
	$pbvote_imported_keyword = $_SESSION['pbv_keyword_session'];
}else{
	$pbvote_imported_keyword = false;
}

/**********************************************************************************************************************/


$filtering_active = false;
if (!empty($pbvote_imported_scategory) || !empty($pbvote_imported_sstatus) || !empty($pbvote_imported_keyword)) {$filtering_active = true;}

$user_id = get_current_user_id();
$plugin_path_url = imc_calculate_plugin_base_url();
$issues_pp_counter = 0;

get_header();


if ( is_front_page() || is_home() ) {
	$front_page_id = get_option('page_on_front');
	$my_permalink = _get_page_link($front_page_id);
}else{
	$my_permalink = get_the_permalink();
} ?>
    <div class="imc-SingleHeaderStyle imc-BGColorWhite">

        <nav class="imc-OverviewHeaderNavStyle">
            <ul class="imc-OverviewNavUlStyle">
                <li>
                    <label class="imc-NaviSelectStyle">
                        <select id="imcSelectDisplayComponent" title="Issues to display" onchange="imcFireNavigation('imcSelectDisplayComponent')">
                            <option class="imc-CustomOptionDisabledStyle" value="<?php echo esc_attr($pbvote_imported_ppage); ?>" selected disabled><?php echo __('Display: ', 'pb-voting'); ?> <?php echo esc_html($pbvote_imported_ppage_label); ?></option>

                            <option value="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $issues_per_page = '6', $pbvote_imported_order, $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>">6</option>
                            <option value="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $issues_per_page = '12', $pbvote_imported_order, $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>">12</option>
                            <option value="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $issues_per_page = '24',$pbvote_imported_order, $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>">24</option>
                            <option value="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $issues_per_page = '-1', $pbvote_imported_order, $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>"><?php echo __('All', 'pb-voting'); ?></option>
                        </select>
                    </label>
                </li>

                <li>
                    <label class="imc-NaviSelectStyle">
                        <select id="imcSelectOrderingComponent" title="Order by" onchange="imcFireNavigation('imcSelectOrderingComponent')">
                            <option class="imc-CustomOptionDisabledStyle" value="<?php echo esc_attr($pbvote_imported_order); ?>" selected disabled><?php echo __('Order: ', 'pb-voting'); ?>  <?php echo esc_html($pbvote_imported_order_label); ?></option>

                            <option value="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $pbvote_imported_ppage, $theorder = '1', $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>"><?php echo __('Date', 'pb-voting'); ?></option>
                            <option value="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $pbvote_imported_ppage, $theorder = '2', $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>"><?php echo __('Votes', 'pb-voting'); ?></option>
                        </select>
                    </label>
                </li>

				<?php if ($pbvote_imported_view == '1') { ?>

                    <li><a href="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $pbvote_imported_ppage, $pbvote_imported_order, $theview = '1', $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>" class="imc-SingleHeaderLinkStyle imc-NavSelectedStyle"><i class="material-icons md-36 imc-VerticalAlignMiddle">view_stream</i></a></li>

                    <li><a href="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $pbvote_imported_ppage, $pbvote_imported_order, $theview = '2', $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>" class="imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-VerticalAlignMiddle">apps</i></a></li>

				<?php } else { ?>

                    <li><a href="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $pbvote_imported_ppage, $pbvote_imported_order, $theview = '1', $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>" class="imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-VerticalAlignMiddle">view_stream</i></a></li>

                    <li><a href="<?php echo esc_url( $my_permalink . pbvote_create_filter_variables_long($perma_structure, $pbvote_imported_ppage, $pbvote_imported_order, $theview = '2', $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword) ); ?>" class="imc-SingleHeaderLinkStyle imc-NavSelectedStyle"><i class="material-icons md-36 imc-VerticalAlignMiddle">apps</i></a></li>

				<?php } ?>
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
							name="pbvToggleCatsCheckbox" value="">
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

                                    <input checked="checked" class="imc-CheckboxStyle" id="pbv-cat-checkbox-<?php echo esc_html($pbvcategory->term_id); ?>" type="checkbox" name="<?php echo esc_attr($pbvcategory->name); ?>" value="<?php echo esc_attr($pbvcategory->term_id); ?>">
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
				$paged = 1;
				if ( get_query_var( 'paged' ) ) {$paged = get_query_var('paged'); // On a paged page.
				} else if ( get_query_var( 'page' ) ) {$paged = get_query_var('page'); // On a "static" page.
				}

				//Basic query calls depending the user
				if ( is_user_logged_in() && current_user_can( 'administrator' ) ){ //not user
					// $custom_query_args = imcLoadIssuesForAdmins($paged,$pbvote_imported_ppage,$pbvote_imported_sstatus,$pbvote_imported_scategory);
					$custom_query_args = pbvLoadIssuesForGuests($paged,$pbvote_imported_ppage,$pbvote_imported_sstatus,$pbvote_imported_scategory);
				} else {
					$custom_query_args = pbvLoadIssuesForGuests($paged,$pbvote_imported_ppage,$pbvote_imported_sstatus,$pbvote_imported_scategory);
				}

				//search string
				if(!$pbvote_imported_keyword == false){
					$custom_query_args['s'] = $pbvote_imported_keyword;
					$custom_query_args['exact'] = false;
				}

				//sorting by date or likes
				if ($pbvote_imported_order == '1') {
					$custom_query_args['orderby'] = 'date';
				}else{
					$custom_query_args['meta_key'] = 'imc_likes';
					$custom_query_args['orderby'] = 'meta_value_num';
					$custom_query_args['order']= 'DESC';
				}

				// Instantiate custom query
				// $custom_query = new WP_Query($custom_query_args);
				$custom_query = pom_fun($custom_query_args);

				// Pagination fix
				$temp_query = $wp_query;
				$wp_query = NULL;
				$wp_query = $custom_query;

				$pbvote_imported_view = "1";
				// Output custom query loop
				if ($custom_query->have_posts()) :
					while ($custom_query->have_posts()) :

						$custom_query->the_post();
						$issue_id = get_the_ID();
						$myIssue = (get_current_user_id() == get_the_author_meta('ID') ? true : false);

						$pendingColorClass = 'imc-ColorRed';
						$issues_pp_counter = $issues_pp_counter + 1;

						if ($pbvote_imported_view == '1') {
							//LIST VIEW
							pbvote_archive_show_list($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url);
						} else {
							//GRID VIEW
							pbvote_archive_show_grid($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url);
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
							<?php if($filtering_active) { ?>
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
				$start_indicator = (($paged - 1) * $pbvote_imported_ppage) + 1;
				if ($total_issues === 0) {$start_indicator = 0;}
				$end_indicator = (($paged - 1) * $pbvote_imported_ppage) + $issues_pp_counter; ?>

                <p class="img-PaginationLabelStyle imc-TextColorSecondary"><?php echo __('Showing','pb-voting'); ?> <b><?php echo esc_html($start_indicator); ?></b> - <b><?php echo esc_html($end_indicator) ?></b> <?php echo __('of','pb-voting'); ?> <b><?php echo esc_html($total_issues) ?></b> <?php echo __('issues','pb-voting'); ?></p>

				<?php imc_paginate($custom_query, $paged, $pbvote_imported_ppage, $pbvote_imported_order, $pbvote_imported_view, $pbvote_imported_sstatus, $pbvote_imported_scategory, $pbvote_imported_keyword); ?>
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

            var imported_cat = <?php echo json_encode($pbvote_imported_scategory4checkbox); ?>;

            var imported_status = <?php echo json_encode($pbvote_imported_sstatus4checkbox); ?>;
            var imported_keyword = <?php echo json_encode($pbvote_imported_keyword); ?>;
            var i;


            if (imported_status || imported_cat || imported_keyword) {
				console.log("import status: " + imported_status);
                jQuery('#pbvFilteringIndicator').css('color', '#1ABC9C');
				jQuery('#pbvStatusCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });

                if (imported_status) {
                    jQuery('#imcStatFilteringLabel').show();

                    jQuery('#pbvToggleStatusCheckbox').prop('checked', false);

                    for (i=0;i<imported_status.length;i++) {
						console.log("status: " + i + " - " +imported_status[i]);
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


        function imcFireNavigation(id) {
            location.href = jQuery('#'+id)[0].value;
            jQuery( id +" option:disabled" ).prop('selected', true);
        }

        // Checkbox select propagation
        jQuery(function () {
            jQuery("input[type='checkbox']").change(function () {
                jQuery(this).siblings('#pbvCatCheckboxes')
                    .find("input[type='checkbox']")
                    .prop('checked', this.checked);

                jQuery(this).siblings('#pbvStatusCheckboxes')
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
            var tempfilter1 = <?php echo json_encode( pbvote_create_filter_variables_short($perma_structure, $pbvote_imported_ppage, $pbvote_imported_order, $pbvote_imported_view ) ); ?>;
            var filter1 = decodeURIComponent(tempfilter1);
            var filter2 = '&pbv_sstatus=' + selectedStatus;
            var filter3 = '&pbv_scategory=' + selectedCats;
            var filter4 = '&pbv_keyword=' + keywordString;
            var link = base + filter1 + filter2 + filter3 + filter4;

            window.location = link;
        }

        function pbvOverviewResetFilters() {
            var i;
            var	checkboxes = document.getElementsByTagName('input');
			console.log("reset filter");

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
