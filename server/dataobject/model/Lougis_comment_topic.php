<?php
/**
 * Table Definition for lougis.comment_topic
 */
require_once 'DB/DataObject.php';

class Lougis_comment_topic extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.comment_topic';            // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.comment_topic_id_seq%29 primary_key
    public $active;                          // bool(1)  not_null default_true
    public $page_id;                         // int4(4)  
    public $title;                           // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_comment_topic',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
	
	public function hasPage() {
		
		if ( empty($this->id) ) return false;
		$page = new \Lougis_cms_page();
		$page->id = $this->page_id;
		if ( $page->count() > 0 ) return true;
		return false;
		
	}
}
