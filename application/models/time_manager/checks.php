<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once("periods.php");

/**
 * Checks
 *
 *
 * @package	Time_manager
 * @author	ten.mohm
 */
class Checks extends CI_Model
{
    const TABLE_NAME = "checks";	

    /**
     * Gets all the checks of a user for a certain period of time.
     * @param $user_id
     * @param $time_period
     * @return an array of checks
     */
    public function get_checks($user_id, $time_period = Periods::ALL_TIME) {
        $checks = NULL;
        if (!empty($user_id)) {
            $this->db->order_by("date", "asc");
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Checks::TABLE_NAME);
            $checks = $query->result_array();
        }
        else {
            log_message('error', "User id vide");
        }
        return $checks;
    }

    /**
     * Returns the last check value
     * @param $user_id
     * @return array last check (NULL if not found)
     */
    public function get_last_check($user_id) {
        $result = NULL;
        if (!empty($user_id)) {
            $this->db->order_by("date", "desc");
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Checks::TABLE_NAME,1,0);
            if ($query->num_rows() == 1) $result = $query->result_array()[0];
        }
        else {
            log_message('error', "User id vide");
        }
        return $result;
    }
    
    public function get_first_check($user_id) {
        $result = NULL;
        if (!empty($user_id)) {
            $this->db->order_by("date", "asc");
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Checks::TABLE_NAME,1,0);
            if ($query->num_rows() == 1) $result = $query->result_array()[0];
        }
        else {
            log_message('error', "User id vide");
        }
        return $result;
    }
    
    public function update_checks($checks_to_update, $checks_to_add, $ids_to_delete, $user_id) {
        
        // Delete
        if (isset($ids_to_delete) and count($ids_to_delete) > 0) {
            $this->db->where_in('id', $ids_to_delete);
            $this->db->delete(Checks::TABLE_NAME);
        }
        
        // Insert
        if (isset($checks_to_add) and count($checks_to_add) > 0) {
            $this->db->insert_batch(Checks::TABLE_NAME, $checks_to_add);
        }
        
        // Update
        if (isset($checks_to_update) and count($checks_to_update) > 0) {
    	   $this->db->update_batch(Checks::TABLE_NAME, $checks_to_update, 'id');
        }
    }
    public function delete_checks($ids) {}
    
    /**
     * Creates a check in for the user specified
     * @param unknown $user_id the user's id
     */
    public function create($user_id) {
    
        if (!empty($user_id)) {
            $data = array(
                    'date' => date("Y-m-d H:i:s"),
                    'user_id' => $user_id
                    );

            $this->db->insert(Checks::TABLE_NAME, $data); 
        }
        else {
            log_message('error', "User id vide");
        }
    }
    
    /**
     * Counts all the user's checks
     * @param unknown $user_id
     * @return number
     */
    public function count_checks($user_id) {
    
    	$number_of_checks = 0;
        if (!empty($user_id)) {
            $this->db->where("user_id", $user_id);
        	$number_of_checks = $this->db->count_all_results(Checks::TABLE_NAME);
        }
        else {
            log_message('error', "User id vide");
        }
        
        return $number_of_checks;
    }
    
    /** 
     * @return a mysql date for today at midnight
     */
    private function today() {
    	return date("Y-m-d H:i:s" ,strtotime('today midnight'));
    }
    
    public function clean_checks($user_id) {
    
        if (!empty($user_id)) {
            $this->db->where("user_id", $user_id);
            $this->db->delete(Checks::TABLE_NAME); 
        }
        else {
            log_message('error', "User id vide");
        }
    }
}

