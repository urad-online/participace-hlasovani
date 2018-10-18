<?php
class PbVote_GenWidget
{
    private $result, $atts;
    private $template_file = PB_VOTE_PATH_TEMPL . '/reg_widget_content.php';
    private $status_taxo = PB_VOTING_STATUS_TAXO ;
    private $show_for_statuses = array( 'aktivni',);

    public function __construct( $atts)
    {
        $this->read_atts($atts);
    }

    public function render_widget( )
    {
        if ( $post_id = $this->get_post_id() ) {
            $this->voting_id = $post_id;
        } else {
            $this->voting_id = get_the_ID();
        }


        if ($this->show_widget())  {
            $this->get_voting_meta();
            ob_start();

            include( $this->template_file );

            return ob_get_clean() ;
        } else {
            return "";
        }
    }

    private function show_widget()
    {
        $vote_status = wp_get_object_terms($this->voting_id, $this->status_taxo);
        if (is_wp_error($vote_status)) {
            return false;
        }

        if (! empty( $vote_status[0]->term_id)) {
            $temp_term = get_term_meta( $vote_status[0]->term_id);
            if ((!empty( $temp_term['allow_voting'][0]) ) && ($temp_term['allow_voting'][0] ) ) {
                return true;
            }
        }
        // if ( (count( $vote_status) > 0) && ( ! in_array( $vote_status[0]->slug, $this->show_for_statuses) ) && ( ! $this->atts['force_display'])) {
        //     return false;
        // }

        return false;
    }

    private function read_atts( $input )
    {
        $atts = array_change_key_case((array)$input, CASE_LOWER);

        $this->atts = shortcode_atts([ 'voting_id' => 0,
                                'voting_slug' => "",
                                'force_display' => false,
                                ], $atts);
    }
    public static function get_url( $base, $survey_id = '' )
    {
        if ( (! empty( $survey_id )) && ( ! strpos( $base, $survey_id) )) {
            $base .= "/index.php/" . $survey_id;
        }

        return $base;
    }
    public function get_post_id()
    {
        if ( !empty( $this->atts['voting_slug'] )) {
            $args = array(
                'name'        => $this->atts['voting_slug'],
                'post_type'   => PB_VOTING_POST_TYPE,
                'numberposts' => 1,
            );
            $temp_posts = get_posts($args);
            if( $temp_posts ) {
                return $temp_posts[0]->ID;
            }
        } elseif (!empty( $this->atts['voting_id'] )){
            $post = get_post( $this->atts['voting_id'] );
            return $post->ID;
        }
        return false;
    }

    private function get_voting_meta()
    {
        $this->pbvoting_meta = get_post_meta( $this->voting_id , '', false);

        if ((! empty($this->pbvoting_meta['token-message-type'][0])) && ($this->pbvoting_meta['token-message-type'][0])) {
            $this->msg_type = 'sms';
        } else {
            $this->msg_type = 'email';
        }
    }
    private function get_meta_value( $meta_key = "", $input = "")
    {
        if ( !empty( $this->pbvoting_meta[ $meta_key][0])) {
            return $this->pbvoting_meta[ $meta_key][0];
        } else {
            return $input;
        }
    }
}
