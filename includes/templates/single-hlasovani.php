<?php
/**
 * The template for displaying all single issues and attachments
 *
 */

// $pb_project_single = new pbProjectSingle;
global $wp_query, $post,$pb_issues_view_filters;
include_once( PB_VOTE_PATH_TEMPL . '/archive-imc_issues_body.php');
wp_reset_postdata();
$pb_issues_view_filters = new  PbVote_ArchiveDisplayOptions( 'imc');
pom_fun( $pb_issues_view_filters);

$post_id = get_the_ID();
$hlasovani_meta = get_post_meta( $post_id, '', false);
$params = hlasovani_query_arg( $hlasovani_meta['items'] ) ;
// $insertpage = getIMCInsertPage();
// $editpage = getIMCEditPage();
// $listpage = getIMCArchivePage();
// $voting_page = get_first_pbvoting_post();
get_header();


?>

    <div>
        <?php echo "textik </br>";
        echo $post_id . "</br>";
    echo apply_filters( 'the_content', $post->post_content ) . "</br>";
    pb_items_archive_imc_issues_body($pb_issues_view_filters);
    ?>
    </div>
<?php get_footer(); ?>
