<?php
/**
 * The template for displaying all imc issues with pb-vorting filtering options
 *
 */

// $pb_project_single = new pbProjectSingle;
global $wp_query, $post,$pb_issues_view_filters;
include_once( PB_VOTE_PATH_TEMPL . '/archive-imc_issues_body.php');
$pb_issues_view_filters = new  PbVote_ArchiveDisplayOptions( 'imc');

$voting_ids = null;
// tohle se musi dat do foreach pro vsechna hlasovani vybrana ve screene
// $hlasovani_meta = get_post_meta( $voting_ids, '', false);
// $params = hlasovani_query_arg( $hlasovani_meta['items'] ) ;

get_header();


?>

    <div>
        <input type="hidden" id="singleHlasovaniVotingId" value="<?php echo esc_html( $voting_ids); ?>"></input>
        <?php
          echo apply_filters( 'the_content', $post->post_content ) . "</br>";
          pb_items_archive_imc_issues_body($pb_issues_view_filters);
    ?>
    </div>
<?php get_footer(); ?>
