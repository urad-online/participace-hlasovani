<?php
class PbVote_ValidateCaptcha
{
    private $result, $atts;
    private $google_api_url = 'https://www.google.com/recaptcha/api/siteverify';


    public function send_request( $captcha_response, $IP_address = "" )
    {
        $params = array(
            'secret' => GOOGLE_CAPTCHA_SECRET_KEY,
            'response' => $captcha_response,
            'remoteip' => $IP_address,);

        $opts = array(
            'http' => array(
                'method' => 'POST',
                'content' => http_build_query( $params),
            )
        );
        $context = stream_context_create($opts);

        $result =  wp_remote_post(  $this->google_api_url, array('body' => $params));
        $this->result = json_decode( $result['body'], true);

        return ( $this->result['success'] === true);
         // $this->result = file_get_contents( $this->google_api_url, false, $context );
        // if ($fp = fopen($this->google_api_url, 'r', false, $context)) {
        //     $response = '';
        //     while ($row = fgets($fp)) {
        //         $response .= trim($row) . "\n";
        //     }
        //     $resp = json_decode( $response);
        // } else {
        //     throw new \Exception('Unable to connect to ' . $this->google_api_url);
        // }
    }

    public function get_error()
    {
        if ( ! is_array( $this->result)) {
            return array(
                'result'  => 'error',
                'message' => 'Neznámý výsledek validace CAPTCHA');
        }

        if ($this->result['success']) {
            return array(
                'result'  => 'ok',
                'message' => 'Validace CAPTCHA byla úspěšná.');
        } else {
            return array(
                'result'  => 'error',
                'message' => 'Chyba validace CAPTCHA . Kód - '. implode( " ", $this->result['error-codes']));

        }
    }
}
