<?
function open_db() {
	$connection = pg_connect("dbname=lougis_beta user=lougis_webuser password=lp123qweasdzxc host=lounaispaikka4.utu.fi port=5432");
	if(!$connection)
	{
		throw new Exception("Database Connection Error");
	}
	return $connection;
}
	
function phorum_insert_lougis_post() {
//function phorum_insert_lougis_post($page_id,$thread,$phorum_user_id,$subject,$body,$datestamp="") {
//function phorum_insert_lougis_post($article_id,$user_id,$forum_id,$subject,$body,$datestamp=""){
	// date is optional, default will be current time
	// returns Phorum URL

	//$dblink = openDB(); // this is my DB wrapper
	
	$MsgData = $_REQUEST['msg'];
	
	$dblink = pg_connect("dbname=lougis_beta user=lougis_webuser password=lp123qweasdzxc host=lounaispaikka4.utu.fi port=5432");
	
	if ((int)$page_id==0){
		return;
	}

	chdir(PATH_PUBLIC."phorum/"); //aseta polku
	include_once("./include/thread_info.php");

	chdir(PATH_PUBLIC); //aseta polku

	// get phorum user info
	$user = phorum_db_user_get($MsgData['phorum_user_id'],false);
	$author = $user["username"];
/*
	// first check death hasn't already been inserted
	$result = pg_query($dblink ,$sql);
	$sql = "SELECT * FROM lougis_phorum_page WHERE `article_id` = $article_id";
	if (pg_num_rows($result)>0){
		// we've already added this death, bomb out
		return;
	}
*/
	// if we get to here, we need to insert the death
	$message["forum_id"] = 3;
	$message["thread"]=$MsgData['thread'];
	$message["parent_id"]=0;
	$message["message_id"]=0;
	$message["status"] = PHORUM_STATUS_APPROVED;
	$message["sort"] = PHORUM_SORT_DEFAULT;
	$message["closed"] = 0;
	$message["user_id"]=$MsgData['phorum_user_id'];
	//$message["author"]=$author;
	$message["subject"]=$MsgData['subject'];
	$message["moderator_post"]=0;
	$message["msgid"] = md5(uniqid(rand())) . "." . preg_replace("/[^a-z0-9]/i", "", "Everkosto");

	if (strlen($MsgData['datestamp'])>0){
		$message["datestamp"]=$MsgData['datestamp'];
		$convert = true;
	}
	else{
		$convert = false;
	}
	$message["body"]=$MsgData['body'];

	$success = phorum_db_post_message($message,$convert);

	if ($success){
		phorum_update_thread_info($message["thread"]);
		phorum_db_update_forum_stats(false, 1, $message["datestamp"],1);
		// message ID updated by reference, insert it into tblArticles
		$sql = "INSERT INTO lougis_phorum_page (page_id,forum_id,thread_id)
			VALUES ($MsgData['page_id'],{$message["forum_id"]},{$message["thread"]})";
          $result = pg_query($dblink, $sql);
	  $returnvalue = "dev.everkosto.lounaispaikka.fi/phorum/read.php?{$message["forum_id"]},{$message["thread"]}"; //aseta osoite
	}
	
	pg_close();
	return $returnvalue;

}
?>