<?php
if (! defined ( 'BASEPATH' ))
    exit ( 'No direct script access allowed' );

/**
 * Time_manager
 *
 * Authentication library for Code Igniter.
 *
 * @package Time_manager
 * @author 10MOhm
 */
class Time_manager {
    function __construct() {
        $this->ci = & get_instance ();
        $this->ci->load->database ();
        $this->ci->load->model ( 'time_manager/checks' );
        $this->ci->load->model ( 'time_manager/overtime' );
        $this->ci->load->model ( 'time_manager/parameters' );
        $this->ci->load->helper ( 'time_manager_helper' );
    }
    
    /**
     * Determines if user checked in
     *
     * @param $user_id
     * @return boolean true if last check is a check in false otherwise
     */
    public function is_user_checked_in($user_id) {
        return $this->ci->checks->get_last_check ( $user_id )['check_in'];
    }
    
    /**
     * Determines if the user has to export his checks
     *
     * @param $last_check
     * @return boolean true if last check was last month
     */
    public function is_export_needed($last_check) {
        $result = FALSE;
        
        if (isset ( $last_check )) {
            $last_month = ( int ) date ( 'm', strtotime ( $last_check['date'] ) );
            $this_month = ( int ) date ( 'm' );
            $result = $last_month != $this_month;
        }
        return $result;
    }
    
    /**
     * Checks the user in
     *
     * @param $user_id
     */
    public function check($user_id) {
        // The check type is out if the last check is in and vice versa
        $is_check_in = ! $this->is_user_checked_in ( $user_id );
        $this->ci->checks->create ( $is_check_in, $user_id );
    }
    
    /**
     * Calculates the statistics of the user based on his punches
     *
     * @param integer user_id the user's id
     * @param boolean $minimal True if the stats are ment to be calculated for the 'all page action'
     * @return array the stats array
     */
    public function calculate_stats($user_id, $minimal = FALSE) {
        $checks = $this->ci->checks->get_checks ( $user_id );
        $working_time = $this->ci->parameters->get_working_time ( $user_id );
        $overtime = $this->ci->overtime->get_overtime ( $user_id );
        return $this->_calculate_stats($checks, $overtime, $working_time);
    }
    
    /**
     * Subfunction of calculate_stats, added for unit testing 
     *
     * @param array $checks user's checks
     * @param array $overtime user's overtime of other months
     * @param integer $working_time user's work day length
     * @param boolean $minimal True if the stats are ment to be calculated for the 'all page action'
     * @return array the stats array
     */
    public function _calculate_stats($checks, $overtime, $working_time, $minimal = FALSE) {
        
        $time_spent = calculate_time_spent ( $checks );
        $days = count_days ( $checks );
        
        $stats = array ();
        // Today's stats
        $stats['time_spent_t'] = $time_spent['day'];
        $stats['time_left_t'] = calculate_time_left ( $stats['time_spent_t'], $working_time );
        $stats['end_time'] = calculate_end_time ( $stats['time_left_t'] );
        $stats['ratio'] = $working_time > 0 ? $stats['time_spent_t'] / $working_time : 0;
        
        $stats['time_left'] = duration_to_string ( $stats['time_left_t'] );
        $stats['time_spent'] = duration_to_string ( $stats['time_spent_t'] );
        
        if ($minimal) {
            unset($time_spent['week']);
            unset($time_spent['day']);
        }
        
        // Period stats
        foreach ( array_keys ( $time_spent ) as $period ) {
            /*
             * Calulations
             */
            $stats['periods'][$period]['time_spent_t'] = $time_spent[$period];
            $time_spent_cheated = $time_spent[$period];
            $days_worked_cheated = $days[$period];
            if ($period == 'day') {
                $days_worked_cheated = 1;
            } else if ($time_spent['day'] != 0) {
                $time_spent_cheated += $stats['time_left_t'];
            }
            
            $stats['periods'][$period]['days_worked'] = $days[$period];
            $stats['periods'][$period]['overtime_t'] = calculate_overtime ( 
                    $time_spent_cheated, $working_time, $days_worked_cheated );
            if ($period == 'day') {
                $stats['periods'][$period]['end_time'] = calculate_end_time ( 
                    - $stats['periods'][$period]['overtime_t'] );
            } else {
                $stats['periods'][$period]['end_time'] = date("H:i:s", strtotime($stats['end_time']) - $stats['periods'][$period]['overtime_t']);
            }
            
            /*
             * To string operations
             */
            $stats['periods'][$period]['time_spent'] = duration_to_string ( 
                    $stats['periods'][$period]['time_spent_t'], $working_time );
            $stats['periods'][$period]['overtime'] = duration_to_string ( 
                    $stats['periods'][$period]['overtime_t'], $working_time );
        }
        
        // Adds an "all" period that includes overtime for the previous months
        $last_overtime = 0;
        
        if (isset ( $overtime ) && count ( $overtime ) > 0) {
            $last_overtime = $overtime[count ( $overtime ) - 1]['amount'];
        }
        /*
         * Calulations
        */
//         $stats['periods']['all'] = $stats['periods']['month'];
        $stats['periods']['all']['overtime_t'] += $last_overtime;
        $stats['periods']['all']['end_time'] = date("H:i:s", strtotime($stats['end_time']) - $stats['periods']['all']['overtime_t']);
        
        /*
         * To string operations
        */
        $stats['periods']['all']['overtime'] = duration_to_string ( $stats['periods']['all']['overtime_t'],
                $working_time );
        $stats['periods']['all']['time_spent'] = duration_to_string ( $stats['periods']['all']['time_spent_t'],
                $working_time );
        
        // Overtime evolution
        $stats['overtime_evolution'] = overtime_to_chart_array ( $overtime, $working_time );
        
        return $stats;
    }
    
    /**
     * Gets all the checks from the db and rearranges them in a manageable way
     *
     * @param number $user_id the user's id
     */
    public function get_all_checks($user_id) {
        $checks = $this->ci->checks->get_checks ( $user_id );
        return db_to_form_checks ( $checks );
    }
    public function get_db_checks($user_id) {
        return $this->ci->checks->get_checks ( $user_id );
    }
    public function update_checks($checks, $checks_to_add, $ids_to_delete, $user_id) {
        log_message ( 'debug', 'Library : update_checks, checks : ' . print_r ( $checks, TRUE ) );
        log_message ( 'debug', 'Library : update_checks, ids to delete : ' . print_r ( $ids_to_delete, TRUE ) );
        $this->ci->checks->update_checks ( form_to_db_checks ( $checks ), 
                prepare_checks_to_add_for_db ( $checks_to_add ), $ids_to_delete, $user_id );
    }
    
    /**
     * Gets the preferences of the user
     * It converts the preferences in the db to a readable format for the app
     *
     * @param integer $user_id the user's id
     * @return array the preferences array of the user
     */
    public function get_preferences($user_id) {
        $parameters = $this->ci->parameters->get_parameters ( $user_id );
        $preferences = duration_to_preferences ( @$parameters['working_time'] );
        $preferences['stats_period'] = @$parameters['stats_period'];
        return $preferences;
    }
    
    /**
     * Save the preferences of the user
     * Transforms the preferences to a storable format in the db
     *
     * @param array $preferences the preferences array
     * @param integer $user_id the user's id
     */
    public function save_preferences($preferences, $user_id) {
        // Convert the 'working time' array from preferences to a duration (in minutes)
        $duration = preferences_to_duration ( $preferences );
        $this->ci->parameters->set_parameters ( $duration, $user_id );
    }
    public function get_data_info($user_id) {
        $today = time ( "today 00:00:00" );
        $beginning_month = strtotime ( date ( '01-m-Y' ) );
        $end_of_month = strtotime ( date ( 't-m-Y' ) );
        $diff = ( int ) (($today - $beginning_month) / (24 * 3600));
        $total = ( int ) (($end_of_month - $beginning_month) / (24 * 3600));
        
        return array ('number_of_checks' => $this->ci->checks->count_checks ( $user_id ),
                'percent_month' => ( int ) ($diff * 100 / $total) 
        );
    }
    
    /**
     * A method that should be called on all pages.
     * Determines wether the user has
     * to export his check. Determines wether the user is checked in.
     *
     * @param number $user_id the user's id
     * @return array reed the method
     */
    public function all_pages_action($user_id) {
        $last_check = $this->ci->checks->get_last_check ( $user_id );
        $working_time = $this->ci->parameters->get_working_time( $user_id );
        
        // Minimal calculation of the stats
        $stats = $this->calculate_stats($user_id, TRUE);
        $overtime = $stats['time_left_t'] - $stats['periods']['all']['overtime_t'] > 0 ? FALSE : TRUE;
        $overtime_absolute = $working_time > 0 ? $stats['time_spent_t'] > $working_time : FALSE;
        
        return array ('is_user_checked_in' => $last_check['check_in'] ? TRUE : FALSE,
                'is_export_needed' => $this->is_export_needed ( $last_check ),
                'is_overtime_filled' => $working_time != NULL && count($working_time) > 0 ? TRUE : FALSE,
                'ratio' => $stats['ratio'] < 1 ? $stats['ratio'] * 100 : ($stats['ratio'] - 1) * 100,
                'overtime' => $overtime,
                'overtime_absolute' => $overtime_absolute
        );
    }
    
    public function get_csv_export($user_id) {
        $checks = $this->ci->checks->get_checks ( $user_id );
        
        if (count($checks) > 0) {
            $stats = $this->calculate_stats($user_id, TRUE);
            
            // Here we save the overtime amount in the db right away to prevent 
            // calculation of the stats elsewhere
            $this->ci->overtime->set_overtime($stats['periods']['all']['overtime_t'], $user_id, $checks[count($checks) - 1]['date']);
            
        	$month = date('F-Y', strtotime($checks[count($checks) - 1]['date']));
            
            $data = checks_to_csv($checks, $stats);
	        $name = $month.'.csv';
	        $raw_data = $checks;
	        $raw_name = $month.'.raw.csv';
            
            return array(
                'month' => $month,
                'name' => $name,
                'data' => $data,
                'raw_name' => $raw_name,
                'raw' => $raw_data
            );
        }
        else {
            return NULL;
        }
    }
    
    public function clean_after_export($user_id) {
        $checks = $this->ci->checks->clean_checks ( $user_id );
    }
}

/* End of file Time_manager.php */
/* Location: ./application/libraries/Time_manager.php */
