<?php
class PbVote_RenderFieldAttachmentFile extends PbVote_RenderFieldText
{
		private $butt_add_label, $butt_del_label, $no_photo_label;
		private $max_size_label, $sel_photo_label ;
		private $attrib	 = ' readonly="readonly" ';
		private $texts =  array(
			"label_select"    => "Vyberte soubor",
			"label_show_att"  => "Zobrazit přílohu",
			"label_miss_att" => "Chybí příloha",

		);

		protected function set_value( $value = '')
		{
				$this->value = $value;
				if ($value) {
					$this->filename = basename($value);
				} else {
					$this->filename = $value;
				}
				$this->link = $this->render_file_link();
		}

		protected function render_header( $order )
		{
			$output  = '<div class="imc-row" id="pbProjectSection'.$this->field['title'].'"><div class="imc-row">';
			$output .= parent::render_header( $order);
			$output .= '</div>';

			return $output;
		}

		public function render_body()
		{
			$output  = '<div class="imc-row"><div class="imc-grid-5 imc-columns"><input '.$this->attrib.' autocomplete="off" ';
			$output .= 'placeholder="'.$this->texts["label_select"].'" type="text" name="'.$this->field['id'].'Name" id="'.$this->field['id'].'Name" class="imc-InputStyle" ';
			$output .= 'value="'.$this->filename.'"/>';
			$output .= '<label id="'.$this->field['id'].'NameLabel" class="imc-ReportFormErrorLabelStyle imc-TextColorPrimary"></label></div>';
			$output .= '<div class="imc-grid-6 imc-columns"><div class="u-cf">';
			$output .= '<div class="imc-row">'.$this->link;
			$output .= '<input autocomplete="off" class="imc-ReportAddImgInputStyle" id="'.$this->field['id'].'" type="file" name="'.$this->field['id'].'" onchange="pbProjectAddFile(this)" />';
			$output .= '<label for="'.$this->field['id'].'"><i class="material-icons md-24 imc-AlignIconToButton">'.$this->field['material_icon'].'</i>'.$this->field['AddBtnLabel'].'</label>';
			$output .= '<button type="button" class="imc-button" onclick="imcDeleteAttachedFile(\''.$this->field['id'].'\');">';
			$output .= '<i class="material-icons md-24 imc-AlignIconToButton">delete</i>'.$this->field['DelBtnLabel'].'</button>';
			$output .= '</div></div></div></div></div>';
			return $output;

		}

		private function render_file_link()
		{
				if (! empty($this->value)) {
						return '<a id="'.$this->field['id'].'Link" href="'.$this->value.'" target="_blank" data-toggle="tooltip" title="'.$this->texts["label_show_att"].'" class="u-pull-right
								imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-SingleHeaderIconStyle">file_download</i></a>';
												// <i class="material-icons md-36 imc-SingleHeaderIconStyle">open_in_browser</i></a>';
				} else {
						return '<a hidden id="'.$this->field['id'].'Link" data-toggle="tooltip" title="'.$this->texts["label_miss_att"].'" class="u-pull-right
								imc-SingleHeaderLinkStyle"><i class="material-icons md-36 imc-SingleHeaderIconStyle">file_download</i></a>';
				}
		}

}
