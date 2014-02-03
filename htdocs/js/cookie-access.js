function getParametres() {
    return __getParametres(false);
}

function getParametresAndRaiseAlert() {
    return __getParametres(true);
}

function __getParametres(raiseAlert) {
    var parametresLocal = $.cookie('parametres');
	// Récupération des paramètres de l'application
	if (parametresLocal === undefined) {
        if (raiseAlert) {
            noTimeParametres();
        }
        parametresLocal = [];
	} else {
		if (parametresLocal['days'] === undefined) {
			parametresLocal['days'] = 0;
		}
		if (parametresLocal['hours'] === undefined) {
			parametresLocal['hours'] = 0;
		}
		if (parametresLocal['minutes'] === undefined) {
			parametresLocal['minutes'] = 0;
		}
		if (parametresLocal['seconds'] === undefined) {
			parametresLocal['seconds'] = 0;
		}
	}
    return parametresLocal;
}

function getIndicators(date) {
    var indicators = $.cookie('indicators');
    if (indicators === undefined || indicators.length === 0) {
        indicators = {
            'totalTimeToday' : 0,
            'totalTimeEver' : 0,
            'dayRatio' : 0,
            'timeLeft' : 0,
            'timeLeftOverTime' : 0,
            'date' : date.getTime(),
            'isOverTimeForDay' : false,
            'isOverTime' : true,
            'overTimeAmount' : 0,
            'endTime' : date.getTime(),
            'endTimeOverTime' : date.getTime(),
            'corruptedModel' : false,
            'numberOfDays' : 1,
        };
    }
    return indicators;
}

function getPunches() {

}

function sizeRatio(punches) {
    return Math.round((punches.length * 100 / 68) * 100) / 100;
}

function cookieHasPlace() {
    var punches = $.cookie('punches');
    var canPunchIn = false;
    if (punches === undefined 
        || (!isPowerOn() && punches.length < 67) 
        || (isPowerOn() && punches.length <= 67)) {
        
        canPunchIn = true;
    }
    return canPunchIn;
}

/**
 * Saves the state and time of the punch
 * @param string check either I or O
 * @param string date the date in ms of the punch
 */
function saveCheck(check, date) {
    var now = new Date().getTime();
    if (date !== undefined) {
        now = new Date(date);
    }

    // Préparation de l'enregistrement en cookie
    var punch = {
            'check' : check,
            'date' : now
        };

    var punches = $.cookie('punches');
    if (punches === undefined) {
        punches = [punch];
    }
    else {
        punches.push(punch);
    }

    savePunchesInCookie(punches);
}

function savePunchesInCookie(punches) {
    // Enregistrement du cookie
    $.cookie('punches', punches, {expires : 30});
    $( "#progressbar" ).progressbar( "option", "value", sizeRatio(punches) );
    setPunchesRange(punches);
}

/**
 * Saves the parametres in a cookie as an associative array
 */
function saveParametres() {
    var parametres = {
        'days' : $('#total-time-options #days').val(),
        'hours' : $('#total-time-options #hours').val(),
        'minutes' : $('#total-time-options #minutes').val(),
        'seconds' : $('#total-time-options #seconds').val()
    };
    $.cookie('parametres',parametres, {expires: 30});
    
    // Forces a reset of the estimated end time by setting the date to '' which will force the calculation of the date
    $('#time-end').text('');
}