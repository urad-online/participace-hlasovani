<?php
/**
 * The template for displaying all single hlasivani and all linked issues
 *
 */

global $wp_query, $post,$pb_issues_view_filters;

include_once( PB_VOTE_PATH_TEMPL . '/archive-imc_issues_body.php');
$pb_issues_view_filters = new  PbVote_ArchiveDisplayOptions( 'imc');

$voting_ids = get_the_ID();
global  $voting_ids;
// $hlasovani_meta = get_post_meta( $voting_ids, '', false);
get_header();
do_shortcode('[INSERT_ELEMENTOR id="4470"]');
?>

    <div>
        <input type="hidden" id="singleHlasovaniVotingId" value="<?php echo esc_html( $voting_ids); ?>"></input>
        <?php
          echo apply_filters( 'the_content', $post->post_content );
          pb_items_archive_imc_issues_body($pb_issues_view_filters);
        ?>
    </div>
<?php get_footer(); ?>
