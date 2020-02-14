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
        if (count($posts_in) > 0) {
          $this->query_args['post__in'] = $posts_in;
        }
    }
    public function set_query_args_period_by_voting_ids()
    {
        $taxo_items = array();
        if(! empty( $this->user_params['svoting'] )) {
            $voting_ids = explode(",",  $this->user_params['svoting']);
            foreach ($voting_ids as $post_id) {
                $items =  get_the_terms( $post_id, $this->taxo_period);
                if (!empty($items)) {
                    $taxo_items = array_merge( $taxo_items, (array) $items[0]->term_id );
                }
            }
        }

        $taxo_in = array_unique($taxo_items);
        if (count($taxo_in) > 0) {
          $this->set_query_add_taxo( $this->taxo_period, $taxo_in);
        }
    }

}
