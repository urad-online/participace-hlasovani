<?php
class PbVote_SaveMetaTextarea extends PbVote_SaveMetaText
{
		public function get_value( )
		{
				return esc_attr(sanitize_textarea_field($_POST[ $this->field['id']]));
		}
}
