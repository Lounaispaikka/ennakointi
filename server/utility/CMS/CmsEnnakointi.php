<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

/**
 * CmsEnakointi is used by public side scripts to output Ennakointi-site specific CMS content.
 */
class CmsEnnakointi extends \Lougis\abstracts\Utility {

	public function latestNewsEnnakointi($amount = 3) {
		global $Page;
		$amount = (int)($amount);
		if ( empty($Page->id) ) return false;
    	$newsArray = array();
    	$News = new \Lougis_news();
    	$sql = "SELECT lns.* FROM lougis.news AS lns
				JOIN lougis.news_page AS lnsp ON lnsp.news_id = lns.id
				WHERE lnsp.page_id = ".$Page->id."
				ORDER BY lns.seqnum
				LIMIT ".$amount.";"; //lis. rivi
    	$News->query($sql);
    	while( $News->fetch() ) { 
    		$newsArray[] = clone($News);
    	}
    	return $newsArray;
		
	}
	
	
	//tiedosto-listaus
	public function ennakointiFiles() {
		global $Page;
		if ( empty($Page->id) ) return false;
		
		$FilePages = array();
		$FilePage = new \Lougis_cms_page();
		$sql = "select *
				from lougis.cms_page, lougis.file
				where cms_page.parent_id = ".$Page->id."
				and cms_page.page_type = 'file'
				and cms_page.id = file.page_id;";
		$FilePage->query($sql);
		while($FilePage->fetch() ) {
			$FilePages[] = clone($FilePage);
		}
		return $FilePages;
	}
	
	/*public function latestCommentsEnnakointi($amount = 3) {
	
		global $Page;
		$comments = array();
		if ( empty($Page->id) ) return false;
		$Comment = new \Lougis_cms_comment();
		$sql = 'SELECT
lougis.comment_msg.date_created,
lougis.comment_msg.title,
lougis.cms_page.title,
lougis."user".firstname,
lougis."user".lastname
FROM
lougis.comment_msg ,
lougis.comment_topic ,
lougis.cms_page ,
lougis."user"
WHERE
lougis.comment_topic."id" = lougis.cms_page.comments_id AND
lougis.comment_msg.topic_id = lougis.comment_topic."id" AND
lougis."user"."id" = lougis.comment_msg.user_id AND
lougis.cms_page."id" IN '.(37) 
ORDER BY
lougis.comment_msg.date_created DESC;';
		$Comment->query($sql);
		while( $Comment->fetch() ) {
			$comments[] = clone($Comment);
		}
		return $comments;
	}*/
}