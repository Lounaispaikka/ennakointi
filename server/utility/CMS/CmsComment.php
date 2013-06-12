<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

/**
 * CmsComment is used by public side scripts to output Lougis-Comments.
 */
class CmsComment extends \Lougis\abstracts\Utility {

	public function getTopics ( $parent_id ) {
		devlog($parent_id, "e_comm");
		$Topics = array();
	    $Topic = new \Lougis_comment_topic();
		$parent = pg_escape_string($parent_id);
	    /*$Topic->query(
		'SELECT distinct on (comment_topic.id) comment_topic.id AS "topicid", comment_msg.date_created as "date", cms_page.title as "title", cms_page.id as "comment_place" 
			FROM lougis.comment_topic, lougis.comment_msg, lougis.cms_page 
			WHERE ((comment_topic.id = comment_msg.topic_id) AND (cms_page.comments_id = comment_topic.id))
			UNION SELECT distinct on (comment_topic.id) comment_topic.id AS "topicid", comment_msg.date_created as "date", chart.title as "title", chart.id as "comment_place" 
			FROM lougis.comment_topic, lougis.comment_msg, lougis.chart 
			WHERE ((comment_topic.id = comment_msg.topic_id) AND (chart.comments_id = comment_topic.id))
		ORDER BY date DESC;');*/
		/*$Topic->query(
		'	SELECT DISTINCT ON (comment_topic.id) comment_topic."id" AS "topicid", comment_msg.date_created as "date", cms_page.title as "title", cms_page."id" as "comment_place"
			FROM lougis.comment_topic, lougis.comment_msg, lougis.cms_page 
			WHERE (comment_topic."id" = comment_msg.topic_id) AND (cms_page.comments_id = comment_topic.id) AND cms_page.parent_id='.$parent.';
		');*/
		$parent_page = new \Lougis_cms_page($parent_id);
		$parent_list = array();
		$parent_list = $parent_page->getEveryChildren();
		$child_array = array();
		foreach ($parent_list as $pp ) {
			$child_array[] = $pp->id;
		}
		devlog($child_array, "e_comm");
		
		/*$Topic->query(
		'	SELECT DISTINCT ON (comment_topic.id) comment_topic."id" AS "topicid", comment_topic.page_id, comment_msg.date_created as "date", cms_page.title as "title", comment_topic."title" as "ctitle"
			FROM lougis.comment_topic, lougis.comment_msg, lougis.cms_page
			WHERE comment_topic.page_id in(
			select cms_page."id"
			from lougis.cms_page
			where cms_page.parent_id in ('.implode(",",$child_array).')
			AND lougis.comment_msg.topic_id = lougis.comment_topic."id"
			AND lougis.cms_page."id" = lougis.comment_topic.page_id);'
		);*/
		$Topic->query(
		'SELECT DISTINCT ON (comment_topic."id") comment_topic."id" AS "topicid", comment_topic.page_id, comment_topic."title" as "ctitle", cms_page."id" as "page", cms_page.title, comment_msg.date_created
FROM lougis.comment_topic, lougis.cms_page, lougis.comment_msg
WHERE cms_page."id" = comment_topic.page_id
AND comment_msg.topic_id = comment_topic."id"
AND cms_page."id" in ('.implode(",",$child_array).');');
		while( $Topic->fetch() ) {
		    $Topics[] = clone($Topic);
	    }
	    return $Topics;
	
	}
	
	public function getTopicMsgs ( ) {
		try {
			$topicId = $_REQUEST['topic'];
			if ( empty($topicId) ) throw new \Exception("Topic id required");
			$Comments = array();
			$Cm = new \Lougis_comment_msg();
			$Cm->topic_id = $topicId;
			$Cm->whereAdd('parent_id IS NULL');
			$Cm->orderBy('date_created ASC');
			$Cm->find();
			while( $Cm->fetch() ) {
				//$Cm->loadReplys();
				$Comments[] = clone($Cm);
			}
			$res = array(
				"success" => true,
				"msg" => $Comments,
				"testi" => 'testisuc'
			);
		
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

}