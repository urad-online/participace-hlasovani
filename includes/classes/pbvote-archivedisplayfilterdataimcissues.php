<?php
class PbVote_ArchiveDisplayFilterDataImcIssues extends PbVote_ArchiveDisplayFilterData
{
    public $taxo_status   = "imcstatus";
    public $taxo_category = "imccategory";
    public $post_type     = 'imc_issues';

    public function set_query_args_voting()
    {
        $imc_items = array();
        if(! empty( $this->user_params['svoting'] )) {
            $voting_ids = explode(",",  $this->user_params['svoting']);
            foreach ($voting_ids as $post_id) {
                $items =  get_post_meta( $post_id, "_pods_items", true);
                if (!empty($items)) {
                    $imc_items = array_merge( $imc_items, (array) $items );
                }
            }
        }
        $posts_in = array_unique($imc_items);
        if (count($posts_in) == 0) {
          array_push($posts_in, 0);
        }
        $this->query_args['post__in'] = $posts_in;
    }
    
    public function set_query_args_custom_1()
    {
        $this->user_params['voting_id'] = "1248,1212,1267";
        $this->user_params['voting_id'] = "1248";
        $imc_items = array();
        if(! empty( $this->user_params['voting_id'] )) {
            $voting_ids = explode(",",  $this->user_params['voting_id']);
            foreach ($voting_ids as $post_id) {
                $items =  get_post_meta( $post_id, "_pods_items", true);
                if (!empty($items)) {
                    $imc_items = array_merge( $imc_items, (array) $items );
                }
            }
        }
        $posts_in = array_unique($imc_items);
        if (count($posts_in) > 0) {
            $this->query_args['post__in'] = $posts_in;
        }
    }
}
