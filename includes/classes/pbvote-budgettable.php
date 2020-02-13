<?php
class PbVote_BudgetTable
{
    private $budget_item_type = array("Příprava", "Realizace");
    private $integer_regexp = '/^[0-9,.]+$/';
    private $vat_values = array( 21, 5, 0 );
    private $table_def = array(
      array('id' => "type", 'width' => '15%', 'label' => "Druh", 'input_type' => "select_type", 'class' => "text-left", 'attr' => ""),
      array('id' => "description", 'width' => '45%', 'label' => "Popis", 'input_type' => "text", 'class' => "text-left", 'attr' => ""),
      array('id' => "units", 'width' => '8%', 'label' => "Počet jednotek", 'input_type' => "text", 'class' => "integer calculate", 'attr' => ""),
      array('id' => "unit_price", 'width' => '8%', 'label' => "Jednotková cena", 'input_type' => "text", 'class' => "integer calculate", 'attr' => ""),
      // array('id' => "vat", 'width' => '8%', 'label' => "DPH", 'input_type' => "select_vat", 'class' => "percent", 'attr' => ""),
      array('id' => "total_price", 'width' => '8%', 'label' => "Celková cena", 'input_type' => "text", 'class' => "integer result", 'attr' => " disabled "),
    );
    private $budget_data = array(array('Příprava', 'Vypracování projektu', '1', '15000', '21', '15000'), array('Realizace', 'Dozor na stavbě', '1', '5000', '21', '5000'));
    private $total_sum = 0;

    public function __construct( $allow_edit = true, $data )
    {
      if (empty($data)) {
        $this->budget_data = array();
      } else {
        $this->budget_data = $data;
      }
      $this->allow_edit = $allow_edit;

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
                'budget_data' => $this->budget_data,
              ));
    }
    public function render_table( )
    {
      $output =  '<div class="container"><div class="table-wrapper">';
      $output .= '<input type="hidden" id="pb_project_naklady" name="pb_project_naklady" value="'. json_encode( ($this->allow_edit) ? $this->budget_data : array(), JSON_UNESCAPED_UNICODE) .'">';
      // $output .= '<div class="table-title"><div class="imc-row"><div class="imc-grid-8 imc-columns"><h2>Rozpočet návrhu</h2></div></div></div>';
      $output .= '<table class="pbvote-budget-table table-bordered" style="width:100%">';
      $output .= $this->render_col_header();
      $output .= $this->render_body();
      $output .= '</table>';
      $output .= $this->render_add_button();
      $output .= '</div></div>';
      return $output;
    }
    private function render_col_header()
    {
      $output = '<colgroup>';
      foreach ($this->table_def as $item ) {
        $output .= '<col style="width:' . $item['width'] . '">';
      }
      if ($this->allow_edit) {
        $output .= '<col style="width:8%">';
      }
      $output .= '</colgroup><thead><tr>';
      foreach ($this->table_def as $item ) {
        $output .= '<th>' . $item['label'] . '</th>';
      }
      if ($this->allow_edit) {
        $output .= '<th>' . __('Úpravy', 'pb-voting') . '</th>';
      }
      $output .= '</tr></thead>';
      return $output;
    }

    private function render_add_button()
    {
      $output =  '<div class="table-title row-total"><div class="imc-row">';
      $output .= '<div class="imc-grid-8 imc-columns">';
      $output .=   '<h4><span class="keep-space">Celková částka včetně 10% rezervy:   </span><span id="total_budget_sum" name="total_budget_sum">'.number_format($this->total_sum,0).'</span><span> Kč </span></h4>';

      $output .= '</div>';
      if ($this->allow_edit) {
        $output .= '<div class="imc-grid-4 imc-columns">';
        $output .= '<button type="button" class="imc-button u-pull-right add-new"><i class="material-icons md-24 imc-AlignIconToButton">add_circle</i>Přidat položku</button></div>';
      }
      $output .= '</div></div>';
      return $output;
    }

    private function render_body()
    {
        $this->total_sum = 0;
        // if ($this->allow_edit) {
        //   $output = '<tbody></tbody>';
        // } else {
          $output = '<tbody class="pbvote-budget-table-body">';
          if (count( $this->budget_data) > 0) {
            $field_count = count( $this->table_def);
            foreach ($this->budget_data as $item) {
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
