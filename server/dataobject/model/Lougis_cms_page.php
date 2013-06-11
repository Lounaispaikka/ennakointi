<?php
/**
 * Table Definition for lougis.cms_page
 */
require_once 'DB/DataObject.php';

class Lougis_cms_page extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.cms_page';                 // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.cms_page_id_seq%29 unique_key primary_key
    public $site_id;                         // varchar(-1)  not_null unique_key multiple_key
    public $lang_id;                         // bpchar(-1)  not_null
    public $title;                           // varchar(-1)  not_null
    public $url_name;                        // varchar(-1)  unique_key multiple_key
    public $nav_name;                        // varchar(-1)  not_null
    public $published;                       // bool(1)  not_null default_false
    public $created_date;                    // timestamptz(8)  not_null
    public $created_by;                      // int4(4)  
    public $keywords;                        // varchar(-1)  
    public $description;                     // varchar(-1)  
    public $code_after;                      // varchar(-1)  
    public $code_before;                     // varchar(-1)  
    public $parent_id;                       // int4(4)  
    public $visible;                         // bool(1)  not_null default_true
    public $seqnum;                          // int4(4)  
    public $template;                        // varchar(-1)  
    public $page_type;                       // varchar(-1)  
    public $extra1;                          // text(-1)  
    public $extra2;                          // text(-1)  
    public $restricted_access;               // bool(1)  not_null default_true
    public $comments_id;                     // int4(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_cms_page',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public $contentObject = null;
    
    public function getContent( $Published = null ) {
    	
    	if ( empty($this->contentObject) ) {
	    	$Pc = new \Lougis_cms_content();
	    	$Pc->lang_id = $this->lang_id;
	    	$Pc->page_id = $this->id;
	    	if ( !empty($Publised) ) $Pc->published = $Publised;
	    	$Pc->find(true);
	    	if ( !empty($Pc->date_created) ) $this->contentObject = $Pc;
    	}
    	if ( empty($this->contentObject) ) return false;
    	return $this->contentObject;
    
    }
    
    public function getContentHtml( ) {
    
    	$Pc = $this->getContent();
    	return stripslashes($Pc->content);
    
    }
    
    public function hasColumnContent() {
    
    	if ( $this->hasNews() ) return true;
    	$Pc = $this->getContent();
    	$ColContent = trim($Pc->content_column);
    	if ( !empty($ColContent) ) return true;
    	return false;
    	
    }
    
    public function getColumnHtml() {
    
    	$Pc = $this->getContent();
    	return stripslashes($Pc->content_column);
	
    }
    
    public function hasParentPage( $ParentId ) {
    	
    	$sql = "
		WITH RECURSIVE recurseCmsPages(id, parent_id) AS (
		    SELECT id, parent_id, title FROM lougis.cms_page WHERE id = ".$this->id."
		  UNION
		    SELECT cp.id, cp.parent_id, cp.title
		    FROM lougis.cms_page cp
		    JOIN recurseCmsPages rcp ON rcp.parent_id = cp.id
		  )
		SELECT * FROM recurseCmsPages;
    	";
    	$db =  &$this->getDatabaseConnection();
    	$res = pg_query( $db->connection, $sql );
    	while( $row = pg_fetch_object($res) ) {
    		if ( $row->id == $ParentId ) return true;
    	}
    	return false;
    
    }
    
    public function getParentPage() {
    	
    	if ( empty($this->parent_id) ) return null;
    	return new \Lougis_cms_page($this->parent_id);
    	
    }
    
    public function getParentPages( ) {
    	
    	$parents = array();
    	if ( empty($this->parent_id) ) return null;
    	$Parent = $this->getParentPage();
    	$parents[] = clone($Parent);
    	while( !empty($Parent->parent_id) ) {
    		$Parent = new \Lougis_cms_page($Parent->parent_id);
    		$parents[] = clone($Parent);
    	}
    	return $parents;
    
    }
	
	public function getParentIdArray() {
		
		$parents = array();
		if ( empty($this->parent_id) ) return $parents;
		$Parent = $this->getParentPage();
		$parents[] = $Parent->id;
		while( !empty($Parent->parent_id) ) {
    		$Parent = new \Lougis_cms_page($Parent->parent_id);
    		$parents[] = $Parent->id;
    	}
    	return $parents;
		
	}
    
    public function hasNews() {
    	
    	if ( empty($this->id) ) return false;
    	$Np = new \Lougis_news_page();
    	$Np->page_id = $this->id;
    	if ( $Np->count() > 0 ) return true;
    	return false;
    	
    }
    
    public function getNews() {
    	
    	if ( empty($this->id) ) return false;
    	$NewsArray = array();
    	$News = new \Lougis_news();
    	$sql = "SELECT lns.* FROM lougis.news AS lns
				JOIN lougis.news_page AS lnsp ON lnsp.news_id = lns.id
				WHERE lnsp.page_id = ".$this->id."
				ORDER BY lns.seqnum
				LIMIT 3"; //lis. rivi
    	$News->query($sql);
    	while( $News->fetch() ) { 
    		$NewsArray[] = clone($News);
    	}
    	return $NewsArray;
    	
	}
	
	public function hasTop() {
    	
    	if ( empty($this->id) ) return false;
    	$Np = new \Lougis_news_page();
    	$Np->page_id = $this->id;
    	if ( $Np->count() > 0 ) return true;
    	return false;
    	
    }
	
	/*
	public function getNews( $type, $amount = null ) {
    	
    	if ( empty($this->id) ) return false;
    	$NewsArray = array();
    	$News = new \Lougis_news();
    	$sql = "SELECT lns.* FROM lougis.news AS lns
				JOIN lougis.news_page AS lnsp ON lnsp.news_id = lns.id
				WHERE lnsp.page_id = ".$this->id."
				AND lnsp.news_type =".$type."
				LIMIT ".$amount."
				ORDER BY lns.seqnum";
    	$News->query($sql);
    	while( $News->fetch() ) { 
    		$NewsArray[] = clone($News);
    	}
    	return $NewsArray;
    	
    }
	*/
	public function getPageUrl() {
    
    	return "/".$this->lang_id."/".$this->id."/";
    
    }
    
    public function getPageFullUrl() {
    
    	return "http://".$_SERVER['HTTP_HOST']."/".$this->lang_id."/".$this->id."/";
    
    }
	
    
	public function getChildPagesByType( $type ) {
    
    	$Kids = array();
    	$Kid = new \Lougis_cms_page();
    	$Kid->parent_id = $this->id;
    	$Kid->page_type = $type;
    	$Kid->orderBy('seqnum');
    	$Kid->find();
    	while( $Kid->fetch() ) {
    		$Kids[] = clone($Kid);
    	}
    	
    	return $Kids;
    
    }
	
	public function getEveryChildren() {
		$Kids = array();
		$Kid = $Kid = new \Lougis_cms_page();
		$Kid->parent_id = $this->id;
		$Kid->find();
		while( $Kid->fetch() ) {
    		$Kids[] = clone($Kid);
			$NewKid = new \Lougis_cms_page();
			$NewKid->parent_id = $Kid->id;
			$NewKid->find();
			while( $NewKid->fetch() ) {
				$Kids[] = clone($NewKid);	
			}
    	}
		return $Kids;
	}
 
}
