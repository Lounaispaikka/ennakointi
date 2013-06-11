<?php
/**
 * Table Definition for lougis.ui_translate_key
 */
require_once 'DB/DataObject.php';

class Lougis_ui_translate_key extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.ui_translate_key';         // table name
    public $id;                              // int4(4)  not_null unique_key multiple_key primary_key
    public $site_id;                         // varchar(-1)  not_null
    public $lang_id;                         // varchar(-1)  not_null unique_key multiple_key
    public $text;                            // varchar(-1)  not_null unique_key multiple_key

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_ui_translate_key',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
	
	public $translations;
    
    public function loadTranslations() {
    	
    	$Trs = array();
    	$Tr = new Lougis_ui_translate_text();
    	$Tr->key_id = $this->id;
    	$Tr->orderBy('lang_id');
    	$Tr->find();
    	while( $Tr->fetch() ) {
    		$Trs[$Tr->lang_id] = clone($Tr);
    	}
    	$this->translations = $Trs;
    	return true;
    	
    }
    
    public function getTranslations( $Lid = null ) {
    	
    	if ( !is_array($this->translations) ) $this->loadTranslations();
    	if ( !empty($Lid) ) {
    		if ( !empty($this->translations[$Lid]) ) return $this->translations[$Lid];
    		return null;
    	} 
    	return $this->translations;
    	
    }
    
    public function clearTranslations() {
    	
    	$Tr = new Lougis_ui_translate_text();
    	$Tr->key_id = $this->id;
    	$Tr->delete();
    	$this->translations = null;
    	return true;
    	
    }
}
