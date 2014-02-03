<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Overtime
 *
 *
 * @package	Time_manager
 * @author	ten.mohm
 */
class Overtime extends CI_Model
{
    const TABLE_NAME = "overtime";
	/**
	 * @param $user_id 
	 * @return int amount of overtime in minutes
	 */	
	function get_overtime($user_id) {
		
		$overtime = NULL;
		
		if (!empty($user_id)) 
		{
			$this->db->order_by("date", "asc");
			$this->db->where("user_id", $user_id);
			$query = $this->db->get(Overtime::TABLE_NAME);
        	$overtime = $query->result_array();
		}
		else {
			log_message('error', "User id vide");
		}
		return $overtime;
	}
    
	/**
	 * @param $amount int amount of overtime in minutes
	 * @param $user_id 
	 */	
	function set_overtime($amount, $user_id, $date) {
        
		if (!empty($user_id)) 
		{
            $date = date("Y-m-01", strtotime($date));
        
            // Deletes the previous overtime for the month if the export failed (for example)
            $this->db->where("user_id", $user_id);
            $this->db->where("date", $date);
            $this->db->from(Overtime::TABLE_NAME);
			$number = $this->db->count_all_results();
            
            if ( $number > 0) {
                $this->db->where("user_id", $user_id);
                $this->db->where("date", $date);
                $this->db->delete(Overtime::TABLE_NAME); 
            }
        
            $data = array(
                    'amount' => $amount,
                    'date' => $date,
                    'user_id' => $user_id
                    );

            $this->db->insert(Overtime::TABLE_NAME, $data); 
		}
		else {
			log_message('error', "User id vide");
		}
    }
}
	