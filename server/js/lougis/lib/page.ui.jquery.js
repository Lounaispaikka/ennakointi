/*
* 
* FUNKTIO: Lis‰‰ uusi alasivu ennakointiin
*
*/
function openAddPageDialog(parent_id) {
	
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
				text: "Sulje",
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
	newPage(parent_id);
	return false;
}

/*
* uuden sivun lomake
*
*/
function newPage(parent_id) {
	console.log("np");
	//clear div first
	jQuery('#cmsForm').empty();
	//dform
	jQuery('#cmsForm').dform({
		//"action" : "/run/lougis/cms/createNewPage/",
		"method" : "post",
		"html" :
			[
				//Hidden fields
				{
					"name" : "cms_page[page_type]",
					"id" : "page_type",
					"type" : "hidden",
					"value" : "teema_aineisto"
				},
				{
					"name" : "cms_page[parent_id]",
					"id" : "parent_id",
					"type" : "hidden",
					"value" : parent_id
				},
				/*{ //t‰m‰ pit‰‰ pist‰‰ automaattisesti sivun page_id:ksi (php-funktio hoitaa sen)
					"name" : "cms_page[url_name]",
					"id" : "url_name",
					"caption" : "url-nimi",
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},*/
				//Input fields
				{
					"name" : "cms_page[title]",
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
					"name" : "cms_page[nav_name]",
					"caption" : "Sivun nimi navigaatiossa",
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
					"name" : "cms_page[description]",
					//"caption" : "Lyhyt kuvaus",
					"type" : "hidden",
					"value" : null
					/*"type" : "textarea",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
					*/
				},
				{
					"name" : "cms_page[visible]",
					"value": "t",
					"type" : "hidden"
				},
				{
					"name" : "cms_page[published]",
					"value": "t",
					"type" : "hidden"
				}
				
			]
		
	});
	//Buttons behaviour
	//Seuraava ja peruuta
	
	$(".cancel-btn").click(function() {
		$(this).dialog('close');
		return false;
	});
	//form submit
    var options = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
        success:       showResponse, // post-submit callback
        url:       "/run/lougis/cms/createNewPage/" ,       // override for form's 'action' attribute 
        type:      "post",     // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
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
					editPageContentDialog(responseText.page_id);
				}		
			}
		});
		$("#response_msg").append("<li>" + responseText.msg + "</li>" );
		console.log(responseText);
		console.log(statusText);
		
		/*console.log(responseText);
		console.log(statusText);
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		jQuery('#addPageFormDialog').dialog('close');*/
/*//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
                         window.location.reload();
             }, 3300); */
	} 
}

//show page edit dialog
function editPageContentDialog(pageId, contentData) {
	
	//dialog width and height according to window size
	var wWidth = $(window).width();
    var dWidth = wWidth * 0.8;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.8;
	
	//Edit this page
	jQuery('#editPageContentDialog').dialog({
		autoOpen: true,
		modal: true,
		width: 600,
		width: dWidth,
		height: dHeight,
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
					$('#cmsForm_content').submit();
				}
			}
        ],
	});
	
	if ( typeof pageId === 'undefined' ) {
		var pageId = null;
	}
	if ( typeof contentData === 'undefined' ) {
		var	contentData = null	
    }		
	
	//clear divs first
	jQuery('#cmsForm_content').empty();

	
	jQuery('#cmsForm_content').dform({
		"method" : "post",
		"html" :
			[
				{
					"name" : "page_id",
					"id" : "page_id",
					"type" : "hidden",
					"value" : pageId
				},
				{
					"name" : "new_content",
					"id" : "cms_content",
					"caption" : "Sis&auml;lt&ouml;",
					"value" : contentData,
					"type" : "textarea"
				},
			]
	});
	
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageContentDialog').dialog('close');
		return false;
	});
	
	
	//form submit
    var options = { 
        beforeSerialize: CKUpdate, // ckeditor textareas saved before send		
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/cms/savePageContent/" ,       // override for form's 'action' attribute 
        type:     "post",       // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
    
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm_content').ajaxForm(options); 
	
	 //tuhoa ckeditor instanssi. editori ei ilman t‰t‰ toimi muuten kuin sivun uudelleenlat. j‰lkeen
	var editor = CKEDITOR.instances['cms_content'];
    if (editor) { editor.destroy(true); }
	
	//luo uusi ckeditor-instanssi
	CKEDITOR.replace( 'cms_content', {
		language: 'fi'
	});

	// before serialize
	function CKUpdate() {
		for ( instance in CKEDITOR.instances ) {
            CKEDITOR.instances[instance].updateElement();
        }
        return true; 
	} 
	
	 
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

/**********************************************************************************************/
//delete news+page
function delPage(pageId, parentId) {
	$.ajax({
		url: "/run/lougis/cms/deletePage/",
		type: "POST",
		data: {
			page_id: pageId
		}
	}).done(function(res) {
		$( "#dialog-message" ).dialog({
			modal: true,
			buttons: {
				"OK": function() {
					
					$("#response_msg").empty();
					$(".ui-dialog-content").dialog("close");
					window.location.href = '/fi/' + parentId + '/';
				}		
			}
		});
		$("#response_msg").append("<li>" + res.msg + "</li>" );
		
	});
}