<?php
/**
 * Table Definition for lougis.session
 */
require_once 'DB/DataObject.php';

class Lougis_session extends \Lougis\DB_Session_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.session';                  // table name
    public $session_id;                      // varchar(-1)  not_null primary_key
    public $lifetime;                        // timestamptz(8)  
    public $created;                         // timestamptz(8)  not_null
    public $updated;                         // timestamptz(8)  not_null
    public $ended;                           // timestamptz(8)  
    public $ip;                              // varchar(-1)  
    public $hostname;                        // varchar(-1)  
    public $user_agent;                      // varchar(-1)  
    public $session_data;                    // varchar(-1)  
    public $user_id;                         // int4(4)  
    public $admin_session;                   // bool(1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_session',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public $id;
	public $_cookie_name;
	public $_session_max_lifetime;
	
	function __construct() {
	
		$this->_cookie_name = ( strpos($_SERVER['HTTP_HOST'], 'dev.') !== false ) ? 'lougis_aluetietopalvelu_dev' : 'lougis_aluetietopalvelu';
		$this->_session_max_lifetime = 60*60*24;
	    parent::__construct();
	    $this->id = $this->session_id;
	}
	
	public function isLogged() {
	    if ( !empty($this->user_id) && empty($this->ended) ) return true;
	    return false;
	}
	
	public function isAdminLogged() {
	    if ( !empty($this->user_id) && empty($this->ended) && $admin_session ) return true;
	    return false;
	}
	
	public function isAdmin() {
		if ( !empty($this->user_id) && empty($this->ended) && $this->admin_session ) return true;
		return false;
	}
	
	public function loginUser( $userId, $admin_session = false ) {
	
		$this->user_id = $userId;
		$_SESSION['user_id'] = $this->user_id;
		if( $admin_session == true) $this->admin_session = true;
		else $this->admin_session = false;
		if(!$this->save()) return false;
		devlog($this, "e_user");
		return true;
	
	}
	
	public function logoutUser( ) {
	
		$_SESSION['user_id'] = null;
		$this->ended = date(DATE_W3C);
		session_destroy();
		return $this->save();
	
	}
    
}
