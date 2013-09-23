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
	return false;
}

function newPage(parent_id) {
	//clear div first
	jQuery('#cmsForm').empty();
	//dform
	jQuery('#cmsForm').dform({
		"action" : "/run/lougis/cms/createNewPage/",
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
        success:       showResponse, // post-submit callback
        url:       "/run/lougis/cms/createNewPage/" ,       // override for form's 'action' attribute 
        type:      "post",     // 'get' or 'post', override for form's 'method' attribute 
        dataType:  "json"        // 'xml', 'script', or 'json' (expected server response type) 
    }; 
	// bind form using 'ajaxForm' 
    $('#cmsForm').ajaxForm(options); 
	 
	// post-submit callback 
	function showResponse(responseText, statusText)  { 
		console.log(responseText);
		console.log(statusText);
		jQuery("#formResponse").fadeOut( 100 , function() {
			jQuery("#formResponse p").html(responseText.msg);
		}).fadeIn( 1000 ).delay(1300).fadeOut(1000);	
		jQuery('#addPageFormDialog').dialog('close');
/*//quick fix sivun lataus, t‰m‰n voisi muuttaa ajax lataukseksi
		setTimeout(function(){
                         window.location.reload();
             }, 3300); */
	} 
}