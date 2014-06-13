jQuery((function($){

	$.tablesorter.addParser({
		id: 'wp-date-parser',
		is: function(s, table, cell) {
		    return $(cell).hasClass("wp-date");
		},
		format: function(s,table,cell,cellIndex) {
		    return Date.parse(s);
		},
		type: 'numeric'
	});

	$(".tablesorter").tablesorter();

}).bind(null,jQuery))