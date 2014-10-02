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
class StaticPage extends CI_Model {
    const TABLE_NAME = "static_page";
    const INFORMATION_PAGE = "informations";
    
    /**
     * Updates or create the informations if they don't exist
     * @param unknown $informations
     */
    public function update_informations($informations) {
        
        $data = array(
                'content' => $informations,
                'page_name' => self::INFORMATION_PAGE
        );
        
        // Then look for the information page
        $this->db->from(self::TABLE_NAME);
        $this->db->where('page_name',self::INFORMATION_PAGE);
        
        // The page exists
        if ($this->db->count_all_results() === 1) {
            $this->db->update(self::TABLE_NAME, $data);
        } else {            
            $this->db->insert(self::TABLE_NAME, $data);
        }
    }
    
    /**
     * Gets the content of the information page
     * @return string the html content to display
     */
    public function get_informations() {
        $this->db->select('content');
        $this->db->where('page_name',self::INFORMATION_PAGE);
        $query = $this->db->get(self::TABLE_NAME);
        return count($query->result_array()) === 1 ? $query->result_array()[0]['content'] : '';
    }
    
    public function get_information_page_id() {
        $this->db->select('id');
        $this->db->where('page_name',self::INFORMATION_PAGE);
        $query = $this->db->get(self::TABLE_NAME);
        return count($query->result_array()) === 1 ? $query->result_array()[0]['id'] : '';
    }
}