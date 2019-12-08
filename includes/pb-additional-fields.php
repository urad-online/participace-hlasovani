<?php
/**
 * 20.01
 * Add additional fields to imc_issues
 *
 */
// $voting_enabled = $comments_enabled = false;
$generaloptions     = get_option( 'general_settings' );
$voting_enabled     = ( empty($generaloptions["imc_ratings"]))  ? false : $generaloptions["imc_ratings"];
$comments_enabled   = ( empty($generaloptions["imc_comments"])) ? false : $generaloptions["imc_comments"];

 class informacekprojektuMetabox {
 	private $screen = array(
 		'imc_issues',
 	);
    private $cond_url ;
 	private $meta_fields;
 	private function set_meta_fields()
    {
        $pom = new PbVote_RenderForm();
        $this->meta_fields = $pom->get_form_fields_mtbx();
        unset( $pom);
    }
 	public function __construct() {
 		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
 		add_action( 'admin_footer', array( $this, 'media_fields' ) );
 		add_action( 'save_post', array( $this, 'save_fields' ) );
        $this->set_meta_fields();
 	}
 	public function add_meta_boxes() {
 		foreach ( $this->screen as $single_screen ) {
 			add_meta_box(
 				'informacekprojektu',
 				__( 'Information to project', 'pb-voting' ),
 				array( $this, 'meta_box_callback' ),
 				$single_screen,
 				'normal',
 				'default'
 			);
 		}
 	}
 	public function meta_box_callback( $post ) {
 		wp_nonce_field( 'informacekprojektu_data', 'informacekprojektu_nonce' );
 		echo 'Detaily projektu';
 		$this->field_generator( $post );
 	}
 	public function media_fields() {
 		?><script>
 			jQuery(document).ready(function($){
 				if ( typeof wp.media !== 'undefined' ) {
 					var _custom_media = true,
 					_orig_send_attachment = wp.media.editor.send.attachment;
 					$('.informacekprojektu-media').click(function(e) {
 						var send_attachment_bkp = wp.media.editor.send.attachment;
 						var button = $(this);
 						var id = button.attr('id').replace('_button', '');
 						_custom_media = true;
 							wp.media.editor.send.attachment = function(props, attachment){
 							if ( _custom_media ) {
 								$('input#'+id).val(attachment.url);
 							} else {
 								return _orig_send_attachment.apply( this, [props, attachment] );
 							};
 						}
 						wp.media.editor.open(button);
 						return false;
 					});
 					$('.add_media').on('click', function(){
 						_custom_media = false;
 					});
 				}
 			});
 		</script><?php
 	}
 	public function field_generator( $post ) {
 		$output = '';
 		foreach ( $this->meta_fields as $meta_field ) {
 			$label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
 			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
 			if ( empty( $meta_value )  && ! empty( $meta_field[ 'default'] )) {
 				$meta_value = $meta_field['default']; }
 			switch ( $meta_field['type'] ) {
 				case 'media':
                    $link = pb_render_file_link_metabox($meta_value, $meta_field['id']);
 					$input = sprintf(
 						'<input style="width: 70%%;" id="%s" name="%s" type="text"
                            value="%s"><p style="width:2%%; display:inline-block"></p><input
                            style="width: 15%%; padding-left: 10px;display:inline-block;" class="button informacekprojektu-media"
                            id="%s_button" name="%s_button" type="button" value="Upload" /><p style="width:2%%; display:inline-block"></p>'.$link,
 						$meta_field['id'],
 						$meta_field['id'],
 						$meta_value,
 						$meta_field['id'],
 						$meta_field['id']
 					);
 					break;
 				case 'checkbox':
 					$input = sprintf(
 						'<input %s id=" % s" name="% s" type="checkbox" value="1">',
 						$meta_value === '1' ? 'checked' : '',
 						$meta_field['id'],
 						$meta_field['id']
 						);
 					break;
 				case 'textarea':
 					$input = sprintf(
 						'<textarea style="width: 100%%" id="%s" name="%s" rows="5">%s</textarea>',
 						$meta_field['id'],
 						$meta_field['id'],
 						$meta_value
 					);
 					break;
 				default:
 					$input = sprintf(
 						'<input %s id="%s" name="%s" type="%s" value="%s">',
 						$meta_field['type'] !== 'color' ? 'style="width: 100%"' : '',
 						$meta_field['id'],
 						$meta_field['id'],
 						$meta_field['type'],
 						$meta_value
 					);
 			}
 			$output .= $this->format_rows( $label, $input );
 		}
 		echo '<table class="form-table"><tbody>' . $output . '</tbody></table>';
 	}
 	public function format_rows( $label, $input ) {
 		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';
 	}
 	public function save_fields( $post_id ) {
 		if ( ! isset( $_POST['informacekprojektu_nonce'] ) )
 			return $post_id;
 		$nonce = $_POST['informacekprojektu_nonce'];
 		if ( !wp_verify_nonce( $nonce, 'informacekprojektu_data' ) )
 			return $post_id;
 		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
 			return $post_id;
 		foreach ( $this->meta_fields as $meta_field ) {
 			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
 				switch ( $meta_field['type'] ) {
 					case 'email':
 						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
 						break;
 					case 'text':
 						$_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
 						break;
 				}
 				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
 			} else if ( $meta_field['type'] === 'checkbox' ) {
 				update_post_meta( $post_id, $meta_field['id'], '0' );
 			}
 		}
 	}
    public function get_fields()
    {
        return $this->meta_fields;
    }
 }
if (class_exists('informacekprojektuMetabox')) {
 	new informacekprojektuMetabox;
};

function pb_render_file_link_metabox($url, $id)
{
    $display = 'Zobrazit';

    if (! empty($url)) {
        return '<a id="'.$id.'Link" href="'.$url.'" target="_blank" data-toggle="tooltip" title="Zobrazit přílohu" class="u-pull-right
        imc-SingleHeaderLinkStyle" style="width:15%%">'.$display.'</a>';
        // <i class="material-icons md-36 imc-SingleHeaderIconStyle">open_in_browser</i></a>';
    } else {
        return '';
    }
}

/**
 * 6.09
 * Function that checks if user can edit an issue
 * basically checks if current user is issue's author
 * and if status changed
 */

function pb_user_can_edit($post_id, $current_user) {

	$status_terms = get_terms( 'imcstatus' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
    $terms_count = count($status_terms);
    if ( $terms_count > 2 ) {
        $edit_statuses = array($status_terms[0]->term_id, $status_terms[1]->term_id);
    } elseif ($terms_count > 1) {
        $edit_statuses = array($status_terms[0]->term_id, );
    } else {
        $edit_statuses = array();
    }

	// Issue is not current user's
	$my_issue = get_post($post_id);
	$author_id = intval($my_issue ->post_author, 10); // Author's id of current #post

	if($author_id == $current_user) {
		return in_array( getCurrentImcStatusID($post_id), $edit_statuses , true);
	}

	return false;
}
