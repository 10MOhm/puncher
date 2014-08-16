<?php
$DATE_FORMAT = "d/m/Y";
$TIME_FORMAT = "H:i:s";

function has_errors($errors) {
    if ($errors != '') {
        return 'wrong';
    }
    return '';
}

function no_slash($var) {
    return str_replace("/", "", $var);
}

function to_slash($var) {
    return substr($var, 0, 2) . '/' . substr($var, 2, 2) . '/' . substr($var, 4, 4);
}

function no_slash_to_xdate($var) {
    return substr($var, 4, 4) . '-' . substr($var, 2, 2) . '-' . substr($var, 0, 2);
}

/*
 * ---------------------------------------------------------------------------
 * 
 * 									UTILS
 * 
 * ----------------------------------------------------------------------------
 */
function french_to_international_date($date) {
    $parts = explode("/", $date);
    if (count($parts) != 3) {
        return FALSE;
    }
    return $parts[2] . "/" . $parts[1] . "/" . $parts[0];
}

function preferences_to_duration($preferences) {
    $hours = $preferences['hours'];
    $minutes = $preferences['minutes'];
    return $hours * 60 + $minutes;
}

function duration_to_preferences($duration) {
    if (isset($duration)) {
        $hours = ( int ) ($duration / 60);
        $minutes = $duration % 60;
        return array ('hours' => $hours,'minutes' => $minutes );
    }
}

/**
 * Transforms a duration (number of seconds) into a well formated time string
 *
 * @param number $timestamp the duration to convert
 * @param number $working_time optional the time to be worked for a day,
 * used to calculate the time string in work days
 * @return string
 */
function duration_to_string($timestamp, $working_time = NULL) {
    $prefix = "";
    if ($timestamp < 0) {
        $timestamp *= - 1;
        $prefix = "- ";
    }
    
    $days = "";
    if (isset($working_time) && $working_time != 0) {
        $days = ( int ) ($timestamp / $working_time);
        
        if ($days > 0) {
            $days .= ' jours ';
            $timestamp -= $days * $working_time;
        } else {
            $days = "";
        }
    }
    
    $seconds = $timestamp;
    $minutes = ( int ) ($seconds / 60);
    $hours = str_pad(( int ) ($minutes / 60), 2, "0", STR_PAD_LEFT);
    $seconds = str_pad($seconds - $minutes * 60, 2, "0", STR_PAD_LEFT);
    $minutes = str_pad($minutes - $hours * 60, 2, "0", STR_PAD_LEFT);
    return $prefix . $days . $hours . ':' . $minutes . ':' . $seconds;
}

/**
 * Transforms a duration into a float representing workdays
 *
 * @param number $timestamp the duration to convert
 * @param number $working_time the time to be worked for a day,
 * used to calculate the time string in work days
 */
function duration_to_days($timestamp, $working_time) {
    return (( int ) ($timestamp * 100 / $working_time)) / 100;
}

function mysql_to_php_date($date) {
    $new = strtotime($date);
    return date("d/m/Y", $new);
}

function mysql_date_to_time_array($date) {
    $new = strtotime($date);
    return array ('hour' => date("H", $new),'minute' => date("i", $new) );
}

function string_to_stripped_date($string, $timestamp = NULL) {
    $timestamp = $timestamp == NULL ? time() : $timestamp;
    return date("Y-m-d", strtotime($string, $timestamp));
}

/*
 * ---------------------------------------------------------------------------
*
* 							PUNCHES SCREEN
*
* ----------------------------------------------------------------------------
*/

/**
 * Reorders the checks from the db to an array like so :
 * (
 * [22/10/2013] => Array
 * (
 * [0] => Array
 * (
 * [id] => 1
 * [user_id] => 1
 * [check_in] => 1
 * [date] => 2013-10-22 20:03:22
 * [hour] => 20
 * [minute] => 03
 * )
 *
 * [1] => Array
 * (
 * [id] => 2
 * [user_id] => 1
 * [check_in] => 0
 * [date] => 2013-10-22 20:03:42
 * [hour] => 20
 * [minute] => 03
 * )
 *
 * )
 *
 * @param unknown $checks
 */
function db_to_form_checks($checks) {
    $rearranged = array ();
    
    foreach ( $checks as $check ) {
        $check = array_merge($check, mysql_date_to_time_array($check['date']));
        $rearranged[mysql_to_php_date($check['date'])][] = $check;
    }
    
    return $rearranged;
}

/**
 * The same as db_to_form_checks but in reverse
 *
 * @param unknown $checks
 */
function form_to_db_checks($checks, $is_to_add = FALSE) {
    $rearranged = array ();
    
    foreach ( $checks as $check ) {
        // If we're preparing the checks to add array the id can't be present
        // (insert_batch won't work otherwise)
        if ($is_to_add) {
            unset($check['id']);
        }
        $rearranged[] = update_time($check);
    }
    
    return $rearranged;
}

/**
 * Prepares the checks to add for the insert in db (removes id and updates time)
 *
 * @param array the checks' array to update (db format)
 */
function prepare_checks_to_add_for_db($checks) {
    log_message('debug', 'prepare_checks_to_add_for_db, checks_to_add : ' . print_r($checks, TRUE));
    $checks = form_to_db_checks($checks, TRUE);
    return $checks;
}

/**
 * Updates the time of the checks set in the form in case their hour / minute changed
 *
 * @param $check the check to update
 */
function update_time($check) {
    $date = new DateTime($check['date']);
    $date->setTime($check['hour'], $check['minute']);
    $check['date'] = $date->format("Y-m-d H:i:s");
    unset($check['hour']);
    unset($check['minute']);
    return $check;
}

/*
 * ---------------------------------------------------------------------------
 *
 * 									DATA
 *
 * ----------------------------------------------------------------------------
 */

/**
 * Transforms db checks to a human readable csv
 *
 * @param unknown $checks ascending order
 * @param array $stats
 * @return unkown either the csv as an array or false if the user's checks are corrputed
 */
function checks_to_csv($checks, $stats) {
    
    // Set the headers
    $csv = array (
            [ "Date de check in","Date de check out","Heure de check in","Heure de check out","temps passé" ] );
    
    // If the last check is a check in, delete it, the user is already supposed to have fixed it
    if (is_user_checked_in($checks)) {
        array_shift($checks);
    }
    
    // Checks to CSV
    for($i = 0; $i < count($checks) - 1; $i += 2) {
        $first = $checks[$i];
        $second = $checks[$i + 1];
        
        $date_parts_1 = explode(" ", $first['date']);
        $date_parts_2 = explode(" ", $second['date']);
        $time_spent = strtotime($second['date']) - strtotime($first['date']);
        $time_spent = duration_to_string($time_spent);
        
        $csv[] = array ($date_parts_1[0],$date_parts_2[0],$date_parts_1[1],$date_parts_2[1],$time_spent );
    }
    
    $csv[] = array ('','','Jours travaillés','Heures supplémentaires','Temps passé' );
    $csv[] = array ('','',$stats['periods']['all']['days_worked'],$stats['periods']['all']['overtime'],
            $stats['periods']['all']['time_spent'] );
    
    return $csv;
}

/*
 * ---------------------------------------------------------------------------
 * 
 * 									STATS
 * 
 * ----------------------------------------------------------------------------
 */

/**
 * Calculates the time spent at work this day
 *
 * @param array $checks today's checks for the user
 * @return number the time spent in seconds
 */
function calculate_time_spent($checks) {
    
    // Reference dates
    $today = string_to_stripped_date("today");
    $a_week_ago = string_to_stripped_date("last monday", strtotime('tomorrow'));
    $a_month_ago = string_to_stripped_date("first day of this month");
    log_message('debug', 'Premier jour du mois : ' . $a_month_ago);
    
    // Times
    $time_today = 0;
    $time_week = 0;
    $time_month = 0;
    $time_all = 0;
    
    // Utils variables
    $last_check_out_time = strtotime('now');
    
    // Adds a check out if the last check is a check in,
    if (is_user_checked_in($checks)) {
        array_push($checks, array ('date' => date('Y-m-d H:i:s', strtotime('now')) ));
    }
    
    // The checks are run in reverse order, we calculate the time spent between a check out and a check in
    $checks = array_reverse($checks);
    foreach ( $checks as $index => $check ) {
        
        $time = strtotime($check['date']);
        
        // Since the checks are parsed in reversed order, if the index is even, it's a check out
        if ($index % 2 == 0) {
            $last_check_out_time = $time;
        } else {
            $diff = $last_check_out_time - $time;
            $date = string_to_stripped_date($check['date']);
            $last_date = $index > 0 ? string_to_stripped_date($checks[$index - 1]['date']) : NULL;
            
            // Calculates time spent based on the period
            if ($date === $today || $last_date === $today) {
                // Time for today or if the last check in was today and a check out (we calculate the time
                // over night)
                $time_today += $diff;
            } else if (($date < $today && $date >= $a_week_ago) || ($last_date >= $a_week_ago)) {
                // Time for the week or if the last check in was this week and a check out (we calculate 
                // the time over night)
                $time_week += $diff;
            } else if (($date < $a_week_ago && $date >= $a_month_ago) || ($last_date >= $a_month_ago)) {
                $time_month += $diff;
            } else {
                $time_all += $diff;
            }
        }
    }
    
    $time_week += $time_today;
    $time_month += $time_week;
    $time_all += $time_month;
    
    return array ('day' => $time_today,'week' => $time_week,'month' => $time_month,'all' => $time_all );
}

/**
 * Simple function to determine if the user is checked in
 *
 * @param unknown $checks
 */
function is_user_checked_in($checks) {
    return count($checks) % 2 != 0;
}

/**
 * Calculates the user's overtime
 *
 * @param number $time_spent the time already spent in seconds
 * @param number number $working_time total time to spend at work for a day
 * @return number the overtime in seconds
 */
function calculate_overtime($time_spent, $working_time, $days) {
    $regular_total_worktime = $working_time * $days;
    log_message('debug', 'Temps de travail normal : ' . $regular_total_worktime . " ($working_time * $days)");
    $overtime = $time_spent - $regular_total_worktime;
    log_message('debug', 'Heures supp : ' . $overtime . " ($time_spent - $regular_total_worktime)");
    if ($days == 0) {
        $overtime = 0;
    }
    return $overtime;
}

/**
 * Counts the number of days where at least one check occured
 *
 * @param unknown $checks the checks array (db format) to parse
 */
function count_days($checks) {
    
    // Reference dates
    $today = string_to_stripped_date("today");
    $a_week_ago = string_to_stripped_date("last monday", strtotime('tomorrow'));
    $a_month_ago = string_to_stripped_date("first day of this month");
    log_message('debug', 'Premier jour du mois : ' . $a_month_ago);
    
    $periods = array ('day' => 0,'week' => 0,'month' => 0,'all' => 0 );
    
    $last_date = NULL;
    foreach ( $checks as $check ) {
        $date = explode(" ", $check['date']);
        
        if (count($date) > 0 && $date[0] != $last_date) {
            $last_date = $date[0];
            
            // Calculates days worked based on the period
            if ($date[0] == $today) {
                $periods['day'] ++;
            } else if ($date[0] < $today && $date[0] >= $a_week_ago) {
                $periods['week'] ++;
                log_message('debug', 'Week days : ' . $periods['week']);
            } else if ($date[0] < $a_week_ago && $date[0] >= $a_month_ago) {
                $periods['month'] ++;
            } else {
                $periods['all'] ++;
            }
        }
    }
    
    $periods['week'] += $periods['day'];
    $periods['month'] += $periods['week'];
    $periods['all'] += $periods['month'];
    
    return $periods;
}

/**
 * Calcultates the time left to work this day
 *
 * @param number $time_spent the time already spent in seconds
 * @param number $working_time total time to spend at work for a day
 * @return number the time left to spend at work
 */
function calculate_time_left($time_spent, $working_time) {
    return $working_time - $time_spent;
}

/**
 * Calculates the time to leave work
 *
 * @param number $time_left the time left to send at work today
 */
function calculate_end_time($time_left) {
    return date("H:i:s", time() + $time_left);
}

/**
 * Transforms the DB retrieved overtime to a google chart readable array
 *
 * @param array $db_overtime
 * @param number $working_time
 */
function overtime_to_chart_array($db_overtime, $working_time) {
    $overtime = NULL;
    if (isset($db_overtime) && count($db_overtime) > 0) {
        $overtime = array (array ("Date","Heures supplémentaires" ) );
        
        foreach ( $db_overtime as $row ) {
            $overtime[] = array (date('m-Y', strtotime($row['date'])),
                    duration_to_days($row['amount'], $working_time) );
        }
    }
    return $overtime;
}