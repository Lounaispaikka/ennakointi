function showForm() {

	$("#kysely_form").dform({
		"action" : "/run/lougis/kysely/saveAnswer",
		"method" : "get"
	});

	$("#kysely_form").dform({
		"html": [{
			"type" : "text",
			"caption" : "testitext"
		}]
	});
	
}