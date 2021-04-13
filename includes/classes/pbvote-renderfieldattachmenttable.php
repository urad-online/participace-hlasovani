<?php
class PbVote_RenderFieldAttachmentTable extends PbVote_RenderFieldText
{
    private $field_id = 'pb_project_attachment';
    private $id_prefix = 'pbVoteAttachTblInput';
    private $attach_list;
    private $attach_list_keys = array();
    private $table_def = array(
      array('id' => "title", 'width' => '80%', 'label' => "Název", 'input_type' => "file", 'class' => "pbvote-attach-table-text text-left", ),
      array('id' => "actions", 'width' => '10%', 'label' => "Úpravy", 'input_type' => "buttons", 'class' => "text-left"),
    );
    private $texts =  array(
      "label_show_att"  => "Zobrazit přílohu",
			"label_del_att"   => "Odstranit přílohu",
			"label_title_att" => "Zadejte název přílohy",
			"label_add_cond"  => "Přidat přílohu lze pokud je zadaný název a vybraný soubror",
			"label_add_att"   => "Přidat přílohu",
			"error_file_size" => "Soubor musí být menší než %d MB.",
			"info_file_type"  => "Jsou povolené tyto typy souborů - gif, png, jpg, jpeg, pdf.",
		);
    private $file_size_limit = 2000000;

    public function __construct( $field, $value,  $allow_edit = true )
    {
        $this->allow_edit = $allow_edit;
        parent::__construct( $field, $value);
    }

    public function do_action( )
    {

        if (! empty($this->field['id'])) {
            $this->field_id = $this->field['id'];
            $this->id_prefix = "pbVoteAttachTblInput".$this->field['id'];
        }

        if ( ! wp_style_is('material-icons') ) {
            wp_register_style('material-icons', PB_VOTE_URL . '/assets/css/pb-styles-material-icons.css', array(),'1.0', "all");
            wp_enqueue_style('material-icons');
        }
    }

    protected function set_value( $value = '')
    {
        if (! empty( $value)) {
          $this->attach_list_keys = unserialize( $value);
        }
        $this->get_attachment_list();
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

    public function render_body( )
    {

      $pom_value = json_encode( $this->attach_list_keys , JSON_UNESCAPED_UNICODE);
      $keys_list = str_replace( '"', '', $pom_value);
      $output =  '<div class="attachment-container"><div class="table-wrapper">';
      $output .= '<input type="hidden" class="pbvote-attach-table-hidden-list" id="'.$this->field_id.'" name="'.$this->field_id.'" value="'. $keys_list .'">';
      $output .= '<table class="pbvote-attach-table table-bordered" style="width:100%">';
      $output .= $this->render_col_header();
      $output .= $this->render_table_body();
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


    private function render_table_body()
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
          $output .= '<a class="attach-delete attach-action-icon" title="'.$this->texts["label_del_att"].'" data-toggle="tooltip"><i class="material-icons">delete_forever</i></a>';
        }
        $output .= '<a class="attach-show attach-action-icon" id="attach_table_link_'.$id.'" href="'.$value['link'].'" target="_blank" title="'.$this->texts["label_show_att"].'" data-toggle="tooltip"><i class="material-icons ">open_in_browser</i></a>';
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
                <div style="display:inline-block;min-width:30%;">
                    <div><input autocomplete="off" placeholder="'.$this->texts["label_title_att"].'" type="text" maxlength="30" style="min-width:350px;" id="'.$this->id_prefix.'Title" class="imc-InputStyle attach-input-add-mandatory pbVoteAttachTblInputTitle" value=""></input></div>
                </div>
                <div style="display:inline-block;">
                    <div><input disabled autocomplete="off"
                        type="text" id="'.$this->id_prefix.'FileName" class="pbvote-DisabledInputStyle attach-input-add-mandatory pbVoteAttachTblInputFileName" value=""></input>
                    </div>
                </div>

                <div style="display:inline-block;">
                        <input autocomplete="off" class="imc-ReportAddImgInputStyle pbvote-AddFileInputStyle attach-input-add-file attach-input-add-mandatory pbVoteAttachTblInputFile" id="'.$this->id_prefix.'File" type="file" name="'.$this->id_prefix.'File" onchange="pbProjectAttachTblAddFile(this,'.$this->file_size_limit.')"></input>
                        <label for="'.$this->id_prefix.'File">
                            <i class="material-icons md-24 imc-AlignIconToButton">file_upload</i>Vyhledat soubor
                        </label>
                </div>
                <div style="display:inline-block;" class="u-pull-right">
                  <button disabled type="button" title="'.$this->texts["label_add_cond"].'" class="imc-button attach-add-new"><i class="material-icons md-24 imc-AlignIconToButton">add_circle</i>'.$this->texts["label_add_att"].'</button>
                </div>
              </div>
              <div class="imc-row"><span class="u-pull-left imc-ReportFormSubmitErrorsStyle">
                <p id="pbvote-error-message-size"  hidden>'.sprintf( $this->texts["error_file_size"], $this->file_size_limit/1000000).'</p>
                <p id="pbvote-error-message-type"  hidden>'.$this->texts["info_file_type"].'</p>
              </span></div>
          </div>';
      } else {
        $output = "";
      }
      return $output;
    }
}
