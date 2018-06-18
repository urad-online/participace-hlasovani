<?php
class PbVote_CodeSms
{
    private $login, $password;
    private $sms_api;
    private $result;

    public function __construct()
    {
        $this->login    = SMSGATE_LOGIN;
        $this->password = SMSGATE_PASSWD;
        require_once PB_VOTE_PATH_INC .'/smssluzbacz/apixml30.php';
        $this->sms_api  = new ApiXml30( $this->login, $this->password );
    }

    public function check_voter_id( $id )
    {
        $phone_regexp = '/^(\+420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/';

        if ( preg_match($phone_regexp, $id) ) {
            $id = str_replace( ' ', '', $id);
            if (strlen( $id ) === 9 ) {
                $id = '+420'.$id;
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

        $sms_get = new SimpleXMLElement( htmlspecialchars_decode( $this->sms_api->smsgate_get_account_info() )) ;

        return (array) $sms_get ;
    }

    public function send_new_code( $input )
    {
        if (empty( $input['code'] ) ) {
            return false;
        }

        $sms_text = "Aktivacni kod: ".$input['code']." platny do ". $input['expiration_time'];

        $sms_send = $this->sms_api->send_message( $input['voter_id'], $sms_text);

        $sms_send = new SimpleXMLElement( htmlspecialchars_decode( $sms_send) );

        if (! empty( $sms_send->id) ) {
            $this->result =   array( "result" => "error", "message" => "Chyba: ".$sms_send->id ." , ".$sms_send->message,);
            return false;
        } elseif (! empty( $sms_send->message->id)) {
            $this->result = $sms_send->message->id;
            return $sms_send->message->id;
        } else {
            $this->result =  array( "result" => "error", "message" => "Chyba parsovani XML zpravy",);
            return false;
        }
    }

    public function get_error_description()
    {
        return $this->result;
    }

}
