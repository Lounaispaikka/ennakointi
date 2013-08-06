function teemaDialog(toimialaId) {

	function getTeemat(toimialaId) {
		
		var req = jQuery.ajax({
		url: '/run/lougis/cms/getTeemaPages/',
		data: { toimiala_id: toimialaId },
		type: 'POST',
		dataType: 'json'
		});
		
			req.done(function(xhr) {
				//console.log("xhr-success", xhr);
				$.each(xhr, function(i, item) {
					console.log("teema-success", item);
				
					var button = $('<button class="muokkaa_teema" id=\"teema_' + item.page_id + '\"><img src=\"/img/icons/16x16/pencil.png\" >' + item.title + '</button>');
					$("#valitse_teema").append(button);
					
					$("#teema_" + item.page_id).click(function() { 
						console.log("you just clicked: " + item.page_id);
						//$("#toimialaDialog").dialog('close');
						
						//$('#addToimiala').tabbedDialog(xhr[i].id);
					});
		
				});
			});
		
			req.fail(function(xhr) {
				//console.log("xhr-fail", xhr);
			});
	}
	
	
	$('#valitse_teema').empty(); //tyhjennä ensin, ettei tule montaa kopiota listasta
	$('#teemaDialog').dialog({
		autoOpen: true,
		modal: true,
		width: 600,
		buttons: {
			"Peruuta": function() {
				$(this).dialog('close');
			}
		}
	});
	getTeemat(toimialaId);
	
	$(".cancel-btn").click(function() {
			//sulkee kaikki dialogit
			$(".ui-dialog-content").dialog("close");
			console.log("sulje");
			return false;
			
	});
}

