<?php
class PbVote_RenderFieldAddressWithMap extends PbVote_RenderFieldText
{
		private $butt_locate, $address_placeholder;
		// private $katastr_url = "https://www.ikatastr.cz/ikatastr.htm#zoom=19&";
		// private $default_gps_coordinates = array( 'lat' => "50.10766", 'lon' => "14.47145");
		// private $katastr_layer = "layers_3=000B00FFTFFT";

		protected function do_action()
		{
				$this->butt_locate 				 = __('Locate', 'pb-voting');
				$this->address_placeholder = __('Add an address','pb-voting');
		}

		protected function render_header( $order )
		{
			$output  = '<div class="imc-row-no-margin"><h3 class="imc-SectionTitleTextStyle">';
			$output .= $order . $this->field['label'] . $this->mandatory . $this->render_tooltip() .'</h3>';

			return $output;
		}

		public function render_body()
		{
				$output  = '<button class="imc-button u-pull-right" type="button" onclick="imcFindAddress(\''.$this->field['id'].'\', true)">';
				$output .= '<i class="material-icons md-24 imc-AlignIconToButton">search</i>'.$this->butt_locate.'</button>';
				$output .= '<div style="padding-right: .5em;" class="imc-OverflowHidden">';
				$output .= '<input name="'.$this->field['id'].'" placeholder="'.$this->address_placeholder.'" id="'.$this->field['id'].'" class="u-pull-left imc-InputStyle" value="'.$this->value.'"/></div>';
				$output .= '<input title="lat" type="hidden" id="imcLatValue" name="imcLatValue"/>';
				$output .= '<input title="lng" type="hidden" id="imcLngValue" name="imcLngValue"/>';
				$output .= '</div><div class="imc-row"><div id="imcReportIssueMapCanvas" class="u-full-width imc-ReportIssueMapCanvasStyle"></div></div>';

				$output .= $this->render_link_katastr();
				return $output;
		}
		 public function render_link_katastr($latlng = array())
		{
				// if ( empty( $latlng ) ) {
				// 		$latlng = $this->default_gps_coordinates;
				// }
				// $url = $katastr_url . "lat=" . $latlng['lat']."&lon=".$latlng['lon']."&".$katastr_layer."&ilat=".$latlng['lat']."&lon=".$latlng['lon'];

				return '<div class="imc-row" ><span>Kliknutím na tento </span>
						<a id="pb_link_to_katastr" href="#" data-toggle="tooltip" title="Přejít na stránku s katastrální mapou"
								class=""><span>odkaz</span></a><span> zobrazíte katastrální mapu na vámi označeném místě.
						Ve vyskakovacím okně (musíte mít povoleno ve vašem prohlížeči) získáte informace k vybranému pozemku. Nalezněte všechna katastrální čísla týkajících se návrhu, kliknutím do mapy ověřte,
						zda jsou všechny dotčené pozemky ve správě HMP nebo MČ a tedy splňujete podmínky pravidel participativního rozpočtu. Seznam všech dotčených pozemků uveďte do pole níže (jedna položka na jeden řádek).</span>
						</div>';
		}

}
