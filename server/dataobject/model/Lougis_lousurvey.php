<?php
/**
 * Table Definition for lougis.lousurvey
 */
require_once 'DB/DataObject.php';

class Lougis_lousurvey extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.lousurvey';                // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.lousurvey_id_seq%29 primary_key
    public $title;                           // varchar(-1)  not_null
    public $description;                     // varchar(-1)  
    public $created_by;                      // int4(4)  not_null
    public $start_date;                      // timestamptz(8)  
    public $end_date;                        // timestamptz(8)  
    public $form_ext;                        // varchar(-1)  
    public $form_dform;                      // varchar(-1)  
    public $site_id;                         // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_lousurvey',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
