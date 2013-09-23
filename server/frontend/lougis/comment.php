<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class Comment extends \Lougis\abstracts\Frontend {

	public function newComment() {
		
		global $Site, $Session;
		
		try {
			if(!isset($_SESSION['user_id'])) throw new \Exception("Ongelma kirjautumisessa. Ota yhteyttä sivuston ylläpitoon");
			// testirivi: throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon comment_session". $_REQUEST['type']. ": ".$_REQUEST['type_id']);
		
			//Tarkistetaan koskeeko kommentti sivua, chartia...
			/*switch($_REQUEST['type']) {
				case 'chart':
					$MsgTopic = new \Lougis_chart($_REQUEST['type_id']);
					break;
				case 'file':
					$MsgTopic = new \Lougis_file($_REQUEST['type_id']);
					break;
				case 'link':
					$MsgTopic = new \Lougis_link($_REQUEST['type_id']);
					break;
				//default == page
				default:
					$MsgTopic = new \Lougis_cms_page($_REQUEST['type_id']);
			}*/
			//Tarkistetaan ettei type_id ole nolla, jos on nolla niin tehdään uusi sivusta riippumaton topic
			/*if ( $_REQUEST['this_page'] == null) {
				$MsgTopic = new \Lougis_cms_page($_REQUEST['type_id']);
				if ( !$MsgTopic->hasCommentTopic() ) {
					$NewTopic = new \Lougis_comment_topic();
					$NewTopic->active = true;
					if ( !$NewTopic->save() ) throw new \Exception("1 Tekninen virhe uuden kommenttisivun luomisessa. Ota yhteyttä sivuston ylläpitoon");
					$MsgTopic->comments_id = $NewTopic->id;
					$_SESSION['comments_id'] = $MsgTopic->comments_id;
					if ( !$MsgTopic->save() ) throw new \Exception("1 Tekninen virhe uuden kommenttilinkin luomisessa. Ota yhteyttä sivuston ylläpitoon");
				}
			}*/
			/*if ( $_REQUEST['this_page'] == null) {
				$NewTopic = new \Lougis_comment_topic();
				if ( !$NewTopic->hasPage() ) {
					$NewTopic->active = true;
					$NewTopic->page_id = $_REQUEST['type_id'];
					if ( !$NewTopic->save() ) throw new \Exception("1 Tekninen virhe uuden kommenttisivun luomisessa. Ota yhteyttä sivuston ylläpitoon");
				}
			}
			else {*/
			
			$CmData = $_REQUEST['comment'];
			
				$cid = new \Lougis_comment_msg();
				
				$cid->id = $_REQUEST['reply_to'];
				$cid->find();
				$cid->fetch();
				$_SESSION['comments_id'] = $cid->topic_id;
				if($CmData['topic_id'] != null) $_SESSION['comments_id'] = $CmData['topic_id'];
				
				if($_REQUEST['reply_to'] == null && $CmData['topic_id'] == null ) { 
				
					$NewTopic = new \Lougis_comment_topic();
					$NewTopic->active = true;
					$NewTopic->page_id = $_REQUEST['this_page'];
					$NewTopic->title = $CmData['title'];
					//$NewTopic->id = $cid->topic_id;
					if ( !$NewTopic->save() ) throw new \Exception("2 Tekninen virhe uuden kommenttisivun luomisessa. Ota yhteyttä sivuston ylläpitoon");
					$_SESSION['comments_id'] = $NewTopic->id;
				
				}
		//	}
			
			
			//Sitten itse kommentti...
			
			
			
			$Required = array('msg', 'check');
			
			foreach($Required as $ReqVal) {
				if ( empty($CmData[$ReqVal]) || strlen($CmData[$ReqVal]) < 2 ) throw new \Exception("Lomakkeessa tyhjä kenttä. Kaikki kentät ovat pakollisia");
			}
			//if ( strlen($CmData['nick']) > 200 || strlen($CmData['title']) > 200 ) throw new \Exception("Liian pitkä otsikko tai nimi.");
			//if ( empty($CmData['check']) || $CmData['check'] != date('Y') ) throw new \Exception("Virheellinen vastaus tarkistuskysymykseen! Kirjoita vastauskenttään oikea vuosi.");
			//if ( empty($_SESSION['comments_id']) ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon comment_session");
			
			
			$Cm = new \Lougis_comment_msg();
			//$Cm->setFrom($CmData);
			devlog($CmData, "ecom");
			$Cm->user_id = $_SESSION['user_id'];	
			$Cm->topic_id = $_SESSION['comments_id'];
			$Cm->lang_id = $_SESSION['lang_id'];
			$Cm->title = strip_tags($CmData['title']);
			if($CmData['parent_id'] != null) $Cm->parent_id = $CmData['parent_id'];
			$Cm->msg = strip_tags($CmData['msg']);
			devlog($Cm, "ecom");
			if ( !$Cm->save() )throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon msg". $Cm->_lastError);
			devlog($Cm, "ecom");
			
			$res = array(
				"success" => true,
				"comment" => $Cm->toArray(),
				"msg" => "Viesti on l&auml;hetetty!"
			);
                        $mail_msg = "Viestin otsikko: ".$CmData['title']."\n\n".$CmData['msg']."\n\n (Tämä on automaattinen viesti, älä vastaa).";
			//mail('ville@lounaispaikka.fi', $mail_msg, 'From: ymparisto@lounaispaikka.fi');
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function newTopic() {
	
		/*try {
		
			$Topic = new \Lougis_comment_topic();
			$Topic->active = true;
			if ( !Topic->save() ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon");
			
			$res = array(
				"success" => true,
				"comment" => $Topic->toArray()
			);
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
		}
		$this->jsonOut($res);*/
		
	}
	
	public function likeMsg() {
		
		global $Site, $Session;
		
		try {
			
			if ( !isset($_SESSION['rated_comments']) ) $_SESSION['rated_comments'] = array();
			
			$MsgId = $_REQUEST['msgid'];
			if ( empty($MsgId) || in_array($MsgId, $_SESSION['rated_comments']) ) throw new \Exception("Tämä kommentti jo arvioitu.");
			
			$Cm = new \Lougis_cms_comment($_REQUEST['msgid']);
			if ( $_REQUEST['likeval'] > 0 ) {
				$Cm->likes = $Cm->likes+1;
			} else {
				$Cm->dislikes = $Cm->dislikes+1;
			}
			
			if ( !$Cm->save() ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon");
			array_push($_SESSION['rated_comments'], $MsgId);
			$res = array(
				"success" => true,
				"comment" => $Cm->toArray()
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	/* original replybox
	public function replyBoxHtml() {
		
		$MsgId = $_REQUEST['msgid'];
		if ( empty($MsgId) ) {
			echo "Tekninen virhe!";
			die;
		}
		$Rules = \Lougis_cms_comment::getRules();
		?>
<img class="closereplybox" src="/img/close.png" alt="" title="Sulje" onclick="closeReplyBox(<?=$MsgId?>);" />
<h2>Vastaa viestiin</h2>
<div id="replyform<?=$MsgId?>"></div>
		<?
		//echo $Rules;
		
	}
	*/
	public function replyBoxHtml() {
		
		$MsgId = (int)($_POST['msgid']);
		if ( empty($MsgId) ) {
			echo "Tekninen virhe!";
			die;
		}
		$Rules = \Lougis_cms_comment::getRules();
		?>
<img class="closereplybox" src="/img/close.png" alt="" title="Sulje" onclick="cancelMsgEdit();" />
<h2>Vastaa viestiin</h2>
<div id="replyform<?=$MsgId?>" ><form id="vastaa_form<?=$MsgId?>" class="ui-widget"></form></div>
		<?
		//echo $Rules;
		
	}
	
	//luo kommenttisivun html:n, ajax-hakuihin
	public function getCommentsHtml() {
		//kommentit sivulla
		if($_POST['topic_id']) {
			$topic_id = (int)$_POST['topic_id'];
			$ct = new \Lougis_comment_topic($topic_id);	
		}
		//kommentit keskustelu-osiossa
		else {
			$page_id = (int)$_POST['page_id'];
			$ct = new \Lougis_comment_topic();
			$ct->page_id = $page_id;
			$ct->find();
			$ct->fetch();
		}
		
		if ( $ct->id != null ) $Comments = \Lougis_comment_msg::getAllForTopic($ct->id);
?>
	<? if ( count($Comments) > 0 ) { ?>
		<table id="messages">
		<? foreach($Comments as $Cm) { ?>
			<tr>
				<td class="message_info">
					<p><span class="author"><?=$Cm->getUsername()?></span></p>
					<p><span class="author"><?=date('d.m.Y H:i:s', strtotime($Cm->date_created))?></span></p>
				</td>
				<td class="message_msg">
					<h1 class="msg_topic"><?=$Cm->title?></h1>
					<p><?=nl2br($Cm->msg)?></p>
					<? /* if($_SESSION['user_id'] === $Cm->user_id) { ?>
					<button id="del_<?=$Cm->id?>" class="del_comment_btn">Poista viesti</button>
					<? } */?>
				</td>
<? //poistetaan vastaus mahdollisuus, ts. kaikki viestit menevät yhdelle tasolle
/*
				<td>
					<a class="replythread" onclick="showReplyBox(<?=$page_id?>, <?=$Cm->id?>);">Vastaa</a>
					<div id="replybox<?=$Cm->id?>" class="replybox">
				</td>
*/ ?>
			</tr>
		<? if ( count($Cm->replys) > 0 ) { ?>
			<? foreach($Cm->replys as $Reply) { ?>
			<tr class="replys">
				<td>
					<p><span class="author"><?=$Cm->getUsername()?></span></p>
					<p><span class="author"><?=date('d.m.Y H:i:s', strtotime($Cm->date_created))?></span></p>
				</td>
				<td>
					<h1 class="msg_topic">VS: <?=$Cm->title?></h1>
					<p><?=nl2br($Reply->msg)?></p>
				</td>
<? /*
				<td>
				</td>
*/?>
				</tr>
			<? 		} 
				}
			}?>
		</table>

<?		} 
/*

		<ul id="messages">
		<? foreach($Comments as $Cm) { ?>
			<li id="cm<?=$Cm->id?>"><a name="cm<?=$Cm->id?>"></a>
				<?/*
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
				*/ /* ?>
				<span class="author">"<?=$Cm->getUsername()?>" kirjoitti <?=date('d.m.Y H:i:s', strtotime($Cm->date_created))?></span>
				<h3><?=$Cm->title?></h3>
				
				<p><?=nl2br($Cm->msg)?></p>
				
				<a class="replythread" onclick="showReplyBox(<?=$page_id?>, <?=$Cm->id?>);">Vastaa</a>
				
				<div id="replybox<?=$Cm->id?>" class="replybox">
					
				</div>
				
				<? if ( count($Cm->replys) > 0 ) { ?>
				<ul class="replys">
				<? foreach($Cm->replys as $Reply) { ?>
					<li><a name="cm<?=$Reply->id?>"></a>
						<? /*
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
						*//* ?>
						<span class="author">"<?=$Reply->getUsername()?>" kirjoitti <?=date('d.m.Y H:i:s', strtotime($Reply->date_created))?></span>
						<p><?=nl2br($Reply->msg)?></p>
					</li>
				<? } ?>
				</ul>
				<? } ?>
			</li>
		<? } ?> 
		</ul>
		<? } ?>
<? */
		
		
	}
	
	public function getTopicMsgs ( ) {
		try {
			$topicId = pg_escape_string($_REQUEST['topic']);
			if ( empty($topicId) ) throw new \Exception("Topic id required");
			$Comments = array();
			$Cm = new \Lougis_comment_msg();
			/*$Cm->topic_id = $topicId;
			$Cm->whereAdd('parent_id IS NULL');
			//$Cm->whereAdd("{$Cm->user_id} = {$User->id}");
			$Cm->orderBy('date_created ASC');
			$Cm->find();*/
			$Cm->query('
				SELECT lougis.comment_topic.title as title, lougis.comment_msg.msg as msg, lougis.comment_msg.id as id, lougis.user.firstname as first, lougis.user.lastname as last, lougis.comment_msg.user_id as user_id
				FROM lougis.comment_msg, lougis."user", lougis.comment_topic
				WHERE comment_msg.topic_id ='.$topicId.'
				AND comment_msg.parent_id is null
				AND comment_msg.user_id = "user"."id"
				AND comment_msg.topic_id = comment_topic.id
				ORDER BY comment_msg.date_created ASC;
			');

			while( $Cm->fetch() ) {
				
				$Cm->loadReplys($Cm->topic_id);
				$Comments[] = clone($Cm);
				devlog($Cm);
			}
			/*$res = array(
				"success" => true,
				"msg" => $Comments,
				"testi" => 'testisuc'
			);*/
			$res = $Comments;
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage(),
				"testi" => 'testifail'
			);
			
		}
		echo json_encode($res);
		//return $Comments;
	}
	
	public function deleteComment() {
		global $Site;
		try {
			
			$Comment = new \Lougis_comment_msg($_REQUEST['comment_id']);
			//auth user or admin
			if ( $Comment->user_id !== $_SESSION['user_id'] ) throw new \Exception('Kommentin poistaminen epäonnistui: Käyttäjän tunnistaminen ei onnistunut!');
			if ( empty($Comment->msg) ) throw new \Exception('Kommentin poistaminen epäonnistui: Kommenttia ei voitu ladata!');
			if ( $Comment->site_id != $Site->id ) throw new \Exception('Kommentin poistaminen epäonnistui: Virheellinen sivusto!');
				
			if ( !$Comment->delete() ) throw new \Exception('Kommentin poistaminen epäonnistui: '.$News->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Kommentti poistettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	
}
?>
