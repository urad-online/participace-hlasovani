<?php
class PbVote_ProjectSaveData {
    private $post_id         = null;
    private $post_data       = null;
    private $status_taxo     = 'imcstatus';

    /*
    * Create new project
    */
    public function project_insert( $voting_id )
    {

        $parent_voting_period = get_parent_taxo_slug($voting_id);

        if (! empty( $_POST['my_custom_taxonomy']) ) {
          $imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));
          $tax_input['imccategory'] = $imccategory_id;
        } else {
          $all_categories_terms = get_terms( 'imccategory' , array( 'hide_empty' => 0 , 'orderby' => 'id', 'order' => 'ASC') );
          $imccategory_id = $all_categories_terms[0]->term_id;
          $tax_input['imccategory'] = $imccategory_id;
        }

    	// Check options if the status of new issue is pending or publish
        global $generaloptions;
        $moderateOption = $generaloptions["moderate_new"];

    	//CREATE THE ISSUE TO DB

      	$this->post_data = array(
      		'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
      		'post_content' => esc_attr(strip_tags($_POST['postContent'])),
      		'post_type'   => 'imc_issues',
      		// 'post_status' => ($moderateOption == 2) ? 'publish' : 'pending',
      		'post_status' => 'pending',
      		'post_name'   => sanitize_title( $_POST['postTitle']),
      		'tax_input'   => $tax_input,
      	);

        $this->get_metadata_from_request( $_POST, false);

        // List of attachment is wrong. All metadata are saved within insert
        $attach_temp     = $this->post_data['meta_input']['pb_project_attachment'];
        $attach_temp_sec = $this->post_data['meta_input']['pb_project_attachment_sec'];
        $this->post_data['meta_input']['pb_project_attachment'] = array();
        $this->post_data['meta_input']['pb_project_attachment_sec'] = array();
      	$this->post_id = wp_insert_post( $this->post_data, true);

      	if ( $this->post_id && ( ! is_wp_error($this->post_id)) ) {
            // create link to parent "hlasovani"
            if (!empty($parent_voting_period)) {
              wp_set_object_terms($this->post_id, $parent_voting_period, 'voting-period');
            }

            $save_attach = new PbVote_SaveDataAttachment( $this->post_id, $attach_temp, 'pb_project_attachment' );
            $this->post_data['meta_input']['pb_project_attachment'] = $save_attach->update_attachments();
            $save_attach_sec = new PbVote_SaveDataAttachment( $this->post_id, $attach_temp_sec, 'pb_project_attachment_sec');
            $this->post_data['meta_input']['pb_project_attachment_sec'] = $save_attach_sec->update_attachments();

            // list of attachments is updated after file insert
            update_post_meta($this->post_id, 'pb_project_attachment', $this->post_data['meta_input']['pb_project_attachment']);
            update_post_meta($this->post_id, 'pb_project_attachment_sec', $this->post_data['meta_input']['pb_project_attachment_sec']);
            $this->add_link_to_voting( $voting_id );
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
        }

      return $this->post_id;
    }

    /*
    * Update existing project
    */
    public function update_project()
    {
      	$this->post_id = intval( sanitize_text_field( $_GET['edit_id'] ));
      	$issue_id = $this->post_id ;

      	$lat = esc_attr(strip_tags($_POST['imcLatValue']));
      	$lng = esc_attr(strip_tags($_POST['imcLngValue']));

        if (! empty( $_POST['my_custom_taxonomy']) ) {
          $imccategory_id = esc_attr(strip_tags($_POST['my_custom_taxonomy']));
          $tax_input['imccategory'] = $imccategory_id;
        } else {
          $imccategory_id = "";
          $tax_input = array();
        }
      	$address = esc_attr(strip_tags($_POST['postAddress']));

      	//UPDATE THE ISSUE TO DB
      	$this->post_data = array(
      		'ID' => $issue_id,
              'post_title' => esc_attr(strip_tags($_POST['postTitle'])),
              'post_content' => esc_attr(strip_tags($_POST['postContent'])),
              'tax_input' => $tax_input,
        );


      	$post_id = wp_update_post( $this->post_data, true );

      	if (is_wp_error($post_id)) {
              return $post_id;
      	}

        $this->get_metadata_from_request( $_POST, true);

        $save_attach = new PbVote_SaveDataAttachment( $this->post_id, $this->post_data['meta_input']['pb_project_attachment'], 'pb_project_attachment' );
        $this->post_data['meta_input']['pb_project_attachment'] = $save_attach->update_attachments();
        $save_attach_sec = new PbVote_SaveDataAttachment( $this->post_id, $this->post_data['meta_input']['pb_project_attachment_sec'], 'pb_project_attachment_sec');
        $this->post_data['meta_input']['pb_project_attachment_sec'] = $save_attach_sec->update_attachments();

      	$this->update_postmeta();
        $this->project_update_image();

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
            'pb_project_reason'              => esc_attr(sanitize_textarea_field($data['pb_project_reason'])),
            'pb_project_curr_state'          => esc_attr(sanitize_textarea_field($data['pb_project_curr_state'])),
            'pb_project_future_state'        => esc_attr(sanitize_textarea_field($data['pb_project_future_state'])),
            // 'pb_project_locality'            => json_decode(stripslashes($data['pb_project_locality'])),
            'pb_project_parcely'             => esc_attr(sanitize_textarea_field($data['pb_project_parcely'])),
            'pb_project_naklady'             => json_decode(stripslashes($data['pb_project_naklady'])),
            'pb_project_naklady_navyseni'    => (! empty($data['pb_project_naklady_navyseni'])) ? esc_attr(sanitize_textarea_field($data['pb_project_naklady_navyseni'])) : '0',
            // 'pb_project_navrhovatel_org'     => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_org'])),
            'pb_project_navrhovatel_jmeno'   => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_jmeno'])),
            'pb_project_navrhovatel_telefon' => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_telefon'])),
            'pb_project_navrhovatel_email'   => esc_attr(sanitize_email($data['pb_project_navrhovatel_email'])),
            'pb_project_navrhovatel_adresa'  => esc_attr(sanitize_text_field($data['pb_project_navrhovatel_adresa'])),
            'pb_project_prohlaseni_veku'     => (! empty($data['pb_project_prohlaseni_veku'])) ? esc_attr(sanitize_text_field($data['pb_project_prohlaseni_veku'])) : '0',
            'pb_project_podminky_souhlas'    => (! empty($data['pb_project_podminky_souhlas'])) ? esc_attr(sanitize_text_field($data['pb_project_podminky_souhlas'])) : '0',
            'pb_project_private_note'        => esc_attr(sanitize_textarea_field($data['pb_project_private_note'])),
            'pb_project_attachment'          => json_decode(stripslashes($data['pb_project_attachment'])),
            'pb_project_attachment_sec'      => json_decode(stripslashes($data['pb_project_attachment_sec'])),
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
    		$attachment_id = imc_upload_img( $image, $this->post_id, $this->post_data['post_title'], $orientation);
    	} else {
    		$attachment_id = imc_upload_img( $image, $this->post_id, $this->post_data['post_title'], null);
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
    private function add_link_to_voting( $parent_id )
    {
        $items =  get_post_meta( $parent_id, "_pods_items", true);
        array_push( $items, $this->post_id );
        update_post_meta( $parent_id, "_pods_items", $items);
        add_post_meta( $parent_id, "items", $this->post_id, false);
    }
}
