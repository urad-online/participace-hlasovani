<?php
/**
 * The template for displaying all single issues and attachments
 *
 */

// $pb_project_single = new pbProjectSingle;
global $wp_query, $post,$pb_issues_view_filters;
include_once( PB_VOTE_PATH_TEMPL . '/archive-imc_issues_body.php');
$pb_issues_view_filters = new  PbVote_ArchiveDisplayOptions( 'imc');

$voting_ids = get_the_ID();
<<<<<<< HEAD
global  $voting_ids;
$hlasovani_meta = get_post_meta( $voting_ids, '', false);
// $params = hlasovani_query_arg( $hlasovani_meta['items'] ) ;
// $insertpage = getIMCInsertPage();
// $editpage = getIMCEditPage();
// $listpage = getIMCArchivePage();
// $voting_page = get_first_pbvoting_post();
=======

>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
get_header();


?>

    <div>
        <input type="hidden" id="singleHlasovaniVotingId" value="<?php echo esc_html( $voting_ids); ?>"></input>
        <?php
<<<<<<< HEAD
          echo apply_filters( 'the_content', $post->post_content );
          pb_items_archive_imc_issues_body($pb_issues_view_filters);
        ?>
=======
          echo apply_filters( 'the_content', $post->post_content ) . "</br>";
          pb_items_archive_imc_issues_body($pb_issues_view_filters);
    ?>
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    </div>
<?php get_footer(); ?>
