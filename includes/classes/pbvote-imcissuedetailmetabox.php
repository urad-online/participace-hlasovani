<?php
/**
 * 20.01
 * Add additional fields to imc_issues
 *
 */
class PbVote_ImcIssueDetailMetabox {
  private $screen = array( 'imc_issues', );
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
   add_action( 'save_post', array( $this, 'save_fields' ), 1 );
       $this->set_meta_fields();
  }

  public function add_meta_boxes() {
    foreach ( $this->screen as $single_screen ) {
      add_meta_box(
         'informacekprojektu',
         __( 'Informace k projektu', 'pb-voting' ),
         array( $this, 'meta_box_callback' ),
         $single_screen,
         'normal',
         'default'
      );
    }
  }

  public function meta_box_callback( $post ) {
    wp_nonce_field( 'informacekprojektu_data', 'informacekprojektu_nonce' );
    echo __( 'Detaily projektu', 'pb-voting' );
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
           $link = $this->pb_render_file_link_metabox($meta_value, $meta_field['id']);
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

  public function save_fields( $post_id )
  {
    if ( ! isset( $_POST['informacekprojektu_nonce'] ) ) {
      return $post_id;
    }
    $nonce = $_POST['informacekprojektu_nonce'];
    if ( !wp_verify_nonce( $nonce, 'informacekprojektu_data' ) ) {
      return $post_id;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
      return $post_id;
    }

    foreach ( $this->meta_fields as $meta_field ) {
      $old = get_post_meta($post_id, $meta_field['id'], true);
      $new = $_POST[$field['id']];
      if ( $new && $new != $old ) {
        switch ( $meta_field['type'] ) {
          case 'email':
          $_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
          break;
          case 'text':
          $_POST[ $meta_field['id'] ] = sanitize_text_field( $_POST[ $meta_field['id'] ] );
          break;
        }
        update_post_meta( $post_id, $meta_field['id'], $new );
      } elseif ( $meta_field['type'] === 'checkbox' ) {
        update_post_meta( $post_id, $meta_field['id'], '0' );
      } elseif ('' == $new && $old) {
        delete_post_meta( $post_id, $meta_field['id'], $old );
      }
    }
  }
  public function get_fields()
  {
    return $this->meta_fields;
  }

  private function pb_render_file_link_metabox($url, $id)
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

}
