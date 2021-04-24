<?php
class PbVote_SaveMetaText
{
		protected $input_id = "";
		protected $value_set = null;
		public function __construct( $field )
		{
				$this->field = $field;
				$this->check_value_set();
		}

		protected function check_value_set()
		{
			if (isset( $_POST[ $this->field['id']])) {
				$this->value_set = true;
			}
		}

		public function set_meta_value( &$meta_list )
		{
			$this->list = &$meta_list;
			if ($this->value_set) {
				$this->add_value( );
			}
		}

		protected function add_value( )
		{
				$this->list[ $this->field['id']] = $this->get_value();
		}
		public function get_value()
		{
			return esc_attr(sanitize_text_field($_POST[ $this->field['id']]));
		}
}
