<?php
class PbVote_SaveDataAttachment {
    private $post_id = null;
    private $ids     = array();
    private $prefix_new = "new";

    public function __construct( $post_id = 0, $attachment_list)
    {
        $this->post_id = $post_id;
        if (is_array( $attachment_list)) {
          $this->ids = $attachment_list;
        }
    }

    public function update_attachments()
    {
        if (! $this->post_id) {
            return false;
        }
        $list_new = array();
        foreach ($this->ids as $value) {
            $item = array(
              "id"    => $value,
              "title" => esc_attr(sanitize_text_field( $_POST['attach_table_title_input_'.$value])),
            );
            if (! empty( $_FILES['attach_table_file_input_'.$value])) {
              $item['file'] = 'attach_table_file_input_'.$value ;
            } else {
              $item['file'] = '';
            }
            array_push( $list_new, $item );
        }

        $list_old = get_post_meta($this->post_id,'pb_project_attachment', true );
        if (empty($list_old)) {
            $list_old = array();
        }

        $list_final = array();
        foreach ($list_new as $item ) {
            $item_id = intval( $item['id']);
            if( ($item_id) && (! (array_search ( $item_id , $list_old) === false))) {
                $this->update_attachment_title( $item);
            } else {
                if (! empty($item['file'])) {
                    $item_id = $this->insert_attachment_1_new( $_FILES[ $item['file'] ], $item);
                }
            }
            array_push( $list_final, $item_id );
        }
        return $list_final;
    }

    private function insert_attachment_1_new( $file, $meta_values )
    {
        if (( $file['error'] == '0') && (! empty($meta_values['title']))  &&
                ( $this->check_file_type($file['name'],'pb_project_attachment')) ) {
            $attachment_id = pbvote_upload_img( $file, $this->post_id, $meta_values['title'], null);
            if ( $attachment_id) {
              return $attachment_id;
            } else {
              return "";
            }
        }
    }
    private function update_attachment_title( $item)
    {
      $old_title = get_the_title( $item['id']);
      if ($old_title != $item['title']) {
          $post_data = array(
                'ID' => $item['id'],
                'post_title' => esc_attr(strip_tags($item['title'])),
          );
          $post_id = wp_update_post( $post_data, true );
      }
    }
    /*
    * check allowed file types
    */
    private function check_file_type( $file, $attach_type)
    {
        switch ($attach_type) {
            case 'featured_image':
            $allowed_file_type = PbVote_RenderForm::get_file_type_image();
            break;

            case 'pb_project_mapa':;
            case 'pb_project_podporovatele':
            case 'pb_project_attachment':
            $allowed_file_type = PbVote_RenderForm::get_file_type_image().PbVote_RenderForm::get_file_type_scan();
            break;

            default:
            $allowed_file_type = PbVote_RenderForm::get_file_type_image().PbVote_RenderForm::get_file_type_scan().PbVote_RenderForm::get_file_type_docs();
            break;
        }
        $type = wp_check_filetype(basename($file)) ;
        return  strpos( $allowed_file_type, $type['ext']);
    }

}
