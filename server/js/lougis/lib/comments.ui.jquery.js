/**
* comments.ui.jquery.js
* Kommentointi javascriptit
*
**/

function likeComment( MsgId ) {
	
	console.log("likeComment", MsgId);
	likeAjax( MsgId, 1 );
	return false;
	
}

function dislikeComment( MsgId ) {
	
	console.log("dislikeComment", MsgId);
	likeAjax( MsgId, -1 );
	return false;
	
}

function likeAjax( MsgId, LikeValue ) {
	
	var request = $.ajax({
		url: '/run/lougis/comment/likeMsg/',
		data: {
			msgid: MsgId,
			likeval: LikeValue
		},
		type: "POST"
	});
	
	request.done(function(response) {
		console.log(response);
	});
	
	request.fail(function(response) {
		console.log(response);
	});
		
	
	console.log("likeAjax", MsgId, LikeValue);
	
}
/* 
function showReplyBox( ParentMsgId ) {
	
	var request = $.ajax({
		url: '/run/lougis/comment/replyBoxHtml/',
		data: {
			msgid: ParentMsgId
		},
		type: "POST"
	});
	
	request.done(function(response) {
		console.log(response);
		createMessageForm('replyform'+ParentMsgId, this_page, ParentMsgId);
	});
	
	request.fail(function(response) {
		console.log(response);
	});
	
	this_page = null;
	console.log(ParentMsgId);
	createMessageForm('replyform'+ParentMsgId, this_page, ParentMsgId );
	$("#newcomment").hide();
	$("#replybox"+ParentMsgId).show();
	return false;
} */
function showReplyBox( this_page, ParentMsgId ) {
	
	if (typeof this_page == 'undefined') this_page = null;
	
	var request = $.ajax({
		url: '/run/lougis/comment/replyBoxHtml/',
		data: {
			msgid: ParentMsgId
		},
		type: "POST"
	});
	
	request.done(function(response) {
		$("#newcomment").hide();
		$("#replybox"+ParentMsgId).empty();
		$("#replybox"+ParentMsgId).append(response);
		$("#replybox"+ParentMsgId).show();
		createMessageForm('replyform'+ParentMsgId, this_page, ParentMsgId );
		
	});
	
	request.fail(function(response) {
		console.log(response);
	});
	
	return false;
}

function closeReplyBox( /*ParentMsgId*/ ) {	
	$(".replybox").hide(); //sulje kaikki replyboxit	
}

/* function showNewMsg(this_page, replyTo, topic_id ) {	
	
	if (typeof topic_id == 'undefined') topic_id = null;
	
	createMessageForm('newcommentform', this_page, replyTo, topic_id );
	//show and animate new comment msgbox
	$("#newcomment").show();
	return false;
	
} */
//create new message if topic already made
function showNewMsg(this_page, topic_id) {	
	$("#kommentti_form").empty();
	if (typeof topic_id == 'undefined') topic_id = null;
	if (typeof this_page == 'undefined') this_page = null;
	var replyTo = null;
	
	createMessageForm('newcommentform', this_page, replyTo, topic_id );
	//show and animate new comment msgbox
	$(".replybox").hide();
	$("#newcomment").show();
	return false;
	
}

//create new topic and message
function showNewTopic(this_page) {	
	$("#kommentti_form").empty();
	if (typeof this_page == 'undefined') this_page = null;
	
	var replyTo = null;
	var topic_id = null;
	
	createMessageForm('newcommentform', this_page, replyTo, topic_id );
	//show and animate new comment msgbox
	$(".replybox").hide();
	$("#newcomment").show();
	return false;
	
}

function CommentPage(page) {	
	
	createMessageForm( 'newcommentform', page );
	$("#newcomment").show();
	return false;
	
}

function hideNewMsg() {
	
	$("#newcomment").hide();
	$("#kommentti_form").empty();
	return false;
	
}

//estä viesti-linkkien käyttö
function disableLinks() {
	//hide
	$("#newthread").hide();
	$(".replythread").hide();
}
//salli viesti-linkkien käyttö
function enableLinks() {
	//show
	$("#newthread").show();
	$(".replythread").show();
}

function cancelMsgEdit() {
	hideNewMsg();
	closeReplyBox();
	enableLinks();
	return false;
}

function createMessageForm( targetId, this_page, replyTo, topic_id ) {
	
	disableLinks();
	
	//tarkista ja aseta arvot
	console.log("createmsgform");
	if ( typeof replyTo == 'undefined' ) replyTo = null;
	if ( typeof topic_id == 'undefined' ) topic_id = null;
	if ( typeof this_page == 'undefined' ) this_page = null;
	
	if ( targetId === 'newcommentform' ) {
		var form_jq_name = $('#kommentti_form');
		var form_name = 'kommentti_form';
		replyTo = null;
	}	
	else {
		var form_jq_name = $('#vastaa_form'+replyTo);
		var form_name = 'vastaa_form'+replyTo;
	}
	//form_jq_name.empty();
	console.log(form_jq_name);
	
	//luo lomake
	form_jq_name.dform({
		"action" : "",
		"method" : "post",
		"name": form_name,
		"class": "comment_form_fields",
		"html" :
			[
				//Hidden fields
				{
					"name" : "this_page",
					/* "id" : "this_page", */
					"type" : "hidden",
					"value" : this_page
				},
				{
					"name" : "reply_to",
					/* "id" : "reply_to", */
					"type" : "hidden",
					"value" : replyTo
				},
				{
					"caption" : "Viestin otsikko",
					"name" : "comment[title]",
					/* "id" : "comment[title]", */
					"type" : "text"
				},
				{
					"caption" : "Viesti",
					"name" : "comment[msg]",
					/* "id" : "parent_id", */
					"type" : "textarea"
				},
				{
					"caption" : "Mik&auml; on t&auml;m&auml;n vuoden vuosiluku?",
					"name" : "comment[check]",
					/* "id" : "comment[check]", */
					"type" : "text"
				},
				{
					"name" : "comment[topic_id]",
					/* "id" : "comment[topic_id]", */
					"type" : "hidden",
					"value" : topic_id
				},
				{
					"name" : "comment[parent_id]",
					/* "id" : "comment[parent_id]", */
					"type" : "hidden",
					"value" : replyTo
				},
				{
					"type" : "submit",
					"value" : "Tallenna",
					"class": "send_comment_btn"
				}				
			]
	});
	form_name = null;
	
	var options = { 
       // target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
        url:       '/run/lougis/comment/newcomment/' ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
   
    }; 
	// bind form using 'ajaxForm' 
    form_jq_name.ajaxForm(options); 
	
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		var queryString = $.param(formData); 
		return true; 
	} 

	// post-submit callback 
	function showResponse(responseText)  { 
		$( "#response_msg" ).empty();
		if(responseText.success == false) {
			console.log("false tuli");
		}
		else {
			$("#response_msg").empty();
			$( "#response_msg" ).append(responseText.msg);
			hideNewMsg();
			$( "#dialog-message" ).dialog({
				modal: true,
				buttons: {
					"Sulje": function() {
						$(".ui-dialog-content").dialog("close");
					}			
				}
			});
		}
		
		
		//ajax reload page comments
		var request = $.ajax({
			url: '/run/lougis/comment/getCommentsHtml/',
			data: {
				page_id: this_page
			},
			type: "POST"
		});
		request.done(function(response) {
			$("#ajax_request_div").empty();
			$("#ajax_request_div").append(response);
			
		});
		
		enableLinks();
		console.log(responseText);
	
	}
	
	
}

