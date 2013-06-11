<?php

global $Site, $Page;

//$TopNews = $Site->getNews(4);

require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
?>
<div id="content">
	<div id="front-content">
	<h1><?=$Pg->title?></h1>
	{PAGE_CONTENT}
	<?
	/*$AdminUser = new \Lougis_user();
			$AdminUser->query(	'SELECT
								lougis."user"."id"
								FROM
								lougis."user"
								JOIN lougis.group_user
								ON lougis.group_user.user_id = lougis."user"."id"
								JOIN lougis."group"
								ON lougis."group"."id" = lougis.group_user.group_id
								WHERE lougis."user"."id" = 164
								AND lougis."group".is_admin = true
							;');
	*/?>
	</div>
	<? /*<div id="front-news">
		<h1>Ajankohtaista</h1>
		<ul>
		<? foreach($TopNews as $News) { ?>
		<li>
		<a href="/fi/34/?nid=<?=$News->id?>#n<?=$News->id?>">
		<h1><?=$News->title?></h1>
		<span class="newsInfo"><?=date("d.m.Y", strtotime($News->created_date))?></span>
		<p><?=$News->description?></p>
		</a>
		</li>
		<? } ?>
		</ul>
		<p><a href="/fi/ajankohtaista/">Lis&auml;&auml; ajankohtaista...</a></p>
	</div> */ ?>
</div>
<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>