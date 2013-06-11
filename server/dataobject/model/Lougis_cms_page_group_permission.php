<?php
/**
 * Table Definition for lougis.cms_page_group_permission
 */
require_once 'DB/DataObject.php';

class Lougis_cms_page_group_permission extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.cms_page_group_permission';    // table name
    public $group_id;                        // int4(4)  not_null primary_key multiple_key
    public $page_id;                         // int4(4)  not_null primary_key multiple_key

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_cms_page_group_permission',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
