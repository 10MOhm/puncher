$(function() {
	$('.day').hide();
	$('.day').last().show();
    
    $('a#previous').click(function() {
        if ($('.day').index($('.day:visible')) != 0) {
            $('.day:visible').hide().prev().show();
        }
        return false;
    });
    $('a#next').click(function() {
        if ($('.day').index($('.day:visible')) != $('.day').length -1) {
            $('.day:visible').hide().next().show();
        }
        return false;
    });
    
    $('a.button.add').click(add_row);
    
    $('input:reset').click(function() {
    	$('.added').remove();
    });
});

function add_row() {
	this_tr = $(this).closest('tr');
    new_tr = this_tr.prev().clone();
    
    hour = new_tr.find('input.hour');
    minute = new_tr.find('input.minute');
    $delete = new_tr.find('input.delete');
    
    hour_name = hour.attr('name');
    m_hour = hour_name.split('_');
    new_key = parseInt(m_hour[2]) + 1;
    new_name = m_hour[0] + "_" + m_hour[1] + "_" + new_key;
    hour.attr('name', new_name);
    hour.attr('id', new_name);

    minute_name = minute.attr('name');
    m_minute = minute_name.split('_');
    new_name = m_minute[0] + "_" + m_minute[1] + "_" + new_key;
    minute.attr('name', new_name);
    minute.attr('id', new_name);
    
    delete_name = $delete.attr('name');
    m_delete = delete_name.split('_');
    new_name = m_delete[0] + "_" + m_delete[1] + "_" + new_key;
    $delete.attr('name', new_name);
    $delete.attr('id', new_name);
    
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
    new_tr.addClass("added");
    
    this_tr.before(new_tr);
    return false;
}