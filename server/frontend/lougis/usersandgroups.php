<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');
require_once(PATH_SERVER.'utility/UsersAndGroups/User.php');
require_once(PATH_SERVER.'utility/UsersAndGroups/Group.php');

class Usersandgroups extends \Lougis\abstracts\Frontend {

    public function jsonListUsers() {
        $user = new \Lougis\utility\User();
        $response = array(
            "success" => true,
            "users" => $user->getPublicUsers()
        );
        $this->jsonOut($user->getPublicUsers());
	}
	
	public function jsonListUsersOfGroup() {
        $user = new \Lougis\utility\User();
        $response = array(
            "success" => true,
            "users" => $user->getUsersOfGroup($_REQUEST['page_id'])
        );
        $this->jsonOut($user->getUsersOfGroup($_REQUEST['page_id']));
	}
	
	public function jsonListRestOfUsers() {
        $user = new \Lougis\utility\User();
        $response = array(
            "success" => true,
            "users" => $user->getRestOfUsers($_REQUEST['page_id'])
        );
        $this->jsonOut($user->getRestOfUsers($_REQUEST['page_id']));
	}

    public function jsonListGroupsWithUsers() {
        $group = new \Lougis\utility\Group();

        $response = array(
            "success" => true,
            "groups" => $group->getGroupsWithUsers()
        );

        $this->jsonOut($response);
    }
	
	public function jsonGroupByPageId() {
		devlog($_REQUEST['page_id']);
        $group = new \Lougis\utility\Group();
		//$group = new \Lougis_group($_REQUEST['page_id']);
        $response = array(
            "success" => true,
			//"id" => $group->id
            "group" => $group->getGroupByPageId($_REQUEST['page_id'])

        );
        $this->jsonOut($response);
    }

    public function editUser() {
        $data = file_get_contents("php://input");
        $userData = json_decode($data, true);
        
        $user = new \Lougis\utility\User();
        $id = $user->saveUser($userData);
        $response = array(
            "success" => true,
            "userId" => $id
        );
        $this->jsonOut($response);
    }

    public function editGroup() {
		
		$groupData = $_REQUEST;
		
        $group = new \Lougis\utility\Group();
        $group->saveGroup($groupData);
        $response = array(
            "success" => true
        );
        $this->jsonOut($response);
    }
	
	
	public function editToimialaGroup() {

		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.');
			//user can't remove herself from the users
			$user_group = explode(",", $_REQUEST['admin-group']);
			if( !in_array($_SESSION['user_id'], $user_group) )throw new \Exception('Et voi poistaa itseäsi käyttäjäryhmästä.');
			
			
			$pg = new \Lougis_cms_page();
			$pg->id = $_REQUEST['page_id'];
			$pg->find();
			$pg->fetch();
			
			$grp = new \Lougis_group();
			$grp->page_id = $_REQUEST['page_id'];
			$grp->find();
			$grp->fetch();
			/*
			if($grp < 0)
				$grp 
			
			
			
			*/
			
			//$user_group = explode(",", $_REQUEST['admin-group']);
			devlog($user_group, "e_grp");
			//$user_group = json_encode($user_group);
			
			//$groupData = $_REQUEST;
			$groupData['id'] = $grp->id;
			//$groupData['name'] = $grp->name;
			$groupData['name'] = ($grp->name == '')? $pg->title: $grp->name;
			$groupData['public_joining'] = $grp->public_joining;
			$groupData['description'] = $grp->description;
			$groupData['parent_id'] = $grp->parent_id;
			$groupData['is_admin'] = $grp->is_admin;
			$groupData['page_id'] = $_REQUEST['page_id'];
			$groupData['users'] = $user_group;
			$group = new \Lougis\utility\Group();
			
			$group->saveGroup($groupData);
			
			$response = array(
				"success" => true,
				"msg" => "Käyttäjien oikeudet tallennettu onnistuneesti!",
				"type" => "group"
			);
		
		} catch(\Exception $e) {
			
			$response = array(
				"success" => false,
				"msg" => "flases",//$e->getMessage(),
				"type" => "group"
			);
			
		}
		
        $this->jsonOut($response);
    }
    public function deleteUser() {
        $id = $_REQUEST['userId'];
        $user = new \Lougis\utility\User();
        $user->deleteUser($id);
        $response = array(
            "success" => false,
			"msg" => "Käyttäjä poistettu."
        );
        $this->jsonOut($response);
    }

    public function deleteGroup() {
        $id = $_REQUEST['groupId'];
        $user = new \Lougis\utility\Group();
        $user->deleteGroup($id);
        $response = array(
            "success" => true
        );
        $this->jsonOut($response);
    }

    public function jsonLoggedUserInfo() {
        $user = new \Lougis\utility\User();
        $info = $user->getLoggedUserInfo();
        $this->jsonOut($info);
    }
    
    public function logoutUser() {
    
		session_destroy();
		unset($_SESSION['user_id']);
		unset($_SESSION['site_id']);
		unset($_SESSION['admin_login']);
		header('Location: /hallinta/?logout');
	
    }
    
	//public-sivun käyttäjät
	public function getName() {
		$user = new \Lougis_user($_REQUEST['user_id']);
		$name = $user->getFullName();
		$this->jsonOut($name);
	}
	
	//public-sivun käyttäjät
	public function getPhorumUserId() {
		$user = new \Lougis_user($_REQUEST['user_id']);
		$phorum_user_id = $user->getPhorumUserId();
		$this->jsonOut($phorum_user_id);
	}
}
?>