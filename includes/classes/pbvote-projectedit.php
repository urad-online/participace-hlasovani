<?php
/**
 * PB 1.00
 * Renders part of the form with PB Project additional fields
 * Used both by insert and edit page
 * class pbProjectEdit renders form
 * class pbProjectSaveData saves data
 *
 */
class PbVote_ProjectEdit
{
    private $file_type_image = "gif, png, jpg, jpeg";
    private $file_type_scan  = "pdf" ;
    private $file_type_docs  = "doc, xls, docx, xlsx";
    private $pb_submit_btn_text = array(
            'completed_off' => 'Uložit si pro budoucí editaci',
            'completed_on'  => 'Odeslat návrh ke schválení',
        );
    private $fields_definition  = array();
    private $fields_order  = array();
    private $form_fields;

    public function __construct()
    {
        $this->form_fields = new PbVote_RenderForm();
        $this->fields_definition = $this->form_fields->get_form_fields();
        $this->fields_order      = $this->form_fields->get_form_fields_layout();
    }
    /*
    * Renders the form part with additional fields
    */
    public function template_project_edit( $latlng = array(), $data = null)
    {

        ob_start();
        $this->render_form( $latlng, $data, 1 );
        return ob_get_clean();
    }

    private function render_form( $latlng, $data, $order_num = 1 )
    {
        foreach ($this->fields_order as $field) {
            echo '<div class="imc-row">';
            if ( $field['type'] === 'field' ) {
                $this->render_field(
                        $order_num,
                        $this->fields_definition[ $field['data']['field'] ],
                        $this->render_field_get_value( $this->fields_definition[ $field['data']['field'] ]['id'],
                        $data ),
                        $latlng,
                        $field['data']['columns']
                );
                $order_num ++;
            } elseif ($field['type'] === 'row') {
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
                $this->render_budget_table( $order, $field, $value, $help);
                break;
            case 'media':
                $this->render_file_attachment( $order, $field, $value, $help);
                break;
            case 'featured_image':
                $this->render_image( $order, $field, $value, $help);
                break;
            case 'checkbox':
                $this->render_checkbox( $order, $field, $value, $help);
                break;
            case 'checkboxgroup':
                $this->render_checkboxgroup( $order, $field, $value, $help);
                break;
            case 'textarea':
                $this->render_textarea( $order, $field, $value, $help);
                break;
            case 'email':
                $this->render_text( $order, $field, $value, $help);
                break;
            case 'category':
                $this->render_category( $order, $field, $value, $help);
                break;
            case 'imcmap':
                $this->render_map( $order, $field, $value, $help);
                $this->render_link_katastr( $latlng );
                break;
            default:
                $this->render_text( $order, $field, $value, $help);
        }

        if ( ! empty($columns) ) {
            echo '</div>';
        }
    }

    private function render_textarea( $order, $input = null, $value = '', $help = '' )
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
        }
        if ( ! empty($input['rows'])) {
            $rows = $input['rows'];
        } else {
            $rows = 3;
        }

        // $output = '<div class="imc-row">
        $output = '<h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3>
            <textarea placeholder="%s" rows="%d"
                 class="imc-InputStyle" name="%s"
                 id="%s">%s</textarea>
            <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
            // </div>';
        if ( empty( $input ) ) {
            return $output;
        } else {
            printf( $output,
                $order,
                $input['label'],
                $mandatory,
                ( empty( $input['placeholder'])) ? "" : $input['placeholder'],
                $rows,
                $input['id'],
                $input['id'],
                $value,
                $input['id']
            );
        }
    }
    private function render_text( $order, $input = null, $value = '', $help = '' )
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
        }
        $options = '';
        if ( ! empty($input['options'])) {
            $options = " ".$input['options'];
        }

        $output = '<h3 class="imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3><input type="%s" %s autocomplete="off"
            data-tip="zde kliknete" placeholder="%s" name="%s" id="%s" class="imc-InputStyle" value="%s" ></input>
            <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

        if ( empty( $input ) ) {
            return $output;
        } else {
            printf( $output,
                $order,
                $input['label'],
                $mandatory,
                $input['type'],
                $options,
                ( empty( $input['placeholder'])) ? "" : $input['placeholder'],
                $input['id'],
                $input['id'],
                $value,
                $input['id']
            );
        }
    }
    private function render_budget_table( $order, $input = null, $value = '', $help = '' )
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
        }
        $options = '';
        if ( ! empty($input['options'])) {
            $options = " ".$input['options'];
        }

        if (empty($value)) {
          $value_table = array();
        } else {
          $value_table = unserialize( $value);
        }

        $table = new PbVote_BudgetTable( true, $value_table);
        $output = '<h3 class="imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3>';

        printf( $output,
            $order,
            $input['label'],
            $mandatory
        );

        $output = $table->render_table();
        echo $output;
    }

    private function render_file_attachment( $order, $input, $value = '', $help = '')
    {
        if ( !empty( $input['mandatory']) && $input['mandatory'] ) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
        } else {
            $mandatory = $this->render_mandatory( false);
        }
        $options = ' readonly="readonly" ';
        if ($value) {
            $filename = basename($value);
        } else {
            $filename = $value;
        }

        $link = $this->render_file_link($value, $input['id']);
        // <span id="%sName" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'. __('Vyberte soubor','pb-voting') .'</span>
        $output = '<div class="imc-row" id="pbProjectSection%s">
                    <div class="imc-row">
                        <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s %s'.$this->render_tooltip( $help ).'</h3>
                    </div>
                    <div class="imc-row">
                        <div class="imc-grid-5 imc-columns">
                            <input %s autocomplete="off"
                                placeholder="Vyberte soubor" type="text" name="%sName" id="%sName" class="imc-InputStyle" value="%s"/>
                            <label id="%sNameLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>
                        </div>

                        <div class="imc-grid-6 imc-columns">
                        <div class="u-cf">
                            <div class="imc-row">%s
                                <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="%s" type="file" name="%s" onchange="pbProjectAddFile(this)" />
                                <label for="%s">
                                    <i class="material-icons md-24 imc-AlignIconToButton">%s</i>%s
                                </label>
                                <button type="button" class="imc-button" onclick="imcDeleteAttachedFile(\'%s\');">
                                    <i class="material-icons md-24 imc-AlignIconToButton">delete</i>%s</button>
                            </div>
                        </div>
                        </div>
                    </div></div>';
        if ( empty( $input ) ) {
            return $output;
        } else {
            printf( $output,
                $input['title'],
                $order,
                $input['label'],
                $mandatory,
                $options,
                $input['id'],
                $input['id'],
                $filename,
                $input['id'],
                $link,
                $input['id'],
                $input['id'],
                $input['id'],
                $input['material_icon'],
                $input['AddBtnLabel'],
                $input['id'],
                $input['DelBtnLabel']
            );
        }
    }

    /*
    * Renders HTML link for opening an file attachment
    */
    private function render_file_link($url, $id )
    {
        if (! empty($url)) {
            return '<a id="'.$id.'Link" href="'.$url.'" target="_blank" data-toggle="tooltip" title="Zobrazit přílohu" class="u-pull-right
                imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-SingleHeaderIconStyle">file_download</i></a>';
                        // <i class="material-icons md-36 imc-SingleHeaderIconStyle">open_in_browser</i></a>';
        } else {
            return '<a hidden id="'.$id.'Link" data-toggle="tooltip" title="Chybí příloha" class="u-pull-right
                imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-SingleHeaderIconStyle">file_download</i></a>';
        }
    }
    private function render_checkbox( $order, $input, $value = '', $help = '')
    {
        $checked = '';
        $required = '';
        $mandatory = '';
        if ( ! empty( $value) ){
            if ( $value ) {
                $checked = 'checked';
            }
        } else {
            if (! empty($input['default']) && ( $input['default'] != 'no') && ( $input['default'] != '0') ) {
                $checked = 'checked';
            }
        }
        if ( ! empty( $input['mandatory'])) {
            $mandatory = $this->render_mandatory( $input['mandatory']) ;
            if ($input['mandatory']) {
                $required = "required";
                $required = "";
            }
        }

        $output = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;"><label id="%sName" for="%s">%s%s'. $this->render_tooltip($help) .'</label>
            </h3><input type="checkbox"  %s %s name="%s" id="%s" class="imc-InputStyle" value="1"
            style="width:20px; height:20px; display:inline-block;margin-left:10px"/>
            <label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

        if ( empty( $input ) ) {
            return $output;
        } else {
            printf( $output,
                $input['id'],
                $input['id'],
                $order,
                $input['label'],
                $checked,
                $required,
                $input['id'],
                $input['id'],
                $input['id']
            );
        }
    }
    private function render_checkboxgroup( $order, $input, $value , $help = '')
    {
        $required = '';
        $mandatory = '';
        if (empty($value)) {
          $values = array();
        } else {
          $values = unserialize( $value);
        }

        $output = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;"><label id="%sName" for="%s">%s%s'. $this->render_tooltip($help) .'</label>
            </h3><label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
        if ( empty( $input ) ) {
            return $output;
        } else {
            $output .= '<div class="pbvote-CheckboxGroup-container">';
            $output .= '<input class="pbvote-project-checkboxgroup-input" type="hidden" id="%s" name="%s" value="'. json_encode( empty($values) ? array() : $values , JSON_UNESCAPED_UNICODE) .'">';
            foreach( $input['items'] as $pb_item ) {
                if ((! empty($values)) && in_array($pb_item['iid'], $values, true)) {
                  $checked = 'checked="checked"';
                } else {
                  $checked = '';
                }
                $output .= '<div><input '.$checked.' class="pbvote-CheckboxGroupStyle imc-CheckboxStyle pbvote-CheckboxGroup-member" id="'.$pb_item['iid'].'" type="checkbox" name="'.$pb_item['iid'].'" value="'.$pb_item['iid'].'">';
                $output .= '<label for="'.$pb_item['iid'].'">'.$pb_item['ilabel'].'</label>';
                $output .= '</div>';
            }
            $output .= '</div>';
            printf( $output,
                $input['id'],
                $input['id'],
                $order,
                $input['label'],
                $input['id'],
                $input['id'],
                $input['id'],
            );
        }
    }

    public function render_mandatory( $mandatory = false)
    {
        if ( $mandatory ) {
            return '';
        } else {
            return ' ( ' . __('volitelné','pb-voting') .' )';
            return '<span class="imc-OptionalTextLabelStyle">" " (' . __('optional','pb-voting') .')></span>';
        }
    }

    private function render_map( $order = '', $input = '', $value = '', $help = '')
    {
        $output = '<input required name="postAddress" placeholder="%s" id="imcAddress" class="u-pull-left imc-InputStyle"/>';
        $output = '
            <div class="imc-row-no-margin">
                <h3 class="imc-SectionTitleTextStyle">%s%s</h3>
                <button class="imc-button u-pull-right" type="button" onclick="imcFindAddress(\'imcAddress\', true)">
                    <i class="material-icons md-24 imc-AlignIconToButton">search</i>%s</button>
                <div style="padding-right: .5em;" class="imc-OverflowHidden">
                    <input name="postAddress" placeholder="%s" id="imcAddress" class="u-pull-left imc-InputStyle"/>
                </div>
                <input title="lat" type="hidden" id="imcLatValue" name="imcLatValue"/>
                <input title="lng" type="hidden" id="imcLngValue" name="imcLngValue"/>
            </div>
            <div class="imc-row">
                <div id="imcReportIssueMapCanvas" class="u-full-width imc-ReportIssueMapCanvasStyle"></div>
            </div>
        ';
        printf( $output,
            $order,
            $input['label'],
            // __('Address','pb-voting'),
            __('Locate', 'pb-voting'),
            __('Add an address','pb-voting')
        );
    }

    /*
    * Renders featured_image
    */
    private function render_image( $order = '', $input = '',  $value = '', $help = '')
    {
        $output = '
            <div class="imc-row" id="imcImageSection">
                <h3 class="u-pull-left imc-SectionTitleTextStyle">%s%s' . $this->render_mandatory(false) .'</h3>
                <div class="u-cf">
                    <input autocomplete="off" class="imc-ReportAddImgInputStyle" id="imcReportAddImgInput" type="file" name="featured_image" />
                    <label for="imcReportAddImgInput">
                        <i class="material-icons md-24 imc-AlignIconToButton">photo</i>%s
                    </label>
                    <button type="button" class="imc-button" onclick="imcDeleteAttachedImage(\'imcReportAddImgInput\');">
                        <i class="material-icons md-24 imc-AlignIconToButton">delete</i>%s</button>
                </div>
                <span %s id="imcNoPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">%s</span>
                <span style="display: none;" id="imcLargePhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">%s</span>
                <span style="display: none;" id="imcPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">%s</span>
                <span class="imc-ReportGenericLabelStyle imc-TextColorPrimary" id="imcPhotoAttachedFilename"></span>
                <br>
                <br>
            </div>
            <input title="orientation" type="hidden" id="imcPhotoOri" name="imcPhotoOri"/>
            ';
        if ( $value ) {
            $output .= '<img id="imcPreviousImg" class="u-cf" style="max-height: 200px;" src="%s">';
            $no_photo = 'style="display: none;"';
        } else {
            $output .= "%s";
            $no_photo = '';
        }

        printf( $output,
            $order,
            $input['label'],
            // __('Photo','pb-voting'),
            __('Add photo','pb-voting'),
            __('Delete Photo', 'pb-voting'),
            $no_photo,
            __('No photo attached','pb-voting'),
            __('Photo size must be smaller in size, please resize it or select a smaller one!','pb-voting'),
            __('A photo has been selected:','pb-voting'),
            $value
        );
    }

    /*
    * Renders link to katastr with
    */
    private function render_link_katastr($latlng)
    {
        if (! empty( $latlng ) ) {
            $url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat=".$latlng['lat']."&lon=".$latlng['lon']."&layers_3=000B00FFTFFT&ilat=".$latlng['lat']."&lon=".$latlng['lon'];
        } else {
            $url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&lat=50.10766&lon=14.47145&layers_3=000B00FFTFFT";
        }
        $output = '<div class="imc-row" ><span>Kliknutím na tento </span>
            <a id="pb_link_to_katastr" href="#" data-toggle="tooltip" title="Přejít na stránku s katastrální mapou"
                class=""><span>odkaz</span></a><span> zobrazíte katastrální mapu na vámi označeném místě.
            Ve vyskakovacím okně (musíte mít povoleno ve vašem prohlížeči) získáte informace k vybranému pozemku. Nalezněte všechna katastrální čísla týkajících se návrhu, kliknutím do mapy ověřte,
            zda jsou všechny dotčené pozemky ve správě HMP nebo MČ a tedy splňujete podmínky pravidel participativního rozpočtu. Seznam všech dotčených pozemků uveďte do pole níže (jedna položka na jeden řádek).</span>
            </div>';
        printf($output);
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
    * Renders select with taxo category
    */
    private function render_category(  $order, $input, $value, $help = '')
    {
        if ( empty( $value )) {
            $value = 0;
        }

        $output = '<h3 class="imc-SectionTitleTextStyle">%s%s</h3>
            <label class="imc-CustomSelectStyle u-full-width">';
        $output .= pbvote_insert_cat_dropdown( 'my_custom_taxonomy', $value );
        $output .= '</label><label id="%sLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
        if ( empty( $input ) ) {
            return $output;
        } else {
            printf( $output,
                $order,
                $input['label'],
                $input['id']
            );
        }
    }

    /*
    * Definition of rules for FormValidator in validate.js
    */
    public function render_fields_js_validation()
    {
        return $this->form_fields->get_form_fields_js_validation();
    }

}

 ?>
