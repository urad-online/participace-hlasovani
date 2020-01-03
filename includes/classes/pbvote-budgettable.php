<?php
class PbVote_BudgetTable
{
    private $budget_item_type = array("Příprava", "Realizace", "Údržba");
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
      wp_register_script('pb-budget-table',   PB_VOTE_URL . '/assets/js/pb-budget-table.js', array('jquery'),'1.1', false);

      wp_enqueue_script('pb-budget-table');
      wp_localize_script('pb-budget-table', 'pbTableListsData', array(
                'types' => $this->budget_item_type,
                // 'vat'   => $this->vat_values,
                'table_def' => $this->table_def,
                'budget_data' => $this->budget_data,
              ));
    }
    public function render_table( )
    {
      $output =  '<div class="container"><div class="table-wrapper">';
      $output .= '<input type="hidden" id="pb_project_naklady" name="pb_project_naklady" value="'.json_encode( $this->budget_data, JSON_UNESCAPED_UNICODE).'">';
      // $output .= '<div class="table-title"><div class="imc-row"><div class="imc-grid-8 imc-columns"><h2>Rozpočet návrhu</h2></div></div></div>';
      $output .= '<table class="pbvote-budget-table table-bordered" style="width:100%">';
      $output .= $this->render_col_header();
      $output .= $this->render_body();
      $output .= '<tbody></tbody></table>';
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
      $output .= '<div class="imc-grid-8 imc-columns"><div class="imc-row"><div class="imc-grid-4 imc-columns"><h6>Celková částka :</h6></div>';
      $output .= '<div class="imc-grid-2 imc-columns"><h6 id="total_budget_sum" name="total_budget_sum">'.number_format($this->total_sum,0).'</h6></div></div></div>';
      if ($this->allow_edit) {
        $output .= '<div class="imc-grid-4 imc-columns">';
        $output .= '<button type="button" class="imc-button add-new"><i class="material-icons md-24 imc-AlignIconToButton">add_circle</i>Přidat položku</button></div>';
      }
      $output .= '</div></div>';
      return $output;
    }
    private function render_body()
    {
        $this->total_sum = 0;
        if ($this->allow_edit) {
          $output = '<tbody></tbody>';
        } else {
          $output = '<tbody>';
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
              $output .= '</tr>';
            };
          }
          $output .= '</tbody>';
        }
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
