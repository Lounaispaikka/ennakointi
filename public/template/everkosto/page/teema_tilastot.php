<?php

global $Site, $Page ,$Cms;

$Pg = $Cms->getPage();

$Class = "col2"; //class ulkoasulle eli col2 = kaksi palstaa
$Parent = $Cms->findCurrentPageTopParent( );

//add if else
$Chart = new \Lougis_chart();
$Chart->page_id = $Pg->id;
$Chart->find();
$Chart->fetch();

//charts for frontpage
$child_charts_pages = new \Lougis_cms_page();
$child_charts_pages->parent_id = $Pg->id;
$child_charts_pages->find();
$chparr = array();
while ($child_charts_pages->fetch()) {
	$chparr[] = clone($child_charts_pages);
}

require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 

?>
<? if($Pg->page_type === "chart" && $Chart->id != null) { ?>
	<script src="http://code.highcharts.com/highcharts.js"></script>
	<script src="/js/lougis/lib/adapt-chart-to-legend.js"></script>
	<script type="text/javascript" src="/js/lougis/lib/chart.ui.jquery.js"></script>
	<script type="text/javascript" >
		$(function() {
			createChartGraph(<?=$Chart->id?>);	
		});
	</script>
<? } ?>
<div id="breadcrumb"><? $Cms->outputBreadcrumb() ?></div>
<div id="formResponse">
	<p></p>
</div>
<div id="leftCol" class="<?=$Class?>">
	<? $Cms->outputLeftNavigation($Parent); ?>
</div>
<?  //if user is creator of page or admin
		if ( $_SESSION['user_id'] === $Pg->created_by && $Pg->page_type === "chart") { ?>
		<div id="editTools" style="float:right;">
			<a href="javascript:void(0)" id="editChart" class="linkJs"><img src="/img/icons/16x16/document_prepare.png" >Muokkaa tietoja</a>
			<a href="javascript:void(0)" id="delChart" class="linkJs"><img src="/img/icons/16x16/delete.png" >Poista</a>

		</div>
		<? } ?>
<div id="content" class="<?=$Class?>">
	<h1><?=$Pg->title?></h1>
	<? //chart frontpage ?>
	<ul>
	<? foreach($chparr as $ch) { ?>
		<li id="<?=$ch->id?>"><div id="<?=$ch->id?>"><a href="../<?=$ch->id?>/" ><?=$ch->title?></a> </li> 
	<? } ?>
	</ul>
	
	<div id="chart_container"></div>
	<? if($Pg->page_type == 'chart') { 
require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php');
} ?>
</div>

<script type="text/javascript">
	$(function() {

		$('#delChart').click(function(){
			var del = window.confirm("Oletko varma, että haluat poistaa tiedoston?");
			if(del == true) {
				delChart(<?=$Chart->id?>);
				return false;
			} else {
				return false;
			}	
		});
		$('#editChart').click(function(){
			window.alert("Tilaston muokkaaminen valmistuu pian. Tässä demoversiossa sitä ei vielä valitettavasti ole.");
			
		});
				
	});
	
	
</script>


<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>