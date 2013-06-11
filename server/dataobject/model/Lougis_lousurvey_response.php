<?php
/**
 * Table Definition for lougis.lousurvey_response
 */
require_once 'DB/DataObject.php';

class Lougis_lousurvey_response extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.lousurvey_response';       // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.lousurvey_response_id_seq%29 primary_key
    public $lousurvey_id;                    // int4(4)  not_null
    public $user_id;                         // int4(4)  
    public $firstname;                       // varchar(-1)  
    public $lastname;                        // varchar(-1)  
    public $email;                           // varchar(-1)  
    public $organization;                    // varchar(-1)  
    public $response_dform;                  // varchar(-1)  
    public $sent_date;                       // timestamp(8)  
    public $response_ext;                    // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_lousurvey_response',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
