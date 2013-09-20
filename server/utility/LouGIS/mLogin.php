<?php /*modal-login 
*
* mLogin.php
*/

require_once('../server/config.php');
	
/*global $Site, $Session;

$Session = new \Lougis_session();
$_SESSION['site_id'] = 'everkosto';
$Site = new \Lougis_site( $_SESSION['site_id'] );
*/
$errMsg = "";
if ( isset($_GET['logout']) ) $errMsg = "Olet kirjautunut ulos onnistuneesti.";
 
//$redir = ( !empty($_REQUEST['redir']) ) ? $_REQUEST['redir'] : "admin";
$redir = ( !isset($_REQUEST['redir']) ) ? "public" : "admin";
require_once(PATH_SERVER.'utility/UsersAndGroups/User.php');
if(isset($_POST['email']) && isset($_POST['password'])) {
    $user = new \Lougis\utility\User();
    $success = $user->login($_POST['email'], $_POST['password']);
    if(!$success) {
    	$errMsg = "Kirjautuminen ep&auml;onnistui, <br/ >ole hyv&auml; ja yrit&auml; uudelleen.";
    } else {
    	switch($redir) {
			case 'admin':
				$_SESSION['admin_login'] = true;
				header('Location: /hallinta/');
				break;
    		default:
    			$_SESSION['admin_login'] = false;
				break;
    	}
    }
}
?>