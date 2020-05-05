<?php
class PbVote_SaveCodeDeliveryStatus
{
    const REGISTER_TBL_NAME     = "pb_register" ;
    const REGISTER_LOG_TBL_NAME = "pb_register_log" ;
    const INITIAL_STATUS        = 'new';
    private $voting_id, $voter_id, $register_id;

    public function __construct( $voting_id, $voter_id)
    {
      global $wpdb;
      $this->db = $wpdb;
      $this->voting_id = $voting_id;
      $this->voter_id  = $voter_id;
    }
    private $texts =  array(
      "error_save_code"   => "Chyba při ukládání kódu",
      "error_save_status" => "Chyba při ukládání stavu odesílání kódu",
    );
    public function save_code( $code, $issued_time, $expiration_time, $msg)
    {
        $sql_comm = $this->db->prepare( 'INSERT INTO '.$this->db->prefix . self::REGISTER_TBL_NAME
            . ' (voting_id, voter_id, registration_code, issued_time, expiration_time, message_id, status)
            VALUES ( %d, %s, %s, %s, %s, %s, %s)',
                intval( $this->voting_id ),
                $this->voter_id ,
                $code,
                $issued_time,
                $expiration_time,
                $msg,
                self::INITIAL_STATUS
                );

        $result = $this->db->query( $sql_comm );
        if ($result) {
            $this->register_id = $this->db->insert_id;
            $this->result_msg = array( "result" => "ok", "code" => $code, 'expiration_time' => $expiration_time, );
            $this->add_register_log( "generate code" , "", self::INITIAL_STATUS, $msg, "" );
            return true;
        } else {
            $this->result_msg = array( 'result' => 'error', 'message' => $this->texts["error_save_code"],);
            $this->register_id = null;
            return false;
        }
        // return $output;
    }
    public function add_register_log( $step = "", $time = "", $status = "", $desc = "", $ref_id = "")
    {
        if (! $this->register_id) {
            return false;
        }
        if (empty($time)) {
            $time = date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ));
        }
        $sql_comm = $this->db->prepare( 'INSERT INTO '.$this->db->prefix . self::REGISTER_LOG_TBL_NAME
            . ' (reference_id, new_status, log_timestamp, step, description, register_id)
            VALUES ( %s, %s, %s, %s, %s, %d)',
                $ref_id ,
                $status ,
                $time,
                $step,
                $desc,
                $this->register_id
                );

        $result = $this->db->query( $sql_comm );
        if (!$result) {
            $this->result_msg = array( 'result' => 'error', 'message' => $this->texts["error_save_status"],);
            return false;
        } else {
            $this->set_voter_status( $this->register_id, $status);
        }
        return true;
    }
    public function get_result_msg()
    {
        return $this->result_msg;
    }
    public function set_voter_status ( $id, $status)
    {
        $sql_comm = $this->db->prepare( 'UPDATE '.$this->db->prefix . self::REGISTER_TBL_NAME .
            ' SET status = %s WHERE id = %d',
            $status,
            intval( $id )
        );
        $result = $this->db->query( $sql_comm );

        return $result ;

    }

}
