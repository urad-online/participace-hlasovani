<?php
/**
 * PB 1.00
 * Renders part of the form with PB Project additional fields
 * Used both by insert and edit page
 * class pbProjectEdit renders form
 * class pbProjectSaveData saves data
 *
 */
class PbVote_RenderFormEdit
{
    private $pb_submit_btn_text = array(
            'completed_off' => 'Uložit si pro budoucí editaci',
            'completed_on'  => 'Odeslat návrh ke schválení',
        );
    private $help_page_slug = PB_HELP_SLUG;
    private $fields_definition  = array();
    private $fields_order  = array();
    private $form_fields;
    private $texts = array(
      "link_to_help_label" => "Návod k vyplnění formuláře",
    );

    public function __construct()
    {
        $this->form_fields = new PbVote_RenderFormDefinition();
        $this->fields_definition = $this->form_fields->get_form_fields();
        $this->fields_order      = $this->form_fields->get_form_fields_layout();
    }
    /*
    * Renders the form part with additional fields
    */
    public function render_form_edit( $latlng = array(), $data = null)
    {

        ob_start();
        $this->render_form( $latlng, $data, 1 );
        return ob_get_clean();
    }

    private function render_form( $latlng, $data, $order_num = 1 )
    {
        echo $this->render_help_link();
        foreach ($this->fields_order as $field) {
            echo '<div class="imc-row">';
            switch ($field['type']) {
              case 'field':
                  $this->render_field(
                    $order_num,
                    $this->fields_definition[ $field['data']['field'] ],
                    $this->render_field_get_value( $this->fields_definition[ $field['data']['field'] ]['id'],
                    $data ),
                    $latlng,
                    $field['data']['columns']
                  );
                  $order_num ++;
                break;
              case 'row':
                  foreach ($field['data'] as $subfield) {
                    if ( $subfield['type'] === 'field') {
                      $this->render_field(
                        $order_num,
                        $this->fields_definition[ $subfield['data']['field'] ],
                        $this->render_field_get_value( $this->fields_definition[$subfield['data']['field'] ]['id'],
                        $data ),
                        $latlng,
                        $subfield['data']['columns']
                      );
                      $order_num ++;
                    }
                  }
                  break;
              case 'section':
                  $this->render_section_header(
                      $field['data']['label'],
                      $field['data']['help'],
                      $field['data']['class']
                  );
                  break;

                default:
                break;
            }
            echo '</div>';
        }
    }

    private function render_field_get_value( $id, $values)
    {
        if (! empty($values[ $id][0])) {
            return $values[ $id][0];
        } else {
            return '';
        }
    }

    /*
    * Core functin for field renderingRenders the form part with additional fields
    */
    private function render_field( $order = '' , $field, $value = '', $latlng = '', $columns = 0 )
    {
        if (! empty( $order )) {
            $order = $order . ". ";
        }
        if (! empty( $field['help'])) {
            $help = $field['help'];
        } else {
            $help = '';
        }
        if ( ! empty($columns) ) {
            echo '<div class="imc-grid-'.$columns.' imc-columns">';
        }
        switch ( $field['type'] ) {
            case 'budgettable':
                $field_render = new PbVote_RenderFieldBudgetTable( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'attachment':
                $field_render = new PbVote_RenderFieldAttachmentTable( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'media':
                $field_render = new PbVote_RenderFieldAttachmentFile( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'featured_image':
                $field_render = new PbVote_RenderFieldImageFile( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'checkbox':
                $field_render = new PbVote_RenderFieldCheckbox( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'checkboxgroup':
                $field_render = new PbVote_RenderFieldCheckboxGroup( $field, $value);
                // echo $field_render->render_field($order, $help);
                break;
            case 'textarea':
                $field_render = new PbVote_RenderFieldTextArea( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'email':
                $field_render = new PbVote_RenderFieldText( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'category':
                $field_render = new PbVote_RenderFieldCategory( $field, $value);
                // echo $field_render->render_field($order);
                break;
            case 'imcmap':
                $field_render = new PbVote_RenderFieldAddressWithMap( $field, $value);
                // echo $field_render->render_field($order);
                break;
            default:
                $field_render = new PbVote_RenderFieldText( $field, $value);
                // echo $field_render->render_field($order);
        }
        echo $field_render->render_field($order);

        if ( ! empty($columns) ) {
            echo '</div>';
        }
    }

    /*
    * Renders link to katastr with
    */
    private function render_tooltip( $text = "")
    {
        if (! empty( $text)) {
            return '<span class="pb_tooltip"><i class="material-icons md-24" style="margin-left:2px;">help_outline</i>
            <span class="pb_tooltip_text" >' . $text . '</span></span>' ;
        } else {
            return '';
        }
    }

    /*
    * Definition of rules for FormValidator in validate.js
    */
    public function render_fields_js_validation()
    {
        return $this->form_fields->get_form_fields_js_validation();
    }

    protected function render_help_link($slug = '', $icon_size = '28')
    {
        $url = '';
        if ( empty($slug)) {
              $slug = $this->help_page_slug;
        }

        $page = get_page_by_path($slug);
        if ($page) {
          $url = get_permalink($page->ID);
        }

        if (! empty($url)) {
          return '<div class="imc-row" ><div class=" pbvote-helpLink"><a href="' . $url . '" class="pbvote-SingleHeaderLinkStyle" target="_blank">
          <i class="material-icons md-'.trim($icon_size).' imc-SingleHeaderIconStyle pbvote-helpLinkIcon">help_outline</i>
          <span class="imc-hidden-xs imc-hidden-sm imc-hidden-md pbvote-helpLinkText">' . $this->texts["link_to_help_label"] .'</span></a></div></div>';
        } else {
            return '';
        }
    }

    protected function render_section_header( $label = '', $help = '', $class = '')
    {
        if (! empty($label)) {
          echo '<div class="u-pull-left pbvote-SectionTitleRow"><h3 class="u-pull-left '.$class.'">'.$label.$this->render_tooltip( $help ).'</h3></div>';
        }
    }
    public function get_field_property( $field = '', $property = '')
    {
        if ((!empty($field)) && (!empty($this->fields_definition[ $field ]))) {
            if ((!empty($property)) && (!empty($this->fields_definition[ $field ][$property]))) {
                return $this->fields_definition[ $field ][$property];
            } else {
                return $this->fields_definition[ $field ];
            }
        } else {
           return "";
        }
    }

    public function get_fields()
    {
      return $this->fields_definition;
    }

}

 ?>
