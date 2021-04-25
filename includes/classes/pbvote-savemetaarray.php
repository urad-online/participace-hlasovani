<?php
class PbVote_SaveMetaArray extends PbVote_SaveMetaText
{
		public function get_value()
		{
			return json_decode(stripslashes(sanitize_text_field($_POST[ $this->field['id']])));
		}
}
