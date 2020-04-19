<?php
class PbVote_Setting {
    const PAGE   = 'pb-setting';
    const DOMAIN = 'pb-voting';
    private $option_items = array("pb_enable_rating" => 2, "pb_help_slug" => "" , "pb_help_rating_section_id"  => "");
    private $setting_tabs = array( "setting" => "", "delivery_log" => "deliverylog");
    private $table_def_log = array(
        array("label" => "Hlasování", "width" => "10%",   "field" => "post_title"),
        array("label" => "Hlasující", "width" => "10%",   "field" => "voter_id"),
        array("label" => "Platnost od", "width" => "10%", "field" => "issued_time"),
        array("label" => "Platnost do", "width" => "10%", "field" => "expiration_time"),
        array("label" => "Stav", "width" => "15%",   "field" => "status"),
        array("label" => "Zpráva", "width" => "25%", "field" => "message_id"),
    );
    private $table_def_status_change = array(
        array("label" => "Hlasování", "width" => "10%",   "field" => "post_title"),
        array("label" => "Hlasující", "width" => "10%",   "field" => "voter_id"),
        array("label" => "Platnost od", "width" => "10%", "field" => "issued_time"),
        array("label" => "Akce", "width" => "10%", "field" => "step"),
        array("label" => "Čas změny ", "width" => "10%", "field" => "log_timestamp"),
        array("label" => "Stav", "width" => "10%",   "field" => "new_status"),
        array("label" => "Reference", "width" => "15%", "field" => "reference_id"),
    );

    public function __construct()
    {
        $that = $this;
        add_action('admin_menu', array( $that, 'pbvote_admin_menu'));
    }

    public function pbvote_admin_menu()
    {
        $that = $this;
  			add_menu_page(
    				__('Participace nastaveni', $that::DOMAIN),
    				__('Participace nastaveni', $that::DOMAIN),
    				// 'manage_options',
    				'manage_options',
    				$that::PAGE,
    				array($that, 'renderSettings')
    			);

    }


  	public function renderSettings()
  	{
        $tab = $this->get_tab_name();
        if ($tab === self::PAGE) {
          $this->renderSettings_content();
        } else {
          $this->renderSettings_log();
        }

    }

      private function renderSettings_content()
      {
        if (isset($_POST['_setting_save'])) {
          self::save_settings();
        }
        $this->get_options();
        echo self::render_settings_menu('pb-setting');
        ?>

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

      private function render_settings_menu( $page )
      {
        $output  = '<ul class="nav nav-tab-wrapper" >';
        $output .= '<li class="nav-tab '. (($page === 'pb-setting') ? 'nav-tab-active">' : '">') . '<a href="?page='.self::PAGE. '">Nastavení</a></li>';
        $output .= '<li class="nav-tab '. (($page === 'deliverylog') ? ' nav-tab-active">' : '">') . '<a href="?page='.self::PAGE. '&settingtab=deliverylog'. '">Doručené tokeny</a></li>';
        $output .= '</ul>';
        echo $output;
      }
      private function get_tab_name()
      {
          if (isset( $_GET['settingtab']) && (! empty($_GET['settingtab']))) {
            return $_GET['settingtab'];
          } else {
            return self::PAGE;
          }
      }

      private function renderSettings_log()
      {
        $this->get_log_query_params();
        $table_def = $this->table_def_log;
        if (isset($_POST['_get_log'])) {
          if ($this->log_query_args['pb_log_type'] == "one") {
              $list = $this->get_logs_for_voterid();
              $table_def = $this->table_def_status_change;
          } else {
              $list = $this->get_logs_all();
          }
        } else {
          $list = array();
        }
        echo self::render_settings_menu('deliverylog');

        $this->renderSettings_log_form();
        echo $this->render_table_log( $table_def, $list);
      }
      protected function get_log_query_params()
      {
          $this->log_query_args['voting_id']   = ((! empty($_POST['log_voting_id'])) && ( !$_POST['log_voting_id'] === "all")) ? $_POST['log_voting_id'] : "" ;
          $this->log_query_args['pb_log_type'] = (! empty($_POST['pb_log_type'])) ? $_POST['pb_log_type'] : "all" ;
          $this->log_query_args['date_from']   = (! empty($_POST['date_from'])) ? $_POST['date_from'] : "" ;
          $this->log_query_args['date_to']     = (! empty($_POST['date_to']))   ? $_POST['date_to']   : "" ;
          $this->log_query_args['voter_id']    = (! empty($_POST['voter_id']))  ? $_POST['voter_id']  : "" ;
      }
      protected function get_logs_for_voterid()
      {
        $db = new PbVote_GetDeliveryLog();

        return $db->get_data_status_change( array(
          "voting_id" => $this->log_query_args['voting_id'],
          "voter_id"  => $this->log_query_args['voter_id'],
          "time_from" => $this->log_query_args['date_from'],
          "time_to"   => $this->log_query_args['date_to'],
        ));
        return array();
      }
      protected function get_logs_all()
      {
          $db = new PbVote_GetDeliveryLog();

          return $db->get_data_status( array(
              "voting_id" => $this->log_query_args['voting_id'],
              "voter_id"  => $this->log_query_args['voter_id'],
              "time_from" => $this->log_query_args['date_from'],
              "time_to"   => $this->log_query_args['date_to'],
            ));

      }

      protected function renderSettings_log_form()
      {
        ?>
          <form method="POST" class="pbvote-delivery-log-form" action="">
            <table class="pbvote-delivery-log-form-table">
              <tbody>
                <tr class="pbvote-delivery-log-form-row">
                  <th scope="row" class="pbvote-delivery-log-form-header">
                    <label for="log_voting_id"><?php echo __('Ročník hlasování', $this::DOMAIN);?></label>
                  </th>
                  <td class="pbvote-delivery-log-form-cell">
                    <?PHP echo $this->dropdown_period( "log_voting_id", $this->log_query_args['voting_id']);?>
                  </td>
                </tr>
                <tr class="pbvote-delivery-log-form-row">
                  <th scope="row" class="pbvote-delivery-log-form-header">
                    <label for="date_from"><?php echo __('"Platnost Od" - větší než: ', $this::DOMAIN);?></label>
                  </th>
                  <td class="pbvote-delivery-log-form-cell">
                    <input type="datetime-local" id="date_from" name="date_from" placeholder="yyyy-mm-dd hh:mm" value="<?PHP echo $this->log_query_args['date_from'];?>">
                  </td>
                </tr>
                <tr class="pbvote-delivery-log-form-row">
                  <th scope="row" class="pbvote-delivery-log-form-header">
                    <label for="date_to"><?php echo __('"Platnost Od" - menší než: ', $this::DOMAIN);?></label>
                  </th>
                  <td class="pbvote-delivery-log-form-cell">
                    <input type="datetime-local" id="date_to" name="date_to" placeholder="yyyy-mm-dd hh:mm" value="<?PHP echo $this->log_query_args['date_to'];?>">
                  </td>
                </tr>
                <tr class="pbvote-delivery-log-form-row">
                  <th scope="row" class="pbvote-delivery-log-form-header">
                    <label for="voter_id"><?php echo __("ID hlasujícího", $this::DOMAIN);?></label>
                  </th>
                  <td class="pbvote-delivery-log-form-cell">
                    <input type="text" id="voter_id" name="voter_id" value="<?PHP echo $this->log_query_args['voter_id'];?>">
                  </td>
                </tr>
                <tr class="pbvote-delivery-log-form-row">
                  <th scope="row" class="pbvote-delivery-log-form-header">
                    <?php echo __("Typ výpisu", $this::DOMAIN);?>
                  </th>
                  <td class="pbvote-delivery-log-form-cell">
                    <input type="radio" id="pb_log_type" name="pb_log_type" value="all" <?PHP echo ($this->log_query_args['pb_log_type'] === "all") ? "checked" : "";?>><?php _e('Souhrn',$this::DOMAIN); ?></input>
                    <input type="radio" id="pb_log_type" name="pb_log_type" value="one" <?PHP echo ($this->log_query_args['pb_log_type'] === "one") ? "checked" : "";?>><?php _e('Detail', $this::DOMAIN); ?></input>
                  </td>
                </tr>
                <tr class="pbvote-delivery-log-form-row">
                  <th scope="row">
                    <span></span>
                  </th>
                  <td class="pbvote-delivery-log-form-cell">
                    <button type="submit" name="_get_log" action="" class="button button-primary">Vyhledat</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </form>
          <div class="pbvote-row pbvote-row-centred">
              <span class="pbvote-RegWidgetInputErrorStyle" id="settingDeliveryLogError"></span>
          </div>
          <div class="pbvote-row pbvote-row-centred">
              <span class="pbvote-RegWidgetInputErrorStyle" id="settingDeliveryLogSuccess"></span>
          </div>
          <?PHP
      }

      private function render_table_log( $table_def, $data = array())
      {
        $output  =  '<div class="container"><div class="table-wrapper">';
        if ( empty($data)) {
            $output .= '<h1 class="imc-Text-XL imc-TextColorSecondary imc-TextItalic imc-TextMedium imc-CenterContents">'. __('Nejsou dostupné záznamy o doručení přístupových kódů','pb-voting') .'</h1>';
        } else {
            $output .= '<table class="pbvote-delivery-log-table" style="width:100%">';
            $output .= '<colgroup>';
            foreach ($table_def as $item ) {
              $output .= '<col style="width:' . $item['width'] . '">';
            }
            $output .= '</colgroup><thead class="pbvote-delivery-log-thead"><tr>';
            foreach ($table_def as $item ) {
              $output .= '<th>' . $item['label'] . '</th>';
            }
            $output .= '</tr></thead>';
            $output .= '<tbody class="pbvote-delivery-log-tbody">';
            foreach ($data as $item) {
              $output .= '<tr class="pbvote-delivery-log-trow">';
              foreach ($table_def as $field ) {
                $output .= '<td class="pbvote-delivery-log-tcell">'.$item->{$field['field']}.'</td>';
              }
              $output .= '</tr>';
            }
            $output .= '</tbody></table>';
        }
        $output .= '</div></div>';
        return $output;
      }

      private function dropdown_period( $fieldName = "log_voting_id", $id = "all")
      {
          $options = $this->get_voting_posts();
          array_unshift($options, array("title" => __('Všechny ročníky','pb-voting'), "id" => "all"));
          $html  = '<select name="' . $fieldName . '" id="'.$fieldName.'"class="pb-select-' . $fieldName . ' "' . '>';
          foreach ($options as $option) {
              if ( $option['id'] == $id) {
                $selected = "selected";
              } else {
                $selected = "";
              }
              $html .= '<option class="pb-SelectOptionStyle" '.$selected.' value="' . $option['id'] . '">&nbsp; ' . $option['title'] . '</option>';
          }
          $html .=  "</select>";
          return $html;
      }
      private function get_voting_posts()
      {
        $query_arg = array(
          'post_type' => PB_VOTING_POST_TYPE,
          'post_status' => array('publish', 'pending', 'draft'),
          'paged' => 1,
          'posts_per_page' => -1,
        );
        $temp_posts = get_posts($query_arg);
        $output = array();
        if ($temp_posts) {
            foreach ($temp_posts as $item ) {
                $output[] = array( "title" => $item->post_title, "id" => $item->ID);
            }
        }

        return $output;

      }

}
