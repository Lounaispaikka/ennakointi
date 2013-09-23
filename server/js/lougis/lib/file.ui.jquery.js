/*
* 
* FUNKTIO: Lis‰‰ uusi tiedosto ennakointiin
*
*/
function openAddFileDialog(parent_id) {
	
	//dialog width and height according to window size
	var wWidth = $(window).width();
    var dWidth = wWidth * 0.8;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.8;
	
	$('#addFileDialog').dialog({
		autoOpen: false,
		modal: true,
		width: dWidth,
		height: dHeight,
		title: "Tiedoston lataus",
		buttons: [
			{
				text: "Sulje",
				click: function() {
					$(this).dialog("close");
				}	
			},
			{
				text: "Tallenna",
				click: function() {
					$('#upfile').submit();
				}
			}
        ]
	});
	$('#addFileDialog').dialog('open');
	uploadFile(parent_id);
	return false;
}

function uploadFile(parent_id) {
	
	$('#upfile').empty();
	$( ".percent" ).empty();
	$('#upfile').dform({
		"method" : "post",
		"html" :
			[
				{
					"name" : "description",
					"caption" : "Kuvaus",
					"type" : "textarea",
					"class" : "lomake"/*,
					
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}*/
				},
				{
					"name" : "f",
					"type" : "file"
				},
				{
					"name" : "parent_id",
					"value" : parent_id,
					"type" : "hidden"
				}/* ,
				{
					"type" : "submit",
					"value" : "Tallenna",
					"class": "next-btn"
				},
				{
					"type" : "button",
					"html" : "Peruuta",
					"class": "cancel-btn"
				} */
			]
	});	
	
	$(".cancel-btn").click(function() {
		$('#addFileDialog').dialog('close');
		return false;
	});
	
	var bar = $('.bar');
	var percent = $('.percent');
	var status = $('#status');
	
	$('#upfile').ajaxForm({
		beforeSend: function() {
			status.empty();
			var percentVal = '0';
			//$( ".percent" ).progressbar({ value: percentVal, max: 100 });
			//bar.width(percentVal)
			percent.html(percentVal);
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			//bar.width(percentVal)
			percent.html(percentVal);
			//$( ".percent" ).progressbar( "option", "value", percentVal );
		},
		url: "/run/lougis/file_upload/uploadFile/",
		datatype: "json",
		success: showResponse,
		type: "POST"
	}); 
	/*complete: function(responseText) {
			//status.html(xhr.responseText);
			console.log(responseText);
			$("#response_msg").empty();
			$( "#response_msg" ).append(responseText.msg);
			$("#addFileDialog").dialog("close");
			$( "#dialog-message" ).dialog({
				modal: true,
				buttons: {
					"Sulje": function() {
						$(".ui-dialog-content").dialog("close");
					}			
				}
			});
	}*/
	function showResponse(responseText)  { 
		console.log(responseText.msg);
		$("#response_msg").empty();
		$("#response_msg").append(responseText.msg);
		$("#addFileDialog").dialog("close");
		$( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
				"Sulje": function() {
					$(".ui-dialog-content").dialog("close");
				}			
			}
		});
	}
}