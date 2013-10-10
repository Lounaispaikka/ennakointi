<?php
/**
 * Table Definition for lougis.user
 */
class Lougis_user extends \Lougis\DB_DataObject_Wrapper
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.user';                     // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.user_id_seq%29 unique_key primary_key
    public $firstname;                       // varchar(-1)  not_null
    public $lastname;                        // varchar(-1)  not_null
    public $email;                           // varchar(-1)  not_null
    public $date_created;                    // timestamptz(8)  not_null
    public $password;                        // varchar(-1)  
    public $phone;                           // varchar(-1)  
    public $organization;                    // varchar(-1)  
    public $phorum_user_id;                  // int4(4)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_user',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE

    public function getBasicInfoArray() {
        return array(
            "id" => intval($this->id),
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "email" => $this->email,
            "phone" => $this->phone,
            "organization" => $this->organization
        );
    }
	/*
    public function setFrom($data) {
        if(!empty($data['password'])) $data['password'] = self::hashPasswd($data['password']);
        parent::setFrom($data);
    }
	*/
	
	public function setPassword( $cleartext ) {
	
		$this->password = self::hashPasswd($cleartext);
		if ( $this->save() ) return true;
		return false;
	
	}
	
	public function isLogged() {
	
		if ( isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->id ) return true;
		return false;
	
	}
	
	/*
	public function isAdminLogged() {
	
		if ( isset($_SESSION['user_id']) && $_SESSION['user_id'] == $this->id && isset($_SESSION['admin_login']) && $_SESSION['admin_login'] === true ) return true;
		return false;
	
	}*/
	
	public function hasPermissionToPages( $user_id ) {
		$user_id = (int)$user_id;
		$permitted_pages = array();
		$UserPerms = new \Lougis_group_user();
		$sql = "select group_user.user_id as user_id, group_user.group_id as group_id, group_permission.page_id as page_id
				from lougis.group_user
				inner join lougis.group_permission
				on lougis.group_permission.group_id = lougis.group_user.group_id
				where group_user.user_id = ".$user_id.";";
		$UserPerms->query($sql);
		while($UserPerms->fetch()) {
            $permitted_pages[] = $UserPerms->page_id;
        }
		return $permitted_pages;
	}
	
    public function getFullname() {
    	
    	return $this->firstname.' '.$this->lastname;
    	
    }
	public function getPhorumUserId() {
    	
    	return $this->phorum_user_id;
    	
    }
	
    public static function hashPasswd($cleartext) {
        return md5($cleartext. LOUGIS_PASSWORD_SALT);
    }
    
    
	public static function getByEmail( $email ) {
		
		$Usr = new \Lougis_user();
		$Usr->get('email', $email);
		return $Usr;
		
		
	}
	

}
