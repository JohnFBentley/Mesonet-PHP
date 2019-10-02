function equalHeight(group) {
    tallest = 0;
    group.each(function() {
	thisHeight = $(this).height();
	if(thisHeight > tallest) {
	    tallest = thisHeight;
	}
    });
    group.height(tallest);
}

$(document).ready(function() {
    equalHeight($(".innertube"));
    $("input[name=select_deselect]").click(function(){
	var checked_status = this.checked;
	$("input[name=var[]]").each(function(){
	    this.checked = checked_status;
	});
    });
    $("#datepicker").datepicker({ dateFormat: 'yy-mm-dd', changeYear: true });
    $("#datepicker2").datepicker({ dateFormat: 'yy-mm-dd', changeYear: true });
    $("#yearmonpick").datepicker({ dateFormat: 'yy-mm',
                                   defaultDate: "-1m",
                                   maxDate: "-1m",
                                   minDate: new Date(2007, 7 - 1),
                                   showButtonPanel: true,
                                   changeYear: true,
                                   changeMonth: true });
    $("#timepicker").timeEntry({initialField: 1,
                                show24Hours: true,
                                showSeconds: false,
                                spinnerImage: 'spinnerText.png',                                
                                spinnerBigSize: [60, 40, 16],
                                spinnerSize: [30, 20, 8],
                                timeSteps: [1, 1, 0]});
    var menupath = {
            '/': 0,
            '/index.php': 0,
            '/station_map.php': 1,
            '/station_data.php': 1,
            '/station_graph.php': 1,
            '/realtime_display.php': 2,
            '/monthly_summary.php': 3,
            '/archived_data.php': 4,
            '/station_metadata.php': 5,
            '/data_products.php': 6,
            '/contactus.php': 7
    };
    $("ul.basictab li a").eq(menupath[window.location.pathname]).addClass('selected');
});
