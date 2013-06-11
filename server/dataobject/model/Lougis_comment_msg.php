<?php
/**
 * Table Definition for lougis.comment_msg
 */
require_once 'DB/DataObject.php';

class Lougis_comment_msg extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.comment_msg';              // table name
    public $id;                              // int4(4)  not_null default_nextval%28cms_comment_id_seq%29 primary_key
    public $lang_id;                         // bpchar(-1)  not_null
    public $title;                           // varchar(-1)  
    public $msg;                             // text(-1)  not_null
    public $parent_id;                       // int4(4)  
    public $date_created;                    // timestamptz(8)  not_null default_now%28%29
    public $hidden;                          // bool(1)  not_null default_false
    public $hidden_by;                       // int4(4)  
    public $likes;                           // int4(4)  default_0
    public $dislikes;                        // int4(4)  default_0
    public $topic_id;                        // int4(4)  not_null
    public $user_id;                         // int4(4)  not_null
    public $date_updated;                    // timestamptz(8)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_comment_msg',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
	
	public static function getAllForTopic( $TopicId ) {
	    
	    $Comments = array();
	    $Cm = new \Lougis_comment_msg();
	    $Cm->topic_id = $TopicId;
	    $Cm->whereAdd('parent_id IS NULL');
	    $Cm->orderBy('date_created ASC');
	    $Cm->find();
	    while( $Cm->fetch() ) {
		    $Cm->loadReplys();
		    $Comments[] = clone($Cm);
	    }
	    return $Comments;
	    
    }
    
    public function loadReplys() {
	    
	    $this->replys = array();
	    $Rep = new \Lougis_comment_msg();
	    $Rep->parent_id = $this->id;
	    $Rep->orderBy('date_created ASC');
	    $Rep->find();
	    while( $Rep->fetch() ) {
		    $this->replys[] = clone($Rep);
	    }
	    return true;
	    
    }
	
	public function getUsername() {
	
		$User = new \Lougis_user($this->user_id);
		return $User->getFullname();
	}
    
    /*public static function getRules() {
	    
	    $Rules = "<h2>Ympäristö Nyt verkkokeskustelun säännöt</h2>
<p>Verkkokeskusteluun tulevat viestit tarkastetaan. Ylläpito voi lyhentää ja muokata kirjoituksia.</p>
<p>Kirjoittaja on juridisessa vastuussa viestinsä sisällöstä. Sisällöltään sopimattomat viestit tai mainokset poistetaan keskusteluista. Muista hyvät tavat, älä huuda!/HUUDA tai kiroile.</p>";

            return $Rules;
	    
    }*/
}
