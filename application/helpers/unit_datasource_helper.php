<?php
function get_checks_2() {
	return Array (
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 09:00:00' ) ) 
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 09:01:00' ) ) 
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 09:02:00' ) ) 
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 09:03:00' ) ) 
			) 
	);
}
function get_checks_722() {
	return Array (
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ('today 9:00:00' ))
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' =>  date ( "Y-m-d H:i:s", strtotime ('today 12:00:00' ))
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' =>  date ( "Y-m-d H:i:s", strtotime ('today 13:15:00' ))
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' =>  date ( "Y-m-d H:i:s", strtotime ('today 17:37:00')) 
			) 
	);
}
function get_checks_overnight() {
	return Array (
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 09:00:00' ) ) 
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 12:00:00' ) ) 
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 13:15:00' ) ) 
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 17:37:00' ) ) 
			) 
	);
}
function get_checks_2_days() {
	return Array (
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 9:00:00' ))
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 12:00:00' ))
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 13:15:00' ))
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 17:37:00' ))
			),
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 9:00:00' ) ) 
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 12:00:00' ) ) 
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 13:15:00' ) ) 
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 17:37:00' ) ) 
			) 
	);
}
function get_checks_month() {
	return Array (
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 9:00:00' ) )
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 12:00:00' ) )
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 13:15:00' ) )
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 17:37:00' ) )
			),
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 9:00:00' ) ) 
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 12:00:00' ) ) 
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 13:15:00' ) ) 
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 17:37:00' ) ) 
			) ,
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 9:00:00' ) ) 
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 12:00:00' ) ) 
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 13:15:00' ) ) 
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'today 17:37:00' ) ) 
			) 
	);
}
    
function get_checks_month_no_checkout() {
	return Array (
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 9:00:00' ) )
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 12:00:00' ) )
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 13:15:00' ) )
			),
			Array (
					'id' => 5,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( '-1 month 17:37:00' ) )
			),
			Array (
					'id' => 2,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 9:00:00' ) ) 
			),
			Array (
					'id' => 3,
					'user_id' => 1,
					'check_in' => 0,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 12:00:00' ) ) 
			),
			Array (
					'id' => 4,
					'user_id' => 1,
					'check_in' => 1,
					'date' => date ( "Y-m-d H:i:s", strtotime ( 'yesterday 13:15:00' ) ) 
			)
	);
}