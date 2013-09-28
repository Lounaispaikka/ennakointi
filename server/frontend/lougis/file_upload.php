<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');
require_once(PATH_PEAR_HTTP.'Upload.php');
use PEAR;

class File_upload extends \HTTP_Upload {

	public function __construct() {
	
	}
	
	public function uploadFile() {
		
		//global $Site, $User;
		
		$upload = new \HTTP_Upload("en");
		$file = $upload->getFiles("f");
		devlog($file, "e_fileupload");
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.');
			//Sallitut tiedostotyypit
			$exts = array("pdf","doc","docx","csv","xls","xlsx","txt","ppt","pptx","odt","ods","odp","gif","jpg","png");
			//Tiedosto
			if (PEAR::isError($file)) { throw new \Exception($file->getMessage()); }
			if ($file->isValid()) {
				$file->setName('uniq'); //tiedoston uudelleen nimeäminen	
				$file->setValidExtensions($exts, 'accept'); //hyväksyy vain sallitut tiedostopäätteet
				if(!is_dir(PATH_UPFILES) && (!mkdir(PATH_UPFILES))) {
					throw new \Exception("Tiedoston k&auml;sittelyss&auml; tapahtui virhe: hakemiston luominen ep&auml;onnistui.");
				}
				$moved = $file->moveTo(PATH_UPFILES);
				if (!PEAR::isError($moved)) {
					$msg = 'Tiedosto ladattiin onnistuneesti.'.PATH_UPFILES. $file->getProp('name');
				} else {
					throw new \Exception($moved->getMessage());
				}
			} elseif ($file->isMissing()) {
				throw new \Exception("No file was provided."); 
			} elseif ($file->isError()) {
				throw new \Exception($file->errorMsg()); 
			}
			
			
			 //Sivun luonti
			
			//hae oikea parent page
			$parent = new \Lougis_cms_page();
			$parent->parent_id = $_REQUEST['parent_id'];
			$parent->page_type = "teema_tiedostot";
			$parent->find();
			$parent->fetch();
			
			//luo cms_page
			$page = new \Lougis_cms_page();
			$page->site_id = 'everkosto';
			$page->lang_id = 'fi';
			$page->title = $file->getProp('real');
			$page->nav_name = $file->getProp('real');
			$page->created_by = $_SESSION['user_id'];
			$page->created_date = date(DATE_W3C);
			$page->published = true;
			$page->template = 'teema_tiedostot.php';
			$page->visible = true;
			$page->restricted_access = true;
			$page->page_type = 'file';
			$page->parent_id = $parent->id;
			//if($_REQUEST['news']['parent_id'] > 0) $page->parent_id = $_REQUEST['news']['parent_id'];
			/*if($_REQUEST['news']['page_id'] < 1)*/ $page->setNextSeqNum(); //jos uusi niin annettaan seqnum
			if ( !$page->save() ) throw new \Exception("Sivun tallennus epäonnistui.");
			$PgArray = $page->toArray();
			
			//oikeudet päästä sivulle
			
			if($page->page_type == "file") {
				$pa = $page->getParentIdArray(); //sivun sukupolvet id arrayna
				$group = new \Lougis_group();
				$group->page_id = $pa[2]; //arrayn indeksissä 2 on oikea parent, eli tämän toimialan id.
				$group->find();
				$group->fetch();
				$permission = new \Lougis_group_permission();
				$permission->page_id = $page->id;
				$permission->group_id = $group->id;
				$permission->find();
				if(!$permission->fetch()) { $permission->insert(); }
			}
			
			//Tietokanta
			$File = new \Lougis_file();
			
			$File->original_name = $file->getProp('real');
			$File->file_name = $file->getProp('name');
			$File->form_name = $file->getProp('form_name');
			$File->file_ext =$file->getProp('ext');
			$File->file_size = $file->getProp('size');
			$File->description =  $_REQUEST['description'];
			//$File->created_date = date(DATE_W3C);
			$File->created_by = $_SESSION['user_id'];
			$File->page_id = $page->id;
			$msg .= "\n ja tiedot tallennettu tietokantaan.";
			if ( !$File->save() ) throw new \Exception('Tiedoston tallennus tietokantaa ep&auml;onnistui: '.$File->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => $msg
			);
		}
		catch(\Exception $e) {
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
		}
		
		echo json_encode($res);
	
	}
	
	public function deleteFile() {
		global $Site;
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.');
			$file_id = (int)$_POST['file_id'];
			$page_id = (int)$_POST['page_id'];
			
			if ($file_id == null && $page_id == null) throw new \Exception('Tiedoston poistaminen ep&auml;onnistui.');
			
			if ( $page_id != null) {
				$page = new \Lougis_cms_page($page_id);
				if ( !$page->delete() ) throw new \Exception('Tiedostosivun poistaminen epäonnistui: '.$page->_lastError);
			}
			else {
				$file = new \Lougis_file($file_id);
				if ( !$file->delete() ) throw new \Exception('Tiedoston poistaminen epäonnistui: '.$file->_lastError);
			}
			//poista tiedosto
			
			$res = array(
					"success" => true,
					"msg" => "Tiedosto poistettu onnistuneesti"
				);
		}
		catch(\Exception $e) {
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
		}
		echo json_encode($res);
		//poista cms_page (samalla poistuu file tietokannasta)
	}
}
?>