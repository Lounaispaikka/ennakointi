<?php 
require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

require_once(PATH_PUBLIC.'phorum/common.php');
require_once(PATH_PUBLIC.'phorum/include/format_functions.php');

global $Site, $Cms;

$Class = null;
$LeftCol = false;
$RightCol = false;
$Pg = $Cms->getPage();
if ( $Cms->currentPageHasParent() || $Cms->currentPageHasChildren() ) { 
	$Parent = $Cms->findCurrentPageTopParent( );
	$LeftCol = true;
}
if ( $Cms->hasRightColumn() || $Pg->page_type == 'ohjelma' || $Pg->page_type == 'toimenpide' ) {
	$RightCol = true;
}
/* Tavoitteet content diviss�
 * if ( $Cms->hasRightColumn() ) {
	$RightCol = true;
}
 */

if ( $LeftCol && $RightCol ) {
	$Class = "col3";
} elseif( $LeftCol ) {
	$Class = "col2";
} elseif( $RightCol ) {
        $Class = "col4";
}


$TavoitteetPrinted = false;
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
                $PageNews = $Pg->getNews();
                $ColCon = $Pg->getColumnHtml();
                
                if (count($PageNews) > 0 || !empty($ColCon)) {
                ?>
                <div id="rightColGrey">
                <?         
                if ( count($PageNews) > 0 ) {
                ?>
                <h1>Ajankohtaiset</h1>
                <div id="pageNews">
                        <? foreach($PageNews as $News) { ?>
                        <p><a href="/fi/ajankohtaista/?nid=<?=$News->id?>#n<?=$News->id?>"><?=$News->title?></a></p>
                        <? } ?>
                </div>
                <? } ?>
               
                <?
                //$ColCon = $Pg->getColumnHtml();
                if ( !empty($ColCon) ) echo $ColCon;
                ?>
                 <?
                if ( $Pg->url_name == 'ymparistoohjelma' ) {
                        $RecentCharts = $Site->getRecentCharts(4);
                        $RecentComments = $Site->getRecentComments(3);
                ?>
                <div id="recentPubCharts">
                       <p><strong>Päivitetyt indikaattorit</strong></p>
                        <dl class="recent_charts">
                        <? foreach($RecentCharts as $Chart) { ?>
                                <dt class="recent_charts"><a href="/fi/indikaattorit/?id=<?=$Chart->id?>"><?=$Chart->title?></a></dt>
                        <? } ?>
                        </dl>
                </div>
             <? /*   <div id="recentComments">
                       <p><strong>Uusimmat kommentit</strong></p>
                        <dl class="recent_charts">
                        <? foreach($RecentComments as $Comment) { ?>
                                <dt class="recent_charts"><a href="/fi/indikaattorit/"><?=$Comment->title?></a></dt>
                        <? } ?>
                        </dl>
                </div>
                       */ ?> 
                <? } ?>
                </div> 
        <? }
	if ( !$TavoitteetPrinted && ( $Pg->page_type == 'strategia'  ) ) {
                echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
                $TavoitteetPrinted = true;
        }
	?>
</div>
<? } ?>


<div id="content" class="<?=$Class?>">
<?
/*if ( !$TavoitteetPrinted && ( $Pg->page_type == 'strategia'  ) ) {
	echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
        $TavoitteetPrinted = true;
}*/
?>

<h1><?=$Pg->title?></h1>
{PAGE_CONTENT}

<?if ( $Pg->page_type == "toimiala") {
	echo '<h2>Ennakointiteemat</h2>';
	echo '<a href="http://dev.everkosto.lounaispaikka.fi/fi/33/">Ennakointi2013</a>';
}
?>
<?
//Ennakointisivu
if ( $Pg->page_type == 'teema' ) {
	require_once(PATH_TEMPLATE.'everkosto/page/teema_aineisto.php');
}
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
<? /*
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
*/ ?>
<?  
if ($Pg->page_type != "toimiala" ) require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php'); ?>

</div>
<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>
