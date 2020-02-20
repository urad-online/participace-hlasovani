<?php
class PbVote_ProjectInsert
{
    private $atts;
    public $return_url = "";
    private $voting_id = 0;
    private $is_submitted =  false;
    private $taxo_status   = PB_VOTING_STATUS_TAXO;
    private $post_type     = PB_VOTING_POST_TYPE;

    public function __construct( $atts)
    {
        $this->read_atts($atts);
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
    private function show_user_cant_edit()
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
                  <i class="imc-EmptyStateIconStyle material-icons md-huge">vpn_lock</i>
                  <div class="imc-Separator"></div>
                  <h3 class="imc-FontRoboto imc-Text-LG imc-TextColorSecondary imc-TextMedium imc-CenterContents"><?php echo __('You are not authorised to report an issue','pb-voting'); ?></h3>
                  <div class="imc-Separator"></div>
                  <a href="<?php echo esc_url(wp_login_url()); ?>" class="imc-Text-XL imc-TextMedium imc-LinkStyle"><?php echo __('Please login!','pb-voting'); ?></a>
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
            <i class="imc-EmptyStateIconStyle material-icons md-huge">vpn_lock</i>
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
        return $output = $this->show_user_cant_edit();
      }

      $this->set_map_options();
      $this->project_single = new PbVote_RenderFormEdit;

      ob_start();

      echo $this->print_form();
      echo $this->add_form_javascript();

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

      $this->map_options['initial_lat']   	= $map_options["gmap_initial_lat"];
      $this->map_options['initial_lng'] 		= $map_options["gmap_initial_lng"];
      $this->map_options['initial_zoom'] 		= $map_options["gmap_initial_zoom"];
      $this->map_options['initial_mscroll'] = $map_options["gmap_mscroll"];
      $this->map_options['initial_bound'] 	= $map_options["gmap_boundaries"];
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
                              'lat' => $this->map_options['initial_lat'],
                              'lon' => $this->map_options['initial_lng'],
                            )) ;?>
                        <!-- Issue's Image -->
                        <div class="imc-row">
                          <span class="u-pull-left imc-ReportFormSubmitErrorsStyle" id="imcReportFormSubmitErrors"></span>
                        </div>
                    </div>
                    <div class="imc-row">
                      <?php wp_nonce_field('post_nonce', 'post_nonce_field'); ?>
                      <input type="hidden" name="submitted" id="submitted" value="true" />
                      <input id="imcInsertIssueSubmitBtn" class="imc-button imc-button-primary imc-button-block pb-project-submit-btn" type="submit" value="Odeslat" />
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
      if ( isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {
        $project_save = new PbVote_ProjectSaveData;

      	$post_id = $project_save->project_insert( $this->voting_id );

      	if( $post_id ) {
          $success = true;
      	}
      }
      return $success;
    }

    private function add_form_javascript()
    {
      ?>
      <script>

          "use strict";
          (function(){

              /*Google Maps API*/
              google.maps.event.addDomListener(window, 'load', imcInitMap);

              jQuery( document ).ready(function() {
                  var validator = new FormValidator('report_an_issue_form',
  					<?PHP echo $this->project_single->render_fields_js_validation(); ?>,
  				function(errors, events) {
    					jQuery('label.imc-ReportFormErrorLabelStyle').html("");
                  if (errors.length > 0) {
                      var i, j;
                      var errorLength;
                      jQuery("#imcReportFormSubmitErrors").html("");
                      jQuery('#postTitleLabel').html();

                      for (i = 0, errorLength = errors.length; i < errorLength; i++) {
                          if (errors[i].name === "featured_image") {
              								imcDeleteAttachedImage('imcReportAddImgInput');
              								jQuery("#imcReportFormSubmitErrors").html(errors[i].message);
                          } else {
              								for(j=0; j < Math.min(1, errors[i].messages.length); j++) {
              									/* zobrazuje se jen prvni chyba, validator vraci stejnou chybu pokud je vice praidel */
              									jQuery('#'+errors[i].id+'Label').html(errors[i].messages[j]);
              									jQuery("#imcReportFormSubmitErrors").append("<p>"+errors[i].message+"</p>");
              								}
                          }
                      }
                  } else {
                      jQuery('#imcInsertIssueSubmitBtn').attr('disabled', 'disabled');
                      jQuery('label.imc-ReportFormErrorLabelStyle').html();
                  }
            });
    				validator.registerConditional( 'pb_project_js_validate_required', function(field){
    					/* povinna pole se validuji pouze pokud narhovatel zaskrtne odeslat k vyhodnoceni
    					 plati pro pole s pravidlem "depends" */
    					return jQuery('#pb_project_edit_completed').prop('checked');
    				});
        				validator.registerCallback( 'pb_project_js_validate_budget', function(value){
                  var result = false;
                  var total = 0;
                  var pom = Array.from(JSON.parse( value ));

                  if( Array.isArray(pom)) {
                      if ( pom.length > 0) {
                        total = Math.round(calculate_total_sum()*1.1);
                        if (total >=350000 && total <= 2000000) {
    											result = true;
    										}
                      }
                  };
        					return result;
        				}).setMessage('pb_project_js_validate_budget', 'Celková částka předpokládaných nákladů musí být mezi 350tis až 2 mil Kč.');

                validator.registerCallback( 'pb_project_js_validate_locality', function(value){
                  var result = false;
                  var pom = Array.from(JSON.parse( value ));
                  if( Array.isArray(pom)) {
                      if ( pom.length > 0) {
                        result = true;
                      }
                  };
        					return result;
        				}).setMessage('pb_project_js_validate_locality', 'Vyberte alespoň jednu lokalitu, které se návrh týká.');
                validator.setMessage( 'required', 'Pole %s je povinné.');
                validator.setMessage( 'min_length', 'Délka pole %s je minimálně %s znaků.');
                validator.setMessage( 'max_length', 'Délka pole %s je maximálně %s znaků.');
                validator.setMessage( 'valid_email', 'Pole %s neobsahuje platnou emailovou adresu.');
                validator.setMessage( 'valid_phone', 'Pole %s neobsahuje platné telefonní číslo.');
              });
          })();

          function imcInitMap() {
              "use strict";
              var mapId = "imcReportIssueMapCanvas";
              // Checking the saved latlng on settings
              var lat = parseFloat('<?php echo floatval($this->map_options['initial_lat']); ?>');
              var lng = parseFloat('<?php echo floatval($this->map_options['initial_lng']); ?>');
              if (lat === '' || lng === '' ) { lat = 40.1349854; lng = 22.0264538; }

              // Options casting if empty
              var zoom = parseInt('<?php echo intval($this->map_options['initial_zoom'], 10); ?>', 10);

              if(!zoom){ zoom = 7; }
              var allowScroll;
              '<?php echo intval($this->map_options['initial_mscroll'], 10); ?>' === '1' ? allowScroll = true : allowScroll = false;
              var boundaries = <?php echo json_encode($this->map_options['initial_bound']);?> ?
  				<?php echo json_encode($this->map_options['initial_bound']);?>: null;

              imcInitializeMap(lat, lng, mapId, 'imcAddress', true, zoom, allowScroll, JSON.parse(boundaries));
              imcFindAddress('imcAddress', false, lat, lng);
          }

          document.getElementById('imcReportAddImgInput').onchange = function (e) {
              var file = jQuery("#imcReportAddImgInput")[0].files[0];
              // Delete image if "Cancel"
              if (document.getElementById("imcReportAttachedImageThumb")) {
                  imcDeleteAttachedImage("imcReportAttachedImageThumb");
              }
              // If image is too big
              // Get filesize
              var maxFileSize = '<?php echo imc_file_upload_max_size(); ?>';
              if(file && file.size < maxFileSize) {
                  loadImage.parseMetaData(file, function(data) { //read image metadata to get orientation info
                      var orientation = 0;
                      if (data.exif) {
                          orientation = data.exif.get('Orientation');
                      }
                      document.getElementById('imcPhotoOri').value = parseInt(orientation, 10);
                      var loadingImage =	loadImage (
                          file,
                          function (img) {
                              if(img.type === "error") {
                                  jQuery("#imcReportFormSubmitErrors").html("The Photo field must contain only gif, png, jpg files.").show();
                                  if (document.getElementById("imcReportAttachedImageThumb")) {
                                      imcDeleteAttachedImage("imcReportAttachedImageThumb");
                                  }
                              } else {
                                  if (document.getElementById("imcReportAttachedImageThumb")) {
                                      imcDeleteAttachedImage("imcReportAttachedImageThumb");
                                  }
                                  img.setAttribute("id", "imcReportAttachedImageThumb");
                                  img.setAttribute("alt", "Attached photo");
                                  img.setAttribute("class", "imc-ReportAttachedImgStyle u-cf");
                                  document.getElementById('imcImageSection').appendChild(img);
                                  jQuery("#imcReportFormSubmitErrors").html("");
                                  jQuery("#imcNoPhotoAttachedLabel").hide();
                                  jQuery("#imcLargePhotoAttachedLabel").hide();
                                  jQuery("#imcPhotoAttachedFilename").html(" " + file.name);
                                  jQuery("#imcPhotoAttachedLabel").show();
                              }
                          },
                          {
                              maxHeight: 200,
                              orientation: orientation,
                              canvas: true
                          }
                      );
                  });
              } else {
                  imcDeleteAttachedImage('imcReportAddImgInput');
                  e.preventDefault();
                  jQuery("#imcNoPhotoAttachedLabel").hide();
                  jQuery("#imcPhotoAttachedLabel").hide();
                  jQuery("#imcLargePhotoAttachedLabel").show();
              }
          };
      </script>

      <?php
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
}
