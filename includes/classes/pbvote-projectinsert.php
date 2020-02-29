<?php
class PbVote_ProjectInsert
{
    private $atts;
    public $return_url = "";
    private $voting_id = 0;
    private $is_submitted =  false;
    private $taxo_status   = PB_VOTING_STATUS_TAXO;
    private $post_type     = PB_VOTING_POST_TYPE;
    private $voting_status;
    private $message_no_add_status;
    private $save_error_message = "";

    public function __construct( $atts)
    {
        $this->read_atts($atts);
        $this->message_no_add_status = __('Akce hlasování je ve fázi kdy není povoleno přidávat návrhy');

        if (! $this->is_submitted) {
          $this->set_map_options();
          $this->project_single = new PbVote_RenderFormEdit;

          wp_localize_script('pb-formvalidator', 'formValidatorData', array(
            'rules' => $this->project_single->render_fields_js_validation(),
            'mapData' => $this->map_options,
            'budgetTable' => $this->project_single->get_field_property( 'cost', 'limit'),
            'fileSize'   => $this->project_single->get_field_property( 'attachment', 'max_size'),
          ));
        }

    }

    private function read_atts( $input )
    {
      $atts = array_change_key_case((array)$input, CASE_LOWER);

      $this->atts = shortcode_atts([
          'voting_id' => 0,
          'voting_slug' => "",
          'force_display' => false,
        ], $atts);

      if (isset($_POST['submitted'])) {
        $this->is_submitted = true;
      }

      if ($this->atts['voting_id'] == 0) {
        if ( ! empty($this->atts['voting_slug'])) {
          $this->voting_id = $this->get_voting_id_by_slug( $this->atts['voting_slug'] );
        } else {
          $this->find_active_event();
        }
      } else {
        $this->voting_id = $this->atts['voting_id'];
      }

      $this->set_return_url();
    }

    private function get_voting_id_by_slug( $slug )
    {
      $args = [
          'post_type'      => PB_VOTING_POST_TYPE,
          'posts_per_page' => 1,
          'post_name__in'  => [ $slug ],
          'fields'         => 'ids'
      ];
      $id = get_posts( $args );
      if ( ! empty($id[0]) ) {
        return $id[0];
      } else {
        return 0;
      }

    }

    private function set_return_url()
    {
      if ( $this->voting_id > 0 ) {
        $this->return_url = get_permalink( $this->voting_id );
      } else {
        $this->return_url = get_home_url();
      }
    }

    public function is_submitted()
    {
      return $this->is_submitted;
    }

    private function show_user_cant_edit_login()
    {
        return $this->show_permission_error( wp_login_url(), __('You are not authorised to report an issue','pb-voting'), __('Please login!','pb-voting'));
    }
    private function show_cant_edit_in_this_status( $message)
    {
        return $this->show_permission_error( $this->return_url, $message, __('Zpět na přehled','pb-voting'));
    }

    private function show_permission_error( $url = "#", $error_message = "", $link_label = "Zpět na přehled")
    {
      $plugin_path_url = pbvote_calculate_plugin_base_url();
      ob_start();
      ?>
      <div class="imc-Separator"></div>
      <div class="imc-container">
          <div class="imc-CardLayoutStyle imc-ContainerEmptyStyle" style="text-align:center;">
              <img src="<?php echo esc_url($plugin_path_url);?>/img/img_banner.jpg" class="" >
              <div class="imc-Separator"></div>
              <div class="imc-row imc-CenterContents ">
                  <i class="imc-EmptyStateIconStyle material-icons md-48">vpn_lock</i>
                  <div class="imc-Separator"></div>
                  <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo $error_message; ?></h3>
                  <div class="imc-Separator"></div>
                  <a href="<?php echo esc_url($url); ?>" class="imc-Text-XL imc-TextMedium imc-LinkStyle"><?php echo $link_label ; ?></a>
                  <div class="imc-Separator"></div>
              </div>
          </div>
      </div>
      <?php
      return ob_get_clean();
    }

    public function show_datasave_ok()
    {
      $plugin_path_url = pbvote_calculate_plugin_base_url();
      ob_start();
      ?>
      <div class="imc-Separator"></div>
      <div class="imc-container">
        <div class="imc-CardLayoutStyle imc-ContainerEmptyStyle" style="text-align:center;">
          <img src="<?php echo esc_url($plugin_path_url);?>/img/img_banner.jpg" class="">
          <div class="imc-Separator"></div>
          <div class="imc-row imc-CenterContents">
            <i class="imc-EmptyStateIconStyle material-icons md-48">vpn_lock</i>
            <div class="imc-Separator"></div>
            <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo __('New project proposal was successfully saved','pb-voting'); ?></h3>
            <div class="imc-Separator"></div>
            <a href="<?php echo esc_url($this->return_url); ?>" class="imc-Text-XL imc-TextMedium imc-LinkStyle"><?php echo __('Back to list of issues','pb-voting'); ?></a>
            <div class="imc-Separator"></div>
          </div>
        </div>
      </div>
      <?php
      return ob_get_clean();

    }

    public function render_form()
    {
      if (! $this->user_can_insert()) {
        return $output = $this->show_user_cant_edit_login();
      }

      $this->voting_status = new PbVote_ControlStatusPermission( $this->voting_id);
      if ( !$this->voting_status->can_add_new()) {
        return $output = $this->show_cant_edit_in_this_status($this->message_no_add_status);
      }

      ob_start();

      echo $this->print_form();

      return ob_get_clean() ;

    }

    private function user_can_insert()
    {
        return is_user_logged_in();
    }

    private function set_map_options()
    {
      wp_enqueue_script('imc-gmap');
      $map_options = get_option('gmap_settings');

      $this->map_options['lat']   	= $map_options["gmap_initial_lat"];
      $this->map_options['lng'] 		= $map_options["gmap_initial_lng"];
      $this->map_options['zoom'] 		= $map_options["gmap_initial_zoom"];
      $this->map_options['mscroll'] = $map_options["gmap_mscroll"];
      $this->map_options['bound'] 	= json_decode($map_options["gmap_boundaries"]);
    }


    private function print_form()
    {
      ?>
      <!-- <div class="imc-BGColorGray"> -->
      <div >
        <div class="imc-Separator"></div>
        <div class="imc-container">
            <!-- INSERT FORM BEGIN -->
            <div id="insert_form_wrapper">
                <form name="report_an_issue_form" action="" id="primaryPostForm" method="POST" enctype="multipart/form-data">
                    <div class="imc-CardLayoutStyle">
                        <h2 class="imc-PageTitleTextStyle imc-TextColorPrimary"><?php echo __('Report a new issue','pb-voting'); ?></h2>
                        <div class="imc-Separator"></div>
                          <?php echo $this->project_single->render_form_edit( array(
                              'lat' => $this->map_options['lat'],
                              'lon' => $this->map_options['lng'],
                            )) ;?>
                        <!-- Issue's Image -->
                        <div class="imc-row">
                          <span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>
                        </div>
                    </div>
                    <div class="imc-row">
                      <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                      <input type="hidden" name="submitted" id="submitted" value="true" />
                      <input id="pbVoteIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn" type="submit" value="Odeslat" />
                    </div>
                </form>
            </div> <!-- Form end -->
        </div>
      </div>
      <?PHP
    }

    public function save_data()
    {
        $success = false;
        $this->voting_status = new PbVote_ControlStatusPermission( $this->voting_id);
        if ( !$this->voting_status->can_add_new()) {
            $this->save_error_message = $this->message_no_add_status;
            return false;
        }

        if ( isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {
            $project_save = new PbVote_ProjectSaveData;

          	$post_id = $project_save->project_insert( $this->voting_id );

          	if( $post_id ) {
                $success = true;
          	} else {
                $this->save_error_message = __( 'Chyba při ukládání dat' , 'pb-voting');
            }
        }
      return $success;
    }

    public function show_data_save_error()
    {
        return $this->show_permission_error( $this->return_url, $this->save_error_message, __('Zpět na přehled','pb-voting'));
    }

    private function find_active_event()
    {
      $all_status_terms = get_terms( $this->taxo_status , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
      $status_allow = array();
      foreach ($all_status_terms as $key => $value) {
        $can_insert = get_term_meta($value->term_id, "allow_adding_project");
        if ( ! empty($can_insert[0]) && $can_insert[0] ) {
          array_push( $status_allow, $value->term_id);
        }
      }
      if (count( $status_allow) > 0 ) {
        $query_args = array(
          'post_type' => $this->post_type,
          'posts_per_page' => 1,
          'tax_query' => array(array(
            'taxonomy' => $this->taxo_status,
            'field' => 'term_id',
            'terms' => $status_allow,
          )),
        );

        $pom = get_posts( $query_args );
        if ( (is_array($pom)) && (count($pom) > 0)) {
          $this->voting_id = $pom[0]->ID;
        }
      }

      return "";
    }
    protected function show_data_save_error9()
    {

    }
}
