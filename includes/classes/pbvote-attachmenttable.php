<?php
class PbVote_AttachmentTable
{
    private $field_id = 'pb_project_attachment';
    private $id_prefix = 'pbVoteAttachTblInput';
    private $attach_list , $attach_list_keys;
    private $table_def = array(
      array('id' => "title", 'width' => '80%', 'label' => "Název", 'input_type' => "file", 'class' => "pbvote-attach-table-text text-left", ),
      array('id' => "actions", 'width' => '10%', 'label' => "Úpravy", 'input_type' => "buttons", 'class' => "text-left"),
    );
    private $labels = array(
      'input_title'     => 'Název přílohy',
      'input_file_name' => 'Název souboru',
    );

    public function __construct( $value_list = array(), $id = '', $allow_edit = true )
    {
        $this->attach_list_keys = $value_list;
        $this->get_attachment_list();

        if (! empty($id)) {
            $this->field_id = $id;
        }
        $this->allow_edit = $allow_edit;

        if ( ! wp_style_is('material-icons') ) {
            wp_register_style('material-icons', PB_VOTE_URL . '/assets/css/pb-styles-material-icons.css', array(),'1.0', "all");
            wp_enqueue_style('material-icons');
        }
    }

    private function get_attachment_list( )
    {
    		$this->attach_list = array();
    		foreach ( $this->attach_list_keys as $value) {
    			  $this->attach_list[$value] = array(
    					"title" => get_the_title( $value ),
    					"link"  => wp_get_attachment_url($value),
      			);
    		}
    }

    public function render_table( )
    {

      $pom_value = json_encode( $this->attach_list_keys , JSON_UNESCAPED_UNICODE);
      $keys_list = str_replace( '"', '', $pom_value);
      $output =  '<div class="container attachment-container"><div class="table-wrapper">';
      $output .= '<input type="hidden" id="'.$this->field_id.'" name="'.$this->field_id.'" value="'. $keys_list .'">';
      $output .= '<table class="pbvote-attach-table table-bordered" style="width:100%">';
      $output .= $this->render_col_header();
      $output .= $this->render_body();
      $output .= '</table>';
      $output .= $this->render_add_new();
      $output .= '</div></div>';
      return $output;
    }
    private function render_col_header()
    {
      $output = '<colgroup>';
      foreach ($this->table_def as $item ) {
        $output .= '<col style="width:' . $item['width'] . '">';
      }
      $output .= '</colgroup><thead><tr>';
      foreach ($this->table_def as $item ) {
        $output .= '<th>' . $item['label'] . '</th>';
      }
      $output .= '</tr></thead>';
      return $output;
    }


    private function render_body()
    {
        $output = '<body class="pbvote-attach-table-body">';
        $field_count = count( $this->table_def);
        $output .= $this->render_empty_hidden_row($field_count);

        if (count( $this->attach_list) > 0) {
          foreach ($this->attach_list as $key => $item) {
              $output .= '<tr>';
              for ( $i = 0; $i < $field_count; $i++) {
                  switch ( $this->table_def[$i]['input_type'] ) {
                      case 'file':
                          $output .= $this->render_attach_table_col_file( $key, $item, $this->table_def[$i]);
                          break;
                      case 'buttons':
                          $output .= $this->render_attach_table_col_buttons( $key, $item, $this->table_def[$i]);
                          break;
                      default:
                          $output .= $this->render_attach_table_col_dafault( $key, $item);
                  }
              }
              $output .= '</tr>';
          };
        }
        $output .= '</tbody>';
        return $output;
    }

    private function render_empty_hidden_row($field_count)
    {
        $output = '<tr id="attach_table_hidden_row" hidden>';
        $key = "new00";
        $item = array( 'title' => "", 'link'=> "#",);
        for ( $i = 0; $i < $field_count; $i++) {
          switch ( $this->table_def[$i]['input_type'] ) {
            case 'file':
            $output .= $this->render_attach_table_col_file( $key, $item, $this->table_def[$i]);
            break;
            case 'buttons':
            $output .= $this->render_attach_table_col_buttons( $key, $item, $this->table_def[$i]);
            break;
            default:
            $output .= $this->render_attach_table_col_dafault( $key, $item);
          }
        }
        $output .= '</tr>';
        return $output;
    }

    private function render_attach_table_col_file( $id, $value, $col_def)
    {
      $output  = '<td><input id="attach_table_title_input_'.$id.'" name="attach_table_title_input_'.$id.'" type="text" class="form-control '.$col_def['class'].'" value="'.$value['title'].'" >';
      $output .= '<div id="place_for_file"></div>';
      $output .= '<input type="hidden" id="attach_table_row_id" value="'.$id.'">';
      $output .= '</td>';
      return $output;
    }
    private function render_attach_table_col_buttons( $id, $value, $col_def)
    {
        $output  = '<td>';
        if ($this->allow_edit) {
          $output .= '<a class="attach-delete attach-action-icon" title="Odstranit přílohu" data-toggle="tooltip"><i class="material-icons">delete_forever</i></a>';
        }
        $output .= '<a class="attach-show attach-action-icon" id="attach_table_link_'.$id.'" href="'.$value['link'].'" target="_blank" title="Zobrazit přílohu" data-toggle="tooltip"><i class="material-icons ">open_in_browser</i></a>';
        $output .= '</td>';
        return $output;
    }

    private function render_attach_table_col_dafault( $id, $value)
    {
        $output = '<td><input id="attach_table_title_input_'.$id.'" type="text" class="form-control" value="'.$value['title'].'" disabled></td>';
        return $output;
    }



    private function render_add_new()
    {
      if ($this->allow_edit) {
        $output =
          '<div class="attach-table-new-container">
              <div class="imc-row">
                <div class="imc-grid-4 imc-columns">
                    <div class pbvote-attach-table-label><h4>'.$this->labels['input_title'].'</h4></div>
                    <div><input autocomplete="off" placeholder="Zadejte název přílohy" type="text" id="'.$this->id_prefix.'Title" class="imc-InputStyle attach-input-add-mandatory" value=""></input></div>
                </div>
                <div class="imc-grid-4 imc-columns">
                    <div class="pbvote-attach-table-label"><h4>'.$this->labels['input_file_name'].'</h4></div>
                    <div><input readonly="readonly" autocomplete="off"
                        placeholder="Vyberte soubor" type="text" id="'.$this->id_prefix.'FileName" class="imc-InputStyle attach-input-add-mandatory" value=""></input>
                    </div>
                </div>

                <div class="imc-grid-4 imc-columns">
                        <input autocomplete="off" class="imc-ReportAddImgInputStyle pbvote-AddFileInputStyle attach-input-add-file attach-input-add-mandatory" id="'.$this->id_prefix.'File" type="file" name="'.$this->id_prefix.'File" onchange="pbProjectAttachTblAddFile(this)"></input>
                        <label for="'.$this->id_prefix.'File">
                            <i class="material-icons md-24 imc-AlignIconToButton">file_upload</i>Vyhledat
                        </label>
                </div>
              </div>
              <div class="imc-row">
                <button disabled type="button" title="Přidat přílohu lze pokud je zadaný název a vybraný soubror" class="imc-button u-pull-right attach-add-new"><i class="material-icons md-24 imc-AlignIconToButton">add_circle</i>Přidat přílohu</button>
              </div>
          </div>';
      } else {
        $output = "";
      }
      return $output;
        $output =  '<div class="imc-row">';
        $output .= '<div class="imc-grid-8 imc-columns">';
        $output .= '<h4><span>Tady bude zadaní nové přílohy</span></h4>';
        $output .= '</div>';
        $output .= '<div class="imc-grid-4 imc-columns">';
        $output .= '<button type="button" class="imc-button u-pull-right attach-add-new"><i class="material-icons md-24 imc-AlignIconToButton">add_circle</i>Přidat přílohu</button></div>';
        $output .= '</div>';
    }
}
