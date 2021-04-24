<?php
class PbVote_SaveMetaCheckbox extends PbVote_SaveMetaText
{
		public function get_value( )
		{
				return (! empty($_POST[ $this->field['id']])) ? esc_attr(sanitize_text_field($_POST[ $this->field['id']])) : '0';
		}
}
