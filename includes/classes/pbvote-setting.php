<?php
class PbVote_Setting {
    const PAGE   = 'pb-setting';
    const DOMAIN = 'pb-voting';
    private $option_items = array("pb_enable_rating" => 2, "pb_help_slug" => "" , "pb_help_rating_section_id"  => "");

    public function __construct()
    {
        add_action('admin_menu', array( $this, 'pbvote_admin_menu'));
    }

    public function pbvote_admin_menu()
    {
  			add_menu_page(
    				__('Participace nastaveni', $this::DOMAIN),
    				__('Participace nastaveni', $this::DOMAIN),
    				// 'manage_options',
    				'manage_options',
    				$this::PAGE,
    				array($this, 'renderSettings')
    			);

    }


    	public function renderSettings()
    	{
        if (isset($_POST['_setting_save'])) {
          self::save_settings();
        }
        $this->get_options();
        ?>
        <h2><?php echo __( 'Nastaveni', $this::DOMAIN);?> </h2>

        <form method="POST" action="">
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <?php echo __("Povolit hodnocení před hlasováním", $this::DOMAIN);?>
                </th>
                <td>
                  <input type="radio" name="pb_enable_rating" value="1" <?PHP echo ($this->option_items ['pb_enable_rating'] === "1") ? 'checked' : "" ?>><?php _e('YES',$this::DOMAIN); ?></input>
                  <input type="radio" name="pb_enable_rating" value="2" <?PHP echo ($this->option_items ['pb_enable_rating'] === "2") ? 'checked' : "" ?>><?php _e('ΝΟ', $this::DOMAIN); ?></input>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <?php echo __("Adresa stránky s nápovědou", $this::DOMAIN);?>
                </th>
                <td>
                  <input type="text" name="pb_help_slug" id="pb_help_slug" class="form-control" value="<?PHP echo $this->option_items ['pb_help_slug'];?>">
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <?php echo __("Jméno sekce s nápovědou k hodnocení návrhů", $this::DOMAIN);?>
                </th>
                <td>
                  <input type="text" name="pb_help_rating_section_id" id="pb_help_rating_section_id" class="form-control" value="<?PHP echo $this->option_items ['pb_help_rating_section_id'];?>">
                </td>
              </tr>
            </tbody>
          </table>
          <div class="form-group">
      			<div class="col-sm-10 col-sm-offset-2">
      				<!-- <input type="submit" name="_setting_save" value="Uložit nastavení" class="btn btn-primary"> -->
              	<button type="submit" name="_setting_save" class="button button-primary">Uložit nastavení</button>
      			</div>
      		</div>

        </form>
        <?php
    	}
      protected function save_settings()
      {
          $setting = $this->option_items ;
          foreach ($this->option_items as $option => $value ) {
            if (isset($_POST[ $option ])) {
                $setting[$option] = $_POST[$option] ;
            }
          }
          $saved_option = get_option( PB_OPTION_NAME );
          if ( !empty($saved_option) ) {
            update_option( PB_OPTION_NAME , $setting);
          } else {
            add_option( PB_OPTION_NAME, $setting);
          }
      }

      protected function get_options()
      {
          $pbvote_options = get_option(PB_OPTION_NAME);
          foreach ($this->option_items as $option => $value) {
            if (isset(  $pbvote_options[ $option] )) {
              $this->option_items [$option] =  $pbvote_options[ $option ] ;
            }
          }
      }
}
