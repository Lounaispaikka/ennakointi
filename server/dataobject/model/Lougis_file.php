<?php
/**
 * Table Definition for lougis.file
 */
require_once 'DB/DataObject.php';

class Lougis_file extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.file';                     // table name
    public $id;                              // int4(4)  not_null primary_key
    public $original_name;                   // varchar(-1)  not_null
    public $file_name;                       // varchar(-1)  not_null
    public $form_name;                       // varchar(-1)  
    public $file_ext;                        // varchar(-1)  
    public $file_size;                       // int4(4)  not_null
    public $created_by;                      // int4(4)  not_null
    public $description;                     // varchar(-1)  
    public $created_date;                    // timestamptz(8)  not_null default_now%28%29
    public $page_id;                         // int4(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_file',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
