<?php

class PbVote_LimeSurveyTokens extends PbVote_GetCode
{
    private $base_url, $login, $password, $rpc_client_file, $reminder;
    private $attributes = array("attribute_1","validfrom","validuntil", "language", "sent", "remindedsent", "remindercount","completed", "email");
    private $recip_language = "cs";

    public function set_pbvoting_meta()
    {
        $this->survey_id = (!empty( $this->pbvoting_meta['survey_id'][0]))       ? $this->pbvoting_meta['survey_id'][0]       : '1862845';
        $this->base_url  = (!empty( $this->pbvoting_meta['voting_url'][0]))      ? $this->pbvoting_meta['voting_url'][0]      : 'https://pruzkumy.urad.online';
        $this->login     = (!empty( $this->pbvoting_meta['voting_login'][0]))    ? $this->pbvoting_meta['voting_login'][0]    : LIMESURVEY_LOGIN ;
        $this->password  = (!empty( $this->pbvoting_meta['voting_password'][0])) ? $this->pbvoting_meta['voting_password'][0] : LIMESURVEY_PASSWORD;
        $this->max_number_of_tokens = (!empty( $this->pbvoting_meta['max_number_of_tokens'][0])) ? intval($this->pbvoting_meta['max_number_of_tokens'][0]) : 2;

        // $this->survey_url  = $this->base_url . '/index.php/admin/survey/sa/view/surveyid/' . $this->survey_id;
        $this->survey_url  = $this->base_url . '/' . $this->survey_id;
        $this->limeapi_url = $this->base_url . '/admin/remotecontrol';

    }

    public function init_token_storage( )
    {
        $this->rpc_client_file = PB_VOTE_PATH_INC.'/jsonrpcphp/JsonRPCClient.php';
        require_once $this->rpc_client_file ;

        if ($this->msg_type === "email" ) {
            $this->used_attr = 'email';
        } else {
            $this->used_attr = 'attribute_1';
        }

        $this->rcp_client = new \org\jsonrpcphp\JsonRPCClient( $this->limeapi_url );
        try {
            $this->sessionKey = $this->rcp_client->get_session_key( $this->login, $this->password );
        } catch (Exception $e) {
            $this->->set_error( 'Chyba připojení na server s průzkumy.',);
            return false;
        }

        return true;
    }

    public function get_voter_by_id( $voting_id, $voter_id)
    {

        $token = $this->get_token_by_attrib( $this->used_attr, $voter_id );

        return $token;

    }

    public function get_token_by_attrib( $attrib_name, $mobile = null )
    {
        $this->reminder = false;
        $this->index = -1;

        if (! empty($mobile) ) {
            $aConditions = array( $attrib_name => $mobile );
        } else {
            $aConditions = null;
        }

        try {
            $list = $this->rcp_client->list_participants( $this->sessionKey, $this->survey_id, 0, 5, false, $this->attributes, $aConditions );
            $last_rec = array( "index" => -1, "expiration_time" => 0);
        } catch (Exception $e) {
            $list = array();
            $this->set_error( 'Chyba připojení na server s průzkumy.');
            return false;
        }


        if (( count($list) > 0 ) && ( empty( $list['status']))) {
            foreach ($list as $key => $value) {
                if ( strtotime($value['validuntil']) > $last_rec['expiration_time']) {
                    $last_rec['index'] = $key;
                    $last_rec['expiration_time'] = strtotime($value['validuntil']);
                }
            }
        }

        if ( $last_rec['index'] > -1 ) {
            $this->reminder = true;
            $this->index = $last_rec['index'];
            return $this->convert_token_record( $list[ $last_rec['index'] ]);
        } else {
            return false;
        }
    }

    public function convert_token_record( $input)
    {
        if ($this->used_attr === "email" ) {
            $voter_id = $input['participant_info']['email'];
        } else {
            $voter_id = $input[ $this->used_attr ] ;
        }

        $output = array(
            "id" => $input['tid'],
            "voter_id" => $voter_id,
            "expiration_time" => $input['validuntil'],
            "status" => ( $input['completed'] == 'N') ? "new" : "closed",
        );
        return (object) $output;
    }

    public function set_voter_status(  $id, $status )
    {
        // nedela nic ,je tu kvuli  prepsani motody ce tride PbVote_GetCode
    }

    public function save_code( )
    {
        // nedela nic ,je tu kvuli  prepsani motody ce tride PbVote_GetCode
    }

    public function get_new_code( )
    {
        if ( empty($this->voter_id)  ) {
            return false;
        }

        if ( $this->index >= ( $this->max_number_of_tokens - 1) ) {
            $this->set_error( 'Vyčerpán počet zaslaných kódů - max '.$this->max_number_of_tokens );
            return false;
        }
        $new_particip = array(array(
            $this->used_attr  => $this->voter_id,
            'validfrom'  => $this->issued_time,
            'validuntil' => $this->expiration_time,
            'language'   => $this->recip_language,
            'sent'       => $this->issued_time, )
        );

        if ($this->reminder) {
            $new_particip[0][ 'remindersent' ] = $this->issued_time;
            $new_particip[0][ 'remindercount' ] = 1;
        }

        try {
            $new_row = $this->rcp_client->add_participants( $this->sessionKey, $this->survey_id, $new_particip, true );
        } catch (Exception $e) {
            $new_row = array( 'status' => 'Chyba připojení na server s průzkumy při vytváření nové registrace.');
        }

        // print_r($new_row, null );

        if (! empty( $new_row[0]['token'])) {
            $this->survey_url .= '?token='.$new_row[0]['token'];
            $this->code_id = $new_row[0]['tid'];
            $this->log_error( "novy token: " . $new_row[0]['token'] );
            return $new_row[0]['token'];
        } else {
            $this->set_error( $new_row['status'] );
            return false;
        }
    }

    public function release_session()
    {
        if (! empty( $this->sessionKey ) ) {
            $this->rcp_client->release_session_key( $this->sessionKey );
        }
    }

    public function clear_new_code()
    {
        $result = true;
        $this->log_error( "Mazání nového záznamu");
        if ( ! empty( $this->code_id )) {
            try {
                $deleted_ids = $this->rcp_client->delete_participants( $this->sessionKey, $this->survey_id, array( $this->code_id));
            } catch (Exception $e) {
                $deleted_ids = array();
            }

            if ( count( $deleted_ids) == 0 ) {
                $this->set_error( 'Chyba při mazání registrace' );
                $result = false;
            }
        }
        return $result;
    }

    public function __destruct()
    {
        $this->release_session();
    }
}
