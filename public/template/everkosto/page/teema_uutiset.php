<?php

global $Site, $Page ,$Cms;

$Pg = $Cms->getPage();

$Class = "col2"; //class ulkoasulle eli col2 = kaksi palstaa
$Parent = $Cms->findCurrentPageTopParent( );

$uutinen_sivu = new \Lougis_cms_page();
//$uutinen_sivu->parent_id = $Pg->id;
//$uutinen_sivu->find();
//$sql = 'select cms_page."id", cms_page."title", news.title as news_title, news.page_id as page_id, news."id" as news_id from lougis.cms_page inner join lougis.news on news.page_id = cms_page."id" where parent_id = '.$Pg->id;
$sql = "select cms_page.id, cms_page.title, news.title as news_title, news.id as news_id
		from lougis.cms_page
		inner join lougis.news on news.id = cms_page.news_id
		where cms_page.page_type = 'news'
		and cms_page.parent_id = ".$Pg->id."
		and news.news_type = 'uutinen';";
$uutinen_sivu->query($sql);
$uutiset = array();
while ($uutinen_sivu->fetch()) {
	$uutiset[] = clone($uutinen_sivu);
}

//yksittäisen uutisen sivu
if($Pg->news_id != null) {
	$tama_uutinen = new \Lougis_news();
	$tama_uutinen->id = $Pg->news_id;
	$tama_uutinen->find(true);
}

require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
?>
<div id="breadcrumb"><? $Cms->outputBreadcrumb() ?></div>
<div id="formResponse">
	<p></p>
</div>
<div id="leftCol" class="<?=$Class?>">
	<? $Cms->outputLeftNavigation($Parent); ?>
</div>

<div id="content" class="<?=$Class?>">
	<h1><?=$Pg->title?></h1>
	
	
<? //uutisetusivu ?>
	<ul>
	<? foreach($uutiset as $uutinen) { ?>
		<li id="<?=$uutinen->id?>"><div id="<?=$uutinen->id?>"><a href="../<?=$uutinen->id?>/" ><?=$uutinen->title?></a> </li> 
	<? } ?>
	</ul>
	
	
<? //uutissivu ?>
	<?  //if user is creator of page or admin
		if ( $_SESSION['user_id'] === $Pg->created_by && $Pg->page_type === "news" ) { ?>
		<div id="editTools" style="float:right;">
			<a href="javascript:void(0)" id="editNews" class="linkJs"><img src="/img/icons/16x16/document_prepare.png" >Muokkaa tietoja</a>
			<a href="javascript:void(0)" id="delNews" class="linkJs"><img src="/img/icons/16x16/delete.png" >Poista</a>

		</div>
	<? } ?>
	<p><b><?=$tama_uutinen->description?></b></p>
	
	<p><?=$tama_uutinen->content?></p>
	
	<p><?=$tama_uutinen->source?></p>
	<a href="<?=$tama_uutinen->source_url?>" class="linkJs" ><?=$tama_uutinen->source_url?></a>
		
<? if ($tama_uutinen->id !=null ) require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php'); ?>	
</div>
<? if ($tama_uutinen->id != null) { ?>
<script type="text/javascript" src="/js/lougis/lib/news.ui.jquery.js"></script>
<script type="text/javascript">
	$(function() {

		$('#delNews').click(function(){
			var del = window.confirm("Oletko varma, että haluat poistaa uutisen?");
			if(del == true) {
				delNews(<?=$Pg->id?>, <?=$Pg->news_id?>);
				return false;
			} else {
				return false;
			}	
		});
		$('#editNews').click(function(){
			window.alert("Uutisen muokkaaminen valmistuu pian. Tässä demoversiossa sitä ei vielä valitettavasti ole.");
			
		});
				
	});
	
	
</script>

<? } ?>

<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>