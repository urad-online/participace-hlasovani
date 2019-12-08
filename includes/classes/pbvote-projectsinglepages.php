<?php
/**
 * PB 1.00
 * FInds URL of pages for issue editing and URL for single voting page
 */
class PbVote_ProjectSinglePages {

    public function __construct( $post_id = 0) {
        $this->post_id = $post_id;
        $this->get_master_voting();
        $this->pages = new PbVote_ControlPages( $this->voting_id);
        $this->set_voting_url();
    }

    private function get_master_voting()
    {
        $args = array(
          'post_type'   => PB_VOTING_POST_TYPE,
          'posts_per_page' => -1,
          'orderby' => 'ID',
          'meta_query' => array(
          		array(
          			'key' => 'items',
          			'value' => $this->post_id,
          		)),
        );

        $custom_query = new WP_Query($args);

        // Output custom query loop
        if ($custom_query->have_posts()) {
          $this->voting_id = $custom_query->posts[0]->ID;
        } else {
          $this->voting_id = 0;
        }
        wp_reset_postdata();
    }

    private function set_voting_url()
    {
        $this->voting_url = esc_url( get_permalink( $this->voting_id )) ;
    }

    public function get_voting_page_url()
    {
        return $this->voting_url;
    }

    public function get_edit_page_url()
    {
       return $this->pages->gen_url_page_edit( $this->post_id );
    }
    public function get_insert_page_url()
    {
       return $this->pages->gen_button_add( false , "36");
    }

}
