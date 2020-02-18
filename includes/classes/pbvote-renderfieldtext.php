<?php
class PbVote_RenderFieldText
{
		protected $required = '';
		protected $mandatory = '';
		protected $value = '';

		public function __construct($field, $value )
		{
				$this->field = $field;
				$this->set_value( $value);
				$this->set_mandatory();
				$this->set_attributes();
		}

		public function render_field( $order )
		{
			$this->do_action();
			if (empty($this->field)) {
				return '<h3>'.$order.'Nedefinováno</h3>';
			}
			$output = $this->render_header( $order );
			$output .= $this->render_body();
			return $output;
		}
		protected function set_value( $value = '')
		{
				if (! empty( $value)) {
					$this->value = $value;
				}
		}

		protected function render_header( $order )
		{
			$output  = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;">';
			$output .= $order . $this->field['label'] . $this->mandatory . $this->render_tooltip() .'</h3>';
			return $output;
		}

		public function render_body()
		{
			$placeholder = (empty( $this->field['placeholder'])) ? "" : $this->field['placeholder'];

			$output = '<input type="'.$this->field['type'].'" '.$this->attributes.' autocomplete="off"';
			$output .= ' placeholder="'.$placeholder.'" name="'.$this->field['id'].'" id="'.$this->field['id'].'" class="imc-InputStyle"';
			$output .= ' value="'.$this->value.'" ></input>';
			$output .= '<label id="'.$this->field['id'].'Label" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';

			return $output;
		}

		protected function render_tooltip()
		{
			if (! empty( $this->field['help'])) {
					return '<span class="pb_tooltip"><i class="material-icons md-24" style="margin-left:2px;">help_outline</i>
					<span class="pb_tooltip_text" >' . $this->field['help'] . '</span></span>' ;
			} else {
					return '';
			}
		}
		protected function set_mandatory()
		{
			if ( !empty( $this->field['mandatory']) && $this->field['mandatory'] ) {
					$this->mandatory = '';
			} else {
					$this->mandatory = ' ( ' . __('volitelné','pb-voting') .' )';
			}
		}
		protected function set_attributes()
		{
			$this->attributes = '';
			if ( ! empty($this->field['attributes'])) {
					$this->attributes = " ".$this->field['attributes'];
			}
		}
		protected function do_action()
		{
			//
		}
}
