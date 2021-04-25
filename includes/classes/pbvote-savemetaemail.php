<?php
class PbVote_SaveMetaEmail extends PbVote_SaveMetaText
{
		public function get_value( )
		{
				return esc_attr(sanitize_email($_POST[ $this->field['id']]));
		}
}
