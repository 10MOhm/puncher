<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once("periods.php");

/**
 * Parameters
 *
 *
 * @package	Time_manager
 * @author	ten.mohm
 */
class Parameters extends CI_Model
{	
    const TABLE_NAME = "parameters";
    
    public function get_working_time($user_id) {
        $result = NULL;

        if (isset($user_id)) {
            $this->db->select('working_time');
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Parameters::TABLE_NAME, 1, 0);
            if ($query->num_rows() == 1) $result = $query->row()->working_time;
        } 
        else {
            log_message('error', "User id vide");
        }

        // Working time is stored in minutes no in seconds to save space
        return $result * 60;
    }

    /*
     * Gets the preferences of the user from the db
     * @param user_id the user's id
     */
    public function get_parameters($user_id) {
        $result = NULL;

        if (isset($user_id)) {
            $this->db->select('working_time');
            $this->db->select('stats_period');
            $this->db->where("user_id", $user_id);
            $query = $this->db->get(Parameters::TABLE_NAME, 1, 0);
            if ($query->num_rows() == 1) $result = $query->row_array();
        } 
        else {
            log_message('error', "User id vide");
        }

        return $result;
    }

    /*
     * Saves in db the preferences of the user
     *
     * @param working_time working time duration in minutes
     * @param user_id the user's id
     * @param stats_period the period on which the stats should be calulated
     *  default is ALL_TIME
     */
    public function set_parameters($working_time, $user_id, $stats_period = Periods::ALL_TIME) {
        if (isset($working_time) && isset($user_id)) {
            $data = array(
                        'working_time' => $working_time,
                        'stats_period' => $stats_period
                        );

            $this->db->where('user_id',$user_id);
            $this->db->from(Parameters::TABLE_NAME);
            if ($this->db->count_all_results() == 0) {
                $data['user_id'] = $user_id;
                $this->db->insert(Parameters::TABLE_NAME, $data);
            }
            else {
                $this->db->update(Parameters::TABLE_NAME, $data, array('user_id' => $user_id));
            }
        }
    }
}

