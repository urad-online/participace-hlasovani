<?php
class PbVote_GetCode
{
    private $code = "" ;
    private $code_length = 10;
    private $expiration_hrs = 1;
    private $table_name = 'pb_register' ;
    private $initial_status = 'new';
    private $voting_id, $voter_it;
    private $issued_time, $expiration_time;
    private $output;

    public function __construct()
    {
        $this->code = "";
        global $wpdb;
        $this->db = $wpdb;
    }

    public function get_code( $input = null )
    {

        $this->voting_id = $input['voting_id'];
        $this->voter_id = $input['voter_id'];

        $issue      =  current_time( 'timestamp', 0 );
        $expiration = $issue + 60*60*intval($this->expiration_hrs);

        $this->issued_time      = date( 'Y-m-d H:i:s', $issue);
        $this->expiration_time  = date( 'Y-m-d H:i:s', $expiration);;

        if ($this->check_new_voter() ) {

            $this->code = $this->generate_code( $this->code_length ) ;

            $this->save_code();
        }

        return  $this->output ;
    }

    private function generate_code( $code_length )
    {
        return bin2hex(random_bytes( $code_length));
    }

    private function save_code()
    {

        $sql_comm = $this->db->prepare( 'INSERT INTO '.$this->db->prefix . $this->table_name
            . ' (voting_id, voter_id, registration_code, issued_time, expiration_time, status)
            VALUES ( %d, %s, %s, %s, %s, %s)',
                intval( $this->voting_id ),
                $this->voter_id ,
                $this->code,
                $this->issued_time,
                $this->expiration_time,
                $this->initial_status
                );

        $result = $this->db->query( $sql_comm );
        if ($result) {
            $this->output = array( "result" => "ok", "code" => $this->code, 'expiration_time' => $this->expiration_time, );
        } else {
            $this->output = array( "result" => "error", "message" => 'Chyba při ukládání kódu',);
        }
        // return $output;
    }
    private function check_new_voter()
    {
        if (! $this->check_voter_id()) {
            $this->output = array( "result" => "error", "message" => 'Chyba kontroly registračního ID  - špatný formát',);
            return false;
        }

        if ( $voter_rec = $this->get_voter_by_id( $this->voting_id, $this->voter_id ) ) {
            return $this->check_code_expiration( $voter_rec );
        } else {
            return true;
        }
    }

    private function get_voter_by_id( $voting_id, $voter_id)
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
        $phone_regexp = '/^(\+420)? ?[1-9][0-9]{2} ?[0-9]{3} ?[0-9]{3}$/';

        $id = trim( $this->voter_id );

        if ( preg_match($phone_regexp, $id) ) {
            $id = str_replace( ' ', '', $id);
            if (strlen( $id ) === 9 ) {
                $id = '+420'.$id;
            }
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
            $this->output = array( "result" => "error", "message" => 'ID '.$this->voter_id.' již hlasovalo',);
            return false;
        }
        if ( strtotime( $db_row->expiration_time) > strtotime( $this->issued_time) ) {
            $this->output = array( "result" => "error", "message" => 'ID '.$this->voter_id.' je již registrováno s platností do '.$db_row->expiration_time,);
            return false;
        } else {
            if ($db_row->status === 'new') {
                $this->set_voter_status( $db_row->id, 'expired' );
            }
            return true;
        }
    }
    private function set_voter_status ( $id, $status)
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
}
