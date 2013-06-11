<?php
/**
 * Table Definition for lougis.language
 */
require_once 'DB/DataObject.php';

class Lougis_language extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.language';                 // table name
    public $id;                              // bpchar(-1)  not_null primary_key
    public $name_local;                      // varchar(-1)  not_null
    public $name_english;                    // varchar(-1)  
    public $locale;                          // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_language',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

	public static function getLanguageName($langId) {
        $Lang = new Lougis_language($langId);
        return $Lang->name;
    }
	
	public static function getLanguages() {
        $lan = new Lougis_language();
        $lan->orderBy('id');
        $lan->find();
        $result = array();

        while($lan->fetch()) {
            $result[] = Array($lan->id, $lan->name);
        }
        return $result;
    }
	
	public static function getLanguageStore() {
        $store['xtype'] = 'arraystore';
        $store['fields'] = array("value", "name");
        $store['data'] = Lougis_language::getLanguages();
        return $store;
    }
}
