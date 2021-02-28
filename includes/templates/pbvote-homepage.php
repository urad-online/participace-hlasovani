<?php
/**
 * The template for home page.Redirect to page according to active hlasovani status.
 *
 */

global $wp_query, $post;

$pb_targetpage = new  PbVote_SelectHomePage();
$home_page_url = get_home_url();
$hlasovani_home_page_url = $pb_targetpage->get_homepage_url();
if (! empty($hlasovani_home_page_url)) {
    wp_redirect( $hlasovani_home_page_url );
    exit;
} else {
    get_header();
    echo "<div>";
    echo apply_filters( 'the_content', $post->post_content );
    echo "</div>";
    get_footer();
}
