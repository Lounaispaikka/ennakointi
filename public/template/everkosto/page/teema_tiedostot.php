﻿<?php

global $Site, $Page ,$Cms;

$Pg = $Cms->getPage();

$Class = "col2"; //class ulkoasulle eli col2 = kaksi palstaa
$Parent = $Cms->findCurrentPageTopParent( );

$pages = $Pg->getParentPages();
//väliaikainen
if($Pg->page_type == "teema_tiedostot") {
	$FilePages = array();
	$FilePage = new \Lougis_cms_page();
	/*$sql = "select cms_page.id as pid, cms_page.title as title, file.created_date as created_date, file.description as description
			from lougis.cms_page, lougis.file
			where cms_page.parent_id = ".$Page->id."
			and cms_page.page_type = 'file'
			and cms_page.file_id = file.id;"; */
	$sql = "SELECT
			lougis.cms_page.id as pid,
			lougis.cms_page.title,
			lougis.file.description as description,
			lougis.file.created_date as created_date,
			lougis.user.firstname,
			lougis.user.lastname
			FROM
			lougis.cms_page
			INNER JOIN lougis.file ON lougis.file.id = lougis.cms_page.file_id
			INNER JOIN lougis.user ON lougis.cms_page.created_by = lougis.user.id
			WHERE
			lougis.cms_page.page_type = 'file' AND
			lougis.cms_page.parent_id = ".$Pg->id.";";
	$FilePage->query($sql);
	while($FilePage->fetch() ) {
		$FilePages[] = clone($FilePage);
	}
}
else {
	/*$file = new \Lougis_file();
	$file->page_id = $Pg->id;
	$file->find();
	$file->fetch();*/
	$file = new \Lougis_file();
	$file->id = $Pg->file_id;
	$file->find(true);
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
if ($Pg->page_type == "teema_tiedostot" && count($FilePages) > 0) { ?>
	<table id="comment_topics">
		<thead>
			<tr>
				<th>Tiedosto</th>
				<th>Lisääjä</th>
				<th>Pvm</th>
			</tr>
		</thead>
		<tbody>
	<? foreach($FilePages as $page) { ?>
			<tr class="topic_row">
				<td class="topic_topic"><a href="../<?=$page->pid?>"><?=$page->title?></a></td>
				<td class="topic_last"><? echo $page->firstname. " " .$page->lastname;?></td>
				<td class="topic_last"><?=date('d.m.Y', strtotime($page->created_date))?></td>
			</tr>
	<? } ?>
		</tbody>
	</table>
<? } 
if ($Pg->page_type == 'file') {?>

<? //yksittäisen tiedoston sivu ?>
	<?  //if user is creator of page or admin
		if ( $_SESSION['user_id'] === $Pg->created_by && $Pg->page_type === "file" ) { ?>
		<div id="editTools" style="float:right;">
			<!--<a href="javascript:void(0)" id="editPageInfo" class="linkJs"><img src="/img/icons/16x16/document_prepare.png" >Muokkaa tietoja</a>-->
			<a href="javascript:void(0)" id="delFile" class="linkJs"><img src="/img/icons/16x16/delete.png" >Poista</a>

		</div>
	<? } ?>
	 <p><?=$file->description?></p>
	 <p>Lataa tiedosto: <a href="../../ymparisto/download.php?id=<?=$file->id?>">Lataa</a></p>
	 
	
<? } ?>
<? if($Pg->page_type == 'file') { 
require_once(PATH_PUBLIC.'comments_frontend/kommentointi.php');
} ?>
</div>
<script type="text/javascript" src="/js/lougis/lib/ennakointi.ui.jquery.js"></script>
<script type="text/javascript" src="/js/lougis/lib/file.ui.jquery.js"></script>
<? if($file->id != null) { ?>
<script type="text/javascript" charset="utf8">
	$(function() {

		$('#delFile').click(function(){
			var del = window.confirm("Oletko varma, että haluat poistaa tiedoston?");
			if(del == true) {
				delFile(<?=$Pg->id?>, <?=$file->id?>);
				return false;
			} else {
				return false;
			}	
		});
				
	});
	
	
</script>

<? } ?>


<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>