<?php
class PbVote_ArchiveDisplayFilterData
{
    protected $taxo_status   = "voting_status";
    protected $taxo_category = "voting_category";
    protected $taxo_period   = "voting-period";
    protected $post_type     = PB_VOTING_POST_TYPE;
    protected $query_args, $query_param, $post_ids_in, $paged, $user_params, $select_statuses;
    protected $ids_in =  array();

    public function __construct( $params)
    {
        global $wp_query;
        $this->user_params = $params;
        $this->set_query_args_page_number();
        $this->query_arg_status = array();

        //Basic query calls depending the user
        if ( is_user_logged_in()){ //not user
            // $custom_query_args = imcLoadIssuesForAdmins($paged,$pbvote_imported_ppage,$pbvote_imported_sstatus,$pbvote_imported_scategory);
            if ( current_user_can( 'administrator' ) ) {
              $this->query_arg_status[] = array(
                'post_type' => $this->post_type,
                'post_status' => array('publish', 'pending', 'draft'),
                'paged' => 1,
                'posts_per_page' => -1,
              );
            } else {
              $this->query_arg_status[] = array(
                'post_type' => $this->post_type,
                'post_status' => array('publish'),
                'paged' => 1,
                'posts_per_page' => -1,
              );
              $this->query_arg_status[] = array(
                'post_type' => $this->post_type,
                'post_status' => array('pending', 'draft'),
                'author' => get_current_user_id(),
                'paged' => 1,
                'posts_per_page' => -1,
              );
            }
        } else {
          $this->query_arg_status[] = array(
            'post_type' => $this->post_type,
            'post_status' => array('publish'),
            'paged' => 1,
            'posts_per_page' => -1,
          );
        }

        $this->query_param = array(
            'post_type' => $this->post_type,
            'post_status' => array('publish', 'pending', 'draft'),
            'paged' => $this->paged,
            'posts_per_page' => $this->user_params['ppage'],
        );
        $this->query_args = array();
        $this->set_query_args_keyword();
        $this->set_query_args_category();
        $this->set_query_args_status();
        $this->set_query_args_order();
        $this->set_query_args_period_by_voting_ids();
        // $this->set_query_args_voting();
        $this->set_query_args_period();
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

    protected function set_query_add_taxo( $taxonomy, $value, $field = "term_taxonomy_id")
    {
        if ((!empty( $value)) && ( $value !== 'all')) {
            if ( is_array($value)) {
              $taxo_values = $value;
            } else {
              $taxo_values = explode(",", $value);
            }
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
                $this->query_param['orderby'] = 'date';
                $this->query_param['order']= 'ASC';
            }else{
                $this->query_param['meta_key'] = 'imc_likes';
                $this->query_param['orderby'] = 'meta_value_num';
                $this->query_param['order']= 'DESC';
            }
        }

    }

    public function set_query_args_voting()
    {
      // not needed for hlasovani
    }
    public function set_query_args_period_by_voting_ids()
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
        $this->query_ids();
        if (count($this->post_ids_in)==0) {
          // otwerwise final query selects all posts
          $this->post_ids_in = array(0);
        }
        $final_query = array_merge(
          $this->query_param,
          array('post__in' => $this->post_ids_in)
        );
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

    protected function query_ids()
    {
      // first id 0 to prevent selecting
      $id_list = array();
      foreach ($this->query_arg_status as $query_status) {
        $sub_query = array_merge( array('fields' => 'ids',), $query_status, $this->query_args);
        $ids = get_posts($sub_query);
        if (! empty($ids) ) {
          $id_list = array_merge( $id_list, (array) $ids);
        }
      }
      $this->post_ids_in = $id_list;
    }
}
//
