
<?php

class PbVote_RenderForm {
    private $fields;
    private $fields_layout;
    private $fields_single;

    public function __construct()
    {
        $this->read_form_fields();
        $this->read_form_fields_layout();
    }
    private function read_form_fields()
    {

        if ( false === ( $this->fields = get_option( 'pb_custom_fields_definition' ) ) ) {
            $this->fields = pb_get_custom_fields();
            add_option( 'pb_custom_fields_definition', json_encode( $this->fields, JSON_UNESCAPED_UNICODE) );
        } else {
            $this->fields = json_decode( $this->fields, true);
        }

    }

    private function read_form_fields_layout()
    {
        if ( false === ( $fields_layouts = get_option( 'pb_custom_fields_layout' ) ) ) {
            $this->fields_layout = pb_get_custom_fields_layout();
            $this->fields_single = pb_get_custom_fields_single();
            add_option( 'pb_custom_fields_layout', json_encode( array(
                'form'   => $this->fields_layout,
                'single' => $this->fields_single,
                ), JSON_UNESCAPED_UNICODE) );
        } else {
            $fields_layouts = json_decode( $fields_layouts, true );
            $this->fields_layout = $fields_layouts['form'] ;
            $this->fields_single = $fields_layouts['single'] ;
        }
    }


    public function get_form_fields()
    {
        return $this->fields;
    }

    public function get_form_fields_mtbx()
    {
        $output = array();
        foreach ($this->fields as $key => $value) {
            if ((!empty($value['show_mtbx'] )) && ($value['show_mtbx'] )) {
                $output[ $key] = array(
                    'label' => $value['label'],
                    'id'    => $value['id'],
                    'type'  => $value['type'],
                );
                if (!empty( $value['default'])) {
                    $output[ $key]['default'] = $value['default'];
                }
            }
        }
        return $output;
    }

    public function get_form_fields_layout()
    {
        return $this->fields_layout;
    }

    public function get_form_fields_js_validation()
    {
        $fields = pb_get_custom_fields();
        $output = array();
        foreach ($this->fields as $key => $value) {
            if (! empty( $value['js_rules'] )) {
                $rule = array(
                    'name'    => $value['id'],
                    'display' => $value['label'],
                    'rules'   => $value['js_rules']['rules'],
                );
                if (! empty( $value['js_rules']['depends'] )) {
                    $rule['depends'] = $value['js_rules']['depends'];
                }
                if (! empty( $value['js_rules']['name'] )) {
                    $rule['name'] = $value['js_rules']['name'];
                }
                array_push( $output, $rule);
            }
        }
        return json_encode( $output );
    }

    public function get_form_fields_layout_single()
    {
        return $this->fields_single;

    }

}
