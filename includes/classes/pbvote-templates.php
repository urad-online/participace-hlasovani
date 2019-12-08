<?php
/**
 * 12.01
 * Add insert, edit & archive templates to every theme
 *
 */

class PbVote_Templates {

    //A reference to an instance of this class.
    private static $instance;

    //The array of templates that IMC plugin tracks.
    protected $templates;
    private $template_path = PB_VOTE_PATH_TEMPL . "/";
    private $post_type     = PB_VOTING_POST_TYPE;

    //Returns an instance of this class.
    public static function get_instance() {
        if ( null == self::$instance ) { self::$instance = new PbVote_Templates();}
        return self::$instance;
    }


    //Initializes the ImcTemplate by setting filters and administration functions.
    private function __construct() {

        $this->templates = array();

        add_filter(
            'theme_page_templates', array( $this, 'add_new_template' )
        );

        // Add a filter to the save post to inject out template into the page cache
        add_filter(
            'wp_insert_post_data',
            array( $this, 'register_project_templates' )
        );

        // Add a filter to the template include to determine if the page has our
        // template assigned and return it's path
        add_filter(
            'template_include',
            array( $this, 'view_project_template')
        );

        // Add your templates to this array.
        $this->templates = array(
            'insert-pbvote_issues.php'   => 'Insert Voting Issue Page',
            'edit-pbvote_issues.php'     => 'Edit Voting Issue Page',
            'archive-pbvote_issues.php'  => 'Archive Voting Issue Page',
            'archive-hlasovani.php'      => 'Přehled hlasování',
        );

        add_filter( 'single_template', array( $this, 'set_single_pbvote_issues_template') );

    }

    //Adds our templates to the page dropdown for v4.7+
    public function add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }


    //Adds our templates to the pages cache in order to trick WordPress into thinking the template file exists where it doens't really exist.
    public function register_project_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, $this->templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }

    //Checks if the templates is assigned to the page
    public function view_project_template( $template ) {

        // Get global post
        global $post;

        // Return template if post is empty
        if ( ! $post ) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        if ( ! isset( $this->templates[get_post_meta(
                $post->ID, '_wp_page_template', true
            )] ) ) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        $file = get_post_meta( $post->ID, '_wp_page_template', true );
        if ( $file ) {
            $file = $this->template_path . $file;
            if ( file_exists( $file ) ) {
                return $file;
            }
        }

        // Return template
        return $template;

    }

    public function set_single_pbvote_issues_template($single_template)
    {
      global $post;

      if ($post->post_type == $this->post_type) {
          $single_template = $this->template_path . 'single-' . $this->post_type . '.php';
      }
      if ($post->post_type == "imc_issues") {
          $single_template = $this->template_path . 'single-' . "pbvote_issues" . '.php';
      }

      return $single_template;
    }


}
