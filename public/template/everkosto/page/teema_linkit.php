<?php

global $Site, $Page ,$Cms;

$Pg = $Cms->getPage();

$Class = "col2"; //class ulkoasulle eli col2 = kaksi palstaa
$Parent = $Cms->findCurrentPageTopParent( );

$uutinen_sivu = new \Lougis_cms_page();
//$uutinen_sivu->parent_id = $Pg->id;
//$uutinen_sivu->find();
$sql = 'select cms_page."id", cms_page."title", news.title as news_title, news.page_id as page_id, news."id" as news_id from lougis.cms_page inner join lougis.news on news.page_id = cms_page."id" where parent_id = '.$Pg->id;
$uutinen_sivu->query($sql);
$uutiset = array();
while ($uutinen_sivu->fetch()) {
	$uutiset[] = clone($uutinen_sivu);
}

//yksittäisen uutisen sivu
$tama_uutinen = new \Lougis_news();
$tama_uutinen->page_id = $Pg->id;
$tama_uutinen->find();
$tama_uutinen->fetch();

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
		<li id="<?=$uutinen->id?>"><div id="<?=$uutinen->news_id?>"><a href="../<?=$uutinen->id?>/" ><?=$uutinen->title?></a> <button id="delNews">Poista</button></div></li> 
	<? } ?>
	</ul>
	<? //uutissivu ?>
	<a href="<?=$tama_uutinen->source_url?>" class="linkJs" ><?=$tama_uutinen->source_url?></a>
	<p>L&auml;hde: <?=$tama_uutinen->source?></p>
	<p><b><?=$tama_uutinen->description?></b></p>
	
		
<? if ($tama_uutinen->id !=null ) require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php'); ?>	
</div>
<script type="text/javascript" src="/js/lougis/lib/ennakointi.ui.jquery.js"></script>
<script type="text/javascript">
	$(function() {

		jQuery('#delNews').click(function(){
			var newsPageId = $("#delNews").closest("li").attr("id");
			var newsId = $("#delNews").closest("div").attr("id");
			delNews(newsPageId, newsId); //ennakointi.ui.jquery.js
			return false;
		});
				
	});
	
	
</script>



<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>