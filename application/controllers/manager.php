<?php
if (! defined('BASEPATH'))
    exit('No direct script access allowed');

require_once ("navItems.php");

class Manager extends CI_Controller {

    public function __construct() {
        parent::__construct();
        
        $this->load->spark('Twiggy/0.8.5');
        $this->load->helper('url');
        $this->load->helper('time_manager_helper');
        $this->load->library('tank_auth');
        $this->load->library('time_manager');
        $this->load->library('session');
        $this->load->library('form_validation');
        
        // Register available functions
        $this->twiggy->register_function('form_error');
        $this->twiggy->register_function('validation_errors');
        $this->twiggy->register_function('set_value');
        $this->twiggy->register_function('has_errors');
        $this->twiggy->register_function('no_slash');
        $this->twiggy->register_function('no_slash_to_xdate');
        $this->twiggy->register_function('phpinfo');
        $this->twiggy->register_function('floor');
    }

    /**
     * Pre controller hook that checks if the user is logged in
     */
    private function _pre_action($page = null) {
        if (! $this->tank_auth->is_logged_in()) {
            redirect('/auth/login/');
        } else {
            $this->all_pages_action($page);
            $this->twiggy->set("active", $page);
            $this->twiggy->set("navigation_items", NavItems::LoggedIn());
        }
    }

    private function all_pages_action($page) {
        $data = $this->time_manager->all_pages_action($this->tank_auth->get_user_id());
        
        if ($data['is_user_checked_in'] && $data['is_export_needed'] && $page != "punches") {
            redirect('/punches/add_check');
        }
        if ($data['is_export_needed'] && ! $data['is_user_checked_in'] && $page != "data" && $page != "export") {
            redirect('/data/export_needed');
        }
        if (! $data['is_overtime_filled'] && $page != "preferences") {
            redirect('/preferences/fill');
        }
        
        $checked_in = $data['is_user_checked_in'] ? "checked_in" : "";
        $overtime = $data['overtime'] ? "overtime" : "";
        $overtime_absolute = $data['overtime_absolute'] ? "overtime_absolute" : "";
        $this->twiggy->set("checked_in", $checked_in, $global = FALSE);
        $this->twiggy->set("ratio", $data['ratio'], $global = FALSE);
        $this->twiggy->set("overtime", $overtime, $global = FALSE);
        $this->twiggy->set("overtime_absolute", $overtime_absolute, $global = FALSE);
        $this->twiggy->set("unread_messages", $data['unread_messages'], $global = FALSE);
    }

    /**
     * Stats screen
     */
    public function stats() {
        $this->_pre_action(__FUNCTION__);
        
        $stats = $this->time_manager->calculate_stats($this->tank_auth->get_user_id());
        $this->twiggy->set('stats', $stats);
        $this->twiggy->template('stats')->display();
    }

    /**
     * Preferences screen
     */
    public function preferences($fill = NULL) {
        $this->_pre_action(__FUNCTION__);
        
        if (isset($fill)) {
            $this->twiggy->set('must_fill', $fill, NULL);
        }
        
        if ($this->input->post()) 
        
        {
            $this->load->helper(array ('form','url' ));
            
            if ($this->form_validation->run('preferences') == TRUE) {
                $preferences = $this->fill_preferences_array();
                $this->time_manager->save_preferences($preferences, $this->tank_auth->get_user_id());
                $preferences['success'] = TRUE;
            } else {
                $preferences = $this->fill_preferences_array();
            }
        } else 
        
        {
            $preferences = $this->time_manager->get_preferences($this->tank_auth->get_user_id());
        }
        
        $this->twiggy->set($preferences, NULL);
        $this->twiggy->template('preferences')->display();
    }

    /**
     * Punches screen
     *
     * @param fix_checks if an export is needed and the last check is a check in, then the user
     * has to fix his checks in order for the export to continue
     */
    public function punches($options = NULL) {
        $this->_pre_action(__FUNCTION__);
        
        if (isset($options) && $options == 'add_check') {
            $this->twiggy->set('add_check', '', NULL);
        }
        
        $checks_to_display = $this->time_manager->get_all_checks($this->tank_auth->get_user_id());
        
        if ($this->input->post()) {
            
            $prevalidation = TRUE;
            $ids_to_delete = array ();
            $checks_to_update = $checks_to_display;
            $checks_to_add = array ();
            
            $new_checks = array ();
            
            /*
        	 * Foreach field, set code igniter validation rules and update the checks array
        	 */
            foreach ( $this->input->post() as $field_name => $field_value ) {
                
                // The field name should look like this : 25102013_minute_25 (mmddyyyy)_hour_key
                $parts = explode("_", $field_name);
                $key = NULL;
                $delete = FALSE;
                $is_new = FALSE;
                
                /*
                 * Prevalidation and preformatting of the array in case of added punches
                 */
                if ($this->is_check_field_name_ok($parts)) {
                    /*
                     * Map all input to a check array
                     */
                    if (! isset($new_checks[to_slash($parts[0])])) {
                        $new_checks[to_slash($parts[0])] = array ();
                    }
                    $new_checks[to_slash($parts[0])][$parts[2]][$parts[1]] = $field_value;
                }
                
                /*
                 * Validation rules and checks' array modifs
                 */
                if (preg_match("/minute/", $field_name)) {
                    $this->form_validation->set_rules($field_name, "Minutes", "less_than[60]|greater_than[-1]");
                    $key = 'minute';
                } else if (preg_match("/hour/", $field_name)) {
                    $this->form_validation->set_rules($field_name, "Heures", "less_than[24]|greater_than[-1]");
                    $key = 'hour';
                } else if (preg_match("/delete/", $field_name)) {
                    $delete = TRUE;
                }
            }
            
            $checks_to_update = array ();
            $checks_to_add = array ();
            $ids_to_delete = array ();
            
            /*
        	 * Split checks into adds, updates, deletes
        	 */
            foreach ( $new_checks as $day_key => &$day ) {
                foreach ( $day as $key => &$check ) {
                    if (isset($checks_to_display[$day_key]) && isset($checks_to_display[$day_key][$key])) {
                        /*
                         * Update or delete
                         */
                        $check['id'] = $checks_to_display[$day_key][$key]['id'];
                        $check['user_id'] = $this->tank_auth->get_user_id();
                        // 2014-03-10 21:33:00
                        $check['date'] = date('y-m-d H:i:s', 
                                strtotime(
                                        french_to_international_date($day_key) . ' ' . $check['hour'] . ':' .
                                                 $check['minute']));
                        
                        if (isset($check['delete'])) {
                            $ids_to_delete[] = $check['id'];
                        } else {
                            $checks_to_update[] = $check;
                        }
                    } else {
                        /*
                         * Add
                         */
                        $check['user_id'] = $this->tank_auth->get_user_id();
                        // 2014-03-10 21:33:00
                        $check['date'] = date('y-m-d H:i:s', 
                                strtotime(
                                        french_to_international_date($day_key) . ' ' . $check['hour'] . ':' .
                                                 $check['minute']));
                        if (! isset($check['delete'])) {
                            $checks_to_add[] = $check;
                        }
                    }
                }
            }
            
            /*
             * Validate and save the punches
             */
            if ($this->form_validation->run() == TRUE && $prevalidation == TRUE) {
                $this->time_manager->update_checks($checks_to_update, $checks_to_add, $ids_to_delete, 
                        $this->tank_auth->get_user_id());
                $this->twiggy->set('success', TRUE);
            }
            
            $checks_to_display = $this->time_manager->get_all_checks($this->tank_auth->get_user_id());
            
            // Updates the check in status after the datas have been saved (in case a check in or check out 
            // has been added for today)
            $this->all_pages_action(__FUNCTION__);
        }
        $this->twiggy->set('checks', $checks_to_display, NULL);
        $this->twiggy->template('punches')->display();
    }

    /**
     * Checks if the result of an explode on check's field name is well formed
     *
     * @param unknown $parts the result of an explode on check's field name
     * @return boolean true is parts are something like 24102013, anything, 33
     */
    private function is_check_field_name_ok($parts) {
        return count($parts) == 3 && strlen($parts[0]) == 8 && is_numeric($parts[0]) && is_numeric($parts[2]) &&
                 $parts[2] >= 0;
    }

    /**
     * Checks if the check which field name is given exists in the checks' array
     * or if it was added through the UI
     *
     * @param array $parts the result of an explode on check's field name
     * @param array $checks the checks known
     * @return boolean true or false
     */
    private function is_new_check($parts, $checks, $checks_to_add) {
        // Either the check doesn't exists yet or it exists in the checks to add array
        return (! (isset($checks[to_slash($parts[0])]) and isset(
                $checks[to_slash($parts[0])][$parts[2]])) and isset($checks[to_slash($parts[0])][$parts[2] - 1])) or
                 (isset($checks_to_add[to_slash($parts[0])]) and
                 isset($checks_to_add[to_slash($parts[0])][$parts[2]])) or ! isset($checks[to_slash($parts[0])]);
    }

    /**
     * Utility function to fill the preferences array based on the data of the form
     *
     * @return array the preferences array to be stored in the db
     */
    private function fill_preferences_array() {
        return array ('hours' => set_value('hours'),'minutes' => set_value('minutes') );
    }

    /**
     * The punch function, supposed to redirect to the last screen
     */
    public function punch() {
        $this->_pre_action();
        $this->time_manager->check($this->tank_auth->get_user_id());
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function data($export_needed = NULL) {
        $this->_pre_action(__FUNCTION__);
        
        if (isset($export_needed)) {
            $this->twiggy->set('export_needed', $export_needed, NULL);
        }
        
        $data_info = $this->time_manager->get_data_info($this->tank_auth->get_user_id());
        $this->twiggy->set('data_info', $data_info, NULL);
        $this->twiggy->template('data')->display();
    }

    public function export() {
        $this->_pre_action(__FUNCTION__);
        
        // Calculate the csvs to edit
        $export = $this->time_manager->get_csv_export($this->tank_auth->get_user_id());
        
        if ($export == null) {
            $this->add_error_for_user(
                    "Vos pointages n'ont pas pu être exportés, une erreur irrécupérable s'est produite. " .
                             "Vos pointages n'ont pas été perdus, contactez l'administrateur du site.");
            log_message('error', 
                    "Erreur irrécupérable lors de l'export (exports null) pour l'utilisateur " +
                             $this->tank_auth->get_user_id());
        } else {
            // Format to csv
            $this->load->helper('csv');
            $formated_checks = "";
            $raw_checks = "";
            
            // If the data is false, then the checks were somehow corrupted, then we only do the raw export
            if ($export['data']) {
                $formated_checks = array_to_csv($export['data']);
            } else {
                $this->add_error_for_user(
                        "Vos pointages n'ont pas pu être correctement interprétés, ils sont probablement corrompus." .
                                 " L'export des heures supplémentaires n'a pas pu être correctement effectué." .
                                 " Vous avez néanmoins la liste de vos pointages si vous souhaitez recalculer vos indicateurs.");
                log_message('error', 
                        "L'export des pointages a échoué pour l'utilisateur " + $this->tank_auth->get_user_id());
            }
            // Save the raw checks anyway for later use
            $raw_checks = array_to_csv($export['raw']);
            
            // Delete all the checks
            $this->time_manager->clean_after_export($this->tank_auth->get_user_id());
            
            // Pack everything in a zip
            $this->load->library('zip');
            $this->zip->add_data($export['name'], $formated_checks);
            $this->zip->add_data($export['raw_name'], $raw_checks);
            $this->zip->download($export['month'] . '.zip');
        }
    }

    private function add_error_for_user($error) {
        // TODO ajouter un message d'erreur pour l'utilisateur
    }

    public function account() {
        $this->_pre_action(__FUNCTION__);
        $this->twiggy->template('account')->display();
    }

    public function info() {
        $informations = $this->time_manager->get_informations($this->tank_auth->get_user_id());
        $this->twiggy->set("navigation_items", NavItems::NotLoggedIn());
        $this->twiggy->set("informations", $informations);
        
        // If User logged in, special navigation items and possibility to edit the informations
        if ($this->tank_auth->is_logged_in()) {
            
            $is_admin = $this->tank_auth->get_user_id() == 1;
            
            $this->twiggy->set("navigation_items", NavItems::LoggedIn());
            $this->twiggy->set("admin_mode", $is_admin ? "admin_mode" : "");
            
            // Edit the informations
            if ($is_admin && $this->input->post() && $this->form_validation->run('infos') == TRUE) {
                $changes = $this->input->post("changes-number", TRUE);
                $informations = $this->input->post("infos", TRUE);
                $informations = $this->time_manager->update_informations($informations,$changes);
                redirect('/info');
            }
        }
        
        $this->twiggy->template('info')->display();
    }
}
