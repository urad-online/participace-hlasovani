<?php
/**
 * PB 1.00
 * Creates pages for listing issues linked to hlasovani
 *
 */
function pbvote_create_list_page() {

    if (! imcplus_get_page_by_slug('imc-edit-issue')) {
        $new_page_id = wp_insert_post(array(
            'post_title' => 'IMC - Edit Issue page',
            'post_type' => 'page',
            'post_name' => 'imc-edit-issue',
            'comment_status' => 'closed',
            'ping_status' => 'closed',
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => get_user_by('id', 1)->user_id,
            'menu_order' => 0,
        ));
        if ($new_page_id && !is_wp_error($new_page_id)) {
            update_post_meta($new_page_id, '_wp_page_template', '/templates/edit-imc_issues.php');
        }

        update_option('hclpage', $new_page_id);
    }
}
