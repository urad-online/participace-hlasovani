<?php
class PbVote_RenderFieldBudgetTable extends PbVote_RenderFieldText
{
    private $budget_item_type = array("Příprava", "Realizace");
    private $integer_regexp = '/^[0-9,.]+$/';
    private $vat_values = array( 21, 5, 0 );
    private $table_def = array(
      array('id' => "type", 'width' => '10%', 'label' => "Druh", 'input_type' => "select_type", 'class' => "text-left", 'attr' => ""),
      array('id' => "description", 'width' => '35%', 'label' => "Popis", 'input_type' => "text", 'class' => "text-left", 'attr' => ""),
      array('id' => "units", 'width' => '10%', 'label' => "Počet jednotek", 'input_type' => "text", 'class' => "integer calculate", 'attr' => ""),
      array('id' => "unit_price", 'width' => '15%', 'label' => "Jednotková cena", 'input_type' => "text", 'class' => "integer calculate", 'attr' => ""),
      // array('id' => "vat", 'width' => '8%', 'label' => "DPH", 'input_type' => "select_vat", 'class' => "percent", 'attr' => ""),
      array('id' => "total_price", 'width' => '15%', 'label' => "Celková cena", 'input_type' => "text", 'class' => "integer result", 'attr' => " disabled "),
    );
    private $action_col_width = '10%';
    protected $value = array();
    private $total_sum = 0;
    private $total_buffer = 0.10;
    private $texts =  array(
      "label_header_action" => 'Úpravy',
      "label_total_part1"   => "Celková částka :   ",
      "label_total_part2"   => "Kč + rezerva",
      "label_add_item"      => "Přidat položku",
    );

    public function __construct( $field, $value,  $allow_edit = true )
    {
        $this->allow_edit = $allow_edit;
        parent::__construct( $field, $value);
    }
    protected function set_value( $value = '')
		{
				if (! empty( $value)) {
          $this->value = unserialize( $value);
				}
		}

    protected function do_action()
    {
        if ( ! wp_style_is('material-icons') ) {
          wp_register_style('material-icons', PB_VOTE_URL . '/assets/css/pb-styles-material-icons.css', array(),'1.0', "all");
          wp_enqueue_style('material-icons');
        }
        wp_register_script('pb-budget-table',   PB_VOTE_URL . '/assets/js/pb-budget-table.js', array('jquery'),'1.1', false);

        wp_enqueue_script('pb-budget-table');
        wp_localize_script('pb-budget-table', 'pbTableListsData', array(
          'types' => $this->budget_item_type,
          // 'vat'   => $this->vat_values,
          'table_def'   => $this->table_def,
          'budget_data' => $this->value,
        ));

    }
    public function render_body( )
    {
      $output =  '<div class=""><div class="table-wrapper">';
      $output .= '<input type="hidden" id="pb_project_naklady" name="pb_project_naklady" value="'. json_encode( ($this->allow_edit) ? $this->value : array(), JSON_UNESCAPED_UNICODE) .'">';
      // $output .= '<div class="table-title"><div class="imc-row"><div class="imc-grid-8 imc-columns"><h2>Rozpočet návrhu</h2></div></div></div>';
      $output .= '<table class="pbvote-budget-table table-bordered" style="width:100%">';
      $output .= $this->render_table_col_header();
      $output .= $this->render_table_body();
      $output .= '</table>';
      $output .= $this->render_add_button();
      $output .= '</div></div>';
      return $output;
    }
    private function render_table_col_header()
    {
      $output = '<colgroup>';
      foreach ($this->table_def as $item ) {
        $output .= '<col style="width:' . $item['width'] . '">';
      }
      if ($this->allow_edit) {
        $output .= '<col style="width:'.$this->action_col_width.'">';
      }
      $output .= '</colgroup><thead><tr>';
      foreach ($this->table_def as $item ) {
        $output .= '<th>' . $item['label'] . '</th>';
      }
      if ($this->allow_edit) {
        $output .= '<th>' . $this->texts["label_header_action"] . '</th>';
      }
      $output .= '</tr></thead>';
      return $output;
    }

    private function render_add_button()
    {
      $output =  '<div class="table-title row-total"><div class="imc-row">';
      $output .= '<div style="display:inline-block;">';
      $output .= '<h4><span class="keep-space">' . $this->texts["label_total_part1"] . '</span><span id="total_budget_sum" class="keep-space">'.number_format($this->total_sum,0).' </span>';
      $output .= '<span class="keep-space"> ' . $this->texts["label_total_part2"] . ' '.($this->total_buffer*100).'% = </span>';
      $output .= '<span class="keep-space" id="total_budget_sum_with_buffer">'.number_format($this->total_sum*(1+$this->total_buffer),0).'</span><span class="keep-space"> Kč</span></h4>';
      $output .=   '<h4>';
      $output .= '</div>';
      if ($this->allow_edit) {
        $output .= '<div style="display:inline-block;" class="u-pull-right">';
        $output .= '<button type="button" class="imc-button u-pull-right add-new"><i class="material-icons md-24 imc-AlignIconToButton">add_circle</i>' . $this->texts["label_add_item"] . '</button>';
        $output .= '</div>';
      }
      $output .= '</div></div>';
      return $output;
    }

    private function render_table_body()
    {
        $this->total_sum = 0;
        // if ($this->allow_edit) {
        //   $output = '<tbody></tbody>';
        // } else {
          $output = '<tbody class="pbvote-budget-table-body">';
          if (count( $this->value) > 0) {
            $field_count = count( $this->table_def);
            foreach ($this->value as $item) {
              $output .= '<tr>';
              for ( $i = 0; $i < $field_count; $i++) {
                $output .= '<td class="' . $this->table_def[$i]['class'] . '">' . $this->format_value( $item[$i], $this->table_def[$i]['class']) . '</td>';
                if (  stripos( $this->table_def[$i]['class'], 'result')  > -1 ) {
                  $this->total_sum += intval($item[$i]);
                }
              }
              if ($this->allow_edit) {
                $output .= $this->render_control_buttons();
              }
              $output .= '</tr>';
            };
          }
          $output .= '</tbody>';
        // }
        return $output;
    }

    private function render_control_buttons()
    {
      $output = '<td>';
      // $output .= '<a class="add" title="Add" data-toggle="tooltip"><i class="material-icons">playlist_add</i></a>';
      $output .= '<a class="add" title="Uložit" data-toggle="tooltip"><i class="material-icons">save</i></a>';
      $output .= '<a class="edit" title="Upravit" data-toggle="tooltip"><i class="material-icons">edit</i></a>';
      $output .= '<a class="delete" title="Smazat" data-toggle="tooltip"><i class="material-icons">delete</i></a>';
      $output .= '<a class="cancel" title="Zrušit" data-toggle="tooltip"><i class="material-icons">cancel</i></a>';
      $output .= '</td>';
      return $output;
    }

    private function format_value( $value, $classes)
    {
      if (stripos( $classes, 'integer') > -1 ) {
        return number_format($value,0);
      } else {
        return $value;
      }
    }
}
