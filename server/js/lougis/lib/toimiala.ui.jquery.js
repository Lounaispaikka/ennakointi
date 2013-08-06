/**
* toimiala.ui.jquery.js
* 
* Toimialan ja teeman lis‰‰minen
* jquery, jquery ui, dform(jquery), form(jquery), validate(jquery)
* @author Ville Glad
* 
*/

function openToimialaDialog() {

	//tyhjenn‰ sek‰ teemaDialog ett‰ toimialaDialog ettei tule konflikteja
	$('#toimialaDialog').empty(); //tyhjenn‰ ensin, ettei tule montaa kopiota listasta
	$('#teemaDialog').empty(); //tyhjenn‰ ensin, ettei tule montaa kopiota listasta
	
	$('#toimialaDialog').dialog({
		autoOpen: true,
		modal: true,
		width: 600,
		buttons: {
			"Peruuta": function() {
				$(this).dialog('close');
			}
		}
	});
	var req = $.ajax({
		url: '/run/lougis/cms/getToimialaPages/',
		type: 'POST',
		dataType: 'json',
		async: true //false //synkronisella requestilla voidaan tallentaa variableihin xhr-data
	});
	
		req.done(function(xhr) {
			console.log("xhr", xhr);
			$("#toimialaDialog").append("<div id='valittavat' />");
			$("#toimialaDialog").append("<div id='ohjeet' />");
			$("#valittavat").append("<h3 class='list-h3'>Toimialat</h3>");
			$("#ohjeet").append("<h3 class='list-h3'>Ohjeet</h3><p>Valitse toimiala vasemmalta painamalla painiketta tai lis&auml;&auml; uusi.</p>");
			$("#ohjeet").append("<button id=\"new_toimiala\" class=\"teema_btn\"><img src=\"/img/icons/16x16/add.png\" >Lis&auml;&auml; uusi toimiala</button>");
			$("#new_toimiala").click(function() {
				addToimiala();
			});
			
			$.each(xhr, function(i) {
				console.log(xhr[i].title);
				
				$("#valittavat").append("<button class='teema_btn' id='toimiala_"  + xhr[i].id + "'><img src=\"/img/icons/16x16/pencil.png\" >" + xhr[i].title + "</button>");
				$("#toimiala_" + xhr[i].id).click(function() { 
					console.log("you just clicked: " + xhr[i].id);
					
					$("#toimialaDialog").dialog('close');
					addToimiala(xhr[i].id);
				});
			});
		});
		
		req.fail(function(xhr) {
			alert('fail');
		});
}



function addToimiala(pageId) {
	
	$("#sortable1").empty();
	$("#sortable2").empty();
	function loadRestOfUsers(pageId) {
	
		var req = jQuery.ajax({
			url: '/run/lougis/usersandgroups/jsonListRestOfUsers/',
			data: { page_id: pageId },
			type: 'POST',
			dataType: 'json'
		});
		req.done(function(xhr) {
			//console.log("xhr-success", xhr);
			$.each(xhr, function(i, item) {
				var li = $('<li class=\"ui-state-default\" id=\"'+ item.id + '\"></li>');
				$("#sortable1").append(li);
				$(li).html("<span class=\"userlist_name\">" + item.firstname + " " + item.lastname +"</span>" +  "<span class=\"userlist_org\">" + item.organization + "</span>" + "<span class=\"userlist_email\">" + item.email + "</span>");
			});
		});
		req.fail(function(xhr) {
			//console.log("xhr-fail", xhr);
		});
	}
	
	function loadGroupUsers(pageId) {
	
		var req = jQuery.ajax({
			url: '/run/lougis/usersandgroups/jsonListUsersOfGroup/',
			data: { page_id: pageId },
			type: 'POST',
			dataType: 'json'
		});
		req.done(function(xhr) {
			//console.log("xhr-success", xhr);
			$.each(xhr, function(i, item) {
				var li = $('<li class=\"ui-state-default\" id=\"'+ item.id + '\"></li>');
				$("#sortable2").append(li);
				$(li).html("<span class=\"userlist_name\">" + item.firstname + " " + item.lastname +"</span>" +  "<span class=\"userlist_org\">" + item.organization + "</span>" + "<span class=\"userlist_email\">" + item.email + "</span>");
				
			});
		});
		req.fail(function(xhr) {
			//console.log("xhr-fail", xhr);
		});
	}
	
	function loadAllUsers() {
		var req = jQuery.ajax({
			url: '/run/lougis/usersandgroups/jsonListUsers/',
			type: 'POST',
			dataType: 'json'
		});
		req.done(function(xhr) {
			//console.log("xhr-success", xhr);
			$.each(xhr, function(i, item) {
				var li = $('<li class=\"ui-state-default\" id=\"'+ item.id + '\"></li>');
				$("#sortable1").append(li);
				$(li).html("<span class=\"userlist_name\">" + item.firstname + " " + item.lastname +"</span>" +  "<span class=\"userlist_org\">" + item.organization + "</span>" + "<span class=\"userlist_email\">" + item.email + "</span>");
			});
		});
		req.fail(function(xhr) {
			//console.log("xhr-fail", xhr);
		});
	}
	
	if( pageId == null ) {
		loadAllUsers();
	}
	else {
		loadGroupUsers(pageId);
		loadRestOfUsers(pageId);
	}
	
	var pageData, pageContent, pageColumn;

	var req = $.ajax({
		url: '/run/lougis/cms/getPageJson/',
		data: { page_id: pageId },
		type: 'POST',
		dataType: 'json',
		async: false //synkronisella requestilla voidaan tallentaa variableihin xhr-data
	});
	
		req.done(function(xhr) {
			console.log("xhr", xhr);
			pageData = xhr.pageEnnakointi;
			pageContent = xhr.content;
			pageColumn = xhr.contentColumn;
		});
		
		req.fail(function(xhr) {
			alert('fail');
		});
	
	
	console.log("pageData", pageData);
	console.log("pageContent", pageContent);
	console.log("pageColumn", pageColumn);
	
	if ( typeof pageData === 'undefined' ) {
    	
		pageData = {
			page_id: null,
			title: null,
			nav_name: null,
			url_name: null,
			visible: true,
			published: true,
			extra1: null
		};
    }
	$('#tiedot_form').empty();
	$('#tiedot_form').dform({
		"action" : "/run/lougis/cms/savePageInfo/",//"/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
						//Hidden fields
						{
							"name" : "cms_page[page_id]",
							"id" : "page_id",
							"type" : "hidden",
							"value" : pageData.id
						},
						{
							"name" : "cms_page[page_type]",
							"id" : "page_type",
							"type" : "hidden",
							"value" : "toimiala"
						},
						/*{
							"name" : "cms_page[template]",
							"id" : "page_type",
							"type" : "hidden",
							"value" : "toimiala.php"
						},*/
						{
							"name" : "cms_page[parent_id]",
							"id" : "parent_id",
							"type" : "hidden",
							"value" : pageData.parent_id
						},
						{
							"name" : "cms_page[url_name]",
							"id" : "url_name",
							"type" : "hidden",
							"value" : pageData.url_name
						},
						//Input fields
						{
							"name" : "cms_page[title]",
							"caption" : "Otsikko",
							"type" : "text",
							"value" : pageData.title							
						},
						{
							"name" : "cms_page[nav_name]",
							"caption" : "Sivun nimi navigaatiossa",
							"type" : "text",
							"value" : pageData.nav_name
						},
						{
							"name" : "cms_page[description]",
							"type" : "hidden",
							"value" : pageData.description
							
						},
						{
							"name" : "cms_page[visible]",
							"value" : "true",
							"type" : "hidden"
						},
						{
							"name" : "cms_page[published]",
							"type" : "hidden",
							"value" : "true" 
						},
						{
							"type" : "submit",
							"value" : "Tallenna",
							"class": "next-btn"
						},
						{
							"type" : "button",
							"html" : "Peruuta",
							"class": "cancel-btn",
							"id" : "cancel-btn"
						}	
				
			]
	});
	var options_info = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
       // beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/cms/savePageInfo/" ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
   
    }; 
	// bind form using 'ajaxForm' 
    $('#tiedot_form').ajaxForm(options_info); 


	
	$('#sisalto_form').empty();
	$('#sisalto_form').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
						{
							"name" : "page_id",
							"id" : "page_id",
							"type" : "hidden",
							"value" : pageData.id
						},
						{
							"name" : "new_content",
							"id" : "cms_content",
							"caption" : "Sis&auml;lt&ouml;",
							"value" : pageContent,
							"type" : "textarea"
						},
						{
							"type" : "submit",
							"value" : "Tallenna",
							"class": "next-btn"
						},
						{
							"type" : "button",
							"html" : "Peruuta",
							"class": "cancel-btn"
						}
				
			]
	});
	var options_sisalto = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSerialize: CKUpdate, // ckeditor textareas saved before send
		beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/cms/savePageContent/" ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
   
    }; 
	// bind form using 'ajaxForm' 
    $('#sisalto_form').ajaxForm(options_sisalto); 

	
	
	//tuhoa ckeditor instanssi. editori ei ilman t‰t‰ toimi muuten kuin sivun uudelleenlat. j‰lkeen
	var editor = CKEDITOR.instances['cms_content'];
    if (editor) { editor.destroy(true); }
	
	//luo uusi ckeditor-instanssi
	CKEDITOR.replace( 'cms_content', {
		language: 'fi'
	});
	
	$('#kayttajat_form').empty();
	$('#kayttajat_form').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
				{
					"name" : "page_id",
					"id" : "page_id",
					"type" : "hidden",
					"value" : pageData.id
				},
				{
					"type" : "hidden",
					"class": "next-btn",
					"id" : "admin-group",
					"name" : "admin-group"
				},
				{
					"type" : "submit",
					"value" : "Tallenna",
					"class": "next-btn",
					"id" : "subu"
				},
				{
					"type" : "button",
					"html" : "Peruuta",
					"class": "cancel-btn"
				}
				
			]
	});
	
	var options_kayttajat = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
        url:       "/run/lougis/usersandgroups/editToimialaGroup/" ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
   
    }; 
	// bind form using 'ajaxForm' 
    $('#kayttajat_form').ajaxForm(options_kayttajat); 
	
	// before serialize
	function CKUpdate() {
		for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances[instance].updateElement();
        }
        return true; 
	}
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		var queryString = $.param(formData); 
		return true; 
	} 

	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		$( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
				"Sulje": function() {
				//	$( this ).dialog( "close" );
				//	$("#addToimiala").dialog( "close" );
					$(".ui-dialog-content").dialog("close");
				},
				"Jatka muokkausta": function() {
				$( this ).dialog( "close" );
				}
				
				
			}
		});
		console.log(responseText);
		console.log(statusText);
	}
	
	$("#addToimiala").tabs().dialog({
		autoOpen: true,
		width: 600,
		draggable: false,
		modal: true,
		/*buttons: {
			'Tallenna': function() {                    
				$(this).dialog('close');                    
			},
			'Close': function() {                    
				$(this).dialog('close');                    
			}
		},*/
		open: function() {
			$('.ui-dialog-titlebar').hide(); // hide the default dialog titlebar
		},
		close: function() {
			$('.ui-dialog-titlebar').show(); // in case you have other ui-dialogs on the page, show the titlebar 
		},
		
	}).parent().draggable({handle: ".ui-tabs-nav"}); // the ui-tabs element (#tabdlg) is the object parent, add these allows the tab to drag the dialog around with it
	// stop the tabs being draggable (optional)
	$('.ui-tabs-nav li').mousedown(function(e){
    	e.stopPropagation();
	});
	
	
	$(".cancel-btn").click(function() {
			//sulkee kaikki dialogit
			$(".ui-dialog-content").dialog("close");
			console.log("sulje");
			return false;
			
	});

}


function saveGroup(pageId, users) {
	console.log("pid", pageId);
	console.log("users", users);
	var req = jQuery.ajax({
			url: '/run/lougis/usersandgroups/editToimialaGroup/',
			data: { page_id: pageId, users: users },
			type: 'POST',
			dataType: 'json'
		});
		req.done(function(xhr) {
			console.log("xhr-success", xhr);
			/*$.each(xhr, function(i, item) {
				var li = $('<li class=\"ui-state-default\" id=\"'+ item.id + '\"></li>');
				$("#sortable1").append(li);
				$(li).html("<span class=\"userlist_name\">" + item.firstname + " " + item.lastname +"</span>" +  "<span class=\"userlist_org\">" + item.organization + "</span>" + "<span class=\"userlist_email\">" + item.email + "</span>");
			});*/
		});
		req.fail(function(xhr) {
			console.log("xhr-fail", xhr);
		});
	
	/*$('#kayttajat_form').dform({
		//"action" : "/run/lougis/usersandgroups/editToimialaGroup/",
		"method" : "post",
		"html" :
			[
				//Hidden fields
				{
					"name" : "page_id",
					"id" : "page_id",
					"type" : "hidden",
					"value" : pageId
				},
				{
					"name" : "users",
					"id" : "users",
					"type" : "hidden",
					"value" : users
				},
				
				{
					"type" : "submit",
					"value" : "Tallenna",
					"class": "next-btn"
				},
				{
					"type" : "button",
					"html" : "Peruuta",
					"class": "cancel-btn"
				}	
				
			]
	});*/
	//form submit
   /* var options_users = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
       // beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/usersandgroups/editToimialaGroup/" ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
   
    }; 
	// bind form using 'ajaxForm' 
    $('#kayttajat_form').ajaxForm(options_users); 
	
	// pre-submit callback 
	/*function showRequest(formData, jqForm) { 
		var queryString = $.param(formData); 
		console.log("fdata", formData);
		console.log("jqf",jqForm);
		return true; 
	} */
	
	// post-submit callback 
/*	function showResponse(responseText, statusText)  { 
		/*jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		//jQuery('#addPageFormDialog').dialog('close');
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		/*setTimeout(function(){
            window.location.reload();
        }, 3300); */
	/*	console.log(responseText);
		console.log(statusText);
	} */
	
}
 /*
$(function() {
	$('#addToimiala').tabbedDialog();
	
});*/