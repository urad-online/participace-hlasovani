<?php
class PbVote_ControlPages
{
    public $taxo_status   = PB_VOTING_STATUS_TAXO;
    public $post_type     = PB_VOTING_POST_TYPE;
    public $allow_voting  = false;
    public $allow_edit    = false;
    public $voting_id, $edit_page, $insert_page;
    private $project_template_suffix = "-project_issues.php";
    private $voting_status;

    public function __construct( $post_id)
    {
        $voting = explode( ',', $post_id);

        if (( !empty($voting)) && (count($voting) == 1) ) {
            $this->voting_id = $voting[0];
            $this->voting_status =  new PbVote_ControlStatusPermission( $this->voting_id);
            $this->allow_edit    = $this->voting_status->can_add_new();
            $this->allow_voting  = $this->voting_status->can_vote();
        } else {
            $this->voting_id = null;
        }
        $this->edit_page   = $this->control_page_link("edit");
        $this->insert_page = $this->control_page_link("insert");
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
            <span class="imc-hidden-xs imc-hidden-sm imc-hidden-md">' . __("Registrace k hlasovaní","pb-voting").'</span></a>';

        if ($list_item) {
            $output = '<li class="u-pull-right">' . $output . '</li>';
        }
        return $output;
    }

    public function gen_button_add( $list_item = true, $icon_size = "36")
    {
        if ($this->allow_edit) {
            $link = $this->insert_page . $this->add_param_to_url();
        } else {
            return "";
        }
        if ( $link == "#")  {
            return "";
        }
        $output = '<a href="' . $link . '" class="imc-SingleHeaderLinkStyle">
            <i class="material-icons md-'.trim($icon_size).' imc-SingleHeaderIconStyle">add_circle</i>
            <span class="imc-hidden-xs imc-hidden-sm imc-hidden-md">' . __("Report an issue","pb-voting") .'</span></a>';
        if ($list_item) {
            $output = '<li class="u-pull-right">' . $output . '</li>';
        }
        return $output;
    }

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
    }
    private function add_param_to_url()
    {
        return '?votingid='.$this->voting_id;
    }
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

    }
}
