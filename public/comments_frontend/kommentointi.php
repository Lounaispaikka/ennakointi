<?
if ( $_SESSION['user_id'] ) {
	global $Cms;
	$Pg = $Cms->getPage();
	
	$_SESSION['page_id'] = $Pg->id;
	if ( !isset($_SESSION['rated_comments']) ) $_SESSION['rated_comments'] = array();
	
	$ct = new \Lougis_comment_topic();
	$ct->page_id = $Pg->id;
	$ct->find();
	$ct->fetch();
	if(isset($_GET['debug'])) print($ct->id.'-'.$ct->page_id);

	if ( $ct->id != null ) $Comments = \Lougis_comment_msg::getAllForTopic($ct->id);
	//$Rules = \Lougis_comment_msg::getRules();

	?>
	<div id="comments">
		<a id="newthread" onclick="showNewMsg('<?=$Pg->id?>');">Kirjoita uusi viesti</a>
	<!--	<a id="newthread" onclick="showNewMsg('<?=$MsgType?>', '<?=$TypeId?>');">Kirjoita uusi viesti</a> -->
		<h2>Keskustelu</h2>
		<div id="newcomment" class="msgform">
			<img id="closenewmsg" src="/img/close.png" alt="" title="Sulje" onclick="hideNewMsg();" />
			<? //=$Rules?>
			<h2>Uusi viesti</h2>
			<div id="newcommentform"></div>
		</div>
		<? if ( count($Comments) > 0 ) { ?>
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
				<h2><?=$Cm->title?></h2>
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
		<? } ?>
	</div>
	<?
	/*
	$Co = new \Lougis\utility\Compiler("comments-ui-extjs", "js");
	$Co->addJs("/js/lougis/lib/comments.ui.extjs.js");
	if ( isset($_REQUEST['debug']) && strpos(PATH_SERVER, 'development') != false ) {
		$Co->outputFilesScriptTags();
	} else {
		$Co->outputScriptHtml();
	}*/
	?>
	<script type="text/javascript" src="/js/lougis/lib/comments.ui.extjs.js"></script>
<?
}
?>