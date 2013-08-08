
//Teema dialogi
function teemaDialog(toimialaId) {
	//tyhjenn‰ sek‰ teemaDialog ett‰ toimialaDialog ettei tule konflikteja
	$('#teemaDialog').empty();
	$('#toimialaDialog').empty();
	function getTeemat(toimialaId) {
		
		var req = jQuery.ajax({
		url: '/run/lougis/cms/getTeemaPages/',
		data: { toimiala_id: toimialaId },
		type: 'POST',
		dataType: 'json'
		});
		
			req.done(function(xhr) {
			
				$("#teemaDialog").append("<div id='valittavat' />");
				$("#teemaDialog").append("<div id='ohjeet' />");
				$("#valittavat").append("<h3 class='list-h3'>Teemat</h3>");
				$("#ohjeet").append("<h3 class='list-h3'>Ohjeet</h3><p>Valitse teema vasemmalta painamalla painiketta tai lis&auml;&auml; uusi.</p>");
				$("#ohjeet").append("<button id=\"new_teema\" class=\"teema_btn\"><img src=\"/img/icons/16x16/add.png\" >Lis&auml;&auml; uusi teema</button>");
				
				$("#new_teema").click(function() {
					$("#teemaDialog").dialog('close');
					addNewTeema(toimialaId);
				});
				
				$.each(xhr, function(i, item) {
					console.log("teema-success", item);
				
					var button = $('<button class="muokkaa_teema" id=\"teema_' + item.page_id + '\"><img src=\"/img/icons/16x16/pencil.png\" >' + item.title + '</button>');
					$("#valittavat").append(button);
					
					$("#teema_" + item.page_id).click(function() { 
						console.log("Teeman page id: " + item.page_id);
						editTeema(item.page_id);
						$("#teemaDialog").dialog('close');
						
						//$('#addToimiala').tabbedDialog(xhr[i].id);
					});
		
				});
			});
		
			req.fail(function(xhr) {
				//console.log("xhr-fail", xhr);
			});
	}
	
	
	$('#valitse_teema').empty(); //tyhjenn‰ ensin, ettei tule montaa kopiota listasta
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

//Teeman muokkaus / luonti
function editTeema(pageId) {
	
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
	
	//Tiedot
	$('#teema_tiedot_form').empty();
	$('#teema_tiedot_form').dform({
		"action" : "/run/lougis/cms/savePageInfo/",//"/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
				//Hidden fields
				{
					"name" : "cms_page[page_id]",
					"id" : "teema_page_id",
					"type" : "hidden",
					"value" : pageData.id
				},
				{
					"name" : "cms_page[page_type]",
					"id" : "teema_page_type",
					"type" : "hidden",
					"value" : "teema"
				},
				{
					"name" : "cms_page[parent_id]",
					"id" : "teema_parent_id",
					"type" : "hidden",
					"value" : pageData.parent_id
				},
				{
					"name" : "cms_page[url_name]",
					"id" : "teema_url_name",
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
    $('#teema_tiedot_form').ajaxForm(options_info);
	
	$('#teema_sisalto_form').empty();
	$('#teema_sisalto_form').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
						{
							"name" : "page_id",
							"id" : "teema_page_id",
							"type" : "hidden",
							"value" : pageData.id
						},
						{
							"name" : "new_content",
							"id" : "teema_cms_content",
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
    $('#teema_sisalto_form').ajaxForm(options_sisalto); 
	
	//tuhoa ckeditor instanssi. editori ei ilman t‰t‰ toimi muuten kuin sivun uudelleenlat. j‰lkeen
	var editor = CKEDITOR.instances['teema_cms_content'];
    if (editor) { editor.destroy(true); }
	
	//luo uusi ckeditor-instanssi
	CKEDITOR.replace( 'teema_cms_content', {
		language: 'fi'
	});

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
		console.log(formData);
		console.log(jqForm);
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
	
	//luodaan dialog
	$("#editTeema").tabs().dialog({
		autoOpen: true,
		width: 600,
		draggable: false,
		modal: true,
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

/**
* FUNKTIO: Lis‰‰ uusi toimiala
*
* toimiala_parent_id on toimiala-yl‰sivun id
*
**/
function addNewTeema(toimiala_id) {
	
	$('#teema_tiedot_empty').empty();
	$('#teema_tiedot_empty').dform({
		"action" : "/run/lougis/cms/savePageInfo/",//"/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
				//Hidden fields
				{
					"name" : "cms_page[page_type]",
					"id" : "page_type",
					"type" : "hidden",
					"value" : "teema"
				},
				{
					"name" : "cms_page[parent_id]",
					"id" : "parent_id",
					"type" : "hidden",
					"value" : toimiala_id
				},
				//Input fields
				{
					"name" : "cms_page[title]",
					"caption" : "Otsikko",
					"type" : "text"
				},
				{
					"name" : "cms_page[nav_name]",
					"caption" : "Sivun nimi navigaatiossa",
					"type" : "text"
				},
				{
					"name" : "cms_page[description]",
					"type" : "hidden",
					"value" : null
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
        url:       "/run/lougis/cms/createNewTeema/" ,       // override for form's 'action' attribute 
        type:      "post",        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
   
    }; 
	// bind form using 'ajaxForm' 
    $('#teema_tiedot_empty').ajaxForm(options_info); 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		
		$( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
				"Sulje": function() {
				//	$( this ).dialog( "close" );
				//	$("#editToimiala").dialog( "close" );
					$(".ui-dialog-content").dialog("close");
					$("#response_msg").empty();
				},
				"Jatka muokkausta": function() {
				$( this ).dialog( "close" );
				}			
			}
		});
		$("#response_msg").append(responseText.msg);
		console.log(responseText.msg);
		console.log(statusText);
	}
	
	
	//Tab dialogin luonti
	
	$("#addTeema").tabs().dialog({
		autoOpen: true,
		width: 600,
		draggable: false,
		modal: true,
		open: function() {
			//$('.ui-dialog-titlebar').hide(); // hide the default dialog titlebar
		},
		close: function() {
			//$('.ui-dialog-titlebar').show(); // in case you have other ui-dialogs on the page, show the titlebar 
		},
		
	}).parent().draggable({handle: ".ui-tabs-nav"}); // the ui-tabs element (#tabdlg) is the object parent, add these allows the tab to drag the dialog around with it
	// stop the tabs being draggable (optional)
	$('.ui-tabs-nav li').mousedown(function(e){
    	e.stopPropagation();
	});
	
	
	$(".cancel-btn").click(function() {
		//sulkee kaikki dialogit
		$(".ui-dialog-content").dialog("close");
		console.log
		("sulje");
		return false;
		
	});
}