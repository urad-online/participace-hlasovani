<?php
class PbVote_RenderFieldTextArea extends PbVote_RenderFieldText
{
		protected $rows = 3;

		protected function set_value( $value = '')
		{
					$this->value = $value;
					if ( ! empty($this->field['rows'])) {
	            $this->rows = $this->field['rows'];
	        }

		}

		public function render_body()
		{
			$placeholder = (empty( $this->field['placeholder'])) ? "" : $this->field['placeholder'];

			$output  = '<textarea placeholder="'.$placeholder.'" '.$this->attributes.' rows="'.$this->rows.'"';
			$output .= 'class="imc-InputStyle pbvote-resizeable"  name="'.$this->field['id'].'"';
			$output .= ' id="'.$this->field['id'].'">'.$this->value.'</textarea>';
			$output .= '<label id="'.$this->field['id'].'Label" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

			return $output;
		}

}
