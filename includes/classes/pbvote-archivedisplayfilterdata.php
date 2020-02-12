<?php
class PbVote_ArchiveDisplayFilterData
{
    public $taxo_status   = "voting_status";
    public $taxo_category = "voting_category";
    public $taxo_period   = "voting-period";
    public $post_type     = PB_VOTING_POST_TYPE;
    public $query_args, $query_args1, $query_args2, $query_param, $paged, $user_params, $select_statuses;
    private $ids_in =  array();

    public function __construct( $params)
    {
        global $wp_query;
        $this->user_params = $params;
        $this->set_query_args_page_number();

        //Basic query calls depending the user
        if ( is_user_logged_in()){ //not user
            // $custom_query_args = imcLoadIssuesForAdmins($paged,$pbvote_imported_ppage,$pbvote_imported_sstatus,$pbvote_imported_scategory);
            if ( ! current_user_can( 'administrator' ) ) {
              $this->select_statuses = array('publish');
              $this->select_statuses = array('publish', 'pending', 'draft');
              $this->query_args2 = array(
                'post_type' => $this->post_type,
                'post_status' => array('pending', 'draft'),
                'author' => get_current_user_id(),
              );
            } else {
              $this->select_statuses = array('publish', 'pending', 'draft');
            }
        } else {
            $this->select_statuses = array('publish');
            // $this->query_args = pbvLoadIssuesForGuests($voting_view_filters->get_filter_params(), $paged);
        }

        $this->query_param = array(
            'paged' => $this->paged,
            'posts_per_page' => $this->user_params['ppage'],
        );
        $this->query_args1 = array(
            'post_type' => $this->post_type,
            'post_status' => $this->select_statuses,
        );

        $this->set_query_args_keyword();
        $this->set_query_args_category();
        $this->set_query_args_status();
        $this->set_query_args_period();
        $this->set_query_args_order();
        $this->set_query_args_voting();
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
    public function set_query_args_period()
    {
        if(! empty( $this->user_params['speriod'] )) {
            $this->set_query_add_taxo( $this->taxo_period, $this->user_params['speriod']);
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
    private function  set_query_args_page_number()
    {
      if(! empty( $this->user_params['page'] )) {
          $this->paged = $this->user_params['page'];
      } else {
          $this->paged = 1;
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

    public function set_query_args_voting()
    {
      // not needed for hlasovani
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
        $final_query = array_merge($this->query_param, $this->query_args1, $this->query_args);
        // $final_query['args'] = array_merge($this->query_args1, $this->query_args);
        // $pom = new WP_Query( $this->query_args );
        $pom = new WP_Query( $final_query );
        // $this->save_to_session( $pom);
        return $pom;
    }

    private function save_to_session( $query)
    {
        if ( ! empty($query) ) {
            $_SESSION[ 'pbvote_issues_query' ] = json_encode( $query );
        } else {
            if ( isset($_SESSION[ 'pbvote_issues_query' ]) ) {
                unset($_SESSION[ 'pbvote_issues_query' ]);
            }
        }
    }

    private function get_from_session()
    {
        if (isset($_SESSION[ 'pbvote_issues_query' ])) {
            return json_decode( $_SESSION[ 'pbvote_issues_query' ], false);
        } else {
            return null;
        }
    }
    public function set_query_paged()
    {
      if ( get_query_var( 'paged' ) ) {
        $this->paged = get_query_var('paged'); // On a paged page.
      } else if ( get_query_var( 'page' ) ) {
        $this->paged = get_query_var('page'); // On a "static" page.
      }
    }
}
//
