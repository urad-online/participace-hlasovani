<?php
class PbVote_RenderFormDefinition {
    public static $file_type_image =  "gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG";
    public static $file_type_scan  =  "pdf,PDF";
    public static $file_type_docs  =  "doc,DOC,xls,XLS,docX,DOCX,xlsx,XLSX";
    private $attachment_size = 2; // megabytes
    private $budget_limit = array('min' => 30000, 'max' => 100000, 'help' => '30 tis. - 100 tis.'); // bytes
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
                'reason', 'curr_state', 'future_state',
                'parcel', 'attachment', 'cost',
                'org_name', 'name',
            );
    }

    private function pb_get_custom_fields_layout()
    {
        return array(
            array( 'type' => 'section', 'data' => array( 'label' => 'A. Základní informace k návrhu', 'help' => '', 'class' => 'pbvote-SectionTitleTextStyle',)),
            array( 'type' => 'section', 'data' => array( 'label' => 'Povinné položky', 'help' => '', 'class' => 'pbvote-SubSectionTitleTextStyle',)),
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'title', 'columns' => 6)),
                array('type' => 'field', 'data' => array( 'field' => 'category', 'columns' => 6)),
            )),
            array( 'type' => 'field', 'data' => array( 'field' => 'photo', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'content', 'columns' => 0)),
            array( 'type' => 'section', 'data' => array( 'label' => 'Nepovinné položky', 'help' => '', 'class' => 'pbvote-SubSectionTitleTextStyle',)),
            array( 'type' => 'field', 'data' => array( 'field' => 'reason', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'curr_state', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'future_state', 'columns' => 0)),
            array( 'type' => 'section', 'data' => array( 'label' => 'B. Lokalizace návrhu (povinné)', 'help' => '', 'class' => 'pbvote-SectionTitleTextStyle',)),
            //array( 'type' => 'field', 'data' => array( 'field' => 'locality', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'postAddress', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'parcel', 'columns' => 0)),
            array( 'type' => 'section', 'data' => array( 'label' => 'C. Přílohy k návrhu (nepovinné)', 'help' => '', 'class' => 'pbvote-SectionTitleTextStyle',)),
            array( 'type' => 'field', 'data' => array( 'field' => 'attachment', 'columns' => 0)),
            array( 'type' => 'section', 'data' => array( 'label' => 'D. Rozpočet návrhu ', 'help' => '', 'class' => 'pbvote-SectionTitleTextStyle',)),
            array( 'type' => 'field', 'data' => array( 'field' => 'cost', 'columns' => 0)),
            array( 'type' => 'section', 'data' => array( 'label' => 'E. Navrhovatel (povinné)', 'help' => '', 'class' => 'pbvote-SectionTitleTextStyle',)),
            array('type' => 'field', 'data' => array( 'field' => 'name', 'columns' => 0)),
            array( 'type' => 'row', 'data' => array(
                array('type' => 'field', 'data' => array( 'field' => 'phone', 'columns' => 6)),
                array('type' => 'field', 'data' => array( 'field' => 'email', 'columns' => 6)),
            )),
            //array( 'type' => 'field', 'data' => array( 'field' => 'org_name', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'address', 'columns' => 0)),
            // array( 'type' => 'field', 'data' => array( 'field' => 'signatures', 'columns' => 0)),
            array( 'type' => 'section', 'data' => array( 'label' => 'F. Uložení a odeslání návrhu', 'help' => '', 'class' => 'pbvote-SectionTitleTextStyle',)),
            array( 'type' => 'field', 'data' => array( 'field' => 'age_conf', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'agreement', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'private_note', 'columns' => 0)),
            array( 'type' => 'field', 'data' => array( 'field' => 'completed', 'columns' => 0)),
        );

    }

    private function pb_get_custom_fields()
    {
        $custom_fields = array(
            'title' => array(
                'label'     => 'Název návrhu',
                'id'        => 'postTitle',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => 'Zadejte krátký název návrhu',
                'help'      => 'Název by měl být krátký a výstižný. Počet znaků je 5 až 60.',
                'show_mtbx' => false,
                'show_form' => true,
                'js_rules'  => array(
                    'rules' => 'required|min_length[5]|max_length[60]',),
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
            'photo' => array(
            		'label'       => 'Ilustrační obrázek',
            		'id'          => 'issue_image',
            		'type'        => 'featured_image',
                'mandatory'     => true,
                'help'          => 'Nahrajte 1 soubor typu .jpg, .jpeg, .png max. velikosti 5 MB',
                'material_icon' => 'image',
                'AddBtnLabel'   => 'Vložit foto',
                'DelBtnLabel'   => 'Smazat foto',
                'show_mtbx'     => false,
                'show_form'     => true,
                'js_rules'      => array(
                    'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG]',
                    ),
            ),
            'content' => array(
                'label'     => 'Popis návrhu',
                'id'        => 'postContent',
                'type'      => 'textarea',
                'mandatory' => true,
                'placeholder' => 'Vyplňte popis svého návrhu',
                'help'      => 'Popis návrhu bude zveřejněn jako úvodní text a bude sloužit k základní informaci při hlasování občanů. Proto jsou název návrhu, ilustrační obrázek a tento text velmi důležité. Jejich dobrou volbou můžete získat svému návrhu větší podporu. K dispozici máte maximálně 500 znaků, minimálně 20.',
                'attribute' => 'min_length[20]|maxlength="500"',
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
                'mandatory' => false,
                'placeholder' => 'Popište důvod, proč návrh předkládáte, jaký problém by byl jeho realizací řešen',
                'help'      => 'Popište důvod, proč návrh předkládáte, jaký problém by byl jeho realizací řešen',
                // 'title'     => "Actions",
                'show_mtbx' => true,
                'show_form' => true,
            ),
            'curr_state' => array(
                'label'     => 'Současný stav',
                'id'        => 'pb_project_curr_state',
                'type'      => 'textarea',
                'mandatory' => false,
                'placeholder' => 'Popište současný stav týkající se plánovaného návrhu',
                'help'      => 'Popište současný stav týkající se plánovaného návrhu',
                // 'title'     => "Actions",
                'show_mtbx' => true,
                'show_form' => true,
            ),
            'future_state' => array(
                'label'     => 'Plánovaný stav',
                'id'        => 'pb_project_future_state',
                'type'      => 'textarea',
                'mandatory' => false,
                'placeholder' => 'Popište plánovaný stav týkající se tohoto návrhu',
                'help'      => 'Popište plánovaný stav týkající se tohoto projektu',
                // 'title'     => "Actions",
                'show_mtbx' => true,
                'show_form' => true,
            ),
            'locality' => array(
                'label' => 'Katastrální části, kterých se návrh týká',
                'id'    => 'pb_project_locality',
                'help'  => 'Zaškrtněte všechny katastrální části, kterých se návrh týká',
                'type'  => 'checkboxgroup',
                'show_mtbx'   => false,
                'show_form'   => true,
                'items' => array(
                    array( 'ilabel' => 'Čakovice', 'iid' => 'pb_project_loc_vrsovice', ),
                    array( 'ilabel' => 'Miškovice',                'iid' => 'pb_project_loc_strasnice', ),
                    array( 'ilabel' => 'Třeboradice',                 'iid' => 'pb_project_loc_malesice', ),
                    array( 'ilabel' => 'Čakovice-sídliště', 'iid' => 'pb_project_loc_zahr_mesto', ),
                ),
                'js_rules'  => array(
                  'rules'   => 'required|!callback_pb_project_js_validate_locality',
                ),
            ),
            'postAddress' => array(
                'label'     => 'Lokalizace místa v mapě',
                'id'        => 'postAddress',
                'type'      => 'imcmap',
                'help'      => 'Lokalitu můžete vyhledat buď zadáním adresy nebo umístěním špendlíku na místo lokality. Mapu si můžete zvětšit na celou obrazovku tlačítkem v pravém horním rohu mapy a přibližovat / oddalovat můžete kolečkem myši při současném stisku tlačítka CTRL.',
                'show_mtbx' => false,
                'show_form' => true,
                'js_rules'  => array(
                    'rules' => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'parcel' => array(
                'label'       => 'Parcelní čísla pozemků, kterých se návrh týká',
                'id'          => 'pb_project_parcely',
                'type'        => 'textarea',
                'mandatory'   => true,
                'placeholder' => 'Pro usnadnění kontroly zadejte každé číslo na samostatný řádek ve formátu číslo/podčíslo katastrální část.',
                // 'title'       => "parcel",
                'help'        => 'Pro usnadnění kontroly zadejte každé číslo na samostatný řádek ve formátu číslo/podčíslo katastrální část.',
                'show_mtbx' => true,
                'show_form' => true,
                'js_rules'  => array(
                  'rules' => 'required',
                  'depends' => 'pb_project_js_validate_required',
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
                'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf. Max velikost 2 MB',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
                ),
            ),
            'cost' => array(
              'label'     => 'Předpokládané náklady (povinné)',
              'id'        => 'pb_project_naklady',
              'type'      => 'budgettable',
              'title'     => "cost",
              'mandatory' => true,
              'help'      => 'Zadejte předpokládané náklady v rozsahu '.$this->budget_limit['help'].' Kč včetně 10 procent rezervy. Částky jsou včetně daně. Počet jednotek a jednotková cena jsou celá čísla, uveďte všechny potřebné položky včetně např. bouracích a stavebních prací nebo finančních prostředků na studie a zpracovávání dokumentace.',
              'show_mtbx' => true,
              'show_form' => true,
              'limit'     => $this->budget_limit,
              'js_rules'  => array(
                'rules'   => 'required|!callback_pb_project_js_validate_budget',
                'depends' => 'pb_project_js_validate_required',
              ),
            ),
            // 'map' => array(
            //     'label'     => 'Mapa (situační nákres) místa, kde se má návrh realizovat (povinná příloha)',
            //     'id'        => 'pb_project_mapa',
            //     'type'      => 'media',
            //     'title'     => "map",
            //     'mandatory' => true,
            //     'material_icon' => 'file_upload',
            //     // 'material_icon' => 'language',
            //     'AddBtnLabel'   => 'Vložit',
            //     'DelBtnLabel'   => 'Smazat',
            //     'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
            //     'show_mtbx'     => false,
            //     'show_form'     => false,
            //     'js_rules'      => array(
            //         'rules' => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
            //         'depends' => 'pb_project_js_validate_required',
            //     ),
            // ),
            // 'mapName' => array(
            //     'label'     => 'Mapa (situační nákres)',
            //     'id'        => 'pb_project_mapaName',
            //     'show_mtbx' => false,
            //     'js_rules'  => array(
            //       'rules'   => 'required',
            //       'depends' => 'pb_project_js_validate_required',
            //     ),
            // ),
            // not used. Kept as a tenplate for media
            // 'attach1' => array(
            //     'label'         => 'Vizualizace, výkresy, fotodokumentace… 1',
            //     'id'            => 'pb_project_dokumentace1',
            //     'type'          => 'media',
            //     'title'         => "attach1",
            //     'mandatory'     => false,
            //     'material_icon' => 'file_upload',
            //     // 'material_icon' => 'content_copy',
            //     'AddBtnLabel'   => 'Vložit',
            //     'DelBtnLabel'   => 'Smazat',
            //     'help'          => 'Povolené typy příloh: gif, png, jpg, jpeg, pdf',
            //     'show_mtbx'   => false,
            //     'show_form'   => false,
            //     'js_rules'    => array(
            //         'rules'   => 'is_file_type[gif,GIF,png,PNG,jpg,JPG,jpeg,JPEG,pdf,PDF]',
            //     ),
            // ),

            'name' => array(
              'label'     => 'Jména a příjmení navrhovatele',
              'id'        => 'pb_project_navrhovatel_jmeno',
              'type'      => 'text',
              'default'   => '',
              'mandatory' => true,
              'placeholder' => 'Vyplňte jméno',
              // 'title'     => "Proposer Name",
              'columns'   => 6,
              'help'      => 'Navrhovatelem je občan městské části s trvalým bydlištěm ve správním obvodě MČ Praha-Čakovice.',
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
                'mandatory' => true,
                'placeholder' => '(+420) 999 999 999',
                // 'title' => "phone",
                'columns' => 6,
                'help'      => 'Telefonní číslo navrhovatele uvádějte včetně předvolby ve formátu ve formátu (+420) 999 999 999',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'required|valid_phone',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'email' => array(
                'label'     => 'E-mail',
                'id'        => 'pb_project_navrhovatel_email',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => '',
                // 'title'     => "email",
                'columns'   => 6,
                'help'      => 'Emailová adresa navrhovatele bude sloužit ke komunikaci o návrhu. Po uložení Vám bude automaticky odeslán link s možností úprav. Při každé změně stavu návrhu bude navrhovatel a zástupce úřadu prostřednictvím emailové komunikace informován.',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'valid_email|required',
                    // 'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'org_name' => array(
              'label'     => 'Název společnosti ',
              'id'        => 'pb_project_navrhovatel_org',
              'type'      => 'text',
              'default'   => '',
              'mandatory' => false,
              'placeholder' => 'Zadejte název právnické osoby',
              // 'title'     => "Proposer Name",
              'columns'   => 6,
              'help'      => 'Vyplňte pouze v případě, že návrh podáváte jménem společnosti se sídlem v MČ a výše uvedené kontakty jsou kontakty na zástupce této společnosti.',
              'show_mtbx'   => false,
              'show_form'   => true,
            ),
            'address' => array(
                'label'     => 'Adresa trvalého bydliště',
                'id'        => 'pb_project_navrhovatel_adresa',
                'type'      => 'text',
                'mandatory' => true,
                'placeholder' => 'název ulice, číslo popisné, část obce',
                // 'title'     => "address",
                'help'      => 'Trvalé bydliště navrhovatele musí být ve správním obvodě městské části Praha Čakovice.',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'required',
                    'depends' => 'pb_project_js_validate_required',
                ),
            ),
            'age_conf' => array(
                'label'     => 'Prohlašuji, že jsem starší 18 let',
                'id'        => 'pb_project_prohlaseni_veku',
                'default'   => 'no',
                'type'      => 'checkbox',
                'help'      => 'Podávání návrhů je umožněno pouze občanům starším 18 let.',
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
                'label'     => 'Souhlasím s <a href="'. site_url("podminky-pouziti-a-ochrana-osobnich-udaju/") . '" target="_blank" title="Přejít na stránku s podmínkami">podmínkami použití a zpracování osobních údajů</a>',
                'id'        => 'pb_project_podminky_souhlas',
                'default'   => 'no',
                'type'      => 'checkbox',
                // 'title'     => "Agreement",
                'mandatory' => true,
                'help'      => 'Bez souhlasu s pravidly projektu Počítáme s vámi! a s podmínkami použití, zpracování a ochraně osobních údajů nebude možné návrh projektu uložit ani odeslat ke kontrole.',
                'show_mtbx'   => true,
                'show_form'   => true,
                'js_rules'    => array(
                    'rules'   => 'required',
                ),
            ),
            'private_note' => array(
                'label'       => 'Neveřejné poznámky',
                'id'          => 'pb_project_private_note',
                'type'        => 'textarea',
                'mandatory'   => false,
                'placeholder' => 'Neveřejné poznámky uveďte zde',
                // 'title'       => "parcel",
                'help'        => 'Tyto informace nebudou publikovány. Mohou sloužit k předání důležitých informací koordinátorům projektu Počítáme s vámi!',
                'show_mtbx' => true,
                'show_form' => true,
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
        );
        return $custom_fields;
    }

}
