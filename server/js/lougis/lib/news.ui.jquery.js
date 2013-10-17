/*
* 
* FUNKTIO: Lisää uusi uutinen ennakointiin
*
*/
function openAddNewsDialog(parent_id) {
	
	//dialog width and height according to window size
	var wWidth = $(window).width();
    var dWidth = wWidth * 0.8;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.8;
	
	//Add new sub page
	jQuery('#addPageFormDialog').dialog({
		autoOpen: false,
		modal: true,
		width: dWidth,
		height: dHeight,
		buttons: [
			{
				text: "Peruuta",
				click: function() {
					$(this).dialog("close");
				}
				
			},
			{
				text: "Tallenna",
				click: function() {
					$('#cmsForm').submit();
				}
			}
        ]
	});
	jQuery('#addPageFormDialog').dialog('open');
	newNews(parent_id);
	jQuery('#addContentDialog').dialog('close');
	return false;
}


//Uutinen

function newNews(parent_id) {
	//clear div first
	jQuery('#cmsForm').empty();
	//dform
	jQuery('#cmsForm').dform({
		"html" :
			[
				{
					"name" : "news[published]",
					"id" : "published",
					"type" : "hidden",
					"value" : "t"
				},	
				{
					"name" : "news[title]",
					"caption" : "Otsikko",
					"type" : "text"/* ,
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					} */
				},
				{
					"name" : "news[description]",
					"caption" : "Lyhyt kuvaus",
					"type" : "textarea"/* ,
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					} */
				},
				{
					"name" : "news[content]",
					"caption" : "Sis&auml;lt&ouml;",
					"type" : "textarea"/* ,
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					} */
				},
				{
					"name" : "news[created_date]",
					"value": null,
					"type" : "hidden"
				},
				{
					"name" : "news[parent_id]",
					"value": parent_id,
					"type" : "hidden"
				},
				{
					"name" : "news[source_url]",
					"caption" : "Lähdelinkki",
					"type" : "text",
					"value" : "http://"/* ,
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					} */
				},
				{
					"name" : "news[source]",
					"caption" : "Lähde",
					"type" : "text"/* ,
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					} */
				}
				
			]
		
	});
	//Buttons behaviour
	//Seuraava ja peruuta
	
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageFormDialog').dialog('close');
		return false;
	});
	//form submit
    var options = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
      
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/news/saveNewsEnnakointi/" ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm').ajaxForm(options); 
	
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		$( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
				"OK": function() {
					$("#response_msg").empty();
					$(".ui-dialog-content").dialog("close");
				}		
			}
		});
		$("#response_msg").append("<li>" + responseText.msg + "</li>" );
		console.log(responseText);
		console.log(statusText);
	} 
	
}

//delete news+page
function delNews(newsPageId, newsId) {
	$.ajax({
		url: "/run/lougis/news/deleteNews/",
		type: "POST",
		data: {
			page_id: newsPageId,
			news_id: newsId
		}
	}).done(function(res) {
		console.log(res.msg);
		window.alert(res.msg);
		/* $( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
				"OK": function() {
					$("#response_msg").empty();
					$(".ui-dialog-content").dialog("close");
				}		
			}
		});
		$("#response_msg").append("<li>" + res.msg + "</li>" ); */
	})
	.fail(function (res) {
		console.log(res.msg);
		window.alert(res.msg);
	});
	
}