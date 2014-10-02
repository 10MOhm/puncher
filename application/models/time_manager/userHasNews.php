<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Overtime
 *
 *
 * @package Time_manager
 * @author ten.mohm
 */
class UserHasNews extends CI_Model {
    const TABLE_NAME = "user_has_news";

    /**
     * Counts the news (information page for now) that user didn't read
     *
     * @param unknown $user_id the user id
     */
    public function count($user_id) {
        $this->db->select('number_of_changes');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get(self::TABLE_NAME);
        
        $unread = 0;
        foreach ( $query->result_array() as $row ) {
            $unread += intval($row['number_of_changes']);
        }
        
        return $unread;
    }

    /**
     * Adds unread inforamtions for all users
     * 
     * @param unknown $number_of_news number of unread informations
     * @param unknown $page_id the page id of the information page
     */
    public function add_unread_information($number_of_news, $page_id, $users) {
        
        $data = array ('number_of_changes' => $number_of_news,'static_page_id' => $page_id);
        $update_data = array ('number_of_changes' => $number_of_news);
        
        foreach ($users as $user) {
            // Then look for the information page
            $this->db->from(self::TABLE_NAME);
            $this->db->where('static_page_id', $page_id);
            $this->db->where('user_id', $user['id']);
            
            // The page exists
            if ($this->db->count_all_results() === 1) {
                $this->db->update(self::TABLE_NAME, $update_data);
            } else {
                $data['user_id'] = $user['id'];
                $this->db->insert(self::TABLE_NAME, $data);
            }    
        }
    }

    /**
     * Deletes all the unread informations for the speicified user
     * 
     * @param unknown $user_id
     */
    public function read_informations($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->delete(self::TABLE_NAME);
    }
}