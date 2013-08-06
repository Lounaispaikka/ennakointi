/**
* ennakointi.ui.jquery.js
* 
* Ennakointiaineiston lis‰‰minen
* jquery, jquery ui, dform(jquery), form(jquery), validate(jquery)
* @author Ville Glad
* 
*/

jQuery(function() {
	
	//Dialog window
	jQuery('#addContentDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600,
		buttons: {
			"Peruuta": function() {
				jQuery(this).dialog('close');
			}
		}
	});
	
});

/*
* 
* FUNKTIO: Lis‰‰ uusi alasivu ennakointiin
*
*/
function openAddPageDialog(parent_id) {
	//Add new sub page
	jQuery('#addPageFormDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600
	});
	jQuery('#addPageFormDialog').dialog('open');
	newPage(parent_id);
	jQuery('#addContentDialog').dialog('close');
	return false;
}

/*
* 
* FUNKTIO: Lis‰‰ uusi tilasto ennakointiin
*
*/

function openAddChartDialog(parent_id) {
	startNewChart(parent_id); //viittaa charts.ui.extjs.js olevaan funktioon
	return false;
	//return false;
}

/*
* 
* FUNKTIO: Lis‰‰ uusi uutinen ennakointiin
*
*/
function openAddNewsDialog(parent_id) {
	//Add new sub page
	jQuery('#addPageFormDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600
	});
	jQuery('#addPageFormDialog').dialog('open');
	newNews(parent_id);
	jQuery('#addContentDialog').dialog('close');
	return false;
}
/*
* 
* FUNKTIO: Lis‰‰ uusi linkki ennakointiin
*
*/
function openAddLinkDialog(parent_id) {
	//Add new sub page
	jQuery('#addPageFormDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600
	});
	jQuery('#addPageFormDialog').dialog('open');
	newLink(parent_id);
	jQuery('#addContentDialog').dialog('close');
	return false;
}


/*function openAddDialog(parent_id) {
	
	//First dialog choice buttons
	jQuery('#alasivuBtn').button(
		{ label: "Lis&auml;&auml; alasivu" },
		{ icons: { primary:"ui-icon-circle-plus" } }
	);
	
	jQuery('#indikaattoriBtn').button(
		{ label: "Lis&auml;&auml; tilasto/indikaattori" },
		{ icons: { primary:"ui-icon-signal" } }
	);
	
	jQuery('#dokumenttiBtn').button(
		{ label: "Lis&auml;&auml; tiedosto/dokumentti" },
		{ icons: { primary:"ui-icon-document" } }
	);
	
	jQuery('#linkkiBtn').button(
		{ label: "Lis&auml;&auml; linkki" },
		{ icons: { primary:"ui-icon-extlink" } }
	);
	
	//Add new sub page
	jQuery('#addPageFormDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600
	});
	jQuery('#alasivuBtn').click(function(){
		jQuery('#addPageFormDialog').dialog('open');
		newPage(parent_id);
		jQuery('#addContentDialog').dialog('close');
		return false;
	});
	jQuery('#addContentDialog').dialog('open');
	
	//Add new file dialog
	jQuery('#addFileDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600
	});
	jQuery('#dokumenttiBtn').click(function(){
		jQuery('#addFileDialog').dialog('open');
		jQuery('#addContentDialog').dialog('close');
		return false;
	});
	
	//Add new chart (ExtJs window / charts.ui.extjs.js)
	jQuery('#indikaattoriBtn').click(function(){
		startNewChart(); //viittaa charts.ui.extjs.js olevaan funktioon
		return false;
	});
	
}*/

/**********************************************************************************************/
//Load all the shit: php run/cms/getPageJson/
function openEditDialog(pageId, editType) {
	loadCmsData(pageId, editType);
	
}

function loadCmsData(pageId, editType) {
	
	var req = jQuery.ajax({
		url: '/run/lougis/cms/getPageJson/',
		data: { page_id: pageId },
		type: 'POST',
		dataType: 'json'
	});
	req.done(function(xhr) {
		if(editType === 'page_info') editPageInfoDialog(xhr.page.page_id, xhr.pageEnnakointi);
		else if(editType === 'page_content') editPageContentDialog(xhr.page.page_id, xhr.content);
		else alert('fail');
	});
	req.fail(function(xhr) {
		alert('fail');
	});
}
/**********************************************************************************************/
//show page edit dialog
function editPageInfoDialog(pageId, pageData) {
	
	//Edit this page
	jQuery('#editPageInfoDialog').dialog({
		autoOpen: true,
		modal: true,
		width: 600
	});
	
	if ( typeof pageData === 'undefined' ) {
    	
		pageData = {
			page_id: null,
			title: null,
			nav_name: null,
			url_name: null,
			in_navigation: true,
			published: true,
			extra1: null
		};
    }		
	
	//clear divs first
	jQuery('#cmsForm_info').empty();
	
	//Add Subscriber
	//Tietokantaan checkbox checked on tallennettu true/false, joten jos value on true niin annetaan atribuutti checked=checked
	jQuery.dform.subscribe("addcheck", function(options, type) {
		if(type === "checkbox" ) {
			if(jQuery(this).attr("value") === "true") {
				jQuery(this).attr("checked", "checked");
			}
		}
	});		
	
	jQuery('#cmsForm_info').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "get",
		"html" :
			[
						//Hidden fields
						{
							"name" : "cms_page[page_id]",
							"id" : "page_id",
							"type" : "hidden",
							"value" : pageId
						},
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
							"value" : pageData.parent_id
						},
						{ //t‰m‰ pit‰‰ pist‰‰ automaattisesti sivun page_id:ksi (php-funktio hoitaa sen)
							"name" : "cms_page[url_name]",
							"id" : "url_name",
							//"caption" : "url-nimi",
							"type" : "hidden",
							"value" : null
						/*	"validate" : {
								"required": true,
								"minlength": 2,
								"messages": {
									"required": "Pakollinen tieto",
								}
							}*/
						},
						//Input fields
						{
							"name" : "cms_page[title]",
							"caption" : "Otsikko",
							"type" : "text",
							"value" : pageData.title,
							"validate" : {
								"required": true,
								"minlength": 2,
								"messages": {
									"required": "Pakollinen tieto",
								}
							}
						},
						{
							"name" : "cms_page[nav_name]",
							"caption" : "Sivun nimi navigaatiossa",
							"type" : "text",
							"value" : pageData.nav_name,
							"validate" : {
								"required": true,
								"minlength": 2,
								"messages": {
									"required": "Pakollinen tieto",
								}
							}
						},
						{
							"name" : "cms_page[description]",
							//"caption" : "Lyhyt kuvaus",
							"type" : "hidden",
							"value" : null,
							//"value" : pageData.description,
						/*	"validate" : {
								"required": true,
								"minlength": 2,
								"messages": {
									"required": "Pakollinen tieto",
								}
							}*/
						},
						{
							"name" : "cms_page[visible]",
							//"caption" : "Navigaatiossa",
							"value" : "t",
							//"value" : pageData.visible,
							//"addcheck" : "addCheck",
							"type" : "hidden"
						},
						{
							"name" : "cms_page[published]",
							//"caption" : "Julkaistu",
							"value" : "t",
							//"value" : pageData.published,
							//"addcheck" : "addCheck",
							"type" : "hidden"
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
	
	//Cancel button behaviour
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageInfoDialog').dialog('close');
		return false;
	});
	
	//form submit
    var options = { 
        //target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/cms/savePageInfo/" ,       // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm_info').ajaxForm(options); 
	
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		//var queryString = $.param(formData); 
		//alert('About to submit: \n\n' + queryString); 
		return true; 
	} 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);
		jQuery('#editPageInfoDialog').dialog('close');
	} 
	
}
/**********************************************************************************************/
//show page edit dialog
function editPageContentDialog(pageId, contentData) {
	
	//Edit this page
	jQuery('#editPageContentDialog').dialog({
		autoOpen: true,
		modal: true,
		width: 600
	});
	
	
	if ( typeof contentData === 'undefined' ) {
    	
		contentData = {
			page_id: null,
			pageContent: null	
		};
    }		
	
	//clear divs first
	jQuery('#cmsForm_content').empty();

	
	jQuery('#cmsForm_content').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "get",
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
	var myNicEditor = new nicEditor({fullPanel : true}).panelInstance('cms_content');
	//Cancel button behaviour
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageContentDialog').dialog('close');
		return false;
	});
	
	
	//form submit
    var options = { 
        //target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/cms/savePageContent/" ,       // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm_content').ajaxForm(options); 
	
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		var queryString = $.param(formData); 
		//alert('About to submit: \n\n' + queryString); 
		return true; 
	} 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		jQuery('#editPageContentDialog').dialog('close');
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
                         window.location.reload();
             }, 3300); 
	} 
	
}

/**********************************************************************************************/

function newPage(parent_id) {
	//clear div first
	jQuery('#cmsForm').empty();
	//dform
	jQuery('#cmsForm').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "get",
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
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},
				{
					"name" : "cms_page[nav_name]",
					"caption" : "Sivun nimi navigaatiossa",
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
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
				/*{
					"name" : "cms_page[visible]",
					"caption" : "Navigaatiossa",
					"addcheck" : "addCheck",
					"type" : "checkbox"
				},
				{
					"name" : "cms_page[published]",
					"caption" : "Julkaistu",
					"addcheck" : "addCheck",
					"type" : "checkbox"
				},*/
				{
					"name" : "cms_page[visible]",
					"value": "t",
					"type" : "hidden"
				},
				{
					"name" : "cms_page[published]",
					"value": "t",
					"type" : "hidden"
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
	//Buttons behaviour
	//Seuraava ja peruuta
	
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageFormDialog').dialog('close');
		return false;
	});
	//form submit
    var options = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/cms/createNewPage/" ,       // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm').ajaxForm(options); 
	
		
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		// formData is an array; here we use $.param to convert it to a string to display it 
		// but the form plugin does this for you automatically when it submits the data 
		var queryString = $.param(formData); 
	 
		// jqForm is a jQuery object encapsulating the form element.  To access the 
		// DOM element for the form do this: 
		// var formElement = jqForm[0]; 
	 
		//alert('About to submit: \n\n' + queryString); 
	 
		// here we could return false to prevent the form from being submitted; 
		// returning anything other than false will allow the form submit to continue 
		return true; 
	} 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		jQuery('#addPageFormDialog').dialog('close');
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
                         window.location.reload();
             }, 3300); 
	} 
}
/***************************************************************************************************/

//Uutinen

function newNews(parent_id) {
	//clear div first
	jQuery('#cmsForm').empty();
	//dform
	jQuery('#cmsForm').dform({
		"action" : "testiNotRela.php",//"/run/lougis/cms/createNewPage/",
		"method" : "get",
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
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},
				{
					"name" : "news[description]",
					"caption" : "Lyhyt kuvaus",
					"type" : "textarea",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},
				{
					"name" : "news[content]",
					"caption" : "Sis&auml;lt&ouml;",
					"type" : "textarea",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
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
					"name" : "news[source]",
					"caption" : "L&auml;hde",
					"type" : "text",
					"value" : "http://",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},
				{
					"name" : "news[source_url]",
					"caption" : "L&auml;hdelinkki",
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
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
	//Buttons behaviour
	//Seuraava ja peruuta
	
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageFormDialog').dialog('close');
		return false;
	});
	//form submit
    var options = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/news/saveNewsEnnakointi/" ,       // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm').ajaxForm(options); 
	
		
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		// formData is an array; here we use $.param to convert it to a string to display it 
		// but the form plugin does this for you automatically when it submits the data 
		var queryString = $.param(formData); 
	 
		// jqForm is a jQuery object encapsulating the form element.  To access the 
		// DOM element for the form do this: 
		// var formElement = jqForm[0]; 
	 
		//alert('About to submit: \n\n' + queryString); 
	 
		// here we could return false to prevent the form from being submitted; 
		// returning anything other than false will allow the form submit to continue 
		return true; 
	} 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		jQuery('#addPageFormDialog').dialog('close');
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
            window.location.reload();
        }, 3300); 
	} 
	
}

//delete news+page
function delNews(newsPageId, parentId) {
	$.ajax({
		url: "/run/lougis/news/deleteNews/",
		type: "POST",
		data: {
			page_id: newsPageId,
			//news_id: newsId
		}
	}).done(function(res) {
		console.log(res.msg, newsPageId);
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(res.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
			window.location.href="/fi/"+parentId+"/";
		}, 3300); 
	});
}

/** **************************************************************************************/

//linkki
function newLink(parent_id) {
	//clear div first
	jQuery('#cmsForm').empty();
	//dform
	jQuery('#cmsForm').dform({
		"method" : "post",
		"html" :
			[
				{
					"name" : "news[published]",
					"id" : "published",
					"type" : "hidden",
					"value" : "t"
				},	
				{
					"name" : "news[source_url]",
					"caption" : "L&auml;hdelinkki",
					"type" : "text",
					"value" : "http://",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},
				{
					"name" : "news[title]",
					"caption" : "Otsikko",
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
				},
				{
					"name" : "news[description]",
					"caption" : "Lyhyt kuvaus",
					"type" : "textarea",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
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
					"name" : "news[source]",
					"caption" : "L&auml;hde",
					"type" : "text",
					"validate" : {
						"required": true,
						"minlength": 2,
						"messages": {
							"required": "Pakollinen tieto",
						}
					}
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
	//Buttons behaviour
	//Seuraava ja peruuta
	
	jQuery(".cancel-btn").click(function() {
		jQuery('#editPageFormDialog').dialog('close');
		return false;
	});
	//form submit
    var options = { 
        target:        '#formResponse',   // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,  // pre-submit callback 
        success:       showResponse, // post-submit callback 
 
        // other available options: 
        url:       "/run/lougis/news/saveNewsLinkki/" ,       // override for form's 'action' attribute 
        //type:      type        // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
        //clearForm: true        // clear all form fields after successful submit 
        //resetForm: true        // reset the form after successful submit 
 
        // $.ajax options can be used here too, for example: 
        //timeout:   3000 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm').ajaxForm(options); 
	
		
	// pre-submit callback 
	function showRequest(formData, jqForm) { 
		// formData is an array; here we use $.param to convert it to a string to display it 
		// but the form plugin does this for you automatically when it submits the data 
		var queryString = $.param(formData); 
	 
		// jqForm is a jQuery object encapsulating the form element.  To access the 
		// DOM element for the form do this: 
		// var formElement = jqForm[0]; 
	 
		//alert('About to submit: \n\n' + queryString); 
	 
		// here we could return false to prevent the form from being submitted; 
		// returning anything other than false will allow the form submit to continue 
		return true; 
	} 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		jQuery('#addPageFormDialog').dialog('close');
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
            window.location.reload();
        }, 3300); 
	} 
	
}

//delete news+page
function delLink(newsPageId, newsId) {
	$.ajax({
		url: "/run/lougis/news/deleteNews/",
		type: "POST",
		data: {
			page_id: newsPageId,
			news_id: newsId
		}
	}).done(function(res) {
		console.log(res.msg, newsPageId);
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(res.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
			window.location.reload();
		}, 3300); 
	});
}