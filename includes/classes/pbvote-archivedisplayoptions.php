<?php
class PbVote_ArchiveDisplayOptions
{
    private $display_param, $filter_params_view = array();
    private $filtering_active =  false;
    public $result, $perma_structure, $parameter_pass;
    private $ppage_values   = array( 6 => "6", 12 => "12", 24 => "24", -1 => "All" ); //all possible options and labels
    private $sorder_values  = array( 1 => "Date",2 => "Name"); // order options and labels
    private $view_values    = array( 1 => "view_stream", 2 => "apps"); // view options and icon names
    private $default_values = array(
        'ppage'  => 6,
        'sorder' => 1,
        'view'   => 1,
    );
    private $param_list_long  = array( 'ppage','sorder', 'view', 'sstatus', 'scategory', 'keyword');
    private $param_list_short = array( 'ppage','sorder', 'view');
    private $param_list_conv_function = array(
            'ppage'  => 'conv_int',
            'sorder' => 'conv_int',
            'view'   => 'conv_int',);

    public function __construct()
    {
        $this->set_urlstructure();
        $this->set_plugin_base_url();
        foreach ($this->param_list_long as $key) {
            $this->get_value_from_url_param( $key, (!empty( $this->param_list_conv_function[$key])) ? $this->param_list_conv_function[$key] : null);
        }

        $this->save_to_session();
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
            return explode( ',', $this->filter_params_view[ $param]);
        } else {
            return array();
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
            $this->parameter_pass = '/?myparam=';
        } else{
            $this->parameter_pass = '&myparam=';
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
            $_SESSION['pbv_display_params'] = $output;
        } else {
            if ( ! empty( $this->session_param) ) {
                $this->filter_params_view = $this->session_param;
            }
        }
    }

    public function get_from_session()
    {
        if (isset($_SESSION['pbv_display_params'])) {
            $this->session_param = json_decode( $_SESSION['pbv_display_params'], true);
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
        if ((!empty($option)) && (!empty($this->$option)) && (!empty( $this->$option[$value]))) {
            if ($translate) {
                $label = __( $this->$option[$value], "pb-voting");
            } else {
                $label = $this->$option[$value];
            }
        }
        return $label;
    }
    public function delppage_get_label( $value = "-1" ) //todelete
    {
        if ($value == "-1" ) {
            return "All";
        } else {
            return $value;
        }
    }
    public function delsorder_get_label( $value = "1" ) //todelete
    {
        switch ($value) {
            case '1':
                $label = __("Date", "pb-voting");
                break;
            case '2':
                $label = __("Votes", "pb-voting");
                break;

            default:
                $label = __("Date", "pb-voting");
                break;
        }
        return $label;
    }
    private function delview_get_label( $value) // todelete
    {
        switch ($value) {
            case '1':
            $label = "view_stream";
            break;
            case '2':
            $label = "apps";
            break;

            default:
            $label = "view_stream";
            break;
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
                $this->get_label( "ppage_values", $key, true ));
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
                $this->get_label( "sorder_values", $key, true ));
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
                $this->get_label( "view_values", $key, false ),
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

}
