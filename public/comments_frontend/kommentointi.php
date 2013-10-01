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
	
	if($ct->id != null) {
		$_SESSION['comment_topic_id'] = $ct->id;
	}
	

	//if ( $ct->id != null ) $Comments = \Lougis_comment_msg::getAllForTopic($ct->id);
	//$Rules = \Lougis_comment_msg::getRules();
	
	?>
	<script>

		var request = $.ajax({
			url: '/run/lougis/comment/getCommentsHtml/',
			data: {
				page_id: <?=$Pg->id?>
				<? if($ct->id != null) { ?>,topic_id: <?=$ct->id?> <? } ?>
			},
			type: "POST"
		});
		request.done(function(response) {
			$("#ajax_request_div").empty();
			$("#ajax_request_div").append(response);
		});

	</script>

	<div id="comments">
		<div id="ajax_request_div">
		</div>
		<div id="dialog-message" title="Viesti l&auml;hetetty!">
			<p id="response">
				<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
				<span id="response_msg"></span>
			</p>
		</div>
	</div>
	<? /*
		<h2>Keskustelu</h2>
		<?
	if ( $ct->id == null ) { ?>
		<a id="newthread" onclick="showNewTopic(<?=$Pg->id?>);">Kommentoi</a>
	<? } ?>
		<div id="ajax_request_div"></div>
		<div id="newcomment" class="msgform">
			<img id="closenewmsg" src="/img/close.png" alt="" title="Sulje" onclick="cancelMsgEdit();" />
			<? //=$Rules?>
			<h2>Uusi viesti</h2>
			<div id="newcommentform">
					<form id="kommentti_form" class="ui-widget"></form>
			</div>
			
		</div>
		<? if ( $ct->id != null ) { ?>
			<a id="newthread" onclick="showNewMsg(<?=$Pg->id?>, <?=$ct->id?>);" style="margin-top:15px;">Kommentoi</a>
		<? } ?>
	</div>
	<div id="dialog-message" title="Viesti l&auml;hetetty!">
		<p id="response">
			<span class="ui-icon ui-icon-circle-check" style="float: left; margin: 0 7px 50px 0;"></span>
			<span id="response_msg"></span>
		</p>
	</div>
	*/ ?>
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
	<!--<script type="text/javascript" src="/js/lougis/lib/comments.ui.extjs.js"></script>-->
	<script type="text/javascript" src="/js/lougis/lib/comments.ui.jquery.js"></script>
	<? /*
	<script type="text/javascript">
		$(function() {	
			
			$('.del_comment_btn').click(function(){
				console.log("del");
				return false;
			});
			
			
		});
	</script>
	*/ ?>
<?
}

?>
