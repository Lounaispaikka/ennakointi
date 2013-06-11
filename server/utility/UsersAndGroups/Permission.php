<?php
namespace Lougis\utility;

require_once(PATH_SERVER.'abstracts/Utility.php');

class Permission extends \Lougis\abstracts\Utility {

	public function groupAuthorized($page_id) {
		
		$UGroups = new \Lougis_group_user();
		$UGroups->query(	'SELECT gu.group_id, gp.page_id
							FROM lougis.group_user AS gu
							JOIN lougis.group_permission AS gp
							ON gp.group_id = gu.group_id
							WHERE gu.user_id = '.$_SESSION['user_id'].'
							AND gp.page_id = '.$page_id.'
						;');
		if($UGroups->fetch()) return true;
		else return false;
		
	}
	
	public function setPermission($page_id, $group_id) {
	
		$GroupPerm = new \Lougis_group_permission();
		$GroupPerm->page_id = $page_id;
		$GroupPerm->group_id = $group_id;
		/*if( !$Group->save() ) {
			return false;
		}
		else return true;*/
		if($GroupPerm->insert()) {
			return true;
		}
		else return false;
	}
}
?>