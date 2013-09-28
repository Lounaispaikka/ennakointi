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
if ( $Cms->hasRightColumn() || $Pg->page_type == 'toimiala' || $Pg->page_type == 'toimenpide'  ) {
	$RightCol = true;
}
/* Tavoitteet content divissä
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
	<? if ( $Pg->page_type == "toimiala" ) {
		?>
		<div id="rightColGrey">
			<h2>Hallintaty&ouml;kalut</h2>
			<button id="hallinta_toimiala_btn" class="ui-button teema_btn"><img src="/img/icons/16x16/pencil.png" > Toimialan asetukset</button>
			<button id="hallinta_teema_btn" class="ui-button teema_btn"><img src="/img/icons/16x16/pencil.png" > Ennakointiteemat</button>
		</div>
		<? } else {
	
                
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
  

        <? }
	if ( !$TavoitteetPrinted && ( $Pg->page_type == 'strategia'  ) ) {
                echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
                $TavoitteetPrinted = true;
        }
	}
	?>
</div>
<? } ?>


<div id="content" class="<?=$Class?>">
{PAGE_CONTENT}
<?
/*if ( !$TavoitteetPrinted && ( $Pg->page_type == 'strategia'  ) ) {
	echo '[AUTOMAATTINEN_YLEMMAT_TAVOITTEET]';
        $TavoitteetPrinted = true;
}*/
?>


<? if ( $Pg->page_type == "toimiala") {
?>
	<script type="text/javascript">
		
		//lataa teemat, funktio ajetaan myös teema-js-filessä
		function loadTeemat() {
			//load teemat
			$.ajax({
				url: '/run/lougis/cms/getTeemat/',
				data: {
					page_id: <?=$Pg->id?>
				},
				type: "POST"
			}).done(function(response) {
				$("#teema_list").empty();
				//$("#teema_list").append("<li><a href=/fi/<?=$teema_li->id?>/><?=$teema_li->title?></a></li>"
				$.each( response, function( ) {
					$("#teema_list").append("<li><a href=/fi/" + this.id + ">" + this.title + "</a></li>");
				});
			});
			return false;
		}
		$(function() {
			
			loadTeemat();
		});
	</script>
	<h2>Ennakointiteemat</<h2>
	<ul id="teema_list"></ul>
<? }	
?>
	

<? 
//Ennakointisivu
if ( $Pg->page_type == 'teema' ) {
	require_once(PATH_TEMPLATE.'everkosto/page/teema_aineisto.php');
}
if ( $Pg->page_type == 'teema_aineisto' && $_SESSION['user_id']) {
?>
<script type="text/javascript" src="/js/lougis/lib/page.ui.jquery.js"></script>

<?  //if user is creator of page or admin
	if ( $_SESSION['user_id'] === $Pg->created_by) { ?>
	<div id="editTools" style="float:right;">
		<a href="javascript:void(0)" id="editPageInfo" class="linkJs"><img src="/img/icons/16x16/document_prepare.png" >Muokkaa tietoja</a>
		<a href="javascript:void(0)" id="editPageContent" class="linkJs"><img src="/img/icons/16x16/page_white_edit.png" >Muokkaa sis&auml;lt&ouml;&auml;</a>
		<a href="javascript:void(0)" id="delPage" class="linkJs"><img src="/img/icons/16x16/delete.png" >Poista</a>

	</div>
<? } ?>
	<div id="pageContent" style="margin-left:15px;margin-top:20px;">{PAGE_CONTENT}</div>
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
			
			$('#delPage').click(function(){
				delPage(<?=$Pg->id?>, <?=$Pg->parent_id?>); 
				return false;
			});
			
			
		});
	</script>

<?
}

if ( $Pg->page_type == 'toimiala' && isset($_SESSION['user_id'])) {
?>
	<script type="text/javascript" src="/js/lougis/lib/teema.ui.jquery.js"></script>
	<script type="text/javascript">
		$(function() {	
			
			$('#hallinta_toimiala_btn').click(function(){
				editToimiala(<?=$Pg->id?>);
				return false;
			});
			
			$('#hallinta_teema_btn').click(function(){
				teemaDialog(<?=$Pg->id?>);
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
	<a href="mailto:?Subject=<?=$Site->title?> - <?=$Pg->title?>&Body=<?=$Pg->getPageFullUrl()?>">SÃ¤hkÃ¶posti</a>
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
<?  /*
if ($Pg->page_type != "toimiala" ) require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php'); */?>

</div>
<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>
