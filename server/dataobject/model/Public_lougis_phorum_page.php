<?php
/**
 * Table Definition for public.lougis_phorum_page
 */
require_once 'DB/DataObject.php';

class Public_lougis_phorum_page extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'public.lougis_phorum_page';       // table name
    public $page_id;                         // int4(4)  not_null primary_key multiple_key
    public $forum_id;                        // int4(4)  not_null
    public $thread_id;                       // int4(4)  not_null

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Public_lougis_phorum_page',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

	
}	