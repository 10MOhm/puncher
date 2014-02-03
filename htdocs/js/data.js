$(function() { 

	$("#progressbar").progressbar({
		value : parseInt($(".progress-label").text())
	});
	
    $( "#dialog-confirm" ).dialog({
    	autoOpen: false,
		resizable: false,
		modal: true
    });
    
    $('#export').click(function(event) {
    	event.preventDefault();
    	var targetUrl = $(this).attr("href");

        $( "#dialog-confirm" ).dialog({
	    	buttons: {
				"Exporter les pointages": function() {
					$( this ).dialog( "close" );
					window.open(targetUrl,'mywindow','width=400,height=200,toolbar=yes, \
							location=yes,directories=yes,status=yes,menubar=yes, \
							scrollbars=yes,copyhistory=yes,resizable=yes');
				},
				Cancel: function() {
					$( this ).dialog( "close" );
					return false;
				}
			}
        });
    	$( "#dialog-confirm" ).dialog( "open" );
    });
});