<?php
class PbVote_GetDeliveryLog {
    const REGISTER_TBL_NAME = 'pb_register';
    const REGISTER_LOG_TBL_NAME = "pb_register_log" ;
    const max_rows = 100;
    private $status_not_delivered = ' AND l.status <> "doruceno"';
    private $order_by = " ORDER BY l.voter_id, l.issued_time";

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    public function get_data_status( $args = "", $not_delivered = true)
    {
        $query_param = $this->set_query_params( $args, $not_delivered);

        $sql_comm = 'SELECT p.post_title, l.voter_id, l.issued_time, l.expiration_time, l.status, l.message_id
                FROM '.$this->db->prefix . self::REGISTER_TBL_NAME . ' l LEFT OUTER JOIN '.$this->db->prefix .'posts p
                  ON ( l.voting_id = p.ID ) WHERE 1 = 1 ' . $query_param ;
        $sql_comm .= $this->order_by . ' LIMIT ' . self::max_rows;

        $result = $this->db->get_results( $sql_comm );

        return $result;
    }

    public function get_data_status_change( $args = "", $not_delivered = false )
    {
        $query_param = $this->set_query_params( $args, $not_delivered);

        $sql_comm = 'SELECT p.post_title, l.voter_id, l.issued_time, s.log_timestamp, s.step, s.new_status, s.reference_id
                FROM '.$this->db->prefix . self::REGISTER_TBL_NAME . ' l
                  LEFT OUTER JOIN '.$this->db->prefix .'posts p ON ( l.voting_id = p.ID )
                  LEFT OUTER JOIN '.$this->db->prefix . self::REGISTER_LOG_TBL_NAME . ' s ON ( l.id = s.register_id )
                  WHERE 1 = 1 ' . $query_param ;
        $sql_comm .= $this->order_by . ' LIMIT ' . self::max_rows;

        $result = $this->db->get_results( $sql_comm );

        return $result;
    }

    private function set_query_params($args, $not_delivered)
    {
      $query_param = "";
      if ($not_delivered) {
        $query_param .= $this->status_not_delivered ;
      }
      if ((isset($args['voting_id'])) && ( !empty($args['voting_id'])) && ( $args['voting_id'] !== "all") ) {
          $query_param .= " AND l.voting_id = " . $args['voting_id'];
      }
      if ((isset($args['voter_id'])) && ( !empty($args['voter_id']))) {
          $query_param .= " AND l.voter_id = \"" . $args['voter_id'] ."\"";
      }
      if ((isset($args['time_from'])) && ( !empty($args['time_from']))) {
          $query_param .= " AND l.issued_time  >= \"" . $args['time_from'] ."\"";
      }
      if ((isset($args['time_to'])) && ( !empty($args['time_to']))) {
          $query_param .= " AND l.issued_time <= \"" . $args['time_to'] ."\"";
      }

      return $query_param;

    }
}
