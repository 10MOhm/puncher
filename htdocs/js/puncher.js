$(document).ready(function(){
    
	initPuncher();
	$.cookie.json = true;
	
	/*
	 * Starts or stops the puncher
	 */
	$('#puncher-button').click(function(){
    	togglePuncherState();
	});
	/*
	 * Opens or closes the cookie information pane
	 */
	$('#left-panel-button').click(function(){
		toggleCookieState();
	});
	/*
	 * Opens or closes the indicators information pane
	 */
	$('#right-panel-button').click(function(){
		toggleIndicatorsState();
	});
	/*
	 * Deals with the deletion of the cookie by providing a confirmation box
	 */
	$('#delete-cookie').click(function(){
		$( "#delete-warning" ).dialog({
			resizable: false,
            movable: true,
			height:140,
			modal: false,
			buttons: {
				"Vider": function() {
					eraseCookie();
					$( this ).dialog( "close" );
				},
				"Annuler": function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	$('#time-options-button').click(showTimeParametres);
	$('#options-button').click(showParametres);
	$('#punches-options-button').click(showPunchesParametres);
	$('#cookies-button').click(showCookieState);
	$('#punches-options .add-punch').click(function() {
        addPunchModifier();
        return false;
    });
    $('#older-punches').click(displayOlderPunches);
    $('#newer-punches').click(displayNewerPunches);
    
    // Regular update of the progress bar and indicators
    $(document).everyTime('1s', 'puncherTimer', function() {
        updateIndicators();
    });
});

/**
 * Deletes the user's cookie and resets all of his data
 */
function eraseCookie() {
    $.removeCookie('punches');
    resetPuncher();
}

/**
 * Turns the puncher's power on
 */
function powerOn() {
    $('#puncher-button').addClass('box-active');
    $('#puncher-container').addClass('on');
    // Changes the color of the progress bar
    var progressbarColor = "#26B3F7";
    if ($('#puncher-container').hasClass('over-time-for-day')) {
        var progressbarColor = "#CC0000";
    }
    if ($('#puncher-container').hasClass('over-time')) {
        var progressbarColor = "#FFBA19";
    }
    $("#knob").trigger('configure', {"fgColor":progressbarColor, "shadow" : true});
}

function isPowerOn() {
    return $('#puncher-button').hasClass('box-active');
}

/**
 * Turns the puncher's power off
 */
function powerOff() {
    $('#puncher-container').removeClass('on');
    $('#puncher-button').removeClass('box-active');
    // Changes the color of the progress bar
    $("#knob").trigger('configure', {"fgColor":"#aaa", "shadow" : false});
}

/**
 * Inits the application's data and displays everything at it's right place
 */
function initPuncher() {
    // Displays the circular loading bar using jquery.knob
    $("#knob").knob({
        "fgColor":"#aaa",
        draw : tronDraw
    });

    // Centers the puncher on the page
    centerPuncher();
    $(window).resize(function() {
        centerPuncher();
    });

    $.cookie.json = true;
    resetPuncher();
}

function resetPuncher() {
    
    // Forces a reset of the estimated end time by setting the date to '' which will force the calculation of the date
    $('#time-end').text('');

    // Loads the initial data of the puncher
    var parametres = $.cookie('parametres');
    var punches = $.cookie('punches');

    // Loads all the other initial datas
    initOptions(punches, parametres);
    initCookieInfos(punches);
    updateIndicators(punches, parametres);
	initToolTip();
    setPunchesRange(punches);
    
    // If the last punch recorded was in the current day and was a check in : power on the puncher
    if (punches !== undefined && punches.length > 0 
        && punches[punches.length -1]['check'] === 'I' 
        && punches[punches.length -1]['date'] >= new Date().setHours(0,0,0,0)) {
        powerOn();
    }
    else {
        powerOff();
    }
}

function initToolTip() {

    var generalParametres = $.cookie('general-parametres');
    
    // Check if the tooltips for the puncher button are on or not
    if (generalParametres === undefined || generalParametres['button-tooltip-on']) {
        $('#puncher-button').tooltip({
            position: { my: "left top+15", at: "left+3 bottom" }
        });
    }
    else {
        $('#puncher-button').removeAttr('title');
    }
    
    // Check if the tooltips are supposed to be on
    // If no parametres are set, we suppose the default value is on
    // TODO: Move the getter of parametres to cookie-access and provide default values
    if (generalParametres === undefined || generalParametres['tooltips-on']) {
        $('#left-panel-button').tooltip({
            position: { my: "right center", at: "left-15 center" }
        });
        $('#right-panel-button').tooltip({
            position: { my: "left+15 center", at: "right center" }
        });
        $('#options-buttons-container').tooltip();
    }
    else {
        $('#left-panel-button').removeAttr('title');
        $('#right-panel-button').removeAttr('title');
    }
}

/**
 * Inits the cookie information such as size rate
 * @param {Object} punches the punches from the cookie
 */
function initCookieInfos(punches) {
    // Progressbar init
    var progressbar = $("#progressbar" ), progressLabel = $( ".progress-label" );

    progressbar.progressbar({change: function() {
        progressLabel.text( progressbar.progressbar( "value" ) + "%" );
    }});

    // Gets the size ratio of the cookie informations
    if (punches !== undefined) {
        progressbar.progressbar( "option", "value", sizeRatio(punches) );
    }
    else {
        progressbar.progressbar( "option", "value", 0 );
    }

    // Button init
    $('#delete-cookie').button({ icons: { primary: "ui-icon-trash" } });
    $('#save-cookie').button({ icons: { primary: "ui-icon-disk" }});
}

/**
 * Updates the indicator values on the screen
 * @param {Object} punches optionnal parameter
 */
function updateIndicators(punches, parametres) {

    // This boolean is to determine wether or not we should calculate
    // all the indicators (they don't have any value yet)
    var firstCalculation = $('#time-end').text() === '';

    if (isPowerOn() || firstCalculation) {
    
        var punches = (typeof punches === "undefined") ? $.cookie('punches') : punches;
        var parametres = (typeof parametres === "undefined") ? $.cookie('parametres') : parametres;
        
        // Gets the general parameters to determine if the calulation must include the previous days
        var generalParametres = $.cookie('general-parametres');
        // Default value is 3 : Infinite data used for calculation
        var indicatorsMode = 3;
        if (generalParametres !== undefined) {
            indicatorsMode = generalParametres['indicators-mode'];
        }
        
        var indicators = calculateIndicators(new Date(), punches, parametres, firstCalculation, indicatorsMode);
        
        var timeLeft = ms2string(indicators['timeLeft']);
        var timeLeftOverTime = ms2string(indicators['timeLeftOverTime']);
        // Si on a dépassé le temps alloué
        if (indicators['isOverTimeForDay']) {
            if (!$('#puncher-container').hasClass('over-time-for-day')) {
                // Modification des styles pour passer en rouge
                $('#puncher-container').addClass('over-time-for-day');
                $('#puncher-container').removeClass('over-time');
                if ($('#puncher-container').hasClass('on')) {
                    $("#knob").trigger('configure', {"fgColor":"#CC0000", "shadow" : true});
                }
            }
            // Rectification des indicateurs
            timeLeft = "+ " + timeLeft;
            timeLeftOverTime = "+ " + timeLeftOverTime;
        }
        else if (indicators['isOverTime']) {
            // the isOverTime is true when we are over time including the previous
            // over time done
            if (!$('#puncher-container').hasClass('over-time')) {
                // Modification des styles pour passer en rouge
                $('#puncher-container').addClass('over-time');
                $('#puncher-container').removeClass('over-time-for-day');
                if ($('#puncher-container').hasClass('on')) {
                    $("#knob").trigger('configure', {"fgColor":"#FFBA19", "shadow" : true});
                }
            }
            // Rectification des indicateurs
            timeLeftOverTime = "+ " + timeLeftOverTime;
        }
        else if ($('#puncher-container').hasClass('over-time-for-day') || $('#puncher-container').hasClass('over-time')) {
            $('#puncher-container').removeClass('over-time-for-day');
            $('#puncher-container').removeClass('over-time');
            if ($('#puncher-container').hasClass('on')) {
                powerOn();
            }
        }
        
        $('#total-time').text(ms2string(indicators['totalTimeToday']));
        $('#time-difference').text(timeLeft);
        $('#time-left-over-time').text(timeLeftOverTime);
        $('#over-time-for-day-amount').text(ms2string(indicators['overTimeAmount']));
        
        // The estimated end time is only calculated on the first calculation
        if (firstCalculation && indicators['endTime'] !== -1) {
            $('#time-end').text(myDateFormat(indicators['endTime']));
            $('#time-end-over-time').text(myDateFormat(indicators['endTimeOverTime']));
        }
        
        $("#knob").val(Math.round(indicators['dayRatio'] * 100) / 100).trigger('change');
        $('#puncher-button').attr('title', (Math.round(indicators['dayRatio'] * 100) / 100) + '%');
    }
    else if (!isPowerOn() && !$('#puncher-container').hasClass('over-time-for-day')) {
        // If the puncher is disabled, the end time rises each second
        var indicators = estimateEndTime(new Date(), punches, parametres, getIndicators(new Date()));
        var endTime = indicators === undefined ? new Date().getTime() : indicators['endTime'];
        var endTimeOverTime = indicators === undefined ? new Date().getTime() : indicators['endTimeOverTime'];
        $('#time-end').text(myDateFormat(endTime));
        $('#time-end-over-time').text(myDateFormat(endTimeOverTime));
    }
}


/**
 * Inits the options container etc.
 * @param parametres the associative array representing the parametres, 
 * used to init the parametres options
 */
function initOptions(punches, parametres) {

	$('#options-buttons-container #time-options-button').button({ icons: { primary: "ui-icon-clock" }, text: false });
	$('#options-buttons-container #options-button').button({ icons: { primary: "ui-icon-gear" }, text: false });
	$('#options-buttons-container #punches-options-button').button({ icons: { primary: "ui-icon-wrench" }, text: false });
	$('#options-buttons-container #cookies-button').button({ icons: { primary: "ui-icon-document" }, text: false });
	
    $('#punches-options #newer-punches').button();
    $('#punches-options #older-punches').button();
    $('#punches-options .delete-punch').button({ icons: { primary: "ui-icon-trash" }, text: false });
    $('#punches-options .add-punch').button({ icons: { primary: "ui-icon-plus" }, text: false });
    
    initGeneralParametres();
    initCookieInfos(punches);
    
	if (parametres != undefined) {
		$('#days').val(parametres['days']);
		$('#hours').val(parametres['hours']);
		$('#minutes').val(parametres['minutes']);
		$('#seconds').val(parametres['seconds']);
	}
	
	$('#total-time-options').dialog({
		draggable: true,
		autoOpen: false,
		show: {
			effect: 'fade',
			duration: 300
		},
		hide: {
			effect: 'fade',
			duration: 300
		},
		close: saveParametres,
	});
	
	$('#options').dialog({
		draggable: true,
		autoOpen: false,
        width: 400,
        resizable: false,
		show: {
			effect: 'fade',
			duration: 300
		},
		hide: {
			effect: 'fade',
			duration: 300
		}
	});
	
	$('#cookie-state').dialog({
		draggable: true,
		autoOpen: false,
		show: {
			effect: 'fade',
			duration: 300
		},
		hide: {
			effect: 'fade',
			duration: 300
		}
	});
    
    $('#punches-options').dialog({
		draggable: true,
		autoOpen: false,
		show: {
			effect: 'fade',
			duration: 300
		},
		hide: {
			effect: 'fade',
			duration: 300
		},
        close: function() {
            setPunchesRange($.cookie('punches'))
        },
        buttons: [{
            text: "Changer les pointages", 
            icons: { primary: "ui-icon-check" }, 
            click: function() {
                changePunches($.cookie('punches'));
                $( this ).dialog( "close" ); 
            }
        }]
	});
}

function initGeneralParametres() {
    
    var generalParametres = $.cookie('general-parametres');
    
    // First we select the first option (on) so that even if generalParametres 
    // is empty, the options are preselected
    $('#tooltips-options input').eq(0).attr('checked','checked');
    $('#button-tooltip-options input').eq(0).attr('checked','checked');
    $('#indicators-mode-options input').eq(3).attr('checked','checked');
    
    if (generalParametres !== undefined) {
        // If the option is not on we change its value (is on by default)
        if (!generalParametres['tooltips-on']) {
            $('#tooltips-options input').eq(0).removeAttr('checked');
            $('#tooltips-options input').eq(1).attr('checked','checked');
        }
        if (!generalParametres['button-tooltip-on']) {
            $('#button-tooltip-options input').eq(0).removeAttr('checked');
            $('#button-tooltip-options input').eq(1).attr('checked','checked');
        }
        if (generalParametres['indicators-mode'] !== undefined) {
            var index = generalParametres['indicators-mode'];
            $('#indicators-mode-options input').eq(index).attr('checked','checked');
        }
    }
	$('#tooltips-options').buttonset().change($changeGeneralParametres);
	$('#button-tooltip-options').buttonset().change($changeGeneralParametres);
	$('#indicators-mode-options').buttonset().change($changeGeneralParametres);
}

function $changeGeneralParametres() {
    // $(this) represents the general option that changed
    var tooltipsOn = $("#tooltips-on:checked").length === 1;
    var buttonTooltipOn = $("#button-tooltip-on:checked").length === 1;
    var indicatorsMode = $("#indicators-mode-options input").index($(":checked"));
    
    var params = {
        'tooltips-on' : tooltipsOn,
        'button-tooltip-on' : buttonTooltipOn,
        'indicators-mode' : indicatorsMode
    };
    
    $.cookie('general-parametres',params);
}

/**
 * Responds to the call to display older punches in the punches management view
 */
function displayOlderPunches() {
    var currentDate = $('#punches-options-date').text();
    currentDate = new XDate(currentDate);
    currentDate.addDays(-1);
    setPunchesRange($.cookie('punches'),currentDate);
}

/**
 * Responds to the call to display newer punches in the punches management view
 */
function displayNewerPunches() {
    var currentDate = $('#punches-options-date').text();
    currentDate = new XDate(currentDate);
    
    // Security to prevent viewing dates in the future
    var now = new XDate();
    if (currentDate.getFullYear() === now.getFullYear()
        && currentDate.getMonth() === now.getMonth()
        && currentDate.getDate() === now.getDate()) {
        return;
    }
    currentDate.addDays(1);
    setPunchesRange($.cookie('punches'),currentDate);
}

/**
 * setPunchesRange
 * @param punches the punches
 * @param date the XDate date to display the punches
 */
function setPunchesRange(punches,date) {

    $('#punches-form #punches-inputs').empty();
    var dateLocal = date;
    if (date === undefined) {
        dateLocal = new XDate();
    }
    
    $('#punches-options-date').text(dateLocal.toString('dd/MM/yyyy'));

    punches = (typeof punches === "undefined") ? $.cookie('punches') : punches;
    
    if (punches !== undefined) {
        var todaysPunches = getDaysPunches(punches, date);
        for (var index in todaysPunches) {
            addPunchModifier(todaysPunches[index]);
        }
    }
    else {
        return undefined;
    }
}

// TODO: Add test 4 me
/**
 * Adds a punch to the punch modify popin view
 * @param an optionnal punch
 */
function addPunchModifier(punch) {
    
    var punchType;
    var punchDate;
    
    if (punch !== undefined) {
        punchType = punch['check'];
        punchDate = new XDate(punch['date']);
    }
    else {
        // Get the last punch value (the one we create has the opposite value)
        var $lastPunch = $('#punches-inputs .punch-modifier:last-child');
        var lastPunchType = $lastPunch .find('input.type').val();
        punchType = lastPunchType === 'I' ? 'O' : 'I';
        // If the punch isn't defined it meens it's a new one
        // in that case the date must be coherent with the date displayed in the popin
        punchDate = new XDate($('#punches-options-date').text());
        
        // It there is a last punch change the values
        if ($lastPunch.length > 0) {
            var lastPunchHour = $lastPunch .find('input.hour').val();
            lastPunchHour = parseInt(lastPunchHour);
            var lastPunchMinute = $lastPunch .find('input.minute').val();
            lastPunchMinute = parseInt(lastPunchMinute);
            punchDate.setHours(lastPunchHour,lastPunchMinute + 1);
        }
    }
    
    var $punchModifier = $('#punches-form .hidden .punch-modifier').clone();
    $punchModifier.find(".punch-type").text(punchTypeToString(punchType));
    $punchModifier.find("input.hour").val(punchDate.getHours());
    $punchModifier.find("input.minute").val(punchDate.getMinutes());
    $punchModifier.find("input.original-time").val(punchDate.getTime());
    $punchModifier.find("input.type").val(punchType);
    $('#punches-form #punches-inputs').append($punchModifier);
    $punchModifier.find('.delete-punch').click($deletePunch);
}

/**
 * Deletes a punch from the punch modify popin view
 * Doesn't take any parameters, supposed to be called from
 * a javascript event
 */
function $deletePunch() {
    $(this).closest(".punch-modifier").remove();
    return false;
}

// TODO: add tests for me
/**
 * Adds, modifies and/or delete punches from the punch list with the punches
 * modified manually in the popin view
 * @param the list of original punches
 */
function changePunches(punches) {

    var punchesLocal = punches;
        
    // Gets all the new values from the form and puts them into an associative array
    // the keys are the original date and the values, the new dates
    var newValues = {};
    var $punchModifiers = $('#punches-form #punches-inputs .punch-modifier');
    for (var index = 0; index < $punchModifiers.length ; index++) {
    
        var minute = $punchModifiers.eq(index).find("input.minute").val();
        minute = parseInt(minute);
        var hour = $punchModifiers.eq(index).find("input.hour").val();
        hour = parseInt(hour);
        var dateTime = $punchModifiers.eq(index).find("input.original-time").val();
        dateTime = parseInt(dateTime);
        var date = new Date(dateTime);
        newValues[dateTime] = {
            'check' : $punchModifiers.eq(index).find("input.type").val(),
            'date' : date.setHours(hour,minute)
        };
    }
    
    if (punchesLocal !== undefined) {
        
        // var day = new Date().setHours(0,0,0,0);
        // Récupération de la date
        var dayStart = new XDate($('#punches-options-date').text());
        dayStart = dayStart.setHours(0,0,0,0);
        var dayEnd = new XDate(dayStart);
        dayEnd.setHours(23,59,59,999);
        
        var toDelete = [];
        for (var index in punchesLocal) {
            // If the punch was done before the day midnight continue parsing
            if (punchesLocal[index]['date'] < dayStart || punchesLocal[index]['date'] > dayEnd) {
                continue;
            }
            else {
                // The form doesn't contains the punch it meens it has to be deleted
                if (newValues[punchesLocal[index]['date']] === undefined) {
                    toDelete.push(index);
                }
                else {
                    // Otherwise if meens it changed
                    var originalDate = punchesLocal[index]['date'];
                    punchesLocal[index]['date'] = newValues[punchesLocal[index]['date']]['date'];
                    // Delete the punch
                    delete newValues[originalDate];
                }
            }
        }
        
        // Delete the entries that must be deleted
        var i = 0;
        for (var index in toDelete) {
            // We delete elements one by one, so each time the punches array's length is one item shorter
            punchesLocal.splice(toDelete[index - i],1);
            i++;
        }
            
        // Get the index to add punches
        // The last index of day is set by default to 0 so that it inserts values at the first place
        var lastIndexOfDay = 0;
        // If we're trying to add punches after the last punches of the day, directly add them at the end
        if (dayStart > punchesLocal[punchesLocal.length - 1]['date']) {
            lastIndexOfDay = punchesLocal.length;
        }
        else if (dayEnd > punchesLocal[0]['date']) {
            // If we're not trying to add punches before the first ones and after the last ones, look for the
            // place to add them
            for (var index in punchesLocal) {
                // If the punch was done before the day midnight continue parsing
                if (punchesLocal[index]['date'] < dayStart || punchesLocal[index]['date'] > dayEnd) {
                    continue;
                }
                // Here we add one in order to insert the punches after the last day found
                lastIndexOfDay = parseInt(index) + 1;
            }
        }
        
        // If newValues is not empty it meeens we have punches to add manualy
        if ( !$.isEmptyObject(newValues)) {
            var j = 0;
            for (var dateKey in newValues) {
                // Add j because we insert the values one by one
                punchesLocal.splice(lastIndexOfDay + j, 0, newValues[dateKey]);
                j++;
            }
        }
    }
    else {
        punchesLocal = [];
        if ( !$.isEmptyObject(newValues)) {
            for (var dateKey in newValues) {
                punchesLocal.push(newValues[dateKey]);
            }
        }
    }
    
    savePunchesInCookie(punchesLocal);
    resetPuncher();
}

/**
 * Transforms the check type into a human readable String
 * @param punchType the type of check in to transform
 * @return a human readable string
 */
function punchTypeToString(punchType) {
    return punchType === 'I' ? 'Check In' : 'Check Out';
}