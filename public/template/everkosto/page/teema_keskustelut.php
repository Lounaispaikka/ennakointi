<?php 
require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

require_once(PATH_PUBLIC.'phorum/common.php');
require_once(PATH_PUBLIC.'phorum/include/format_functions.php');

global $Site, $Cms;

$Class = 'col2';
$LeftCol = true;
$RightCol = false;
$Pg = $Cms->getPage();

$CmsCom = new \Lougis\utility\CmsComment();

if ( $Cms->currentPageHasParent() || $Cms->currentPageHasChildren() ) { 
	$Parent = $Cms->findCurrentPageTopParent( );
	$LeftCol = true;
}


if(isset($_GET['tid'])) {
        if (!preg_match("/^\d+$/", $_GET['tid'])) die("Keskustelua ei löytynyt");
		else { 
			$topic_id = $_GET['tid'];
        //$ct = new \Lougis_comment_topic($_GET['tid']);
		//if(isset($_GET['debug'])) {if($ct->title != null) print($ct->id);}


//if($ct->id != null) $Comments = \Lougis_comment_msg::getAllForTopic($ct->id);*/
?>
<script>
		
		var request = $.ajax({
			url: '/run/lougis/comment/getCommentsHtml/',
			data: {
				page_id: <?=$Pg->id?>,
				topic_id: <?=$topic_id?>
			},
			type: "POST"
		});
		request.done(function(response) {
			$("#ajax_request_div").empty();
			$("#ajax_request_div").append(response);
			
		});
</script>

<? 		}
}
//if ( $Cms->hasRightColumn() || $Pg->page_type == 'keskustelu') {
	//$RightCol = true;
//}
//teeman etusivu eli mitkä keskustelut haetaan

?>
<script type="text/javascript" src="/js/jqueryPlugins/jquery_tablesorter/jquery.tablesorter.min.js"></script> 
<div id="breadcrumb"><? $Cms->outputBreadcrumb() ?></div>
<? if ( $LeftCol ) { ?>
<div id="leftCol" class="<?=$Class?>">
	<? $Cms->outputLeftNavigation($Parent); ?>
</div>
<? } ?>
<? if ( $RightCol ) { ?>
<div id="rightCol" class="<?=$Class?> keskustelu">
	<h2>Aiheet</h2>
<?/* $CommentTopics = $CmsCom->getTopics($Pg->parent_id); ?>
<? foreach($CommentTopics as $Topic) { ?>
	<a href="?tid=<?=$Topic->topicid?>"><? if($Topic->page_id != $Pg->id || $Topic->ctitle == null) {echo $Topic->title;} else { echo $Topic->ctitle; } ?> (viimeisin viesti <?=date('d.m.Y H:i:s', strtotime($Topic->date_created))?>)</a>
	
	<? }*/?>
</div>
<? } ?>



<div id="content" class="<?=$Class?>">

	<h1>Keskustelut</h1>
<? if ( !$topic_id ) { ?>
	<table id="comment_topics" class="tablesorter" >
		<thead>
			<tr>
				<th>Aihe</th>
				<th>Viestej&auml;</th>
				<th>Viimeisin viesti</th>
			</tr>
		</thead>
		<tbody id="topics_body">
	<? $CommentTopics = $CmsCom->getTopics($Pg->parent_id); ?>
	<? foreach($CommentTopics as $Topic) { ?>
			<tr id="<?=$Topic->topicid?>" class="topic_row">
				<td class="topic_topic">
					<a href="?tid=<?=$Topic->topicid?>"><? if($Topic->page_id != $Pg->id || $Topic->ctitle == null) {echo $Topic->title;} else { echo $Topic->ctitle; } ?> </a>
				</td>
				<td class="topic_details">
					
				</td>
					
				<td class="topic_last">
					
					<?=date('d.m.Y H:i:s', strtotime($Topic->date_created))?>
				</td>
			</tr>
	<? } ?>
		</tbody>
	</table>
	
	<p class="comment_add_link" onclick="showNewMsg('<?=$Pg->id?>')";>Aloita uusi keskustelu</p>

	<div id="newcomment" class="replybox" style="display:none;">
			<img id="closenewmsg" src="/img/close.png" alt="" title="Sulje" onclick="hideNewMsg();" />
			<h2>Uusi viesti</h2>
			<div id="newcommentform"></div>
	</div>
<? } else {?>
	<a href="#" onclick="javascript:window.history.back(-1);return false;">N&auml;yt&auml; kaikki keskustelut</a>
<? } ?>
	<div id="ajax_request_div"></div>
<? /*if ( count($Comments) > 0 ) { ?>
	<? $topic_title = new \Lougis_comment_topic($_GET['tid']);
		echo '<h2>'.$topic_title->title.'</h2>';
	?>
	<ul id="messages">
	<? foreach($Comments as $Cm) { ?>
		<li id="cm<?=$Cm->id?>"><a name="cm<?=$Cm->id?>"></a>
			<?
			$clicked = in_array($Cm->id, $_SESSION['rated_comments']);
			?>
			<div id="lbox<?=$Cm->id?>" class="likebox<?=(($clicked) ? ' clicked' : '' )?>">
				<a class="likethumb" onclick="<?=(($clicked) ? '' : 'likeComment('.$Cm->id.');' )?>" title="Äänestä viestiä (+)">
					<img src="/img/thumbup.png" alt="" /> <span><?=$Cm->likes?></span>
				</a>
				<a class="dislikethumb" onclick="<?=(($clicked) ? '' : 'dislikeComment('.$Cm->id.');' )?>" title="Äänestä viestiä (-)">
					<img src="/img/thumbdown.png" alt="" /> <span><?=$Cm->dislikes?></span>
				</a>
			</div>
			<p><?=nl2br($Cm->msg)?></p>
			<span class="author">"<?=$Cm->getUsername()?>" kirjoitti <?=date('d.m.Y H:i:s', strtotime($Cm->date_created))?></span>
			<a class="replythread" onclick="showReplyBox(<?=$Cm->id?>);">Vastaa</a>
			<div id="replybox<?=$Cm->id?>" class="replybox"></div>
			<? if ( count($Cm->replys) > 0 ) { ?>
			<ul class="replys">
			<? foreach($Cm->replys as $Reply) { ?>
				<li><a name="cm<?=$Reply->id?>"></a>
					<?
					$clicked = in_array($Reply->id, $_SESSION['rated_comments']);
					?>
					<div id="lbox<?=$Reply->id?>" class="likebox<?=(($clicked) ? ' clicked' : '' )?>">
						<a class="likethumb" onclick="<?=(($clicked) ? '' : 'likeComment('.$Reply->id.');' )?>" title="Äänestä viestiä (+)">
							<img src="/img/thumbup.png" alt="" /> <span><?=$Reply->likes?></span>
						</a>
						<a class="dislikethumb" onclick="<?=(($clicked) ? '' : 'dislikeComment('.$Reply->id.');' )?>" title="Äänestä viestiä (-)">
							<img src="/img/thumbdown.png" alt="" /> <span><?=$Reply->dislikes?></span>
						</a>
					</div>
					<p><?=nl2br($Reply->msg)?></p>
					<span class="author">"<?=$Reply->getUsername()?>" kirjoitti <?=date('d.m.Y H:i:s', strtotime($Reply->date_created))?></span>
				</li>
			<? } ?>
			</ul>
			<? } ?>
		</li>
	<? } ?> 
	</ul>
	<? }?>
	<? if($_GET['tid'] > 0){ ?> 
	<div id="kirjoita">
		<a id="newthread" onclick="showNewMsg('<?=$Pg->id?>', null, <?=$_GET['tid']?>);" >Uusi viesti</a>
	</div>
	<? } ?> 
	<? /*<div id="accordion">
		
		<h3 class="topic_title" id="<?=$Topic->topicid?>"><?=$Topic->title?> </h3>
		<div id="div_<?=$Topic->topicid?>">
		<!--	<a href="/fi/<?=$Topic->comment_place?>/" > Linkki alkuper&auml;iseen keskusteluun </a> -->			
			<ul id="messages"></ul>
		</div>
		
		<? }}  ?> 
	</div> <? */?>

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
</div>
<script type="text/javascript" src="/js/lougis/lib/comments.ui.extjs.js"></script>
<script>

  jQuery(function() {
	$("#comment_topics").tablesorter(); 
    //Accordion
	/*jQuery( "#accordion" ).accordion({
		heightStyle: "content"
		
	});*/
	
	//Hae aiheen kommentit (... ja uudestaan klikkaamalla päivittää)
	jQuery(".topic_title").click(function() {
		var id = jQuery(this).attr('id');
		var req = jQuery.ajax({
			url: '/run/lougis/comment/getTopicMsgs/',
			data: { topic: id },
			type: 'GET',
			dataType: 'json'
		});
		req.done(function(xhr) {
			jQuery('#messages').empty(); //tyhjennetään 
			jQuery.each(xhr, function(k, v) {
				console.log(k, v);
				//viestit
				jQuery('#messages' ).append('<li id="cm' + v.id + '"><a name="cm' + v.id + '"></a> <h2>' + v.title + '</h2> <p>' + v.msg + '</p> <span class="author">' + v.first + ' ' + v.last + '</span> <a class="replythread" onclick="showReplyBox('+v.id+');">Vastaa</a><div id="replybox'+v.id+'" class="replybox"></div></li>');
				//vastaukset
				if(v.replys.length > 0) {
					jQuery('#messages' ).append('<ul class="replys">');
					jQuery.each(v.replys, function(kr, vr) {
						console.log("reply", kr, vr);
						jQuery('#messages .replys' ).append('<li id="cm' + vr.id + '"><a name="cm' + vr.id + '"></a> <p>' + vr.msg + '</p> <span class="author">' + v.first + ' ' + v.last + ' </span> <a class="replythread" onclick="showReplyBox('+vr.id+');">Vastaa</a><div id="replybox'+vr.id+'" class="replybox"></div></li>');
					});
				jQuery('#messages' ).append('</ul>');
				}
				console.log(jQuery('#messages' ));
			});
		});
		req.fail(function(xhr) {
			alert('fail '+xhr.status + xhr.msg + xhr.testi);
		});
	});
	
  });
 </script>
 
<? require_once(PATH_TEMPLATE.'everkosto/include_footer.php'); ?>
