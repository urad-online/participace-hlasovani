<?php

function pbvote_register_post_type()
{
    $labels = array(
        'name'               => _x( 'Participace hlasování', 'post type general name' ),
        'singular_name'      => _x( 'Participace hlasování', 'post type singular name' ),
        'add_new'            => _x( 'Přidat', 'book' ),
        'add_new_item'       => __( 'Přidat hlasování' ),
        'edit_item'          => __( 'Upravit  hlasování' ),
        'new_item'           => __( 'Nové hlasování' ),
        'all_items'          => __( 'Všechna hlasování' ),
        'view_item'          => __( 'Přehled hlasování' ),
        'search_items'       => __( 'Hledat hlasování' ),
        'not_found'          => __( 'Žádné hlasování nalezeno' ),
        'not_found_in_trash' => __( 'Žádné hlasování nalezeno v koši' ),
        'parent_item_colon'  => '',
        'menu_name'          => 'PB Hlasování'
      );
    $args = array(
        'labels'        => $labels,
        'description'   => 'Správa hlasování o participativních projektech',
        'public'        => true,
        'menu_position' => 5,
        // 'supports'      => array( 'title', 'thumbnail', 'excerpt',  ),
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive'   => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'show_in_rest'  => false,
        'menu_icon'     => 'dashicons-admin-post',
        // 'register_meta_box_cb' => 'pbvoting_metabox',
        'taxonomies'    => array('voting_status',),
    );

    $type = PB_VOTING_POST_TYPE ;
    register_post_type( $type, $args );

    $taxo_args = array(
        'labels'        => array(
        'name'          => 'Stav hlasování',),
        'description'   => 'Správa hlasování o participativních projektech',
        'public'        => false,
        'hierarchical'  => false,
        'menu_position' => 5,
        // 'supports'      => array( 'title', 'thumbnail', 'excerpt',  ),
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments' ),
        'has_archive'   => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'show_ui'       => true,
        'show_in_menu'  => true,
        'show_in_nav_menus' => true,
        'show_in_admin_bar' => true,
        'show_admin_column' => true,
        'show_in_rest'  => false,
        'menu_icon'     => 'dashicons-admin-post',

    );
    register_taxonomy( 'voting_status', PB_VOTING_POST_TYPE, $taxo_args );
}

class VotingInfoMetabox {
    private $meta_fields;

    public function __construct() {
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'save_fields' ) );
        $this->set_meta_fields();
   }
   private function set_meta_fields()
   {
       // $pom = new PbVote_RenderForm();
       $this->meta_fields = array(
           'voting_date_from' => array(
               'label'     => 'Datum začátku hlasování',
               'id'        => 'voting_date_from',
               'type'      => 'date',
               'mandatory' => true,
               'width'     => '25%',
           ),
           'voting_date_to' => array(
               'label'     => 'Datum konce hlasování',
               'id'        => 'voting_date_to',
               'type'      => 'date',
               'mandatory' => true,
               'width'     => '25%',
           ),
           'pos_votes_min' => array(
               'label'     => 'Min počet kladných hlasů',
               'id'        => 'votes_pos_min',
               'type'      => 'number',
               'mandatory' => true,
               'default'   => 0,
               'width'     => '10%',
           ),
           'pos_votes_max' => array(
               'label'     => 'Max počet kladných hlasů',
               'id'        => 'votes_pos_max',
               'type'      => 'number',
               'mandatory' => true,
               'default'   => 3,
               'width'     => '10%',
           ),
           'votes_neg_min' => array(
               'label'     => 'Min počet záporných hlasů',
               'id'        => 'votes_neg_min',
               'type'      => 'number',
               'mandatory' => true,
               'default'   => 0,
               'width'     => '10%',
           ),
           'votes_neg_max' => array(
               'label'     => 'Max počet záporných hlasů',
               'id'        => 'votes_neg_max',
               'type'      => 'number',
               'mandatory' => true,
               'default'   => 3,
               'width'     => '10%',
           ),
           'reg_code_expiration' => array(
               'label'     => 'Počet hodin expirace registračního kódu',
               'id'        => 'reg_code_expiration',
               'type'      => 'number',
               'mandatory' => true,
               'default'   => 24,
               'width'     => '10%',
           ),
       );
   }
   public function add_meta_boxes() {
       add_meta_box(
           'pbvoting_metabox',
           __( 'PB Hlasování', 'pbvote_domain' ),
           array( $this, 'pbvoting_metabox_content'),
           PB_VOTING_POST_TYPE,
           'advanced',
           'high'
       );
   }
   public function pbvoting_metabox_content( $post, $args = null ) {
       wp_nonce_field( 'mtbx_hlasovani_data', 'mtbx_hlasovani_nonce' );
       $this->field_generator( $post );
   }

   public function field_generator( $post ) {
       $output = '';
       foreach ( $this->meta_fields as $meta_field ) {
           $label = '<label for="' . $meta_field['id'] . '">' . $meta_field['label'] . '</label>';
           $meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
           if ( empty( $meta_value )  && ! empty( $meta_field[ 'default'] )) {
               $meta_value = $meta_field['default']; }

            if (! empty( $meta_field['width'])) {
                $field_width = $meta_field['width'];
            } else {
                $field_width = '100%';
            }
           switch ( $meta_field['type'] ) {
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
                       '<textarea style="width: s%;" id="%s" name="%s" rows="5">%s</textarea>',
                       $field_width,
                       $meta_field['id'],
                       $meta_field['id'],
                       $meta_value
                   );
                   break;
               case 'date':
                   $input = sprintf(
                       '<input style="width: %s;" id="%s" name="%s" type="%s" value="%s">',
                       $field_width,
                       $meta_field['id'],
                       $meta_field['id'],
                       $meta_field['type'],
                       $meta_value
                   );
                   break;
               default:
                   $input = sprintf(
                       '<input style="width: %s;" id="%s" name="%s" type="%s" value="%s">',
                       $field_width,
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
       if ( ! isset( $_POST['mtbx_hlasovani_nonce'] ) )
           return $post_id;
       $nonce = $_POST['mtbx_hlasovani_nonce'];
       if ( !wp_verify_nonce( $nonce, 'mtbx_hlasovani_data' ) )
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
