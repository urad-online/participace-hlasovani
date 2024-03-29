<?php
class PbVote_GetCode
{
    public $code = "", $code_spelling ;
    public $code_length, $expiration_hrs;
    public $voting_id, $voter_id, $survey_id, $pbvoting_meta, $register_id;
    public $issued_time, $expiration_time;
    public $result_msg;
    public $survey_url = "";
    public $code_delivery = null;
    public $db_log = null;
    private $status_taxo = PB_VOTING_STATUS_TAXO;
    private $message_placeholders = array(
            array ( 'placeholder' => '{#token}',
                    'value' => 'code'),
            array ( 'placeholder' => '{#expiration_time}',
                    'value' => 'expiration_time'),
            array ( 'placeholder' => '{#survey_url}',
                    'value' => 'survey_url'),
            array ( 'placeholder' => '{#code_spell}',
                    'value' => 'code_spelling'),
                );
   private $nr_of_delivery_check = 20;
   private $delivery_check_sleep = 3000000;
   private $texts = array();

    public function __construct( $input )
    {
        $this->code           = "";
        $this->expiration_hrs = 24;
        $this->code_length    = 10;

        $this->get_pbvoting_meta( $input );

        $class_name = 'PbVote_Code'.ucfirst($this->msg_type );
        if ( class_exists($class_name) ) {
            $this->code_delivery = new $class_name();
        }
        $this->texts['msg_text'] = __( "Přístupový kód: {#token} platný do {#expiration_time}", 'pb-voting');
        $this->texts['error_save']   = __( "Při ukládání přístupového kódu vznikla chyba. Prosím opakujte nebo nahlaste.", 'pb-voting');
        $this->texts['error_format'] = __( "Telefonní číslo není uvedeno ve správném formátu. Prosím opakujte.", 'pb-voting');
        $this->texts['error_voted']  = __( "Hlasování z tohoto telefonního čísla již proběhlo. Děkujeme.", 'pb-voting');
        $this->texts['wait_for_exp'] = __( "Na toto telefonní číslo byl přístupový kód s platností do %s již odeslán. Nový kód můžete získat po uplynutí této lhůty.", 'pb-voting');
        $this->texts['error_nostatus'] = __( "Nepodařil se zjistit stav hlasování. Prosím opakujte nebo nahlaste.", 'pb-voting');
        $this->texts['voting_blocked'] = __( "Generování přístupových kódů pro toto hlasování není povoleno.", 'pb-voting');
    }

    public function init_token_storage()
    {
        $this->db_log = new PbVote_SaveCodeDeliveryStatus( $this->voting_id, $this->voter_id);
        $this->table_name = 'pb_register' ;
        $this->initial_status = 'new';

        global $wpdb;
        $this->db = $wpdb;

        return true;
    }

    private function get_pbvoting_meta( $input )
    {
        $this->voting_id = trim( $input['voting_id'] );
        $this->voter_id  = trim( $input['voter_id'] );

        $this->pbvoting_meta = get_post_meta( $this->voting_id , '', false);

        if ((! empty($this->pbvoting_meta['token-message-type'][0])) && ($this->pbvoting_meta['token-message-type'][0])) {
            $this->msg_type = 'sms';
        } else {
            $this->msg_type = 'email';
        }

        $this->set_pbvoting_tokem_exp();
        $this->set_pbvoting_meta();

        if (!empty( $this->pbvoting_meta['message_text'][0])) {
            $this->message_text = $this->pbvoting_meta['message_text'][0];
        } else {
            $this->message_text = $this->texts['msg_text'];
        }

    }

    private function set_pbvoting_tokem_exp()
    {
        $this->expiration_hrs = (!empty( $this->pbvoting_meta['reg_code_expiration'][0])) ? intval( $this->pbvoting_meta['reg_code_expiration'][0]) : $this->expiration_hrs;
        $issue                =  current_time( 'timestamp', 0 );
        $expiration           = $issue + 60*60*intval($this->expiration_hrs);

        $this->issued_time      = date( 'Y-m-d H:i', $issue);
        $this->expiration_time  = date( 'Y-m-d H:i', $expiration);
    }

    public function set_pbvoting_meta()
    {
        $this->survey_id = $this->voting_id;
        if (!empty( $this->pbvoting_meta['voting_url'][0])) {
            $this->survey_url = $this->pbvoting_meta['voting_url'][0]. '/' .  $this->$this->survey_id;
        } else {
            $this->survey_url = "";
        }
    }

    public function get_code( $input = null )
    {
        // $this->get_pbvoting_meta( $input );

        if ($this->check_voting_status()) {
            if ($this->init_token_storage()) {

                if ($this->check_new_voter() ) {

                    if ( $this->code = $this->get_new_code() ) {
                        $this->save_code();
                        $this->string_spelling();
                        $this->clear_new_code = false;
                        $sms_result = $this->send_new_code();
                        if ( (! $sms_result) && ($this->clear_new_code) ) {
                            $this->clear_new_code();
                        }
                    }
                }
            }
        }

        return  $this->result_msg ;
    }

    public function get_new_code()
    {
        return bin2hex(random_bytes( $this->code_length));
    }

    public function save_code()
    {
        $result = $this->db_log->save_code( $this->code, $this->issued_time, $this->expiration_time, $this->result_msg);
        if ($result) {
          $this->result_msg = $this->db_log->get_result_msg();
        } else {
          $this->set_error( $this->texts['error_save'] );
        }
    }

    public function clear_new_code()
    {
        //void
    }
    private function check_new_voter()
    {
        if (! $this->check_voter_id()) {
            $this->result_msg = array( "result" => "error", "message" => $this->texts['error_format'],);
            return false;
        }

        if ( $voter_rec = $this->get_voter_by_id( $this->voting_id, $this->voter_id ) ) {
            return $this->check_code_expiration( $voter_rec );
        } else {
            return true;
        }
    }

    public function get_voter_by_id( $voting_id, $voter_id)
    {
        $select = $this->db->prepare( 'SELECT id, voter_id, expiration_time, status
             FROM '.$this->db->prefix . $this->table_name .
             ' WHERE voting_id = %d
                 AND voter_id = %s
                 ORDER BY expiration_time DESC LIMIT 1',
            intval( $voting_id ),
            $voter_id
        );

        $result = $this->db->get_results( $select );
        if ($result && ( count( $result) > 0) ) {
            return $result[0] ;
        } else {
            return false;
        }
    }

    private function check_voter_id( )
    {
        if ( (empty( $this->voter_id ) ) || ( ! $this->code_delivery)) {
            return false;
        }

        if ( $id =  $this->code_delivery->check_voter_id( $this->voter_id ) ) {
            $this->voter_id = $id;
            return true;
        } else {
            return false;
        }
    }

    private function check_code_expiration( $db_row )
    {
        /* supported status are
        *   new
        *   expired
        *   closed
        */
        if ($db_row->status === 'closed') {
            $this->set_error( $this->texts['error_voted'] );
            return false;
        }
        if ( strtotime( $db_row->expiration_time) > strtotime( $this->issued_time) ) {
            $this->set_error( sprintf( $this->texts['wait_for_exp'], $db_row->expiration_time ));
            return false;
        } else {
            if ($db_row->status === 'new') {
                $this->set_voter_status( $db_row->id, 'expired' );
            }
            return true;
        }
    }
    public function set_voter_status ( $id, $status)
    {
        $sql_comm = $this->db->prepare( 'UPDATE '.$this->db->prefix . $this->table_name .
            ' SET status = %s WHERE id = %d',
            $status,
            intval( $id )
        );
        $result = $this->db->query( $sql_comm );

        return $result ;

    }

    public function set_voter_completed( $input = null )
    {
        if ( $input || empty($input['voting_id']) || empty($input['voter_id']) )  {
            return false;
        }

        $this->voting_id = $input['voting_id'];
        $this->voter_id = $input['voter_id'];

        if ( $voter_rec = $this->get_voter_by_id( $this->voting_id, $this->voter_id ) ) {
            return $this->set_voter_status( $voter_rec->id, 'closed' ) ;
        } else {
            return false;
        }
    }

    private function send_new_code()
    {
        if ( (empty( $this->code ) ) || ( ! $this->code_delivery)) {
            return false;
        }
        $msg_data = array(
            'code'            => $this->code,
            'voter_id'        => $this->voter_id,
            'expiration_time' => $this->expiration_time,
            'message'         => $this->message_replace_placeholders(),
        );
        $this->clear_new_code = false;

        $result =  $this->code_delivery->send_new_code( $msg_data );
        $this->result_msg = $this->code_delivery->get_error_description();

        if ($result) {
            $this->db_log->add_register_log( "poslat kod", "", "odeslano", $result, $result);

            // this code can't be used because SMS gate return uknown message. Probably message os created with a delay
            $delivered = false;
            $test_cycle = 1;
            for ($i=0; $i < $this->nr_of_delivery_check; $i++) {
                usleep( $this->delivery_check_sleep);
                $delivered = $this->code_delivery->check_delivery_result( $result);
                if ($delivered) {
                  break;
                }

                $test_cycle += 1;
            }

            if ($delivered) {
                $this->db_log->add_register_log( "dorucit kod", "", "doruceno", "Počet cyklů: ".$test_cycle, $result );
                return true;
            } else {
                $this->db_log->add_register_log( "dorucit kod", "", "nedoruceno", $this->code_delivery->get_error_description()["message"] . " Počet cyklů: ".$test_cycle, $result);
                $this->result_msg = $this->code_delivery->get_error_description();
                $this->clear_new_code = false;
                return false;
            }
          // code...
        } else {
            $this->clear_new_code = true;
            $this->db_log->add_register_log( "poslat kod", "", "neodeslano", $this->result_msg, "");
            return false;
        }
    }

    private function message_replace_placeholders()
    {
        $text = $this->message_text;
        foreach ($this->message_placeholders as $placeholder) {
            $text = str_replace( $placeholder['placeholder'], $this->{$placeholder['value']}, $text );
        }
        return $text;
    }

    private function check_voting_status()
    {
        $vote_status = wp_get_object_terms($this->voting_id, $this->status_taxo);
        if (is_wp_error($vote_status)) {
            $this->set_error( $this->texts['error_nostatus'] );
            return false;
        }

        if (! empty( $vote_status[0]->term_id)) {
            $temp_term = get_term_meta( $vote_status[0]->term_id);
            if ((!empty( $temp_term['allow_voting'][0]) ) && ($temp_term['allow_voting'][0] ) ) {
                return true;
            }
        }
        $this->set_error( $this->texts['voting_blocked'] );

        return false;
    }
    public function string_spelling( $dictionary = array())
    {
        if ( empty($dictionary)) {
            $dictionary = json_decode(
                '{"A":"Adam", "B":"Boris", "C":"Cyril", "D":"Dana", "E":"Eva", "F":"Filip",
                "G":"Gita", "H":"Hana", "I":"Ivan", "J":"Jana", "K":"Karel", "L":"Lea",
                "M":"Marie", "N":"Nora", "O":"Ota", "P":"Pavel", "Q":"Quido", "R":"Rudolf",
                "S":"Sofie", "T":"Tom", "U":"Ulrika", "V":"Viktor", "W":"Waldemar",
                "X":"Xenie", "Y":"Yveta", "Z":"Zita", "a":"auto", "b":"bok", "c":"cukr",
                "d":"deka", "e":"erb", "f":"fous", "g":"guma", "h":"hora", "i":"inkoust",
                "j":"jesle", "k":"koule", "l":"les", "m":"mol", "n":"nos", "o":"okap",
                "p":"plot", "q":"quickstep", "r":"rak", "s":"strom", "t":"trh", "u":"ulice",
                "v":"vrak", "w":"waltz", "x":"xerox", "y":"yperit", "z":"zrak",
                "1":"jedna", "2":"dve", "3":"tri", "4":"ctyri", "5":"pet", "6":"sest", "7":"sedm", "8":"osm", "9":"devet", "0":"nula"}',
                true);
        }
        $string_array = str_split($this->code);
        $string_spell = "(";

        foreach ($string_array as $char) {
            if ( ! empty( $dictionary[ $char ]) ) {
                $char_help = $dictionary[ $char ];
            } else {
                $char_help = $char;
            }
            $string_spell .= $char_help . "-";
        }
        $this->code_spelling = substr( $string_spell, 0, -1) .")";
    }

    public function set_error( $message )
    {
        $this->result_msg = array(
            'result' => 'error',
            'message' => $message,
        );
        $this->log_error( $message);
    }

    public function log_error( $message )
    {
        if ( WP_DEBUG === true && PBVOTE_DEBUG === true ) {
            if ( is_array($message) || is_object($message) ) {
                $message =  print_r($message, true) ;
            }
            error_log( "User ID: " . $this->voter_id . " - " . $message );
        }
    }
}
