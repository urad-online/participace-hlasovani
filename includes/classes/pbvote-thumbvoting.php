<?php
class PbVote_ThumbVoting
{
		private $icon = 'thumb_up';
		private $likers_metakey	= 'imc_allvoters';
		private $likes_count_metakey = 'imc_likes';
		private $likes_table = 'imc_votes';
		private $user_already_voted = false;

		public function __construct( $post_id = 0, $author_id = 0 )
		{
				$this->post_id = $post_id;
				$this->author_id = intval($author_id, 10);
				$this->current_user = intval(get_current_user_id(), 10);
		}

		public function get_likes_count()
		{
				return intval(get_post_meta($this->post_id, $this->likes_count_metakey, true), 10);
		}
		public function update_likes_count()
		{
				if (isset($_POST['post_nonce_field']) && wp_verify_nonce($_POST['post_nonce_field'], 'post_nonce')) {
						$old_likes = $this->get_likes_count();
						$new_likes = $old_likes + 1;

						add_post_meta($this->post_id, $this->likers_metakey, $this->current_user, false);

						global $wpdb;
						$votes_table_name = $wpdb->prefix . $this->likes_table;
						$wpdb->insert(
							$votes_table_name,
							array(
								'issueid' => $this->post_id,
								'created' => gmdate("Y-m-d H:i:s",time()),
								'created_by' => $current_user,
							)
						);

						$update = update_post_meta($this->post_id, $this->likes_count_metakey, $new_likes, $old_likes);
						if ($update) {
								wp_redirect(get_permalink($this->post_id));
						}
				}

		}
		private function set_user_has_voted()
		{
				$voterslist = get_post_meta($this->post_id, $this->likers_metakey, false);
				if (in_array($this->current_user, $voterslist, true)) {
						$this->user_already_voted = true;
				}
		}
		public function render_likes()
		{
				if ( get_post_status( $this->post_id ) != 'publish') {
					 return '';
				}

				$output = '';

				if ( is_user_logged_in() ) {
						$output = '<form action="" id="increaseBtn" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="submitted" id="submitted" value="true"/>';
						$output .= wp_nonce_field('post_nonce', 'post_nonce_field');
						if ($this->current_user === $this->author_id) {
								$output .= '<div class="imc-CardLayoutStyle imc-CenterContents">';
								$output .= '<img alt="My Issue icon" class="imc-VerticalAlignMiddle" title="' . __('My Issue', 'pb-voting') .'" ';
								$output .= 'src="'. esc_url( PB_VOTE_URL . '/img/ic_my_issue_grid.png'). '">'.
								$output .= '<span class="imc-Text-MD imc-TextMedium imc-TextColorSecondary imc-FontRoboto">';
								$output .=  __('My issue', 'pb-voting') .'</span></div>';
						} else {
								$this->set_user_has_voted();
								if ($this->user_already_voted) {
										$output .= '<button type="submit" class="imc-button imc-button-primary-disabled imc-button-block" disabled>';
										$output .= '<i class="material-icons md-18 imc-VerticalAlignMiddle">' .$this->icon. '</i>';
										$output .= '<span	class="imc-Text-MD imc-TextRegular imc-FontRoboto">&nbsp; ' . __('Voted', 'pb-voting'). '</span></button>';
								} else {
										$output .= '<button type="submit" class="u-full-width imc-button imc-button-primary imc-button-block">';
										$output .= '<i class="material-icons md-18 imc-VerticalAlignMiddle">' .$this->icon. '</i>';
										$output .= '<span	class="imc-Text-MD imc-TextRegular imc-FontRoboto">&nbsp;' . __('Vote', 'pb-voting') . '</span></button>';
								}
						}
						$output .= '</form>';
				}
				return $output;
		}
}
