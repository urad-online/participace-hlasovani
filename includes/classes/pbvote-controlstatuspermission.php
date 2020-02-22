<?php
class PbVote_ControlStatusPermission
{
    public $taxo_status   = PB_VOTING_STATUS_TAXO;
    public $post_type     = PB_VOTING_POST_TYPE;
    public $allow_voting  = false;
    public $allow_edit    = false;
    public $voting_id;

    public function __construct( $post_id = 0)
    {

        if (( !empty($post_id)) && ($post_id > 0) ) {
            $this->voting_id = $post_id;
            $this->get_status_meta();
        } else {
            $this->voting_id = null;
        }
    }

    private function get_status_meta()
    {
        $vote_status = wp_get_object_terms($this->voting_id, $this->taxo_status);
        if (is_wp_error($vote_status)) {
            return false;
        }

        if (! empty( $vote_status[0]->term_id)) {
            $temp_term = get_term_meta( $vote_status[0]->term_id);
            if ((!empty( $temp_term['allow_voting'][0]) ) && ($temp_term['allow_voting'][0] ) ) {
                $this->allow_voting =  true;
            }
            if ((!empty( $temp_term['allow_adding_project'][0]) ) && ($temp_term['allow_adding_project'][0] ) ) {
                $this->allow_edit =  true;
            }
        }
    }

    public function can_add_new()
    {
        return $this->allow_edit;
    }
    public function can_vote()
    {
        return $this->allow_voting;
    }
}
