<?php

require_once(PATH_SERVER.'/config.php');

require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
global $Site, $Cms;
if ( $Cms->currentPageHasParent() || $Cms->currentPageHasChildren() ) { 
	$Parent = $Cms->findCurrentPageTopParent( );
	$LeftCol = true;
}
//$ifr = $Site->getChartIframe();
/*if(isset($_GET['id'])) {
        if (!preg_match("/^\d+$/", $_GET['id'])) die("Tilastoa ei löytynyt");
        $Chart = new \Lougis_chart($_GET['id']);
		$_SESSION['chart_id'] = $_GET['id'];
        $Caa = $Chart->toChartArray();
        if ( empty($Chart->created_date) ) {
                echo "Tilastoa ei löytynyt";die;
        }
}*/

$Chart = new \Lougis_chart();
$Chart->page_id = $Pg->id;
$Chart->find();
$Chart->fetch();
		
$_SESSION['chart_id'] = $Chart->id;
$Caa = $Chart->toChartArray();
if ( empty($Chart->created_date) ) {
    echo "Tilastoa ei löytynyt";die;
}

?>
<script type="text/javascript" src="/js/lougis/lib/chart.ui.jquery.js""></script>
<div id="breadcrumb"><? // $Cms->outputBreadcrumb() ?></div>
<div id="leftCol" class="col2">	
<? $Cms->outputLeftNavigation($Parent); ?> 
</div>
<div id="content" class="col2">
        <div id="cms_data" style="display: block;">
<?
$Con = $Page->getContentHtml();
if ( !empty($Con) && !(isset($_SESSION['chart_id'])) ) print $Con; 

?>
            
        </div>
		<? if(isset($_SESSION['chart_id'])) { ?>
        <div id="chartDiv">
            <div style="margin-bottom: 10px;" class="chartinfo">
				<h1>{<?=$Chart->title?></h1>
				<p class="pvm">Tilasto p&auml;ivitetty: <?=$Chart->updated_date?></p>
			</div> 
				
				
			<script type="text/javascript">
				$(function() {
					createChartGraph(<?=$Chart->id?>);
				
				});
					var tpl_h = new Ext.XTemplate(
							'<tpl for=".">',
									'<div style="margin-bottom: 10px;" class="chartinfo">',
									  '<h1>{title}</h1>',
									  '<p class="pvm">Tilasto päivitetty: {updated_date}</p>',
									'</div>',
							'</tpl>'
					);
					var infoHeading = Ext.create('Ext.view.View', {
							store: store,
							itemSelector: 'div.chartinfo',
							tpl: tpl_h,
							id: 'chartInfoHeading',
							width: 680
					});
					var tpl_d = new Ext.XTemplate(
							'<tpl for=".">',
									'<div style="margin-bottom: 10px;" class="chartinfo">',
									'<p class="short_desc">{short_description}</p>',
									'<p>{description}</p>',
								   
									'</div>',
							'</tpl>'
					);

			   
			});
		   
			</script>
			<div style="margin-bottom: 10px;" class="chartinfo">
				<p class="short_desc"><?=$Chart->short_description?></p>
				<p><?=$Chart->description?></p>
								   
			</div>
                 
        </div>
        <?

        ?>
        
        <div id="extraDetails">
                <p>Upotuskoodi:</p><textarea style="margin-left:30px;" id="upotus" rows="4" cols="50" ><?=$ifr;?></textarea>
                <p>Lataa tiedot CSV-tiedostona: <a href="../../ymparisto/dlcsv.php?id=<?=$_SESSION['chart_id']?>">Lataa</a></p>
            
        </div>
		<? /*
		<div id="highchart">
			<script type="text/javascript" src="http://code.highcharts.com/highcharts.js"></script>
			<script src="http://code.highcharts.com/modules/exporting.js"></script>
			<script type="text/javascript">
			$(document).ready(function() {
			//testi
				var chartObj = <?=json_encode($Caa)?>;
				console.log("cc", chartObj.data.data);
				console.log("chartobj", chartObj);
				var count_series = chartObj.data.data[0].length;
				var data_series = [];
				
				
				var data = {"fields":[{"name":"vuosi","type":"int","dataindex":"c0"},{"name":"Pyh\u00e4j\u00e4rvi","type":"int","dataindex":"c1"},{"name":"Kakskerranj\u00e4rvi","type":"float","dataindex":"c2"},{"name":"Tavoite (hyv\u00e4 tila)","type":"int","dataindex":"c3"}],"data":[["1980","10",0,"18"],["1981","16",0,"18"],["1982","18",0,"18"],["1983","14",0,"18"],["1984","12",0,"18"],["1985","15"]],"axis":[1980,1981,1982,1983,1984,1985]};
				var config = {"chart":[{"type":"line","animation":true,"shadow":true,"renderTo":"highchart"}],"title":[{"text":"Testiaineisto"}]};
				aData = [];
				bData = [];
				cData = [];
				xaxis = [];
				conf = [];
				// for (i in data.data) {
					// aData.push( parseInt(data.data[i][1]) );
					// if(data.data[i][2] == null) {bData.push(data.data[i][2]) } else { bData.push( parseInt(data.data[i][2]) );}
					// cData.push( parseInt(data.data[i][3]) );
					// xaxis.push( parseInt(data.data[i][0]) );
				// }
				for (i in config.chart) {
					conf.push(config.chart[i])
				}
				
				for (i in chartObj.data.data) {
					aData.push( parseInt(chartObj.data.data[i][1]) );
					if(chartObj.data.data[i][2] == null) {bData.push(chartObj.data.data[i][2]) } else { bData.push( parseInt(chartObj.data.data[i][2]) );}
					cData.push( parseInt(chartObj.data.data[i][3]) );
					xaxis.push( chartObj.data.data[i][0] );
				}
				data_series = aData;
				//data_series.push(2);
				//data_series.push(3);
				
				var options = {
					chart: config.chart[0]
					,credits: {
						enabled: false
					}
					,xAxis: {
						type: 'category'
						,labels: {
							rotation: 45
							,align:'center'
							
						}
						,categories:xaxis
					}
					,title: false//config.title[0]
					,series: [{name:"Metallinjalostus- ja metallituoteteollisuus",data:aData},{name:"Koneteollisuus",data:bData},{name:"Kulkuneuvoteollisuus",data:cData}]
					
				};
				console.log("series", data_series);
				console.log("adata", aData);
				console.log("xaxis", xaxis);
				
				//options.series[0].data = aData;
				//options.series[1].data = bData;
				//options.series[2].data = cData;
				 
				//options.xAxis.categories = xaxis;
				
				var chart = new Highcharts.Chart(options);
				
			});
			</script>
		</div> */ ?>
        <? } /*?>
        <div id="social">

            <div id="email">
            <a href="mailto:?Subject=<?=$Site->title?> - <?=$Pg->title?>&Body=<?=$Pg->getPageFullUrl()?>">Sähköposti</a>
            </div>

            <div id="fb">
            <iframe src="//www.facebook.com/plugins/like.php?href=<?=urlencode($Pg->getPageFullUrl())?>&amp;locale=fi_FI&amp;layout=button_count&amp;show_faces=false&amp;width=125&amp;action=recommend&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:125px; height:21px;" allowTransparency="true"></iframe>
            </div>

            <div id="twitter">
            <a href="https://twitter.com/share" class="twitter-share-button" data-lang="fi" data-hashtags="ymparistonyt">Twiittaa</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script> 
            </div>

		</div>
		<? */ ?>
<? if(isset($_GET['id'])) {require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php'); }?>
</div>

<? 
require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>
