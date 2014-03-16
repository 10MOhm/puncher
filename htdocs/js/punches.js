$(function() {
	$('.day').hide();
	$('.day').last().show();
    
    $('a#previous').click(function() {
        if ($('.day').index($('.day:visible')) == 0) {
        	add_day_before($('.day:visible'));
        }
        $('.day:visible').hide().prev().show();
        return false;
    });
    $('a#next').click(function() {
        if ($('.day').index($('.day:visible')) == $('.day').length -1) {
        	add_day_after($('.day:visible'));
        }
        $('.day:visible').hide().next().show();
        return false;
    });
    
    $('a.button.add').click(add_row);
    
    $('input:reset').click(function() {
    	$('.added').remove();
    });
});

function add_day_before(item) {
	clone = item.clone();
    currentDate = new XDate(item.attr('class').split(' ')[1]);
    currentDate.addDays(-1);
	clone.find('.check_date').text(currentDate.toString('dd/MM/yyyy'));
	clone.attr('class', 'day ' + currentDate.toString('yyyy-MM-dd'));
	clone.find('tr.check_time').remove();
	clone.hide();
	item.parent().prepend(clone);
    $('a.button.add').click(add_row);
}

function add_day_after(item) {
	
}

function add_row() {
	this_tr = $(this).closest('table').find('tr.check_time'); 
	
	new_tr = null;
	has_check_that_day = this_tr.size() != 0
	// If there is no tr for that day, create one from another day
	if (!has_check_that_day) {
		new_tr = $('tr.check_time').eq(0).clone();
	} else {
	    new_tr = this_tr.last().clone();		
	}
	
    // --------------------------------------------------------------------------------
	//
	// Changes the ids and names "date_type_id" (first check of the day is date_type_0)
	//
	// --------------------------------------------------------------------------------
	
    hour = new_tr.find('input.hour');
    minute = new_tr.find('input.minute');
    $delete = new_tr.find('input.delete');
    
    hour_name = hour.attr('name');
    minute_name = minute.attr('name');
    delete_name = $delete.attr('name');

	if (has_check_that_day) 
	{
		// If there's checks that day, the id changes but not the date
		
	    m_hour = hour_name.split('_');
	    new_key = parseInt(m_hour[2]) + 1;
	    new_hour_name = m_hour[0] + "_" + m_hour[1] + "_" + new_key;

	    m_minute = minute_name.split('_');
	    new_minute_name = m_minute[0] + "_" + m_minute[1] + "_" + new_key;
	    
	    m_delete = delete_name.split('_');
	    new_delete_name = m_delete[0] + "_" + m_delete[1] + "_" + new_key;
	} 
	else 
	{
		// If there isn't any checks that day, the date changes but not the id 
	    m_hour = hour_name.split('_');
	    new_date = $(this).parents('.day').find('.check_date').text().replace(/\//g,'');
	    new_hour_name = new_date + "_" + m_hour[1] + "_" + m_hour[2];

	    m_minute = minute_name.split('_');
	    new_minute_name = new_date + "_" + m_minute[1] + "_" + m_minute[2];
	    
	    m_delete = delete_name.split('_');
	    new_delete_name = new_date + "_" + m_delete[1] + "_" + m_delete[2];
	}
	
    hour.attr('name', new_hour_name);
    hour.attr('id', new_hour_name);
    minute.attr('name', new_minute_name);
    minute.attr('id', new_minute_name);
    $delete.attr('name', new_delete_name);
    $delete.attr('id', new_delete_name);
	
    // --------------------------------------------------------------------------------
	//
	// Changes the values of the times
	//
	// --------------------------------------------------------------------------------    
    

	if (has_check_that_day) 
	{
	    minute.val( new String(parseInt(minute.val()) + 1).lpad('0', 2) );
	    
	    if (minute.val() >= 60) {
	    	minute.val(new String(0).lpad('0', 2));
	    	hour.val( new String(parseInt(hour.val()) + 1).lpad('0', 2) );
	    	
	        if (hour.val() >= 24) {
	        	// Impossible to add a row that day
	        	return false;
	        }
	    }
	    
	    check = new_tr.find('p').text();
	    if (check.match(/In/g)) {
	    	new_tr.find('p').text(check.replace('In', 'Out'));
	    } else {
	    	new_tr.find('p').text(check.replace('Out', 'In'));        	
	    }
	}
	else
	{
		// If there isn't any checks that day, the hour is set to midnight
		hour.val('00');
		minute.val('00');
	    new_tr.find('p').text('Check In');
	}
    new_tr.addClass("added");

	// If there is no tr for that day, add before the add button
	$(this).closest('tr').before(new_tr);
	
    return false;
}