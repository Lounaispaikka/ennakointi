<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

class Group extends \Lougis\abstracts\Frontend {

    public function getGroupsWithUsers() {
        $groups = array();
        $Group = new \Lougis_group();
        $Group->orderBy("name");
        $Group->find();

        while($Group->fetch()) {
            $groups[] = $Group->getBasicInfoWithUsers();
        }
        return $groups;
	}
	
	public function getGroupByPageId($page_id) {
		$ryhma = array();
		$Group = new \Lougis_group();
		$Group->page_id = $page_id;
		$Group->find();
		while($Group->fetch()) {
			$ryhma[] = $Group->getBasicInfoWithUsers();
		}
		return $ryhma;
	}
	
    public function saveGroup($groupData) {
    
        $id = ($groupData['id'] == 0)? null: $groupData['id'];
       
		unset($groupData['id']);
		$page_id = 0;
		$page_id = $groupData['page_id'];
		//unset($groupData['page_id']);
		devlog($groupData, "ennakointi_group");
		//devlog($page_id);
        $Group = new \Lougis_group($id);
        $Group->setFrom($groupData);
		if ( $Group->parent_id == 0 ) $Group->parent_id = null;
        if(empty($Group->date_created)) $Group->date_created = date(DATE_W3C);
        if(empty($Group->created_by)) $Group->created_by = 164;
        if ( !$Group->save() ) {
			devlog($Group, "ennakointi_group");
        	return false;
        }
        
		//$users = json_decode($groupData['users'], true); //käytä tätä, jos lougis-cms
        
		$users = $groupData['users']; //käytä tätä jos frontend-cms
        $GUser = new \Lougis_group_user();
        $GUser->group_id = $Group->id;
        $GUser->delete();
	
        foreach($users as $user) {
            devlog($user, "ennakointi_group");
			$GUser = new \Lougis_group_user();
            $GUser->group_id = $Group->id;
            $GUser->date_added = date(DATE_W3C);
            //$GUser->user_id = $user['user_id']; //lougis-cms
			$GUser->user_id = $user; //frontend-cms
            $GUser->group_admin = $user['group_admin'];
            if ( !$GUser->save() ) {
				return false;
	        }
			
        }
		
		//lisää oikeudet
		$Pg = new \Lougis_cms_page($page_id);
		$child_pages = array();
		$child_pages = $Pg->getEveryChildren();
		devlog(count($child_pages), "ennakointi_group");
		$gp_parent = new \Lougis_group_permission();
		$gp_parent->page_id = $page_id;
		$gp_parent->group_id = $Group->id;
		$gp_parent->find();
		if(!$gp_parent->fetch()) { $gp_parent->insert(); }
		//lisää oikeudet alasivuihin
		foreach($child_pages as $child) {
			$gp_child = new \Lougis_group_permission();
			$gp_child->page_id = $child->id;
			$gp_child->group_id = $Group->id;
			$gp_child->find();
			if(!$gp_child->fetch()) { $gp_child->insert(); }
		}

        return $Group->id;
    	
    	
        
    }

    public function deleteGroup($id) {
        $User = new \Lougis_group($id);
        $User->delete();
    }
}
?>