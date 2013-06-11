<script type="text/javascript" src="/js/lougis/lib/phorum_comments.ui.extjs.js"></script>
<?
chdir(PATH_PUBLIC."phorum/");
include_once("./common.php");
chdir(PATH_PUBLIC);

global $Cms;

$Pg = $Cms->getPage();
$_SESSION['page_id'] = $Pg->id;

//$Thread_id = \Public_lougis_phorum_page::getThreadId($Pg->id);

$Thread_id = $Cms->getPhorumThreadId($Pg->id);
$Msg_array = phorum_db_get_messages($Thread_id);

$Messages = phorum_hook("read", $Msg_array);

$Messages = phorum_format_messages($Messages);

?>
<div id="comments">
	<h2>Keskustelu</h2>
<!--	<a id="newthread" onclick="createNewMsg()">Kirjoita uusi viesti</a> -->
<a id="newthread" onclick="alert('Kehitys kesken...')">Kirjoita uusi viesti</a>
<? if ( !is_null($Thread_id) ) { ?>
	<? /*<div id="newcomment" class="msgform">
		<img id="closenewmsg" src="/img/close.png" alt="" title="Sulje" onclick="hideNewMsg();" />
		<h2>Uusi viesti</h2>
		
		<div id="newcommentform">
		</div>
	</div> */ ?>
	
<?  $msg_count = count($Messages);
	if ( $msg_count > 1 ) { ?>
	<ul id="messages">
	<?//=print_r($Messages)?>
<?  $i=0;
	foreach($Messages as $Msg) { ?>
		<? //poistetaan viestiketjun avaus ja $messages-arrayn viimeinen
			if(($i != 0) && ($i != $msg_count-1)) { ?>
		<li id="cm<?=$Msg['message_id']?>"><a name="cm<?=$Msg['message_id']?>"></a>
		<? /*
			$clicked = in_array($Cm->id, $_SESSION['rated_comments']);
		?>
			<div id="lbox<?=$Cm->id?>" class="likebox<?=(($clicked) ? ' clicked' : '' )?>">
				<a class="likethumb" onclick="<?=(($clicked) ? '' : 'likeComment('.$Cm->id.');' )?>" title="Äänestä viestiä (+)">
					<img src="/img/thumbup.png" alt="" /> <span><?=$Cm->likes?></span>
				</a>
				<a class="dislikethumb" onclick="<?=(($clicked) ? '' : 'dislikeComment('.$Cm->id.');' )?>" title="Äänestä viestiä (-)">
					<img src="/img/thumbdown.png" alt="" /> <span><?=$Cm->dislikes?></span>
				</a>
			</div> */ ?>
		<h2><?=$Msg['subject']?></h2>
		<p><?=nl2br($Msg['body'])?></p>
		<span class="author">"<?=$Msg['author']?>" kirjoitti <?=date("d.m.y H:m:s", $Msg['datestamp'])?></span>
		<a class="replythread" onclick="showReplyBox(<?=$Msg['message_id']?>);">Vastaa</a>
		<a class="quotemsg" onclick="showReplyBox(<?=$Msg['message_id']?>);">Lainaa</a>
	<?/*	<div id="replybox<?=$Msg['message_id']?>" class="replybox"></div> */?>
		<?/* if ( count($Cm->replys) > 0 ) { ?>
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
					<span class="author">"<?=$Reply->nick?>" kirjoitti <?=date('d.m.Y H:i:s', strtotime($Reply->date_created))?></span>
				</li>
			<? } ?>
			</ul>
		<? }*/ ?>
		</li>
	<? } $i++;
		} ?> 
	</ul>
	<? } ?>
</div>
<? }
else { ?>
</div>
<? } ?>
<script type="text/javascript">
	$(document).ready(function(){
	
		var e = jQuery.Event("click");
	jQuery("newthread").trigger( e );
	
	});
</script>