<?php
<<<<<<< HEAD
class PbVote_ProjectSaveData {
=======
/**
 * PB 1.00
 * Renders part of the form with PB Project additional fields
 * Used both by insert and edit page
 * class pbProjectEdit renders form
 * class pbProjectSaveData saves data
 *
 */
/*
* Class pbProjectSaveData - save data for insert and update
*/
class PbVote_ProjectSaveData {
    private $file_type_image = "gif, png, jpg, jpeg";
    private $file_type_scan  = "pdf" ;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    private $post_id         = null;
    private $post_data       = null;
    private $status_taxo     = 'imcstatus';

    /*
    * Create new project
    */
<<<<<<< HEAD
    public function project_insert( $voting_id )
    {
        $imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));
        $voting_period_slug = get_parent_taxo_slug($voting_id);
=======
    public function project_insert( $voting_id = 0)
    {
        $imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));

>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    	// Check options if the status of new issue is pending or publish

      	$generaloptions = get_option( 'general_settings' );
      	$moderateOption = $generaloptions["moderate_new"];

<<<<<<< HEAD
    	//CREATE THE ISSUE TO DB
=======
      	//CREATE THE ISSUE TO DB
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090

      	$this->post_data = array(
      		'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
      		'post_content' => esc_attr(strip_tags($_POST['postContent'])),
      		'post_type' => 'imc_issues',
      		'post_status' => ($moderateOption == 2) ? 'publish' : 'pending',
      		'post_name'   => sanitize_title( $_POST['postTitle']),
<<<<<<< HEAD
      		'tax_input' => array( 'imccategory' => $imccategory_id,
                                'voting-period' => $voting_period_slug),
=======
      		'tax_input' => array( 'imccategory' => $imccategory_id ),
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
      	);

        $this->get_metadata_from_request( $_POST, false);

      	$this->post_id = wp_insert_post( $this->post_data, true);
<<<<<<< HEAD

      	if ( $this->post_id && ( ! is_wp_error($this->post_id)) ) {
      		$this->insert_attachments( $_FILES );
          $this->add_link_to_voting( $voting_id );
      	}

    	// Choose the imcstatus with smaller id
    	// zmenit order by imc_term_order

    	$pb_edit_completed = (! empty( $_POST['pb_project_edit_completed']) ) ?  $_POST['pb_project_edit_completed'] : 0;
    	$all_status_terms = get_terms( $this->status_taxo , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
    	if ( $pb_edit_completed ) {
    		$first_status = $all_status_terms[1];
    	} else {
    		$first_status = $all_status_terms[0];
    	}

    	wp_set_object_terms($this->post_id, $first_status->name, $this->status_taxo);

    	//Create Log if moderate is OFF

    	if($moderateOption == 2) {

    		imcplus_crelog_frontend_nomoder($this->post_id, $first_status->term_id, get_current_user_id());

    	}

    	$this->project_insert_image();

      imcplus_mailnotify_4submit($this->post_id,$imccategory_id, $this->post_data['meta_input']['imc_address']);

      return $this->post_id;
=======
        // $this->post_id = null;

      	if ( $this->post_id && ( ! is_wp_error($this->post_id)) ) {
      		$this->insert_attachments( $_FILES );
          $this->add_issue_to_voting_collection($voting_id, $this->post_id);
      	}

      	// Choose the imcstatus with smaller id
      	// zmenit order by imc_term_order

      	$pb_edit_completed = (! empty( $_POST['pb_project_edit_completed']) ) ?  $_POST['pb_project_edit_completed'] : 0;
      	$all_status_terms = get_terms( $this->status_taxo , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
      	if ( $pb_edit_completed ) {
      		$first_status = $all_status_terms[1];
      	} else {
      		$first_status = $all_status_terms[0];
      	}

      	wp_set_object_terms($this->post_id, $first_status->name, $this->status_taxo);

      	//Create Log if moderate is OFF

      	if($moderateOption == 2) {

      		imcplus_crelog_frontend_nomoder($this->post_id, $first_status->term_id, get_current_user_id());

      	}

      	$this->project_insert_image();

        imcplus_mailnotify_4submit($this->post_id,$imccategory_id, $this->post_data['meta_input']['imc_address']);

        return $this->post_id;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    }

    /*
    * Update existing project
    */
    public function update_project()
    {
<<<<<<< HEAD
    	$this->post_id = intval( sanitize_text_field( $_GET['edit_id'] ));
=======
    	$this->post_id = intval( sanitize_text_field( $_GET['issueid'] ));
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    	$issue_id = $this->post_id ;

    	$lat = esc_attr(strip_tags($_POST['imcLatValue']));
    	$lng = esc_attr(strip_tags($_POST['imcLngValue']));


    	$imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));
    	$address = esc_attr(strip_tags($_POST['postAddress']));

    	//UPDATE THE ISSUE TO DB
    	$this->post_data = array(
    		'ID' => $issue_id,
            'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
            'post_content' => esc_attr(strip_tags($_POST['postContent'])),
            'tax_input' => array( 'imccategory' => $imccategory_id ),
        );

    	$post_id = wp_update_post( $this->post_data, true );

    	if (is_wp_error($post_id)) {
            return $post_id;
    	}
<<<<<<< HEAD
        $this->get_metadata_from_request( $_POST, true);

      	$this->update_postmeta();
        $this->project_update_image();
      	$this->update_attachments( $_FILES );

=======
      $this->get_metadata_from_request( $_POST, true);

    	$this->update_postmeta();

        $this->project_update_image();

    	$this->update_attachments( $_FILES );
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
        $this->update_project_status();

        return $this->post_id;
    }

    /*
    * read post_metadata from $_POST
    */
    public function get_metadata_from_request( $data, $update = false )
    {
        $this->post_data['meta_input'] = array(
            'imc_lat'		=> esc_attr(sanitize_text_field($data['imcLatValue'])),
            'imc_lng'		=> esc_attr(sanitize_text_field($data['imcLngValue'])),
            'imc_address'	=> esc_attr(sanitize_text_field($data['postAddress'])),
            'pb_project_navrhovatel_jmeno'   => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_jmeno'])),
            'pb_project_navrhovatel_adresa'  => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_adresa'])),
            'pb_project_navrhovatel_telefon' => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_telefon'])),
            'pb_project_parcely'             => esc_attr(sanitize_textarea_field($data['pb_project_parcely'])),
            'pb_project_prohlaseni_veku'     => (! empty($data['pb_project_prohlaseni_veku'])) ? esc_attr(sanitize_text_field($data['pb_project_prohlaseni_veku'])) : '0',
            'pb_project_podminky_souhlas'    => (! empty($data['pb_project_podminky_souhlas'])) ? esc_attr(sanitize_text_field($data['pb_project_podminky_souhlas'])) : '0',
            'pb_project_navrhovatel_email'   => esc_attr(sanitize_email($data['pb_project_navrhovatel_email'])),
            'pb_project_cile'                => esc_attr(sanitize_textarea_field($data['pb_project_cile'])),
            'pb_project_akce'                => esc_attr(sanitize_textarea_field($data['pb_project_akce'])),
            'pb_project_prospech'            => esc_attr(sanitize_textarea_field($data['pb_project_prospech'])),
            'pb_project_naklady_celkem'      => esc_attr(sanitize_textarea_field($data['pb_project_naklady_celkem'])),
            'pb_project_naklady_navyseni'    => (! empty($data['pb_project_naklady_navyseni'])) ? esc_attr(sanitize_textarea_field($data['pb_project_naklady_navyseni'])) : '0',
        );
        if ( ! $update ) {
            $this->post_data['meta_input']['imc_likes'] = '0';
            $this->post_data['meta_input']['modality'] = '0';
        }
    }

    /*
    * save featured_image for new project
    */
    private function project_insert_image()
    {

    	$image =  $_FILES['featured_image'];

    	$orientation = intval(strip_tags($_POST['imcPhotoOri']), 10);

    	if ($orientation !== 0) {
<<<<<<< HEAD
    		$attachment_id = imc_upload_img( $image, $this->post_id, $this->post_data['post_title'], $orientation);
    	} else {
    		$attachment_id = imc_upload_img( $image, $this->post_id, $this->post_data['post_title'], null);
=======
    		$attachment_id = pbvote_upload_img( $image, $this->post_id, $this->post_data['post_title'], $orientation);
    	} else {
    		$attachment_id = pbvote_upload_img( $image, $this->post_id, $this->post_data['post_title'], null);
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    	}

    	set_post_thumbnail( $this->post_id, $attachment_id );

    }

    /*
    * save featured_image for updated project
    */
    private function project_update_image()
    {

        $imageScenario = intval(strip_tags($_POST['imcImgScenario']), 10);

        if ( $imageScenario === 1) {
            delete_post_thumbnail( $this->post_id );
        }

        if ( $imageScenario === 2) {
            $this->project_insert_image();
        }
    }

    /*
    * save all file attachment for new project
    */
    private function insert_attachments( $files)
    {
        if (! empty( $this->post_id)) {
            // $_FILE['id'], fields - error, name, size, tmp_name, type, pro prazdne je error = 4 ostatni prazdne,
            $this->insert_attachment_1($files['pb_project_podporovatele'],  'pb_project_podporovatele');
            $this->insert_attachment_1($files['pb_project_mapa'],           'pb_project_mapa');
            $this->insert_attachment_1($files['pb_project_naklady'],        'pb_project_naklady');
            $this->insert_attachment_1($files['pb_project_dokumentace1'],   'pb_project_dokumentace1');
            $this->insert_attachment_1($files['pb_project_dokumentace2'],   'pb_project_dokumentace2');
            $this->insert_attachment_1($files['pb_project_dokumentace3'],   'pb_project_dokumentace3');
        }
    }

    private function insert_attachment_1 ($file, $attachment_type = '' )
    {
        if (( $file['error'] == '0') && (! empty($attachment_type)) &&
                ( $this->check_file_type($file['name'],$attachment_type))) {
            $attachment_id = pbvote_upload_img( $file, $this->post_id, $this->post_id . '-' . $attachment_type, null);
            if ( $attachment_id) {
              $url = wp_get_attachment_url( $attachment_id);
              update_post_meta( $this->post_id, $attachment_type, $url);
            }
            return $attachment_id;
        } elseif ($this->post_id) {
            delete_post_meta( $this->post_id, $attachment_type );
            return true;
        } else {
            return false;
        }
    }

    /*
    * save all file attachment updated project
    */
    private function update_attachments( $files )
    {
        if (! $this->post_id) {
            return false;
        }

        $list = array(
            'pb_project_podporovatele',
            'pb_project_mapa',
            'pb_project_naklady',
            'pb_project_dokumentace1',
            'pb_project_dokumentace2',
            'pb_project_dokumentace3',
        );
        foreach ($list as $key) {
            $this->update_attachment_1($files[ $key ], $key, $_POST[ $key.'Name']);
        }
    }

    private function update_attachment_1( $file, $attachment_type, $meta_value )
    {
        if (( $file['error'] == '0') && (! empty($attachment_type))  &&
                ( $this->check_file_type($file['name'],$attachment_type)) ) {
<<<<<<< HEAD
            $attachment_id = imc_upload_img( $file, $this->post_id, $this->post_id . '-' . $attachment_type, null);
=======
            $attachment_id = pbvote_upload_img( $file, $this->post_id, $this->post_id . '-' . $attachment_type, null);
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
            if ( $attachment_id) {
                $url = wp_get_attachment_url( $attachment_id);
                update_post_meta( $this->post_id, $attachment_type, $url);
            }
            return $attachment_id;
        } elseif ( empty( $meta_value) ) {
            delete_post_meta( $this->post_id, $attachment_type );
            return true;
        } else {
            return false;
        }
    }

    /*
    * check allowed file types
    */
    private function check_file_type( $file, $attach_type)
    {
        switch ($attach_type) {
            case 'featured_image':
<<<<<<< HEAD
            $allowed_file_type = PbVote_RenderForm::get_file_type_image();
=======
            $allowed_file_type = FILE_TYPES_IMAGE;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
            break;

            case 'pb_project_mapa':;
            case 'pb_project_podporovatele':
<<<<<<< HEAD
            $allowed_file_type = PbVote_RenderForm::get_file_type_image().PbVote_RenderForm::get_file_type_scan();
            break;

            default:
            $allowed_file_type = PbVote_RenderForm::get_file_type_image().PbVote_RenderForm::get_file_type_scan().PbVote_RenderForm::get_file_type_docs();
=======
            $allowed_file_type = FILE_TYPES_IMAGE.FILE_TYPES_SCAN;
            break;

            default:
            $allowed_file_type = FILE_TYPES_IMAGE.FILE_TYPES_SCAN.FILE_TYPES_DOCS;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
            break;
        }
        $type = wp_check_filetype(basename($file)) ;
        return  strpos( $allowed_file_type, $type['ext']);
    }

    /*
    * update post_metadata for updated project
    */
    private function update_postmeta()
    {
        foreach ($this->post_data['meta_input'] as $key => $value) {
            update_post_meta($this->post_id, $key, $value);
        }
    }

    /*
    * update terms imcstatus
    */
    private function update_project_status()
    {
        /********************** About changing Project status  ************************/
        $pb_edit_completed = (! empty( $_POST['pb_project_edit_completed']) ) ?  $_POST['pb_project_edit_completed'] : 0;
        $all_status_terms = get_terms( $this->status_taxo , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
        if ( $pb_edit_completed ) {
            $set_status = $all_status_terms[1];
        } else {
            $set_status = $all_status_terms[0];
        }
        $pb_project_status = wp_get_object_terms( $this->post_id, $this->status_taxo);

        if ( $set_status->slug != $pb_project_status[0]->slug) {
            wp_delete_object_term_relationships( $this->post_id, $this->status_taxo );
            wp_set_object_terms( $this->post_id, array($set_status->term_id,), $this->status_taxo, false);
            $this->change_project_status_log( $set_status, $this->post_id, 'Změna stavu navrhovatelem' );
        }

    }

    public function change_project_status_log( $new_step_term, $post_id, $description = 'Změna stavu')
    {
        global $wpdb;

        $current_step_name = $new_step_term->name;
        $transition = __( 'Status changed: ', 'pb-voting' ) . $new_step_term->name;
        $tagid = intval($new_step_term->term_id, 10);
        $theColor = 'tax_imcstatus_color_' . $tagid;
        $term_data = get_option($theColor);
        $currentStatusColor = $term_data;
        $timeline_label = $new_step_term->name;
        $theUser =  get_current_user_id();
        $currentlang = get_bloginfo('language');

        $imc_logs_table_name = $wpdb->prefix . 'imc_logs';

        $wpdb->insert(
            $imc_logs_table_name,
            array(
                'issueid' => $post_id,
                'stepid' => $tagid,
                'transition_title' => $transition,
                'timeline_title' => $timeline_label,
                'theColor' => $currentStatusColor,
                'description' => $description,
                'action' => 'step',
                'state' => 1,
                'created' => gmdate("Y-m-d H:i:s",time()),
                'created_by' => $theUser,
                'language' => $currentlang,
            )
        );

        //fires mail notification
        imcplus_mailnotify_4imcstatuschange($transition, $post_id, $theUser);
    }
<<<<<<< HEAD
    private function add_link_to_voting( $parent_id )
    {
        $items =  get_post_meta( $parent_id, "_pods_items", true);
        $new_items =  array_push( $items, $this->post_id );
        update_post_meta( $parent_id, "_pods_items", $new_items);
    }
}
=======

    private function add_issue_to_voting_collection($voting_id = 0, $post_id = 0)
    {
        $all_meta = get_post_meta($voting_id);
        if ($voting_id && $post_id) {
          $result = add_post_meta( $voting_id, 'items', $post_id);
        }
    }

}

 ?>
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
