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
$params = hlasovani_query_arg( $hlasovani_meta['items']);
?>

    <div><?php echo "textik </br>";
    echo $post_id . "</br>";
    echo apply_filters( 'the_content', $post->post_content ) . "</br>";
    // list_projects( $hlasovani_meta['items']);
    echo get_page_template_slug( 820) ." file </br>";//archive-imc_issues
    echo var_dump( get_page_templates()) . " to jsu sabloy </br>";
	include_once( PB_VOTE_PATH_TEMPL . '/archive-hlasovani.php');

    ?>
</div>
