<?php
class PbVote_SelectHomePage
{
    private $voting_id, $page_url, $status;
    private $taxo_status = PB_VOTING_STATUS_TAXO;

    public function __construct( )
    {
        $this->voting_id = 0;
        $this->page_url = "";
        $this->get_latest_active_hlasovani();
        $this->get_post_term();
    }

    public function get_homepage_url( )
    {
        return $this->page_url;
    }

    private function get_latest_active_hlasovani()
    {
      $args = array(
          'post_type' => PB_VOTING_POST_TYPE,
          'orderby'   => "date",
          'order'     => "DESC",
          'numberposts' => 1,
      );
      $temp_posts = get_posts($args);
      if( $temp_posts ) {
          $this->voting_id = $temp_posts[0]->ID;
      }
    }
    private function get_post_term()
    {
        $term = get_the_terms( $this->voting_id, $this->taxo_status);
        if (count($term)>0) {
          $this->status = $term[0];
        }
    }

}
