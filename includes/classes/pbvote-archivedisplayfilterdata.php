<?php
class PbVote_ArchiveDisplayFilterData
{
    public $taxo_status   = "voting_status";
    public $taxo_category = "voting_category";
    public $post_type     = PB_VOTING_POST_TYPE;
    public $query_args, $paged, $user_params, $select_statuses;
    private $ids_in =  array();

    public function __construct( $params)
    {
        $this->paged = 1;
        $this->user_params = $params;

        if ( get_query_var( 'paged' ) ) {$this->paged = get_query_var('paged'); // On a paged page.
        } else if ( get_query_var( 'page' ) ) {$this->paged = get_query_var('page'); // On a "static" page.
        }

        //Basic query calls depending the user
        if ( is_user_logged_in() && current_user_can( 'administrator' ) ){ //not user
            // $custom_query_args = imcLoadIssuesForAdmins($paged,$pbvote_imported_ppage,$pbvote_imported_sstatus,$pbvote_imported_scategory);
            $this->select_statuses = array('publish', 'pending', 'draft');
            // $this->query_args = pbvLoadIssuesForGuests($voting_view_filters->get_filter_params(), $paged);
        } else {
            $this->select_statuses = array('publish');
            // $this->query_args = pbvLoadIssuesForGuests($voting_view_filters->get_filter_params(), $paged);
        }

        $this->query_args = array(
            'post_type' => $this->post_type,
            'post_status' => $this->select_statuses,
            'paged' => $this->paged,
            'posts_per_page' => $this->user_params['ppage'],
        );

        $this->set_query_args_keyword();
        $this->set_query_args_category();
        $this->set_query_args_status();
        $this->set_query_args_order();
        $this->set_query_args_custom();
    }

    public function get_paged()
    {
        return $this->paged;
    }

    public function set_query_args_keyword()
    {
        if(! empty( $this->user_params['keyword'] )) {
            $this->query_args['s'] = $this->user_params['keyword'];
            $custom_query_args['exact'] = false;
        }
    }
    public function set_query_args_status()
    {
        if(! empty( $this->user_params['sstatus'] )) {
            $this->set_query_add_taxo( $this->taxo_status, $this->user_params['sstatus']);
            // $this->get_id_list_by_taxo($this->taxo_status, $this->user_params['sstatus'], "term_taxonomy_id" ); //todelete
        }
    }
    public function set_query_args_category()
    {
        if(! empty( $this->user_params['scategory'] )) {
            $this->set_query_add_taxo( $this->taxo_category, $this->user_params['scategory']);
            // $this->get_id_list_by_taxo($this->taxo_category, $this->user_params['scategory'], "term_taxonomy_id");  //todelete
        }
    }

    private function set_query_add_taxo( $taxonomy, $value, $field = "term_taxonomy_id")
    {
        if ((!empty( $value)) && ( $value !== 'all')) {
            $taxo_values = explode(",", $value);
            if ( empty( $this->query_args['tax_query'])) {
                $this->query_args['tax_query'] = array(
                    'relation' => 'AND',
                );
            }
            $this->query_args['tax_query'] =  array_merge( $this->query_args['tax_query'],
    			array(array(
    				'taxonomy' => $taxonomy,
    				'field' => $field,
    				'terms' => $taxo_values,
                    'operator' => "IN",
    			),)
    		);
        }
    }

    public function set_query_args_order()
    {
        if(! empty( $this->user_params['sorder'] )) {
            if ( $this->user_params['sorder'] == '1') {
                $this->query_args['orderby'] = 'date';
                $this->query_args['order']= 'ASC';
            }else{
                $this->query_args['meta_key'] = 'imc_likes';
                $this->query_args['orderby'] = 'meta_value_num';
                $this->query_args['order']= 'DESC';
            }
        }

    }

    public function set_query_args_custom()
    {
        // to be defined in extending class
    }
    public function get_id_list_by_taxo($taxonomy, $value, $field = "id" ) //todelete
    {
        if (( empty( $value)) || ( $value == 'all')) {
            return "";
        }

        $taxo_values = explode(",", $value);

        $query_param =  array(
            'post_type' => $this->post_type,
            'post_status' => $this->select_statuses,
            'posts_per_page' => -1,
            'tax_query' => array(array(
    				'taxonomy' => $taxonomy,
    				'field' => $field,
    				'terms' => $taxo_values,
                    'operator' => "IN",
    			),),
            // 'orderby' => 'id',
            // 'order'   => 'ASC',
            'fields'  => 'ids',
    	);

        $filtering_taxo = get_posts($query_param);
        if ((!empty($filtering_taxo)) && (count($filtering_taxo) > 0) ) {
            $postids = array_unique($filtering_taxo);
        } else {
            $postids = array();
        }

        $all_ids = array_merge( $this->ids_in, $postids);
        $this->ids_in = array_unique($all_ids);

    }

    public function get_all_pbvote_taxo( $term_name = "", $hierarchical = false )
    {
     	if (empty($term_name)) {
     		return false;
     	}
        // no default values. using these as examples
        $taxonomies = array(
            $term_name,
        );

        $args = array(
            'orderby'                => 'id',
            'order'                  => 'ASC',
            'hide_empty'             => false,
            'fields'                 => 'all',
        );

    	if ($hierarchical) {
    		$args = array_merge(
    			$args,
    			array(
    			    'hierarchical' => true,
    			    'parent' => 0,
    		    )
    		);
    	}
        $terms = get_terms($taxonomies, $args);

        return $terms;
    }

    public function  get_query_data()
    {
        $pom = new WP_Query( $this->query_args );
        // $pom = get_posts( $this->query_args );
        return $pom;
    }
}
