<?php
class PbVote_CodeSms
{
    private $login, $password;
    private $sms_api;
    private $result, $delivery_status;
    private $texts = array();
    private $country_kod = "+420";

    public function __construct()
    {
        $this->login    = SMSGATE_LOGIN;
        $this->password = SMSGATE_PASSWD;
        require_once PB_VOTE_PATH_INC .'/smssluzbacz/apixml30.php';
        $this->sms_api  = new ApiXml30( $this->login, $this->password );
        $this->texts['default_error'] = __( 'Chyba připojení na server SMS služba.', 'pb-voting');
        $this->texts['error_xml']     = __( 'Chyba parsovaní XML zprávy', 'pb-voting');
        $this->texts['error']            = __( 'Chyba: ', 'pb-voting');
        $this->texts['error_empty']      = __( 'Prázdný token', 'pb-voting');
        $this->texts['err_notdelivered'] = __( "Zpráva nebyla doručena. Stav : ", 'pb-voting');
        $this->texts['sms_text']         = __( "Aktivační kód: %s platný do %s.", 'pb-voting');
    }

    public function check_voter_id( $id )
    {
        $phone_regexp = '/^(\+420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/';

        if ( preg_match($phone_regexp, $id) ) {
            $id = str_replace( ' ', '', $id);
            if (strlen( $id ) === 9 ) {
                $id = $this->country_kod.$id;
            }

            return $id;
        } else {
            return false;
        }
    }

    public function get_smsgate_credit()
    {
        if (empty( $this->sms_api ) ) {
            return false;
        }

        $sms_get = $this->sms_api->smsgate_get_account_info() ;

        return (array) $sms_get ;
    }

    public function send_new_code( $input )
    {
        $this->delivery_status = "X";
        if (empty( $input['code'] ) ) {
            $this->result =  array( "result" => "error", "message" => $this->texts['error_empty'],);
            return false;
        }
        if (! empty($input['message'])) {
            $sms_text = $input['message'];
        } else {
            $sms_text = sprintf( $this->texts['sms_text'], $input['code'], $input['expiration_time']);;
        }

        try {
            $sms_send = $this->sms_api->send_message( $input['voter_id'], $sms_text, null, 0);
        } catch (Exception $e) {
            $this->result = $this->texts['default_error'] ;
            return false;
        }
        $sms_send = new SimpleXMLElement( htmlspecialchars_decode( $sms_send) );

        if (! empty( $sms_send->id) ) {
            $this->result =   array( "result" => "error", "message" => $this->texts['error']. $sms_send->id ." , ".$sms_send->message,);
            return false;
        } elseif (! empty( $sms_send->message->id)) {
            $this->result = (string) $sms_send->message->id;
            return $this->result ;
        } else {
            $this->result =  array( "result" => "error", "message" => $this->texts['error_xml'],);
            return false;
        }
    }

    public function get_error_description()
    {
        return $this->result;
    }

    private function decode_response( $msg = "")
    {
        $result = new SimpleXMLElement( htmlspecialchars_decode( $msg ));
        $output = array();
        foreach ($result->message as $item) {
          $output[] = (array) $item;
        }
        return $output;
    }
    /*
    * Check delivery status of one message for known id
    * @msg - object with the response to posted SMS returned by smsgateapi.sms-sluzba.cz
    */
    public function check_delivery_result($msg_id = "")
    {
        if (empty( $msg_id)) {
          $this->result =  array( "result" => "error", "message" => $this->texts['error_xml'],);
          return false;
        }
        $req = array("act"=>"get_delivery_report","id"=>$msg_id );

        $status = $this->sms_api->get_incoming_messages( $req );
        $result = $this->decode_response( $status)[0];
        if ( isset($result['status'])) {
            $this->delivery_status = $result['status'];
            if ($result['status'] === '0') {
                $this->confirm_message($msg_id);
                return true;
            } else {
                 $this->result =  array( "result" => "error", "message" => "Omlouváme se, ale doposud jsme neobdrželi informaci, zda byla SMS doručena. Máte zapnutý telefon? Je číslo správné? Pokud jste již přístupový kód dostali, pokračujte tlačítkem 'Kód již mám'. Stále nemáte? Je možné, že dorazí později. Pokud nepřijde, opakujte později nebo nás kontaktujte a nahlaste nám tento stav : " .$result['status'] . " - " . $result['description'],);
                return false;
            }
        }
    }
    /**
    * @param int $count - number of returned items
    * @param array $types - query parametrs $what=array("query_incoming"=>0,"query_outgoing"=>0,"query_delivery_report"=>1);
    * @return array/false
    */
    public function get_delivery_report( $count = 30, $types = array("query_delivery_report"=>1) )
    {
        $req = $types;
        $req['count'] = $count;

        try {
            $res = $this->sms_api->get_incoming_messages($req);
        } catch (Exception $e) {
            $this->result = $this->texts['default_error'] ;
            return false;
        }

        return  $this->decode_response($res);
    }
    /**
    * Confirms the message in smsgateapi.sms-sluzba.cz
    * @param int $id - id of message generated by smsgateapi.sms-sluzba.cz
    * @return true/false
    */
    public function confirm_message( $id )
    {
      $what=array("type"=>"delivery_report","id"=>$id);
      try {
        $res = $this->sms_api->confirm_message($what);
      } catch (Exception $e) {
        $this->result = $this->texts['default_error'] ;
        return false;
      }
      return true;
    }

    public function delivery_status()
    {
        return $this->delivery_status;
    }


}
