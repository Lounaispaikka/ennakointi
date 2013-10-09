<?php 
require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

/*require_once(PATH_PUBLIC.'phorum/common.php');
require_once(PATH_PUBLIC.'phorum/include/format_functions.php');
*/
global $Site, $Cms;

$Class = null;
$LeftCol = false;
$RightCol = false;
$Pg = $Cms->getPage();
if ( $Cms->currentPageHasParent() || $Cms->currentPageHasChildren() ) { 
	$Parent = $Cms->findCurrentPageTopParent( );
	$LeftCol = true;
}
if ( $LeftCol && $RightCol ) {
	$Class = "col3";
}
else $Class = "col2";
$CmsCom = new \Lougis\utility\CmsComment();
?>
<div id="breadcrumb"><? $Cms->outputBreadcrumb() ?></div>
<? if ( $LeftCol ) { ?>
<div id="leftCol" class="<?=$Class?>">
	<? $Cms->outputLeftNavigation($Parent); ?>
</div>
<? } ?>
<? if ( $RightCol ) { ?>
<div id="rightCol" class="<?=$Class?>">
        
                <? 
               
                $ColCon = $Pg->getColumnHtml();
                
				?>
                <div id="rightColGrey">
				
				<?
                if ( !empty($ColCon) ) echo $ColCon;
                ?>
				<script type="text/javascript" src="/js/jqueryPlugins/jquery.dform-1.0.1.js"></script>
				<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script> 
				<script type="text/javascript" src="/js/lougis/lib/ennakointi.ui.jquery.js"></script>
				<script type="text/javascript" src="/js/lougis/lib/file.ui.jquery.js"></script>
				<script type="text/javascript" src="/js/lougis/lib/page.ui.jquery.js"></script>
				<script type="text/javascript" src="/js/lougis/lib/link.ui.jquery.js"></script>
				<script type="text/javascript" src="/js/lougis/lib/news.ui.jquery.js"></script>


				<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>

				<!-- chart tools -->

				<script type="text/javascript" src="/js/handsontable/dist/jquery.handsontable.full.js"></script>
				<link rel="stylesheet" media="screen" href="http://handsontable.com/dist/jquery.handsontable.full.css">
				<script src="http://code.highcharts.com/highcharts.js"></script>
				<script src="/js/lougis/lib/adapt-chart-to-legend.js""></script>
				<script type="text/javascript" src="/js/lougis/lib/chart.ui.jquery.js"></script>
				<div id="formResponse">
				<p></p>
				</div>

				<h2>Hallintaty&ouml;kalut</h2>
					<!--<button id="addPage" class="ui-button teema_btn"><img src="/img/icons/16x16/page_add.png" > Lis&auml;&auml; sivu</button>-->
					<!--<button id="addLink" class="ui-button teema_btn"><img src="/img/icons/16x16/link_add.png" > Lis&auml;&auml; linkki</button>-->
					<!--<button id="addNews" class="ui-button teema_btn"><img src="/img/icons/16x16/newspaper_add.png" > Lis&auml;&auml; uutinen</button>-->
					<button id="addChart" class="ui-button teema_btn"><img src="/img/icons/16x16/chart_bar_add.png" > Lis&auml;&auml; tilasto</button>
					<button id="addFile" class="ui-button teema_btn"><img src="/img/icons/16x16/package_add.png" > Lis&auml;&auml; tiedosto</button>

				<div id="addFileDialog" title="Lis&auml;&auml; tiedosto">
				<form id="upfile" class="ui-widget"></form>

				<div class="progress">
					<div class="bar"></div >
					<div class="percent"></div >
				</div>

				<div id="status"></div>
				</div>	
				<div id="addChartDialog_file" title="Lis&auml;&auml; tilasto">
				<div id="chart_upload">
					<p id="chartOhje" style="text-align: left;"></p>
					<form id="chartform_upload" class="ui-widget"></form>
				</div>
				</div>

				<div id="addChartDialog" title="Lis&auml;&auml; tilasto">
				<ul>
					<li><a href="#chart_table">Taulukko</a></li>
					<li><a href="#chart_config">Asetukset</a></li>
					<li><a href="#chart_preview">Esikatselu</a></li>
				</ul>


				<div id="chart_table">
					<div id="datagrid" class="handsontable"></div>
					<form id="chartform_table" class="ui-widget"></form>
				</div>

				<div id="chart_config">
					<p id="chartOhje" style="text-align: left;"></p>
					<form id="chartform_config" class="ui-widget"></form>
				</div>

				<div id="chart_preview">
					<p id="chartOhje" style="text-align: left;"></p>
					<div id="chart_container" style="height: 400px"></div>
					<form id="chartform_preview" class="ui-widget"></form>
				</div>

				</div>

				<!--<div id="addContentDialog" title="Lis&auml&auml; aineisto">
				<button id="alasivuBtn"></button>
				<button id="indikaattoriBtn"></button>
				<button id="dokumenttiBtn"></button>
				<button id="linkkiBtn"></button>
				</div>
				-->
				<div id="addPageFormDialog" title="Lis&auml&auml">
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

				<div id="editPageInfoDialog" title="Muokkaa sivun tietoja" >
				<div id="cmsInfo" style="position:relative;">
					<form id="cmsForm_info" class="ui-widget"></form>
				</div>	
				</div>
				<div id="editPageContentDialog" title="Muokkaa sivun tietoja">
				<div id="cmsContent">
					<form id="cmsForm_content" class="ui-widget"></form>
				</div>
				</div>

				<div id="formResponse">
				<p></p>
				</div>
		
				<?
			/*	//Etsi alasivu tämän sivun alta
				$chartPage = new \Lougis_cms_page();
				$chartPage->parent_id = $Pg->id;
				$chartPage->find();
				$alasivut = array();
				while( $chartPage->fetch() ) $alasivut[] = clone($chartPage);
				//var_dump($alasivut);
				echo array_search('Tilastot', $alasivut);
				*/?>
				<script type="text/javascript">
				$(function() {	

					

					$('#addPage').click(function(){
						openAddPageDialog(<?=$Pg->id?>);
						return false;
					});
					$('#addChart').click(function(){
						openAddChartDialog(<?=$Pg->id?>);
						return false;
					});
					$('#addLink').click(function(){
						openAddLinkDialog(<?=$Pg->id?>);
						return false;
					});
					$('#addFile').click(function(){
						openAddFileDialog(<?=$Pg->id?>);
						return false;
					});
					$('#addNews').click(function(){
						openAddNewsDialog(<?=$Pg->id?>);
						return false;
					});
				});

				</script>
				<?
				
				if ( $Pg->page_type == 'teema_aineisto' && $_SESSION['user_id']) {
				?>
					<a href="javascript:void(0)" id="editPageInfo" class="linkJs">Muokkaa sivun perustietoja</a>
					<a href="javascript:void(0)" id="editPageContent" class="linkJs">Muokkaa sivun sis&auml;lt&ouml;&auml;</a>
					<div id="formResponse">
						<p></p>
					</div>
					<div id="editPageInfoDialog" title="Muokkaa sivun tietoja" >
						<div id="cmsInfo" style="position:relative;">
							<form id="cmsForm_info" class="ui-widget"></form>
						</div>	
					</div>
					<div id="editPageContentDialog" title="Muokkaa sivun tietoja">
						<div id="cmsContent">
							<form id="cmsForm_content" class="ui-widget"></form>
						</div>
					</div>
					
					
					<!--<script type="text/javascript" src="/js/NicEdit/nicEdit.js"></script>-->
					<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/jquery.validate.min.js"></script>
					<script type="text/javascript">
						jQuery(function() {	
							jQuery('#editPageInfo').click(function(){
								openEditDialog(<?=$Pg->id?>, 'page_info');
							
								return false;
							});
							jQuery('#editPageContent').click(function(){
								openEditDialog(<?=$Pg->id?>, 'page_content');
								jQuery('#editPageContentDialog').css("position", "relative");
								jQuery('#editPageContentDialog').css("padding-bottom", "40px;");
								return false;
							});
						});
					</script>
				<?
				}
				?>
                
            
                </div> 
        <? 
	?>
</div>
<? } ?>


<div id="content" class="<?=$Class?>">
{PAGE_CONTENT}
	<div id="teema_container">
		
	</div>



</div>
<? // require_once(PATH_PUBLIC.'ymparisto/kommentointi.php'); ?>
<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>

<?
if ( $_SESSION['user_id']) {
?>

<? } ?>