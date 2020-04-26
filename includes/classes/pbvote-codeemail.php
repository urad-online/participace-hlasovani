<?php
class PbVote_CodeEmail
{
    private $result, $delivery_status;

    public function __construct()
    {
        $this->email_subject = 'Registracni kod pro hlasovani o projektech';
        $this->email_header  = 'Zaslani registracniho kodu';
    }

    public function check_voter_id( $id )
    {
        $email_regexp = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';

        if ( preg_match($email_regexp, $id) ) {
            return $id;
        } else {
            return false;
        }
    }

    public function send_new_code( $input )
    {
        $this->delivery_status = "X";
        if (empty( $input['code'] ) ) {
            return false;
        }

        if (! empty($input['message'])) {
            $email_text = $input['message'];
        } else {
            $email_text = "Aktivacni kod: ".$input['code']." platny do ". $input['expiration_time'];
        }


        $email_send = wp_mail( $input['voter_id'], $this->email_subject, $email_text, $this->email_header );

        if ( $email_send ) {
            $this->result =   array("Registracni kod odeslan",);
            $this->delivery_status = "0";
            return true;
        } else {
            $this->result =  array( "result" => "error", "message" => "Chyba odeslani emailu",);
            $this->delivery_status = "1";
            return false;
        }
    }

    public function get_error_description()
    {
        return $this->result;
    }
    public function delivery_status()
    {
        return $this->delivery_status;
    }

    public function check_delivery_result( $msg_id = "")
    {
        return true;
    }
}
