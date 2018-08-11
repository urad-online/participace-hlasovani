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
        if ($this->atts['voting_id']) {
            $post = get_post( $this->atts['voting_id'] );
        }

        $this->voting_id = get_the_ID();

        if ($this->show_widget())  {
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

        if ( (count( $vote_status) > 0) && ( ! in_array( $vote_status[0]->slug, $this->show_for_statuses) ) && ( ! $this->atts['force_display'])) {
            return false;
        }

        return true;
    }

    private function read_atts( $input )
    {
        $atts = array_change_key_case((array)$input, CASE_LOWER);

        $this->atts = shortcode_atts([ 'voting_id' => 0,
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
}
