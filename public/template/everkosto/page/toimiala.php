<style>
  .list-h3 { padding: 10px 0 10px 5px; margin: 0; font-size: 14px; font-weight: 700; }
  .list-with-heading { display:block; float:left; margin: 0 15px 0 15px; }
  #sortable1, #sortable2 { list-style-type: none; margin: 0; padding: 0 ; float: left; margin-right: 2px; border: 1px solid; min-width: 226px; min-height: 50px; background-color: #fff}
  #sortable1 li, #sortable2 li { margin: 0 2px 0 2px; padding: 2px 2px 2px 2px; font-size: 1.2em; width: 220px; min-height: 45px; }
  #sortable1 li:hover, #sortable2 li:hover {cursor: pointer; border: 2px solid;}
  .userlist_name {display:block; font-size: 14px; color: #000; clear:both;}
  .userlist_org {display:block;font-size: 12px; color: #000; clear:both;}
  .userlist_email {display:block; font-size: 12px; color: blue; clear:both;}
  #cms_content {height: 300px; min-width: 350px; }
  #addToimiala { display:none;}
  #addTeema { display:none;}
  
  #feedback { font-size: 1.4em; }

  
  .teema_btn { display:block; clear:both; margin: 5px 0 5px 0; width: 200px; text-align:left;}
  
  #toimialaDialog #valittavat { float:left; width: 50%; text-align: left;}
  #toimialaDialog #ohjeet {float: left; width: 50%; text-align: left;}
  #addToimiala.ui-tabs, #addToimiala.ui-dialog-content, #addToimiala.ui-dialog { padding: 0; }

  #dialog-message { display: none;}
 
  </style>
<div id="toimialaDialog"></div>
<div id="addToimiala">
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
				<input type="submit" id="lah" />
			</form>
			
		</div>
	</div>
</div>

<div id="teemaDialog">
	
	<h3 class="list-h3">Teemat</h3>
	<button class="teema_btn"><img src="/img/icons/16x16/add.png" >Lis&auml;&auml; uusi teema</button>
	<div id="valitse_teema">	
	</div>
</div>

<div id="addTeema">
	<ul>
		<li><a href="#teema_tiedot">Perustiedot</a></li>
		<li><a href="#teema_sisalto">Sis&auml;lt&ouml;</a></li>
		<li><a href="#toimiala_kayttajat">K&auml;ytt&auml;j&auml;t</a></li>
		<li><a href="#toimiala_teemat">Teemat</a></li>
	</ul>	
		
			
			<div id="teema_tiedot">
				
			</div>
	</ul>
</div>

<div id="dialog-message" title="Tallennettu!">
	<p id="response_msg">
		<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
	
	</p>
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
		//addToimiala(<?=$Pg->parent_id?>);
		
		$( "#selectable" ).selectable({
			selected: function(event, ui) { 
				$(ui.selected).addClass("ui-selected").siblings().removeClass("ui-selected");
				console.log("selected", ui);
			}  
		});
	
	

	});
  
	
	
	
</script>
