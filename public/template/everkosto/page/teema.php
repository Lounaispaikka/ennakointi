<?php 
require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

require_once(PATH_PUBLIC.'phorum/common.php');
require_once(PATH_PUBLIC.'phorum/include/format_functions.php');

global $Site, $Cms;

$Class = null;
$LeftCol = false;
$RightCol = true;
$Pg = $Cms->getPage();
if ( $Cms->currentPageHasParent() || $Cms->currentPageHasChildren() ) { 
	$Parent = $Cms->findCurrentPageTopParent( );
	$LeftCol = true;
}
if ( $LeftCol && $RightCol ) {
	$Class = "col3";
}
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
				<?
				require_once(PATH_TEMPLATE.'everkosto/page/teema_aineisto_col.php');
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
					<script type="text/javascript" src="/js/jqueryPlugins/jquery.dform-1.0.1.js"></script>
					<script type="text/javascript" src="http://malsup.github.com/jquery.form.js"></script> 
					<script type="text/javascript" src="/js/lougis/lib/ennakointi.ui.jquery.js"></script>
					
					<!--<script type="text/javascript" src="/js/NicEdit/nicEdit.js"></script>-->
					<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
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
		<?/*
		<div id="teema_keskustelut">
			<h2>Keskustelu</h2>
			<? $CommentTopics = $CmsCom->getTopics($Pg->id); ?>
			
			<ul>
			<? for($i=0; $i < 3; $i++) {?>
				<li><?if($CommentTopics[$i]->title == "Keskustelut") { ?><a href="/fi/<?=$CommentTopics[$i]->page_id?>/?tid=<?=$CommentTopics[$i]->topicid?>"><? } else {?><a href="/fi/<?=$CommentTopics[$i]->page_id?>/"> <?} if($CommentTopics[$i]->title != "Keskustelut") {echo $CommentTopics[$i]->title;} else { echo $CommentTopics[$i]->ctitle; } ?> (viimeisin viesti <?=date('d.m.Y H:i:s', strtotime($CommentTopics[$i]->date))?>)</a></li>
			<?} ?>
			</ul>
		</div>
	<?
	$ecms = new \Lougis\utility\CmsEnnakointi();
	$uutiset = $ecms->latestNewsEnnakointi();
	if (count($uutiset) > 0 || !empty($ColCon)) {
		if ( count($uutiset) > 0 ) {
		?>
		
		<div id="teema_uutiset">
			<h2>Uutiset ja tapahtumat</h2>
			<ul>
				<? foreach($uutiset as $News) { ?>
				<li><a href="/fi/ajankohtaista/?nid=<?=$News->id?>#n<?=$News->id?>"><?=$News->title?></a></li>
				<? } ?>
			</ul>
		</div>
 <? 	} 
	}?>
		<div class="clear"></div>
		
		<div id="teema_linkit">
			<h2>Linkit</h2>
			<ul>
				<li><a href="http://amtuusimaa.files.wordpress.com/2012/12/amt-lorap-luonnos-5-1-2911123.pdf">Arktisen meriteknologian ennakointi, Yrj&ouml; Myllyl&auml; 2012 <em>(8.3.2013)</em></a></li>
				<li><a href="http://www.prizz.fi/asiakaskuvat/Meri/Finnish offshore industry 2012.pdf">Suomen offshore-toimiala 2012 -raportti, Prizztech <em>(6.3.2013)</em></a></li>
			</ul>
		</div>
*/ ?>
	</div>



</div>
<? // require_once(PATH_PUBLIC.'ymparisto/kommentointi.php'); ?>
<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>
