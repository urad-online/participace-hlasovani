<?php
class PbVote_RenderFieldImageFile extends PbVote_RenderFieldText
{
		private $butt_add_label, $butt_del_label, $no_photo_label;
		private $max_size_label, $sel_photo_label ;

		protected function do_action()
		{
				$this->butt_add_label = __('Add photo','pb-voting');
				$this->butt_del_label = __('Delete Photo', 'pb-voting');
				$this->no_photo_label = __('No photo attached','pb-voting');
				$this->max_size_label = __('Photo size must be smaller in size, please resize it or select a smaller one!','pb-voting');
				$this->sel_photo_label = __('A photo has been selected:','pb-voting');

		}
		protected function render_header( $order )
		{
			$output  = '<div class="imc-row" id="imcImageSection"><h3 class="u-pull-left imc-SectionTitleTextStyle">';
			$output .= $order . $this->field['label'] . $this->mandatory . $this->render_tooltip() .'</h3>';

			return $output;
		}

		public function render_body()
		{
				if ( $this->value ) {
					$img_src = '<img id="imcPreviousImg" class="u-cf pbvote-feature-image-select" style="max-height: 200px;" src="'.$this->value.'">';
					$no_photo = 'style="display: none;"';
				} else {
					$img_src  = $this->value;
					$no_photo = '';
				}
			$output  = '<div class="u-cf"><input autocomplete="off" class="imc-ReportAddImgInputStyle" id="imcReportAddImgInput" type="file" name="featured_image" />';
			$output .= '<label for="imcReportAddImgInput"><i class="material-icons md-24 imc-AlignIconToButton">photo</i>'. $this->butt_add_label .'</label>';
			$output .= '<button type="button" class="imc-button" onclick="imcDeleteAttachedImage(\'imcReportAddImgInput\');">';
			$output .= '<i class="material-icons md-24 imc-AlignIconToButton">delete</i>'. $this->butt_del_label. '</button></div>';
			$output .= '<span '.$no_photo.' id="imcNoPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'.$this->no_photo_label.'</span>';
			$output .= '<span style="display: none;" id="imcLargePhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'.$this->max_size_label.'</span>';
			$output .= '<span style="display: none;" id="imcPhotoAttachedLabel" class="imc-ReportGenericLabelStyle imc-TextColorSecondary">'.$this->sel_photo_label.'</span>';
			$output .= '<span class="imc-ReportGenericLabelStyle imc-TextColorPrimary" id="imcPhotoAttachedFilename"></span></div>';
			$output .= '<input title="orientation" type="hidden" id="imcPhotoOri" name="imcPhotoOri"/>';
			$output .= $img_src;

			return $output;
		}

}
