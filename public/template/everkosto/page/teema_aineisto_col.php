<?
if ( $_SESSION['user_id']) {
?>
<script type="text/javascript" src="/js/jqueryPlugins/jquery.dform-1.0.1.js"></script>
<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script> 
<script type="text/javascript" src="/js/lougis/lib/ennakointi.ui.jquery.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
<div id="formResponse">
	<p></p>
</div>
<!--<a href="#" id="addContent" onclick="createWindow(<?=$Pg->parent_id?>);" style="margin-left: 15px;">Lis&auml;&auml; aineisto</a><br />-->
<!--<a id="addContent" class="linkJs">Lis&auml;&auml; aineisto(ext)</a>-->
<a href="javascript:void(0)" id="addPage" class="linkJs">Lis&auml;&auml; sivu</a>
<a href="javascript:void(0)" id="addChart" class="linkJs">Lis&auml;&auml; tilasto</a>
<a href="javascript:void(0)" id="addLink" class="linkJs">Lis&auml;&auml; linkki</a>
<a href="javascript:void(0)" id="addFile" class="linkJs">Lis&auml;&auml; tiedosto</a>
<a href="javascript:void(0)" id="addNews" class="linkJs">Lis&auml;&auml; uutinen</a>
<!--
<div id="addFileDialog" title="Lis&auml;&auml; tiedosto">
	<form action="" method="post" enctype="multipart/form-data" class="ui-widget">
        <input type="file" name="f"><br>
        <input type="submit" value="L&auml;het&auml;">
    </form>
    
    <div class="progress">
        <div class="bar"></div >
        <div class="percent">0%</div >
    </div>
    
    <div id="status"></div>
</div>	
-->
<!--<div id="addContentDialog" title="Lis&auml&auml; aineisto">
	<button id="alasivuBtn"></button>
	<button id="indikaattoriBtn"></button>
	<button id="dokumenttiBtn"></button>
	<button id="linkkiBtn"></button>
</div>
-->
<div id="addPageFormDialog" title="Lis&auml&auml; alasivu">
	<div id="cmsInfo" style="position:relative;">
		<form id="cmsForm" class="ui-widget">
		</form>
	</div>
		<!--<form>
		<fieldset id="basic">
			<label for="title">Otsikko</label>
			<input type="text" name="cms_page[title]" id="title" class="text" />
			<label for="nav_name">Nim</label>
			<input type="text" name="cms_page[nav_name]" id="nav_name" class="text" />
			<label for="url_name">URL-nimi</label>
			<input type="text" name="cms_page[url_name]" id="url_name" class="text" />
			<label for="description">Lyhyt kuvaus</label>
			<textarea name="cms_page[description]" id="description" class="text" rows="6" cols="80"/>
			</textarea>
		</fieldset>	
		<input type="hidden" name="cms_page[visible]" value="true" />
		<input type="hidden" name="cms_page[published]" value="true" />
		<input type="hidden" name="cms_page[parent_id]" value="<?=$Pg->parent_id?>" />
		
	</form>-->
</div>

<div id="formResponse">
	<p>dadaa</p>
</div>
<?
//Etsi alasivu tämän sivun alta
$chartPage = new \Lougis_cms_page();
$chartPage->parent_id = $Pg->id;
$chartPage->find();
$alasivut = array();
while( $chartPage->fetch() ) $alasivut[] = clone($chartPage);
//var_dump($alasivut);
echo array_search('Tilastot', $alasivut);
?>
<script type="text/javascript">
	jQuery(function() {	
		jQuery('#addPage').click(function(){
			openAddPageDialog(<?=$Pg->id?>);
			return false;
		});
		jQuery('#addChart').click(function(){
			openAddDialog(<?=$Pg->id?>);
			return false;
		});
		jQuery('#addLink').click(function(){
			openAddLinkDialog(<?=$Pg->id?>);
			return false;
		});
		jQuery('#addFile').click(function(){
			openAddDialog(<?=$Pg->id?>);
			return false;
		});
		jQuery('#addNews').click(function(){
			openAddNewsDialog(<?=$Pg->id?>);
			return false;
		});
	});
	
	(function() {
		
	var bar = $('.bar');
	var percent = $('.percent');
	var status = $('#status');
	   
	$('form').ajaxForm({
		beforeSend: function() {
			status.empty();
			var percentVal = '0%';
			bar.width(percentVal)
			percent.html(percentVal);
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			bar.width(percentVal)
			percent.html(percentVal);
		},
		complete: function(xhr) {
			status.html(xhr.responseText);
		},
		url: "/run/lougis/file_upload/uploadFile/"
	}); 

	})();       

</script>
<? } ?>