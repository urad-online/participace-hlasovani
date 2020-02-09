<?php
class PbVote_RenderCheckboxGroup
{
		protected $required = '';
		protected $mandatory = '';

		public function __construct($field, $value )
		{
				$this->field = $field;
				$this->set_value( $value);
		}

		public function render_field( $order, $help = '')
		{
			if (empty($this->field)) {
				return '<h3>'.$order.'Nedefinováno</h3>';
			}
			$output = $this->render_header( $order, $help );
			$output .= $this->render_body();
			return $output;
		}
		public function set_value( $value = '')
		{
			if (empty($value)) {
				$this->value = array();
			} else {
				if ( is_array($value)) {
					$this->value = $value;
				} else {
					$this->value = unserialize( $value);
				}
			}
			$pom_value = json_encode( $this->value , JSON_UNESCAPED_UNICODE);
			$this->value_json = str_replace( '"', '', $pom_value);
		}

		protected function render_header( $order, $help )
		{
			$output  = '<h3 class="imc-SectionTitleTextStyle" style="display:inline-block;"><label id="'.$this->field['id'].'Name" for="'.$this->field['id'].'">';
			$output .= $order . $this->field['label'] . $this->render_tooltip($help) .'</label>';
			$output .= '</h3><label id="'.$this->field['id'].'Label" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label>';
			return $output;
		}

		public function render_body()
		{
			$output  = '<div class="pbvote-CheckboxGroup-container">';
			$output .= '<input class="pbvote-project-checkboxgroup-input" type="hidden" id="'.$this->field['id'].'" name="'.$this->field['id'].'" value="'.$this->value_json.'">';
			foreach( $this->field['items'] as $pb_item ) {
				if ((! empty($this->value)) && in_array($pb_item['iid'], $this->value, true)) {
					$checked = 'checked="checked"';
				} else {
					$checked = '';
				}
				$output .= '<div><input '.$checked.' class="pbvote-CheckboxGroupStyle imc-CheckboxStyle pbvote-CheckboxGroup-member" id="'.$pb_item['iid'].'" type="checkbox" name="'.$pb_item['iid'].'" value="'.$pb_item['iid'].'">';
				$output .= '<label for="'.$pb_item['iid'].'">'.$pb_item['ilabel'].'</label>';
				$output .= '</div>';
			}
			$output .= '</div>';
			return $output;
		}

		protected function render_tooltip($text)
		{
			if (! empty( $text)) {
					return '<span class="pb_tooltip"><i class="material-icons md-24" style="margin-left:2px;">help_outline</i>
					<span class="pb_tooltip_text" >' . $text . '</span></span>' ;
			} else {
					return '';
			}
		}
}
