<?php
class PbVote_ArchiveDisplayOptions
{
    private $display_param, $filter_params_view = array();
    private $filtering_active =  false;
    public $result, $perma_structure, $parameter_pass;
    private $ppage_values   = array( 6 => "6", 12 => "12", 24 => "24", -1 => "Vše" ); //all possible options and labels
    private $sorder_values  = array( 1 => "Datum",2 => "Název"); // order options and labels
    private $view_values    = array( 1 => "view_stream", 2 => "apps"); // view options and icon names
    private $default_values = array(
        'ppage'  => 6,
        'sorder' => 1,
        'view'   => 1,
    );
    public $param_list_long  = array( 'ppage','sorder', 'view', 'sstatus', 'scategory', 'keyword', 'svoting');
    private $param_list_short = array( 'ppage','sorder', 'view');
    private $param_list_conv_function = array(
            'ppage'  => 'conv_int',
            'sorder' => 'conv_int',
            'view'   => 'conv_int',);
    private $translate_domain = 'pb-voting';
    public $session_param_name = "pbv_display_params";
    private $paged  = null;

    public function __construct( $name_suffix = "voting")
    {
        $this->session_param_name .= $name_suffix;
        $this->set_urlstructure();
        $this->set_plugin_base_url();
        foreach ($this->param_list_long as $key) {
            $this->get_value_from_url_param( $key, (!empty( $this->param_list_conv_function[$key])) ? $this->param_list_conv_function[$key] : null);
        }

        $this->save_to_session();
        // pagination page number is not saved in SESSION
        $this->set_parem_query_paged();
        $this->set_default();
        $this->set_filtering_active();
    }

    public function get_value( $param = '')
    {
        if (isset( $this->filter_params_view[ $param] )) {
            return $this->filter_params_view[ $param];
        } else {
            return "";
        }
    }
    public function get_value_array( $param = '')
    {
        if (isset( $this->filter_params_view[ $param] )) {
            return array_map( array($this,"convert_int"), explode( ',', $this->filter_params_view[ $param]));
        } else {
            return array();
        }
    }
    public function convert_int($value)
    {
        if( intval($value) ) {
            return intval($value);
        } else {
            return $value;
        }
    }
    public function set_urlstructure()
    {
        if ( get_option('permalink_structure') ) {
            $this->perma_structure = true;
        } else {
            $this->perma_structure = false;
        }
        if( $this->perma_structure) {
            $this->parameter_pass = '/?edit_id=';
        } else{
            $this->parameter_pass = '&edit_id=';
        }

    }

    private function conv_int( $input)
    {
        return intval( $input);
    }

    public function get_value_from_url_param( $param = "", $conv_function = null )
    {
        if ($param){
            if (isset( $_GET[ $param ] )) {
                $value = sanitize_text_field( $_GET[ $param ] );
                if (($conv_function) && ( method_exists($this, $conv_function)) ) {
                    $value = $this->$conv_function( $value);
                }
            } else {
                $value = '';
            }
            if ( $value ) {
                $this->filter_params_view = array_merge( $this->filter_params_view, array( $param => $value));
            }
        }
    }
    private function set_default()
    {
        foreach ($this->default_values as $key => $value) {
            if ( empty( $this->filter_params_view[ $key ])) {
                $this->filter_params_view = array_merge( $this->filter_params_view, array( $key => $value));
            }
        }
    }
    public function save_to_session()
    {
        $this->get_from_session();
        if ( count($this->filter_params_view) > 0) {
            $output = json_encode( $this->filter_params_view );
            $output = sanitize_text_field( $output );
            $_SESSION[ $this->session_param_name ] = $output;
        } else {
            if ( ! empty( $this->session_param) ) {
                $this->filter_params_view = $this->session_param;
            }
        }
    }

    public function get_from_session()
    {
        if (isset($_SESSION[ $this->session_param_name ])) {
            $this->session_param = json_decode( $_SESSION[ $this->session_param_name ], true);
        } else {
            $this->session_param = null;
        }
    }

    public function set_filtering_active()
    {
        if ( !empty($this->filter_params_view['status']) ||
             !empty($this->filter_params_view['scategory']) ||
             !empty($this->filter_params_view['keyword'])
            ) {
            $this->filtering_active = true;
        }
    }

    public function is_filtering_active()
    {
        return $this->filtering_active;
    }

    public function set_plugin_base_url() {
        	$url_full_path = plugin_basename( __FILE__ );
        	$url_pieces = explode("/", $url_full_path);
        	$this->plugin_base_url = plugin_dir_url( '' ). $url_pieces[0];
    }
    public function get_plugin_base_url()
    {
        return $this->plugin_base_url;
    }

    public function get_label( $option, $value, $translate = true)
    {
        $label = $value;
        $label_array_name = $option . "_values";
        if ((!empty($option)) && (!empty($this->$label_array_name)) && (!empty( $this->$label_array_name[$value]))) {
            if ($translate) {
                $label = __( $this->$label_array_name[$value], $this->translate_domain);
            } else {
                $label = $this->$label_array_name[$value];
            }
        }
        return $label;
    }

    public function create_url_variables_long( $list_value = array() )
    {
        return $this->create_url_variables( $list_value, $this->param_list_long);

    }

    public function create_url_variables_short( $list_value = array() )
    {
        return $this->create_url_variables( $list_value, $this->param_list_short);

    }

    private function create_url_variables( $list_value = array(), $param_list )
    {
        if( $this->perma_structure ){
            $param_sep = '/?';
        }else{
            $param_sep = '&';
        }

        $args = "";

        foreach ($param_list as $key ) {
            if (!empty( $list_value[ $key]) ) {
                $value = $list_value[ $key];
            } else {
                $value = $this->get_value( $key);
            }
            $args .= $param_sep . $key . "=" . $value;
            $param_sep = '&';
        }
        return $args;
    }

    private function get_option_with_url_params( $link = "#", $label = "")
    {
        return '<option value="' . esc_url( $link ) . '">' . $label . '</option>';
    }

    public function generate_options_long_ppage( $perm_link )
    {
        $output = "";
        foreach ( $this->ppage_values as $key => $value  ) {
            $output .= $this->get_option_with_url_params(
                $perm_link . $this->create_url_variables_long( array( "ppage" => $key)),
                // $this->ppage_get_label( $key ));
                $this->get_label( "ppage", $key, true ));
        }
        return $output;
    }

    public function generate_options_long_sorder( $perm_link )
    {
        $output = "";
        foreach ( $this->sorder_values as $key => $value ) {
            $output .= $this->get_option_with_url_params(
                $perm_link . $this->create_url_variables_long( array( "sorder" => $key)),
                // $this->sorder_get_label( $key ));
                $this->get_label( "sorder", $key, true ));
        }
        return $output;
    }

    public function get_filter_params()
    {
        return $this->filter_params_view;
    }
    public function generate_lists_long_view( $perm_link )
    {
        $output = "";
        foreach ( $this->view_values as $key => $value ) {
            $output .= $this->get_list_item_with_url_params(
                $perm_link . $this->create_url_variables_long( array( "view" => $key)),
                // $this->view_get_label( $key ),
                $this->get_label( "view", $key, false ),
                $selected = (($key == $this->get_value('view') ) ? true : false));
        }
        return $output;
    }
    private function get_list_item_with_url_params( $url,  $label, $selected = false)
    {
        if ($selected) {
            $class_selected = "imc-NavSelectedStyle";
        } else {
            $class_selected = "";
        }
        return '<li><a href="' . $url . '" class="imc-SingleHeaderLinkStyle '. $class_selected . '"><i class="material-icons md-36 imc-VerticalAlignMiddle">'. $label . '</i></a></li>';
    }

    public function set_value_param( $key, $value)
    {
        if ((! empty($key)) && (! empty($value))) {
            $this->filter_params_view[ $key] =  $value;
        }
    }
    public function set_parem_query_paged()
    {
      if ( get_query_var( 'paged' ) ) {
        $value = get_query_var('paged'); // On a paged page.
      } else if ( get_query_var( 'page' ) ) {
        $value = get_query_var('page'); // On a "static" page.
      } else {
        $value = 0;
      }
      if ( $value !== 0 ) {
        $this->filter_params_view[ 'page'] =  $value;
      }
    }
    public function add_script_to_page($jsonIssuesArr ,$my_permalink)
    {
      ?>
      <script>
          /*setOverviewLayout();*/
          <?php
          if (!empty( $jsonIssuesArr)) {
            ?>
            document.onload = imcInitOverviewMap(<?php echo json_encode($jsonIssuesArr) ?>, <?php echo json_encode( $this->get_plugin_base_url()) ?>);

          <?php } ?>

          jQuery( document ).ready(function() {

              var imported_cat = <?php echo json_encode( $this->get_value_array('scategory')); ?>;
              var imported_status = <?php echo json_encode($this->get_value_array('sstatus')); ?>;
              var imported_keyword = <?php echo json_encode($this->get_value_array('keyword')); ?>;
              var imported_period  = <?php echo json_encode($this->get_value_array('speriod')); ?>;
              var i;

              // jQuery('span.imc-OverviewFilteringLabelStyle').hide();
              if (imported_status || imported_cat || imported_keyword || imported_period) {
                  jQuery('#pbItemFilteringIndicator').css('color', '#1ABC9C');
                  jQuery('#pbItemStatusCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });
                  jQuery('#pbItemCatCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });
                  jQuery('#pbItemPeriodCheckboxes input:checkbox').each(function() { jQuery(this).prop('checked', false); });

                  if (imported_status.length > 0) {
                      jQuery('#pbItemStatFilteringLabel').show();

                      jQuery('#pbItemToggleStatusCheckbox').prop('checked', false);

                      for (i=0;i<imported_status.length;i++) {
                          jQuery('#pbItem-stat-checkbox-'+imported_status[i]).prop('checked', true);
                      }
                  }

                  if (imported_cat.length > 0) {
                      jQuery('#pbItemCatFilteringLabel').show();

                      jQuery('#pbItemToggleCatsCheckbox').prop('checked', false);

                      for (i=0;i<imported_cat.length;i++) {
                          jQuery('#pbItem-cat-checkbox-'+imported_cat[i]).prop('checked', true);
                      }
                  }

                  if (imported_keyword.length > 0) {
                      jQuery('#pbItemKeywordFilteringLabel').show();

                      jQuery('#pbItemSearchKeywordInput').val(imported_keyword);
                  }

                  if (imported_period.length > 0) {
                    jQuery('#pbItemPeriodFilteringLabel').show();

                    jQuery('#pbItemTogglePeriodCheckbox').prop('checked', false);

                    for (i=0;i<imported_period.length;i++) {
                      jQuery('#pbItem-period-checkbox-'+imported_period[i]).prop('checked', true);
                    }
                  }
              }
          });

          function pbvFireNavigation(id) {
              location.href = jQuery('#'+id)[0].value;
              jQuery( id +" option:disabled" ).prop('selected', true);
          }

          // Checkbox select propagation
          jQuery(function () {
              jQuery("input[type='checkbox']").change(function () {
                  jQuery(this).siblings('#pbItemStatusCheckboxes').find("input[type='checkbox']").prop('checked', this.checked);
                  jQuery(this).siblings('#pbItemCatCheckboxes').find("input[type='checkbox']").prop('checked', this.checked);
                  jQuery(this).siblings('#pbItemCatChildCheckboxes').find("input[type='checkbox']").prop('checked', this.checked);
                  jQuery(this).siblings('#pbItemCatGrandChildCheckboxes').find("input[type='checkbox']").prop('checked', this.checked);
                  jQuery(this).siblings('#pbItemPeriodCheckboxes').find("input[type='checkbox']").prop('checked', this.checked);
              });
          });

          function pbItemOverviewGetFilteringData() {
              var selectedStatus = '';
              var notSelectedStatus = '';
              var selectedCats = '';
              var notSelectedCats = '';
              var keywordString = '';
              var selectedPeriod = '';
              var notSelectedPeriod = '';

              jQuery('#pbItemStatusCheckboxes input:checkbox:checked').each(function() { selectedStatus = selectedStatus + jQuery(this).attr('value') +','; });
              selectedStatus = selectedStatus.slice(0, -1);

              jQuery('#pbItemStatusCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedStatus = notSelectedStatus + jQuery(this).attr('value') +','; });
              notSelectedStatus = notSelectedStatus.slice(0, -1);

              if ( notSelectedStatus.length === 0) {
                selectedStatus = [];
              }

              jQuery('#pbItemCatCheckboxes input:checkbox:checked').each(function() { selectedCats = selectedCats + jQuery(this).attr('value') +','; });
              selectedCats = selectedCats.slice(0, -1);

              jQuery('#pbItemCatCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedCats = notSelectedCats + jQuery(this).attr('value') +','; });
              notSelectedCats = notSelectedCats.slice(0, -1);

              if ( notSelectedCats.length === 0) {
                selectedCats = [];
              }

              jQuery('#pbItemPeriodCheckboxes input:checkbox:checked').each(function() { selectedPeriod += jQuery(this).attr('value') +','; });
              selectedPeriod = selectedPeriod.slice(0, -1);

              jQuery('#pbItemPeriodCheckboxes input:checkbox:not(:checked)').each(function() { notSelectedPeriod += jQuery(this).attr('value') +','; });
              notSelectedPeriod = notSelectedPeriod.slice(0, -1);
              if ( notSelectedPeriod.length === 0) {
                selectedPeriod = [];
              }

              if (jQuery('#pbItemSearchKeywordInput').val() !== '') {
                  keywordString = jQuery('#pbItemSearchKeywordInput').val();
              }

              var link = <?php echo json_encode( $my_permalink ) ; ?>;
              var tempfilter1 = <?php echo json_encode(  $this->create_url_variables_short()); ?>;
              link += decodeURIComponent(tempfilter1);
              if (selectedStatus.length >0) {
                link += '&sstatus=' + selectedStatus;
              }
              if (selectedCats.length >0) {
                link += '&scategory=' + selectedCats;
              }
              if (keywordString.length >0) {
                link += '&keyword=' + keywordString;
              }
              if (selectedPeriod.length >0) {
                link += '&speriod=' + selectedPeriod;
              }

              window.location = link;
          }

          function pbItemOverviewResetFilters() {
              var i;
              var	checkboxes = document.getElementsByTagName('input');

              for (i = 0; i < checkboxes.length; i++)
              {
                  if (checkboxes[i].type === 'checkbox' && checkboxes[i].id !== 'ac-1' )
                  {
                      checkboxes[i].checked = true;
                  }
              }

              jQuery('#pbItemSearchKeywordInput').val('');

              pbItemOverviewGetFilteringData();
          }
      </script>
      <?php
    }

}
