<?php
class PbVote_ControlPages
{
    public $taxo_status   = PB_VOTING_STATUS_TAXO;
<<<<<<< HEAD
    public $post_type     = PB_VOTING_POST_TYPE;
    public $allow_voting  = false;
    public $allow_edit    = false;
    public $voting_id, $dit_page, $insert_page;
    private $project_template_suffix = "-project_issues.php";
=======
    public $allow_voting  = false;
    public $allow_edit = false;
    public $voting_id;
    private $new_issue_template  = PB_VOTING_NEW_ISSUE_TEMPL  ;
    private $edit_issue_template = PB_VOTING_EDIT_ISSUE_TEMPL  ;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090

    public function __construct( $post_id)
    {
        $voting = explode( ',', $post_id);

        if (( !empty($voting)) && (count($voting) == 1) ) {
            $this->voting_id = $voting[0];
            $this->get_status_meta();
        } else {
            $this->voting_id = null;
        }
<<<<<<< HEAD
        $this->edit_page   = $this->control_page_link("edit");
        $this->insert_page = $this->control_page_link("insert");
=======
        $this->page_add  = $this->page_link_add_edit( true);
        $this->page_edit = $this->page_link_add_edit( false);
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    }

    private function get_status_meta()
    {
        $vote_status = wp_get_object_terms($this->voting_id, $this->taxo_status);
        if (is_wp_error($vote_status)) {
            return false;
        }

        if (! empty( $vote_status[0]->term_id)) {
            $temp_term = get_term_meta( $vote_status[0]->term_id);
            if ((!empty( $temp_term['allow_voting'][0]) ) && ($temp_term['allow_voting'][0] ) ) {
                $this->allow_voting =  true;
            }
            if ((!empty( $temp_term['allow_adding_project'][0]) ) && ($temp_term['allow_adding_project'][0] ) ) {
                $this->allow_edit =  true;
            }
        }
    }

    public function page_link_voting()
    {
        $post_url = "#";
    	$slug = "";

        $slug = get_post_meta( $this->voting_id, 'name_page', true);
    	if ( !empty($slug)) {
    		$temp_posts = get_page_by_path($slug);
    		if( $temp_posts ) {
    			$post_url = esc_url( get_permalink($temp_posts->ID));
    		}
    	}

        return $post_url;
    }
<<<<<<< HEAD
    public function control_page_link( $template )
    {
        $control_pages = get_pages(array(
            'hierarchical' => 0,
            'parent' => -1,
            'meta_key' => '_wp_page_template',
            'meta_value' => "/".$template . $this->project_template_suffix,
        ));
        if ($control_pages) {
            return esc_url( get_permalink($control_pages[0]->ID));
=======
    public function page_link_add_edit( $insert = true )
    {
        if ( $insert) {
            $template = $this->new_issue_template;
        } else {
            $template = $this->edit_issue_template;
        }
        $pages = get_pages(array(
            'hierarchical' => 0,
            'parent' => -1,
            'meta_key' => '_wp_page_template',
            'meta_value' => $template,
        ));
        if ($pages) {
            return esc_url( get_permalink($pages[0]->ID));
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
        } else {
            return "#";
        }
    }

    public function can_add_project()
    {
        return $this->allow_edit;
    }
    public function can_vote()
    {
        return $this->allow_voting;
    }
    public function gen_button_vote( $list_item = true, $icon_size = "36")
    {
        if ($this->allow_voting) {
            $link = $this->page_link_voting().$this->add_param_to_url();
        } else {
            return "";
        }
        if ( $link == "#")  {
            return "";
        }

        $output = '<a href="' . $link . '" class="imc-SingleHeaderLinkStyle" target="_self">
            <i class="material-icons md-'.trim($icon_size).' imc-SingleHeaderIconStyle">how_to_vote</i>
<<<<<<< HEAD
            <span class="imc-hidden-xs imc-hidden-sm imc-hidden-md">' . __("Registrace k hlasovaní","pb-voting").'</span></a>';
=======
            <span class="imc-hidden-xs imc-hidden-sm imc-hidden-md">' . __("Register to vote","pb-voting").'</span></a>';
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090

        if ($list_item) {
            $output = '<li class="u-pull-right">' . $output . '</li>';
        }
        return $output;
    }

    public function gen_button_add( $list_item = true, $icon_size = "36")
    {
<<<<<<< HEAD
        if ($this->allow_edit) {
            $link = $this->insert_page . $this->add_param_to_url();
        } else {
            return "";
        }
        if ( $link == "#")  {
            return "";
        }
        $output = '<a href="' . $link . '" class="imc-SingleHeaderLinkStyle">
=======
        if ($this->allow_edit && ( $this->page_add != "#") ) {
            $link = $this->page_add . $this->add_param_to_url();
        } else {
            return "";
        }
        $output = '<a href="' . $link . '" class="u-pull-right imc-SingleHeaderLinkStyle">
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
            <i class="material-icons md-'.trim($icon_size).' imc-SingleHeaderIconStyle">add_circle</i>
            <span class="imc-hidden-xs imc-hidden-sm imc-hidden-md">' . __("Report an issue","pb-voting") .'</span></a>';
        if ($list_item) {
            $output = '<li class="u-pull-right">' . $output . '</li>';
        }
        return $output;
    }
<<<<<<< HEAD

    public function gen_link_add( $list_item = true)
    {
        if ($this->allow_edit) {
            $link = $this->insert_page . $this->add_param_to_url();
        } else {
            return "";
        }
        if ( $link == "#")  {
            return "";
        }
        $output = '<a href="' . $link . '" class="imc-LinkStyle">' . __('Report an issue','pb-voting'). '</a>';

        return $output;
=======
    public function gen_url_page_add()
    {
        if ($this->allow_edit && ( $this->page_add != "#") ) {
            $link = $this->page_add . $this->add_param_to_url();
        } else {
            return "";
        }
        return $link;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    }
    private function add_param_to_url()
    {
        return '?votingid='.$this->voting_id;
    }
<<<<<<< HEAD
    public function get_url_edit()
    {
        return $this->edit_page;
    }
    public function get_parent_url( $issue_id = 0)
    {
      if ( $issue_id == 0 ) {
        return "";
      }

      $query_args = array(
          'post_type' => $this->post_type,
          'posts_per_page' => 1,
          'tax_query' => array(array(
              'taxonomy' => "voting-period",
              'field' => 'slug',
              'terms' => "rok-2019",
          )),
      );

      $pom = get_posts( $query_args );
      if ( (is_array($pom)) && (count($pom) > 0)) {
        return esc_url( get_permalink($pom[0]->ID));
      }

      return "";

=======

    public function gen_url_page_edit( $post_id = 0)
    {
        if ( $this->page_edit == "#" ) {
          return "";
        }

        if ( $post_id ) {
          return $this->page_edit . $this->add_param_to_url() . $this->add_param_to_url_edit( $post_id);
        } else {
          return $this->page_edit . $this->add_param_to_url();
        }
    }
    private function add_param_to_url_edit( $post_id)
    {
        return '&issueid='.$post_id;
>>>>>>> 6571ac8cdc6380f1de48e7819c40d38e03512090
    }
}
