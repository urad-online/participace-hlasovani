<?php
class PbVote_RenderForm {
    public static $file_type_image =  "gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG";
    public static $file_type_scan  =  "pdf,PDF";
    public static $file_type_docs  =  "doc,DOC,xls,XLS,docX,DOCX,xlsx,XLSX";
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
        $this->fields = $this->pb_get_custom_fields();

        return;
        // cancelled using of saved options
        if ( false === ( $this->fields = get_option( 'pb_custom_fields_definition' ) ) ) {
            $this->fields = $this->pb_get_custom_fields();
            add_option( 'pb_custom_fields_definition', json_encode( $this->fields, JSON_UNESCAPED_UNICODE) );
        } else {
            $this->fields = json_decode( $this->fields, true);
        }

    }

    private function read_form_fields_layout()
    {
      $this->fields_layout = $this->pb_get_custom_fields_layout();
      $this->fields_single = $this->pb_get_custom_fields_single();
      return;
      // cancelled using of saved options

        if ( false === ( $fields_layouts = get_option( 'pb_custom_fields_layout' ) ) ) {
            $this->fields_layout = $this->pb_get_custom_fields_layout();
            $this->fields_single = $this->pb_get_custom_fields_single();
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
                if (!empty( $value['items'])) {
                    $output[ $key]['items'] = $value['items'];
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
        $fields = $this->pb_get_custom_fields();
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
    public static function get_file_type_image()
    {
        return self::$file_type_image;
    }

    public static function get_file_type_scan()
    {
        return self::$file_type_scan;
    }

    public static function get_file_type_docs()
    {
        return self::$file_type_docs;
    }

    private function pb_get_custom_fields_single()
    {
        return array(
                'reason', 'locality',
                'parcel', 'cost',
                'attachment',
            );
    }

    private function pb_get_custom_fields_layout()
    {
        return array(
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'title', 'columns' => 6)),
                // array('type' => 'field', 'data' => array( 'field' => 'category', 'columns' => 6)),
            )),
            array( 'type' => 'field', 'data' => array( 'field' => 'content', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'photo', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'reason', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'locality', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'postAddress', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'parcel', 'columns' => 0)),
            // array( 'type' => 'field', 'data' => array( 'field' => 'map', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'cost', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'attachment', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'org_name', 'columns' => 0)),
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'name', 'columns' => 5)),
                array('type' => 'field', 'data' => array( 'field' => 'phone', 'columns' => 3)),
                array('type' => 'field', 'data' => array( 'field' => 'email', 'columns' => 4)),
            )),
            array( 'type' => 'field', 'data' => array( 'field' => 'address', 'columns' => 0)),
            // array( 'type' => 'field', 'data' => array( 'field' => 'signatures', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'age_conf', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'agreement', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'completed', 'columns' => 0)),
        );

    }

    private function pb_get_custom_fields()
    {
        $custom_fields = array(
            'title' => array(
                'label'     => 'Název',
                'id'        => 'postTitle',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => 'Zadejte krátký název projektu',
                'show_mtbx' => false,
                'show_form' => true,
                'js_rules'  => array(
                    'rules' => 'required|min_length[5]|max_length[80]',),
            ),
            'category' => array(
                'label'     => 'Kategorie',
                'id'        => 'my_custom_taxonomy',
                'type'      => 'category',
                'mandatory' => true,
                'show_mtbx' => false,
                'show_form' => false,
                'js_rules'  => array(
                    'rules' => 'required',),
            ),
            'content' => array(
                'label'     => 'Popis',
                'id'        => 'postContent',
                'type'      => 'textarea',
                'mandatory' => true,
                'placeholder' => 'Vyplňte popis svého návrhu, odstavce pro současný a zamýšlený stav. ',
                'help'      => 'Odstavce pro současný a zamýšlený stav. Max 500 znaků',
                'attribute' => 'maxlength="500"',
                'show_mtbx' => false,
                'show_form' => true,
                'js_rules'  => array(
                  'rules' => 'required',
                  'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'reason' => array(
                'label'     => 'Odůvodnění návrhu',
                'id'        => 'pb_project_reason',
                'type'      => 'textarea',
                'mandatory' => true,
                'placeholder' => 'Popište důvod, proč návrh předkládáte, jaký problém bude realizací vyřešen',
                // 'title'     => "Actions",
                'show_mtbx' => true,
                'show_form' => true,
                'js_rules'  => array(
                    'rules' => 'required',
                    'depends' => 'pb_project_js_validate_required',
                    ),
            ),
            'postAddress' => array(
                'label'     => 'Lokace místa',
                'id'        => 'postAddress',
                'type'      => 'imcmap',
                'show_mtbx' => false,
                'show_form' => true,
                'js_rules'  => array(
                    'rules' => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'parcel' => array(
                'label'       => 'Parcelní číslo',
                'id'          => 'pb_project_parcely',
                'type'        => 'textarea',
                'mandatory'   => false,
                'placeholder' => 'Vyplňte číslo parcely ve formátu NNNN/NNNN',
                // 'title'       => "parcel",
                'help'        => 'Pro usnadnění kontroly zadejte prosím, každé číslo na samostatný řádek',
                'show_mtbx' => true,
                'show_form' => true,
            ),
            'photo' => array(
        		'label'       => 'Fotografie',
        		'id'          => 'issue_image',
        		'type'        => 'featured_image',
                'mandatory'     => true,
                'material_icon' => 'image',
                'AddBtnLabel'   => 'Vložit foto',
                'DelBtnLabel'   => 'Smazat foto',
                'show_mtbx'     => false,
                'show_form'     => true,
                'js_rules'      => array(
                    'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG]',
                    ),
            ),
            'map' => array(
                'label'     => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
                'id'        => 'pb_project_mapa',
                'type'      => 'media',
                'title'     => "map",
                'mandatory' => true,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'language',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
                'show_mtbx'     => false,
                'show_form'     => false,
                'js_rules'      => array(
                    'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'mapName' => array(
                'label'     => 'Mapa (situační nákres)',
                'id'        => 'pb_project_mapaName',
                'show_mtbx' => false,
                'js_rules'  => array(
                  'rules'   => 'required',
                  'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'cost' => array(
                'label'     => 'Předpokládané náklady (povinný údaj)',
                'id'        => 'pb_project_naklady',
                'type'      => 'budgettable',
                'title'     => "cost",
                'mandatory' => true,
                'help'      => 'Zadejte předpokládané náklady. Částky uveďte včetně DPH',
                'show_mtbx' => true,
                'show_form' => true,
                'js_rules'  => array(
                  'rules'   => 'required|!callback_pb_project_js_validate_budget',
                  'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'budget_increase' => array(
                'label'     => 'Náklady byly navýšeny o rezervu 10%',
                'id'        => 'pb_project_naklady_navyseni',
                'default'   => 'no',
                'type'      => 'checkbox',
                // 'title'     => "budget_increase",
                'mandatory' => true,
                'columns'   => 6,
                'show_mtbx'   => false,
                'show_form'   => false,
                'js_rules'    => array(
                    'rules'   => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            // not used. Kept as a tenplate for media
            'attach1' => array(
                'label'         => 'Vizualizace, výkresy, fotodokumentace… 1',
                'id'            => 'pb_project_dokumentace1',
                'type'          => 'media',
                'title'         => "attach1",
                'mandatory'     => false,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'content_copy',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
                'show_mtbx'   => false,
                'show_form'   => false,
                'js_rules'    => array(
                    'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
                ),
            ),

            'org_name' => array(
                'label'     => 'Název organizace',
                'id'        => 'pb_project_navrhovatel_org',
                'type'      => 'text',
                'default'   => '',
                'mandatory' => false,
                'placeholder' => 'Zadejte název právnické osoby',
                // 'title'     => "Proposer Name",
                'columns'   => 5,
                'help'      => 'Zadejte pokud je navrhovatelem Právnická osoba',
                'show_mtbx'   => true,
                'show_form'   => true,
            ),
            'name' => array(
              'label'     => 'Jméno a příjmení navrhovatele',
              'id'        => 'pb_project_navrhovatel_jmeno',
              'type'      => 'text',
              'default'   => '',
              'mandatory' => true,
              'placeholder' => 'Vyplňte jméno',
              // 'title'     => "Proposer Name",
              'columns'   => 5,
              'help'      => 'Jméno navrhovatele je povinné',
              'show_mtbx'   => true,
              'show_form'   => true,
              'js_rules'    => array(
                'rules'   => 'required',
                'depends' => 'pb_project_js_validate_required',
              ),
            ),
            'phone' => array(
                'label'     => 'Tel. číslo',
                'id'        => 'pb_project_navrhovatel_telefon',
                'type'      => 'tel',
                // 'options'   => 'pattern="^(\+420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$"',
                'mandatory' => false,
                'placeholder' => '(+420) 999 999 999',
                // 'title' => "phone",
                'columns' => 3,
                'help'      => 'Číslo zadejte ve formátu (+420) 999 999 999',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'valid_phone',
                ),
            ),
            'email' => array(
                'label'     => 'E-mail',
                'id'        => 'pb_project_navrhovatel_email',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => '',
                // 'title'     => "email",
                'columns'   => 4,
                'help'      => 'E-mailová adresa je povinný údaj',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'required|valid_email',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'address' => array(
                'label'     => 'Adresa navrhovatele (název ulice, číslo popisné, část obce)',
                'id'        => 'pb_project_navrhovatel_adresa',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => 'Vyplňte adresu navrhovatele',
                // 'title'     => "address",
                'help'      => 'Pro právnickou osobu uveďte sídlo',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'signatureName' => array(
                'label'     => 'Podpisový arch',
                'id'        => 'pb_project_podporovateleName',
                'show_mtbx'   => false,
                'js_rules'    => array(
                    'rules'   => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'signatures' => array(
                'label'     => 'Podpisový arch (povinná příloha)',
                'id'        => 'pb_project_podporovatele',
                'type'      => 'media',
                'title'     => "signatures",
                'mandatory' => true,
                'material_icon' => 'file_upload',
                // 'material_icon' => 'list',
                'AddBtnLabel'   => 'Vložit',
                'DelBtnLabel'   => 'Smazat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
                'show_mtbx'   => false,
                'show_form'   => false,
                'js_rules'    => array(
                    'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
                ),
            ),
            'age_conf' => array(
                'label'     => 'Prohlašuji, že jsem starší 15 let',
                'id'        => 'pb_project_prohlaseni_veku',
                'default'   => 'no',
                'type'      => 'checkbox',
                'mandatory' => true,
                // 'title'     => "age_conf",
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'agreement'     => array(
                'label'     => 'Souhlasím s <a href="'. site_url("podminky-pouziti-zpracovani-a-ochrana-osobnich-udaju/") . '" target="_blank" title="Přejít na stránku s podmínkami">podmínkami použití</a>',
                'id'        => 'pb_project_podminky_souhlas',
                'default'   => 'no',
                'type'      => 'checkbox',
                // 'title'     => "Agreement",
                'mandatory' => true,
                'help'      => 'K podání projektu musíte souhlasit s podmínkami',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'name'    => 'Souhlas s podmínkami',
                    'rules'   => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'completed'     => array(
                'label'     => 'Popis projektu je úplný a chci ho poslat k vyhodnocení',
                'id'        => 'pb_project_edit_completed',
                'default'   => 'no',
                'type'      => 'checkbox',
                // 'title'     => "completed",
                'mandatory' => false,
                'help'      => 'Pokud necháte nezaškrtnuté, můžete po uložení dat popis projektu doplnit',
            ),
            'locality' => array(
                'label' => 'Lokalita, které se návrh týká',
                'id'    => 'pb_project_locality',
                'help'  => 'Vyberte jednu nebo více lokalit',
                'type'  => 'checkboxgroup',
                'show_mtbx'   => true,
                'show_form'   => true,
                'items' => array(
                    array( 'ilabel' => 'Vršovice-Vinohrady-Michle', 'iid' => 'pb_project_loc_vrsovice', ),
                    array( 'ilabel' => 'Strašnice',                'iid' => 'pb_project_loc_strasnice', ),
                    array( 'ilabel' => 'Malešice',                 'iid' => 'pb_project_loc_malesice', ),
                    array( 'ilabel' => 'Zahradní Město-Záběhlice', 'iid' => 'pb_project_loc_zahr_mesto', ),
                ),
                'js_rules'  => array(
                  'rules'   => 'required|!callback_pb_project_js_validate_locality',
                ),
            ),
            'attachment' => array(
                'label'         => 'Přílohy návrhu',
                'id'            => 'pb_project_attachment',
                'type'          => 'attachment',
                'title'         => "attachment",
                'mandatory'     => false,
                'material_icon' => 'file_upload',
                'AddBtnLabel'   => 'Vyhledat',
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
                ),
            ),
        );
        return $custom_fields;
    }

}
