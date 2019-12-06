<?php
/**
 * The template for displaying all single issues and attachments
 *
 */

// $pb_project_single = new pbProjectSingle;
$post_id = get_the_ID();
$hlasovani_meta = get_post_meta( $post_id, '', false);
$params = hlasovani_query_arg( $hlasovani_meta['items'] ) ;
$insertpage = getIMCInsertPage();
$editpage = getIMCEditPage();
$listpage = getIMCArchivePage();
$voting_page = get_first_pbvoting_post();

if ( get_option('permalink_structure') ) { $perma_structure = true; } else {$perma_structure = false;}
if( $perma_structure){$parameter_pass = '/?edit_id=';} else{$parameter_pass = '&edit_id=';}

$user_id = get_current_user_id();
$plugin_path_url = pbvote_calculate_plugin_base_url();
$issues_pp_counter = 0;

$params = hlasovani_query_arg( $hlasovani_meta['items']);
$hlasovani_list = new WP_Query( $params );

get_header();

?>

<div class="imc-BGColorGray imc-OverviewWrapperStyle">
    <div><?php echo "textik </br>";
    echo $post_id . "</br>";
    echo apply_filters( 'the_content', $post->post_content ) . "</br>";
    list_projects( $hlasovani_meta['items']);

    $imported_view = '1' ;

    if ($hlasovani_list->have_posts()) {
        while ($hlasovani_list->have_posts()) :

            $hlasovani_list->the_post();
            $issue_id = get_the_ID();
            $myIssue = (get_current_user_id() == get_the_author_meta('ID') ? true : false);

            $pendingColorClass = 'imc-ColorRed';
            $issues_pp_counter = $issues_pp_counter + 1;

            if ($imported_view == '1') {
                //LIST VIEW
                imc_archive_show_list($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url);
            } else {
                //GRID VIEW
                imc_archive_show_grid($post, $editpage, $parameter_pass, $user_id, $pendingColorClass, $plugin_path_url);
            }

            $imccategory_currentterm = get_the_terms($post->ID, 'imccategory');
            if ($imccategory_currentterm) {
                $current_category_id = $imccategory_currentterm[0]->term_id;
                $term_thumb = get_term_by('id', $current_category_id, 'imccategory');
                $cat_thumb_arr = wp_get_attachment_image_src( $term_thumb->term_image);
            }

        endwhile;
    } else {
        $href_url = '#';
    }
    wp_reset_postdata();


    ?>
    </div>
</div>
<?php get_footer(); ?>
