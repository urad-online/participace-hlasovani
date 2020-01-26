<?php
/**
 * PB 1.00
 * Creates pages for listing issues linked to hlasovani
 *
 */
function pbvote_create_list_page()
{
    $overview_page = get_page_by_path( 'vsechny-navrhy', OBJECT, 'page');

		if (! $overview_page) {
			$new_page_id = wp_insert_post(array(
					'post_title' => 'Všechny návrhy',
					'post_type' => 'page',
					'post_name' => 'vsechny-navrhy',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_content' => '',
					'post_status' => 'publish',
					'post_author' => get_user_by('id', 1)->ID,
					'menu_order' => 0,
			));
			if ($new_page_id && !is_wp_error($new_page_id)) {
					update_post_meta($new_page_id, '_wp_page_template', '/archive-pbvote_issues.php');
			}
		}
}
function pbvote_create_insert_page()
{
    $overview_page = get_page_by_path( 'novy-navrh', OBJECT, 'page');

		if (! $overview_page) {
			$new_page_id = wp_insert_post(array(
					'post_title' => 'Nový návrh',
					'post_type' => 'page',
					'post_name' => 'novy-navrh',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_content' => '',
					'post_status' => 'publish',
					'post_author' => get_user_by('id', 1)->ID,
					'menu_order' => 0,
			));
			if ($new_page_id && !is_wp_error($new_page_id)) {
					update_post_meta($new_page_id, '_wp_page_template', '/insert-project_issues.php');
			}
		}
}
function pbvote_create_edit_page()
{
    $overview_page = get_page_by_path( 'upravit-navrh', OBJECT, 'page');

		if (! $overview_page) {
			$new_page_id = wp_insert_post(array(
					'post_title' => 'Úprava návrhu',
					'post_type' => 'page',
					'post_name' => 'uprava-navrhu',
					'comment_status' => 'closed',
					'ping_status' => 'closed',
					'post_content' => '',
					'post_status' => 'publish',
					'post_author' => get_user_by('id', 1)->ID,
					'menu_order' => 0,
			));
			if ($new_page_id && !is_wp_error($new_page_id)) {
					update_post_meta($new_page_id, '_wp_page_template', '/edit-project_issues.php');
			}
		}
}
