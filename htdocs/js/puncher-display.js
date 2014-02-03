function togglePuncherState() {
    if (cookieHasPlace()) {
        if (!isPowerOn()) {
            powerOn();
            // Save the state
            saveCheck('I');
        } else {
            powerOff();
            // Save the state
            saveCheck('O');
        }
    }
    else {
        showCookieState();
    }
}

function toggleCookieState() {
    if (!$('#left-panel-button').hasClass('cookie-active')) {
        $('#left-panel-button, #left-panel').addClass('cookie-active box-active', 1000, "easeInOutCubic");
    } else {
        // FIXME Hack to prevent the glow of the indicators button to show on the cookie button when closing
        $('#left-panel-button').removeClass('indicators-active');
        $('#left-panel-button, #left-panel').removeClass('cookie-active box-active', 1000, "easeInOutCubic");
    }
}


function toggleIndicatorsState() {
    if (!$('#right-panel-button').hasClass('indicators-active')) {
        $('#right-panel, #right-panel-button').addClass('indicators-active box-active', 1000, "easeInOutCubic");
    } else {
        // FIXME Hack to prevent the glow of the indicators button to show on the cookie button when closing
        $('#left-panel-button').addClass('indicators-active');
        $('#right-panel, #right-panel-button').removeClass('indicators-active box-active', 1000, "easeInOutCubic");
    }
}

function centerPuncher() {

    // Centrage automatique
    var sections = ['left-panel-section','right-panel-section', 'puncher-section'];
    $('#content').css('position','absolute');
    $('#content').css('top', Math.floor($(window).height() / 2 - $('#content').height() / 2));
    for (var index in sections) {
        $('#' + sections[index]).css('position','absolute');
        $('#' + sections[index]).css('top', Math.floor($('#puncher-container').height() / 2 - $('#' + sections[index]).height() / 2));
    }

    $('#left-panel-section').width($('#puncher-container').width() / 2);
    $('#right-panel-section').width($('#puncher-container').width() / 2);
    $('#left-panel-section').css('right', $('#puncher-container').width() / 2);
    $('#right-panel-section').css('left', $('#puncher-container').width() / 2);

    $('#puncher-section').css('left', Math.floor($('#puncher-container').outerWidth() / 2 - $('#puncher-section').outerWidth() / 2));
}

function noTimeParametres() {
	$('#total-time-options div.ui-state-error').show();
	showTimeParametresIfNotDisplaying();
}

function showTimeParametresIfNotDisplaying() {
	if (!$('#total-time-options').dialog("isOpen")) {
		showTimeParametres();
	}
}

function showTimeParametres() {
	$('#total-time-options').dialog("open");
}
function showParametres() {
	$('#options').dialog("open");
}
function showPunchesParametres() {
	$('#punches-options').dialog("open");
}
function showCookieState(warning) {
	$('#cookie-state').dialog("open");
    
    if (!cookieHasPlace()) {
        $("#cookie-state .ui-state-error").show();
    }
    else {
        $("#cookie-state .ui-state-error").hide();
    }
}

function printPunchesValues(values) {
    var printedText = "";
    for (var index in values) {
        printedText += hmsDateFormat(values[index]) + ' - ';
    }
    printedText = printedText.slice(0, printedText.length - 3);
    $( "#punches-range-values" ).text( printedText );
}