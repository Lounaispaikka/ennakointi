<?php
/**
 * Table Definition for lougis.ui_translate_text
 */
require_once 'DB/DataObject.php';

class Lougis_ui_translate_text extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.ui_translate_text';        // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.ui_translate_text_id_seq%29 primary_key
    public $key_id;                          // int4(4)  not_null
    public $key_lang;                        // varchar(-1)  not_null
    public $key_text;                        // varchar(-1)  not_null
    public $translation_lang;                // varchar(-1)  not_null
    public $translation_text;                // varchar(-1)  not_null
    public $source;                          // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_ui_translate_text',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
