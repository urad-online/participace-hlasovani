<?php
class PbVote_RenderFieldCategory extends PbVote_RenderFieldText
{
		protected $value = 0;
		private $taxo_category_name = 'my_custom_taxonomy';

		public function render_body()
		{
			$output = '<label class="imc-CustomSelectStyle u-full-width">';
			$output .= pbvote_insert_cat_dropdown( $this->taxo_category_name, $this->value );
			$output .= '</label><label id="'.$this->field['id'].'Label" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

			return $output;
		}

}
