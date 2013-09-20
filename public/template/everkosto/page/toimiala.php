<div id="toimialaDialog"></div>
<div id="editToimiala">
	<ul>
		<li><a href="#toimiala_tiedot">Perustiedot</a></li>
		<li><a href="#toimiala_sisalto">Sis&auml;lt&ouml;</a></li>
		<li><a href="#toimiala_kayttajat">K&auml;ytt&auml;j&auml;t</a></li>
	</ul>
	<div>
		<div id="toimiala_tiedot">
			<form id="tiedot_form" class="ui-widget"></form>
		</div>
		<div id="toimiala_sisalto">
			<form id="sisalto_form" class="ui-widget"></form>
		</div>
		<div id="toimiala_kayttajat">
			<div id="list_container">
				<div class="list-with-heading">
					<h3 class="list-h3">Rekister&ouml;ityneet k&auml;ytt&auml;j&auml;t</h3>
					<ul id="sortable1" class="connectedSortable"> 
						
					</ul>
				</div>
				
				<div class="list-with-heading">
					<h3 class="list-h3">Toimialak&auml;ytt&auml;j&auml;t</h3>
					<ul id="sortable2" class="connectedSortable">
					
					</ul>
				</div>
			</div>
			<form id="kayttajat_form" class="ui-widget">
				
			</form>
			
		</div>
	</div>
</div>

<div id="teemaDialog">	
</div>

<div id="editTeema">
	<ul>
		<li><a href="#teema_tiedot">Perustiedot</a></li>
		<li><a href="#teema_sisalto">Sis&auml;lt&ouml;</a></li>
	</ul>	
		
			
			<div id="teema_tiedot">
				<form id="teema_tiedot_form" class="ui-widget"></form>
			</div>
			
			<div id="teema_sisalto">
				<form id="teema_sisalto_form" class="ui-widget"></form>
			</div>
	
</div>

<div id="addToimiala">
	<form id="toimiala_tiedot_empty" class="ui-widget"></form>
</div>
<div id="addTeema">
	<form id="teema_tiedot_empty" class="ui-widget"></form>
</div>

<div id="dialog_confirm">
</div>


<script type="text/javascript"></script>
<script> 
	$(function() {
		var adminGroup;
		$( "#sortable1, #sortable2" ).sortable({
		  connectWith: ".connectedSortable",
		}).disableSelection();
		  cursor: "pointer"
		$("#sortable2").sortable({
			update: function(event,ui) {
				//var newOrder = $(this).sortable('toArray').toString();
				adminGroup = $(this).sortable('toArray');
				console.log("inside", adminGroup);
				$("#admin-group").val(adminGroup);
				//saveGroup(<?=$Pg->parent_id?>, adminGroup);
			},
			sort: function() {
				if ($(this).hasClass("cancel")) {
					return false;
				}
			}
		});
		//$("#kayttajat_form").on("submit", (event){
	/*	$('#lah').bind('click', function() {
			console.log("formsubmit", adminGroup);
			saveGroup("<?=$Pg->parent_id?>", adminGroup);
			return false;
		});
	*/
		//loadRestOfUsers(<?=$Pg->parent_id?>);
		//loadGroupUsers(<?=$Pg->parent_id?>);
		//editToimiala(<?=$Pg->parent_id?>);
		
		$( "#selectable" ).selectable({
			selected: function(event, ui) { 
				$(ui.selected).addClass("ui-selected").siblings().removeClass("ui-selected");
				console.log("selected", ui);
			}  
		});
	
	});
  
	
	
	
</script>
