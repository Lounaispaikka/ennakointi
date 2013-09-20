<?php

global $Site, $Page ,$Cms;

$Pg = $Cms->getPage();

$Class = "col2"; //class ulkoasulle eli col2 = kaksi palstaa
$Parent = $Cms->findCurrentPageTopParent( );

$pages = $Pg->getParentPages();
//v�liaikainen
if($Pg->page_type == "teema_tiedostot") {
	$FilePages = array();
	$FilePage = new \Lougis_cms_page();
	$sql = "select cms_page.id as pid, cms_page.title as title, file.created_date as created_date, file.description as description
			from lougis.cms_page, lougis.file
			where cms_page.parent_id = ".$Page->id."
			and cms_page.page_type = 'file'
			and cms_page.id = file.page_id;";
	$FilePage->query($sql);
	while($FilePage->fetch() ) {
		$FilePages[] = clone($FilePage);
	}
}
else {
	$file = new \Lougis_file();
	$file->page_id = $Pg->id;
	$file->find();
	$file->fetch();
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

<? 
//tiedostojen etusivu
if ($Pg->page_type == "teema_tiedostot") { ?>
	<table id="comment_topics">
		<thead>
			<tr>
				<th>Tiedosto</th>
				<th>Latausaika</th>
			</tr>
		</thead>
		<tbody>
	<? foreach($FilePages as $page) { ?>
			<tr class="topic_row">
				<td class="topic_topic"><a href="../<?=$page->pid?>"><?=$page->title?></a></td>
				<td class="topic_last"><?=date('d.m.Y H:i:s', strtotime($page->created_date))?></td>
			</tr>
	<? } ?>
		</tbody>
	</table>
<? } else {?>

<? //yksitt�isen tiedoston sivu ?>
	 <p><?=$file->description?></p>
	 <p>Lataa tiedosto: <a href="../../ymparisto/download.php?id=<?=$file->id?>">Lataa</a></p>
	 
	
<? } ?>
<? if($Pg->page_type == 'file') { 
require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php');
} ?>
</div>
<script type="text/javascript" src="/js/lougis/lib/ennakointi.ui.jquery.js"></script>




<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>