<?php
class PbVote_SaveMetaPostAddress extends PbVote_SaveMetaText
{
		protected function add_value( )
		{
				$this->list[ 'imc_address'] = esc_attr(sanitize_text_field($_POST[ $this->field['id']]));
				$this->list[ 'imc_lat' ]    = esc_attr(sanitize_text_field($_POST[ 'imcLatValue']));
				$this->list[ 'imc_lng' ]    = esc_attr(sanitize_text_field($_POST[ 'imcLngValue']));
		}

		public function get_value( )
		{
				return array(
					'imc_address' => esc_attr(sanitize_text_field($_POST[ $this->field['id']])),
					'imc_lat'     => esc_attr(sanitize_text_field($_POST[ 'imcLatValue'])),
					'imc_lng'     => esc_attr(sanitize_text_field($_POST[ 'imcLngValue'])),
				);

		}

}
