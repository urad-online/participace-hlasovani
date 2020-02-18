<?php
class PbVote_RenderFieldCheckbox extends PbVote_RenderFieldText
{
		protected $checked = 3;

		protected function set_value( $value = '')
		{
				if ( ! empty( $value) ){
						if ( $value ) {
								$this->checked = 'checked';
						}
				} else {
						if (! empty($this->field['default']) && ( $this->field['default'] != 'no') && ( $this->field['default'] != '0') ) {
								$this->checked = 'checked';
						}
				}
		}
		protected function render_header( $order)
		{
				$output  = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;">';
				$output .= '<label id="'.$this->field['id'].'Name" for="'.$this->field['id'].'">'. $order . $this->field['label']. $this->render_tooltip() .'</label></h3>';

				return $output;
		}

		public function render_body()
		{
			$output  = '<input type="checkbox" ' .$this->checked . '  name="'.$this->field['id'].'" id="'.$this->field['id'].'" ';
			$output .= 'class="imc-InputStyle" value="1" style="width:20px; height:20px; display:inline-block;margin-left:10px"/>';
			// $output .= '<label id="'.$this->field['id'].'Label" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

			return $output;
		}

}
