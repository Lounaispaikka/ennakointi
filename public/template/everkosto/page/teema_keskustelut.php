<?php 
require_once(PATH_TEMPLATE.'everkosto/include_header.php'); 
require_once(PATH_SERVER.'utility/LouGIS/Compiler.php');

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

//count comments by topic


?>
<script>
		
		var request = $.ajax({
			url: '/run/lougis/comment/getCommentsHtmlKeskustelu/',
			data: {
				//page_id: <?=$Pg->id?>,
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
<script type="text/javascript" src="/js/lougis/lib/comments.ui.jquery.js"></script>
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
		<!--		<th>Viestej&auml;</th>-->
				<th>Viimeisin viesti</th>
			</tr>
		</thead>
		<tbody id="topics_body">
	<? $CommentTopics = $CmsCom->getTopics($Pg->parent_id);?>
	<? foreach($CommentTopics as $Topic) { ?>
			<tr id="<?=$Topic->topicid?>" class="topic_row">
				<td class="topic_topic">
					<a href="?tid=<?=$Topic->topicid?>"><? if($Topic->page_id != $Pg->id || $Topic->ctitle == null) {echo $Topic->title;} else { echo $Topic->ctitle; } ?> </a>
				</td>
				<!--<td class="topic_details">
					
				</td>-->
					
				<td class="topic_last">
					
					<?=date('d.m.Y H:i:s', strtotime($Topic->date_created))?>
				</td>
			</tr>
	<? } ?>
		</tbody>
	</table>
	
	<!--<p class="comment_add_link" onclick="showNewMsg('<?=$Pg->id?>')";>Aloita uusi keskustelu</p>-->

<? } else {?>
	<a href="#" onclick="javascript:window.history.back(-1);return false;">N&auml;yt&auml; kaikki keskustelut</a>
<? } ?>
	<div id="comments">
		<div id="ajax_request_div"></div>
	</div>

</div>
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
