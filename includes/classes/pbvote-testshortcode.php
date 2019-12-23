<?php
class PbVote_TestShortCode
{
    private $result, $atts;
    private $template_file = PB_VOTE_PATH_TEMPL . '/reg_widget_content.php';
    private $status_taxo = PB_VOTING_STATUS_TAXO ;
    private $show_for_statuses = array( 'aktivni',);
    private $url_param_name_votingid = "votingid";

    public function __construct( $atts)
    {
        $this->read_atts($atts);
    }

    public function render_form( )
    {
        if ( isset($_POST['submitted']) ) {
            $output = "<p>SUBMITTED</p>";
        } else {
            $output = $this->print_form();
        }

        ob_start();

        echo $output;

        return ob_get_clean() ;
    }

    private function read_atts( $input )
    {
        $atts = array_change_key_case((array)$input, CASE_LOWER);

        $this->atts = shortcode_atts([ 'voting_id' => 0,
                                'voting_slug' => "",
                                'force_display' => false,
                                ], $atts);
    }

    private function print_form()
    {

      $output = '<div id="insert_form_wrapper">';
      $output .= '<form name="test_an_form" action="" id="primaryPostForm" method="POST" enctype="multipart/form-data">';
      $output .= '<div class="imc-row">';
      $output .= '<h3 class="imc-SectionTitleTextStyle">Nazev</h3>';
      $output .= '<input type="text" autocomplete="off" data-tip="zde kliknete" placeholder="zadejte nazev"';
      $output .= ' name="titul_postu" id="titul_postu" class="imc-InputStyle" value="" ></input>';
      $output .= '<label id="titul_postuLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
      $output .= '</div>';
      $output .= '<div class="imc-row">';
      $output .= '<input type="hidden" name="submitted" id="submitted" value="true" />';
      $output .= '<input id="imcInsertIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn" type="submit" value="Odeslat" />';
      $output .= '</div>';
      $output .= '</form>';
      $output .= '</div>';

      return $output;
    }
}
