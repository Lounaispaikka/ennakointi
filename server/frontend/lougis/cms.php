<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class CMS extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function getPageJson() {
		
		try {
		
			$PageId = $_POST['page_id'];
			if ( empty($PageId) ) throw new \Exception("Page id required");
		
			$Pg = $this->_getPageInfo( $PageId );
			$Co = $this->_getPageContent($Pg->id, $Pg->lang_id);
			if ( empty($Pg->created_date) )  throw new \Exception("Page not found");
			$Pg->published = ( $Pg->published == 't' ) ? true : false;
			$Pg->visible = ( $Pg->visible == 't' ) ? true : false;
			$Pga = $Pg->toArray("cms_page[%s]");
			//lisätään ennakointiin
			$Pgax = $Pg->toArray("%s");
			$Pga["cms_page[page_id]"] = $Pg->id;
			$Pga["page_id"] = $Pg->id;
			$res = array(
				"success" => true,
				"page" => $Pga,
				"pageEnnakointi" => $Pgax,
				"content" => $Co->content,
				"content_column" => $Co->content_column
			);
			
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	private function _getPageInfo( $PageId ) {
		
		if ( empty($PageId) ) throw new Exception("Page id required");
		$Pg = new \Lougis_cms_page( $PageId );
		return $Pg;
		
	}
	
	private function _getPageContent( $PageId, $LangId ) {
	
		if ( empty($PageId) ) throw new Exception("Page id required");
		$Co = new \Lougis_cms_content();
		$Co->page_id = $PageId;
		$Co->lang_id = $LangId;
		$Co->orderBy('date_created DESC');
		$Co->limit(1);
		$Co->find();
		$Co->fetch();
		return $Co;
		
	}
	
	public function savePageContent() {
		
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.'); 
			
			devlog($_POST, 'saveteema');
			
			$Pg = $this->_getPageInfo( $_POST['page_id'] );
			$Co = $this->_getPageContent( $Pg->id, $Pg->lang_id );
			$Co->content = pg_escape_string(trim($_POST['new_content']));
			if ( empty($Co->content) ) $Co->content = 'NULL';
			$Co->content_column = pg_escape_string(trim($_POST['new_column']));
			if ( empty($Co->content_column) ) $Co->content_column = 'NULL';
			if ( empty($Co->date_created) ) {
				$Co->date_created = date(DATE_W3C);
				$Co->published = true;
				$Co->created_by = $_SESSION['user_id'];
			}
			if ( !$Co->save() ) throw new \Exception('Sivun sisällön tallentaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Sivun sisältö tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function savePageInfo() {
		
		try {
		
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.'); 
			if ( $_POST['cms_page']['title'] == '' ) throw new \Exception('Anna jotain.'); 
			$Pg = new \Lougis_cms_page($_POST['cms_page']['page_id']);
			if ( empty($Pg->created_date) ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: Sivua ei voitu ladata!');
			if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: Virheellinen sivusto!');
			
			$Pg->setFrom($_POST['cms_page']);
			devlog($_POST['cms_page']);
			//$Pg->url_name = $_POST['cms_page']['parent_id'].'_'.$_POST['cms_page']['page_id']);
			if ( !isset($_POST['cms_page']['parent_id']) || empty($_POST['cms_page']['parent_id']) ) $Pg->parent_id = "NULL";
			if ( !isset($_POST['cms_page']['published']) ) $Pg->published = false;
			if ( !isset($_POST['cms_page']['visible']) ) $Pg->visible = false;
			if ( !isset($_POST['cms_page']['restricted_access']) ) $Pg->restricted_access = true;
			//devlog($Pg, 'pyry');
			//$Pg->created_date = date(DATE_W3C);
			//$Pg->created_by = $_SESSION['user_id'];
			//$Pg->site_id = $_SESSION['site_id'];
			//$Pg->lang_id = 'fi';
			devlog($Pg);
			if ( !$Pg->save() ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);

			$res = array(
				"success" => true,
				"msg" => "Sivun tiedot tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function saveTeema() {
		
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.'); 
		
			devlog($_POST, 'saveteema');
			$Pg = new \Lougis_cms_page($_POST['page_id']);
			$Pg->title = $_POST['title'];
			$Pg->nav_name = $_POST['title'];
			if ( !$Pg->save() ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
			
			$Co = new \Lougis_cms_content();
			$Co->page_id = $_POST['page_id'];
			//$Co->content = $_POST['content'];
			$Co->find();
			$Co->fetch();
			$Cos = new \Lougis_cms_content($Co->id);
			$Cos->content = $_POST['content'];
			devlog($Co, 'saveteema');
			devlog($Cos, 'saveteema');
			if ( !$Cos->save() ) throw new \Exception('Sivun sisällön tallentaminen epäonnistui: '.$Cos->_lastError);
			/*
			$Pg = new \Lougis_cms_page($_POST['cms_page']['page_id']);
			if ( empty($Pg->created_date) ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: Sivua ei voitu ladata!');
			if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: Virheellinen sivusto!');
			
			$Pg->setFrom($_POST['cms_page']);
			devlog($_POST['cms_page']);
			//$Pg->url_name = $_POST['cms_page']['parent_id'].'_'.$_POST['cms_page']['page_id']);
			if ( !isset($_POST['cms_page']['parent_id']) || empty($_POST['cms_page']['parent_id']) ) $Pg->parent_id = "NULL";
			if ( !isset($_POST['cms_page']['published']) ) $Pg->published = false;
			if ( !isset($_POST['cms_page']['visible']) ) $Pg->visible = false;
			//devlog($Pg, 'pyry');
			//$Pg->created_date = date(DATE_W3C);
			//$Pg->created_by = $_SESSION['user_id'];
			//$Pg->site_id = $_SESSION['site_id'];
			//$Pg->lang_id = 'fi';
			devlog($Pg);
			if ( !$Pg->save() ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
*/
			$res = array(
				"success" => true,
				"msg" => "Sivun tiedot tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			//devlog($Pg, 'saveteema');
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function createNewTeema() {
		
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.'); 
			//sivu cms_page
			$Pg = new \Lougis_cms_page();
			$Pg->setFrom($_POST['cms_page']);
			
			//$Pg->url_name($_POST['cms_page']['parent_id'].'_'.$_POST['cms_page']['page_id']);
			$Pg->nav_name = $_POST['cms_page']['title'];
			$Pg->published = "t";
			$Pg->visible = "t";
			$Pg->restricted_access = "t";
			$Pg->page_type = "teema";
			$Pg->template = "teema.php";
			$Pg->created_date = date(DATE_W3C);
			$Pg->created_by = $_SESSION['user_id'];
			$Pg->site_id = $_SESSION['site_id'];			
			$Pg->lang_id = 'fi';
			$Pg->setNextSeqNum();
			
			if ( !$Pg->save() ) throw new \Exception('Teeman tallentaminen epäonnistui: '.$Pg->_lastError);	
			
			devlog($Pg->id);
			
			//sisältö cms_content
			$Co = $this->_getPageContent( $Pg->id, $Pg->lang_id );
			$Co->content = pg_escape_string($_POST['cms_page']['content']);
			if ( empty($Co->date_created) ) {
				$Co->date_created = date(DATE_W3C);
				$Co->published = true;
				$Co->created_by = $_SESSION['user_id'];
			}
			if ( !$Co->save() ) throw new \Exception('Sivun oletussisällön tallentaminen epäonnistui: '.$Co->_lastError);
			
			
			//lisätään ennakointiteemojen vakiosivut
				
				//linkit
				$Pg_linkit = new \Lougis_cms_page();
				$Pg_linkit->title = 'Linkit';
				$Pg_linkit->parent_id = $Pg->id;
				$Pg_linkit->nav_name = 'Linkit';
				$Pg_linkit->created_date = date(DATE_W3C);
				$Pg_linkit->created_by = $_SESSION['user_id'];
				$Pg_linkit->site_id = $_SESSION['site_id'];
				$Pg_linkit->page_type = "teema_linkit";
				$Pg_linkit->template = "teema_linkit.php";
				$Pg_linkit->published = "t";
				$Pg_linkit->visible = "t";
				$Pg_linkit->restricted_access = "t";
				$Pg_linkit->lang_id = 'fi';
				$Pg_linkit->setNextSeqNum();
				if ( !$Pg_linkit->save() ) throw new \Exception('Linkit-sivun tietojen tallentaminen epäonnistui: '.$Pg_linkit->_lastError);
				
				$Co_linkit = $this->_getPageContent( $Pg_linkit->id, $Pg_linkit->lang_id );
				if ( empty($Co_linkit->date_created) ) {
					$Co_linkit->date_created = date(DATE_W3C);
					$Co_linkit->published = true;
					$Co_linkit->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_linkit->save() ) throw new \Exception('Linkit-sivun oletussisällön tallentaminen epäonnistui: '.$Co_linkit->_lastError);
				
				
				//tiedostot
				$Pg_tiedostot = new \Lougis_cms_page();
				$Pg_tiedostot->title = 'Tiedostot';
				$Pg_tiedostot->parent_id = $Pg->id;
				$Pg_tiedostot->nav_name = 'Tiedostot';
				$Pg_tiedostot->created_date = date(DATE_W3C);
				$Pg_tiedostot->created_by = $_SESSION['user_id'];
				$Pg_tiedostot->site_id = $_SESSION['site_id'];
				$Pg_tiedostot->page_type = "teema_tiedostot";
				$Pg_tiedostot->template = "teema_tiedostot.php";
				$Pg_tiedostot->published = "t";
				$Pg_tiedostot->visible = "t";
				$Pg_tiedostot->restricted_access = "t";
				$Pg_tiedostot->lang_id = 'fi';
				$Pg_tiedostot->setNextSeqNum();
				if ( !$Pg_tiedostot->save() ) throw new \Exception('Tiedostot-sivun tietojen tallentaminen epäonnistui: '.$Pg_tiedostot->_lastError);
				
				$Co_tiedostot = $this->_getPageContent( $Pg_tiedostot->id, $Pg_tiedostot->lang_id );
				if ( empty($Co_tiedostot->date_created) ) {
					$Co_tiedostot->date_created = date(DATE_W3C);
					$Co_tiedostot->published = true;
					$Co_tiedostot->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_tiedostot->save() ) throw new \Exception('Tiedostot-sivun oletussisällön tallentaminen epäonnistui: '.$Co_tiedostot->_lastError);

				
				//tilastot
				$Pg_tilastot = new \Lougis_cms_page();
				$Pg_tilastot->title = 'Tilastot';
				$Pg_tilastot->nav_name = 'Tilastot';
				$Pg_tilastot->parent_id = $Pg->id;
				$Pg_tilastot->created_date = date(DATE_W3C);
				$Pg_tilastot->created_by = $_SESSION['user_id'];
				$Pg_tilastot->site_id = $_SESSION['site_id'];
				$Pg_tilastot->lang_id = 'fi';
				$Pg_tilastot->page_type = "teema_tilastot";
				$Pg_tilastot->template = "teema_tilastot.php";
				$Pg_tilastot->published = "t";
				$Pg_tilastot->visible = "t";
				$Pg_tilastot->restricted_access = "t";
				$Pg_tilastot->setNextSeqNum();
				if ( !$Pg_tilastot->save() ) throw new \Exception('Tilastot-sivun tietojen tallentaminen epäonnistui: '.$Pg_tilastot->_lastError);
				
				$Co_tilastot = $this->_getPageContent( $Pg_tilastot->id, $Pg_tilastot->lang_id );
				if ( empty($Co_tilastot->date_created) ) {
					$Co_tilastot->date_created = date(DATE_W3C);
					$Co_tilastot->published = true;
					$Co_tilastot->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_tilastot->save() ) throw new \Exception('Tilastot-sivun oletussisällön tallentaminen epäonnistui: '.$Co_tilastot->_lastError);
				
				//keskustelu
				$Pg_keskustelu = new \Lougis_cms_page();
				$Pg_keskustelu->title = 'Keskustelut';
				$Pg_keskustelu->nav_name = 'Keskustelut';
				$Pg_keskustelu->parent_id = $Pg->id;
				$Pg_keskustelu->created_date = date(DATE_W3C);
				$Pg_keskustelu->created_by = $_SESSION['user_id'];
				$Pg_keskustelu->site_id = $_SESSION['site_id'];
				$Pg_keskustelu->lang_id = 'fi';
				$Pg_keskustelu->page_type = "teema_keskustelut";
				$Pg_keskustelu->template = "teema_keskustelut.php";
				$Pg_keskustelu->published = "t";
				$Pg_keskustelu->visible = "t";
				$Pg_keskustelu->restricted_access = "t";
				$Pg_keskustelu->setNextSeqNum();
				if ( !$Pg_keskustelu->save() ) throw new \Exception('Keskustelu-sivun tietojen tallentaminen epäonnistui: '.$Pg_keskustelu->_lastError);
				
				$Co_keskustelu = $this->_getPageContent( $Pg_keskustelu->id, $Pg_keskustelu->lang_id );
				if ( empty($Co_keskustelu->date_created) ) {
					$Co_keskustelu->date_created = date(DATE_W3C);
					$Co_keskustelu->published = true;
					$Co_keskustelu->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_keskustelu->save() ) throw new \Exception('Keskustelu-sivun oletussisällön tallentaminen epäonnistui: '.$Co_keskustelu->_lastError);
				
				//uutiset
				$Pg_uutinen = new \Lougis_cms_page();
				$Pg_uutinen->title = 'Uutiset';
				$Pg_uutinen->nav_name = 'Uutiset';
				$Pg_uutinen->parent_id = $Pg->id;
				$Pg_uutinen->created_date = date(DATE_W3C);
				$Pg_uutinen->created_by = $_SESSION['user_id'];
				$Pg_uutinen->site_id = $_SESSION['site_id'];
				$Pg_uutinen->lang_id = 'fi';
				$Pg_uutinen->page_type = "teema_uutiset";
				$Pg_uutinen->template = "teema_uutiset.php";
				$Pg_uutinen->published = "t";
				$Pg_uutinen->visible = "t";
				$Pg_uutinen->restricted_access = "t";
				$Pg_uutinen->setNextSeqNum();
				if ( !$Pg_uutinen->save() ) throw new \Exception('Uutiset-sivun tietojen tallentaminen epäonnistui: '.$Pg_uutinen->_lastError);
				
				$Co_uutinen = $this->_getPageContent( $Pg_uutinen->id, $Pg_uutinen->lang_id );
				if ( empty($Co_uutinen->date_created) ) {
					$Co_uutinen->date_created = date(DATE_W3C);
					$Co_uutinen->published = true;
					$Co_uutinen->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_uutinen->save() ) throw new \Exception('Uutiset-sivun oletussisällön tallentaminen epäonnistui: '.$Co_uutinen->_lastError);
				
			//oikeudet
			$parent_parent = new \Lougis_cms_page();
			$parent_parent->id = $Pg->parent_id;
			$parent_parent->find();
			$parent_parent->fetch();
			devlog($parent_parent);
			
			$group = new \Lougis_group();
			$group->page_id = $parent_parent->id;
			$group->find();
			$group->fetch();
			devlog($group);
			
			$permission = new \Lougis_group_permission();
			$permission->page_id = $Pg->id;
			$permission->group_id = $group->id;
			$permission->find();
			if(!$permission->fetch()) { $permission->insert(); }
			
			$linkit_permission = new \Lougis_group_permission();
			$linkit_permission->page_id = $Pg_linkit->id;
			$linkit_permission->group_id = $group->id;
			$linkit_permission->find();
			if(!$linkit_permission->fetch()) { $linkit_permission->insert(); }
			
			$tied_permission = new \Lougis_group_permission();
			$tied_permission->page_id = $Pg_tiedostot->id;
			$tied_permission->group_id = $group->id;
			$tied_permission->find();
			if(!$tied_permission->fetch()) { $tied_permission->insert(); }
			
			$kesk_permission = new \Lougis_group_permission();
			$kesk_permission->page_id = $Pg_keskustelu->id;
			$kesk_permission->group_id = $group->id;
			$kesk_permission->find();
			if(!$kesk_permission->fetch()) { $kesk_permission->insert(); }
			
			$til_permission = new \Lougis_group_permission();
			$til_permission->page_id = $Pg_tilastot->id;
			$til_permission->group_id = $group->id;
			$til_permission->find();
			if(!$til_permission->fetch()) { $til_permission->insert(); }
			
			$uutinen_permission = new \Lougis_group_permission();
			$uutinen_permission->page_id = $Pg_uutinen->id;
			$uutinen_permission->group_id = $group->id;
			$uutinen_permission->find();
			if(!$uutinen_permission->fetch()) { $uutinen_permission->insert(); }
			
			
			$res = array(
				"success" => true,
				"msg" => "Uusi teema luotu",
				"page_id" => $Pg->id
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	
	}
	
	public function createNewPage() {
		
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.'); 
			//kommentit comment_topic
			/* $Topic = new \Lougis_comment_topic();
			$Topic->active = true;
			if ( !$Topic->save() ) throw new \Exception("Tekninen virhe. Ota yhteyttä sivuston ylläpitoon". $Topic->_lastError); */
		
			//sivu cms_page
			$Pg = new \Lougis_cms_page();
			$Pg->setFrom($_POST['cms_page']);
			
			$Pg->url_name($_POST['cms_page']['parent_id'].'_'.$_POST['cms_page']['page_id']);
			if ( !isset($_POST['cms_page']['published']) ) $Pg->published = false;
			if ( !isset($_POST['cms_page']['visible']) ) $Pg->visible = false;
			if ( !isset($_POST['cms_page']['restricted_access']) && $Pg->page_type != "tietopankki" ) $Pg->restricted_access = "t";
			$Pg->created_date = date(DATE_W3C);
			$Pg->created_by = $_SESSION['user_id'];
			$Pg->site_id = $_SESSION['site_id'];			
			$Pg->lang_id = 'fi';
			//$Pg->comments_id = $Topic->id;
			$Pg->setNextSeqNum();
			devlog($Pg, 'saveteema');
			devlog($_POST, 'saveteema');
			if ( !$Pg->save() ) throw new \Exception('Sivun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
			
			//oikeudet
			if($Pg->page_type == "teema_aineisto") {
				$parent_parent = new \Lougis_cms_page();
				$parent_parent->id = $Pg->parent_id;
				$parent_parent->find();
				$parent_parent->fetch();
				
				$group = new \Lougis_group();
				$group->page_id = $parent_parent->parent_id;
				$group->find();
				$group->fetch();
				devlog($group);
				
				$permission = new \Lougis_group_permission();
				$permission->page_id = $Pg->id;
				$permission->group_id = $group->id;
				$permission->find();
				if(!$permission->fetch()) { $permission->insert(); }
			}
			
			
			//sisältö cms_content
			//$Text = "<h1>{$Pg->title}</h1>";
			$Co = $this->_getPageContent( $Pg->id, $Pg->lang_id );
			//$Co->content = pg_escape_string($Text);
			if ( empty($Co->date_created) ) {
				$Co->date_created = date(DATE_W3C);
				$Co->published = true;
				$Co->created_by = $_SESSION['user_id'];
			}
			if ( !$Co->save() ) throw new \Exception('Sivun oletussisällön tallentaminen epäonnistui: '.$Co->_lastError);
			
			/*
			//lisätään ennakointiteemojen vakiosivut
			if ( $_POST['cms_page']['page_type'] == 'teema' ) {
				
				//linkit
				$Pg_linkit = new \Lougis_cms_page();
				$Pg_linkit->title = 'Linkit';
				$Pg_linkit->parent_id = $Pg->id;
				$Pg_linkit->nav_name = 'Linkit';
				$Pg_linkit->created_date = date(DATE_W3C);
				$Pg_linkit->created_by = $_SESSION['user_id'];
				$Pg_linkit->site_id = $_SESSION['site_id'];
				$Pg_linkit->lang_id = 'fi';
				//$Pg_linkit->comments_id = $Topic_linkit->id;
				$Pg_linkit->setNextSeqNum();
				if ( !$Pg_linkit->save() ) throw new \Exception('Linkit-sivun tietojen tallentaminen epäonnistui: '.$Pg_linkit->_lastError);
				
				$Linkit_teksti = "<h1>{$Pg_linkit->title}</h1>";
				$Co_linkit = $this->_getPageContent( $Pg_linkit->id, $Pg_linkit->lang_id );
				$Co_linkit->content = pg_escape_string($Linkit_teksti);
				if ( empty($Co_linkit->date_created) ) {
					$Co_linkit->date_created = date(DATE_W3C);
					$Co_linkit->published = true;
					$Co_linkit->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_linkit->save() ) throw new \Exception('Linkit-sivun oletussisällön tallentaminen epäonnistui: '.$Co_linkit->_lastError);
				
				
				//tiedostot
				$Pg_tiedostot = new \Lougis_cms_page();
				$Pg_tiedostot->title = 'Tiedostot';
				$Pg_tiedostot->parent_id = $Pg->id;
				$Pg_tiedostot->nav_name = 'Tiedostot';
				$Pg_tiedostot->created_date = date(DATE_W3C);
				$Pg_tiedostot->created_by = $_SESSION['user_id'];
				$Pg_tiedostot->site_id = $_SESSION['site_id'];
				$Pg_tiedostot->lang_id = 'fi';
				// $Pg_tiedostot->comments_id = $Topic_tiedostot->id;
				$Pg_tiedostot->setNextSeqNum();
				if ( !$Pg_tiedostot->save() ) throw new \Exception('Tiedostot-sivun tietojen tallentaminen epäonnistui: '.$Pg_tiedostot->_lastError);
				
				$Tiedostot_teksti = "<h1>{$Pg_tiedostot->title}</h1>";
				$Co_tiedostot = $this->_getPageContent( $Pg_tiedostot->id, $Pg_tiedostot->lang_id );
				$Co_tiedostot->content = pg_escape_string($Tiedostot_teksti);
				if ( empty($Co_tiedostot->date_created) ) {
					$Co_tiedostot->date_created = date(DATE_W3C);
					$Co_tiedostot->published = true;
					$Co_tiedostot->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_tiedostot->save() ) throw new \Exception('Tiedostot-sivun oletussisällön tallentaminen epäonnistui: '.$Co_tiedostot->_lastError);

				
				//tilastot
				$Pg_tilastot = new \Lougis_cms_page();
				$Pg_tilastot->title = 'Tilastot';
				$Pg_tilastot->nav_name = 'Tilastot';
				$Pg_tilastot->parent_id = $Pg->id;
				$Pg_tilastot->created_date = date(DATE_W3C);
				$Pg_tilastot->created_by = $_SESSION['user_id'];
				$Pg_tilastot->site_id = $_SESSION['site_id'];
				$Pg_tilastot->lang_id = 'fi';
				// $Pg_tilastot->comments_id = $Topic_tilastot->id;
				$Pg_tilastot->setNextSeqNum();
				if ( !$Pg_tilastot->save() ) throw new \Exception('Tilastot-sivun tietojen tallentaminen epäonnistui: '.$Pg_tilastot->_lastError);
				
				$Tilastot_teksti = "<h1>{$Pg_tilastot->title}</h1>";
				$Co_tilastot = $this->_getPageContent( $Pg_tilastot->id, $Pg_tilastot->lang_id );
				$Co_tilastot->content = pg_escape_string($Tilastot_teksti);
				if ( empty($Co_tilastot->date_created) ) {
					$Co_tilastot->date_created = date(DATE_W3C);
					$Co_tilastot->published = true;
					$Co_tilastot->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_tilastot->save() ) throw new \Exception('Tilastot-sivun oletussisällön tallentaminen epäonnistui: '.$Co_tilastot->_lastError);
				
				//keskustelu
				$Pg_keskustelu = new \Lougis_cms_page();
				$Pg_keskustelu->title = 'Keskustelu';
				$Pg_keskustelu->nav_name = 'Keskustelu';
				$Pg_keskustelu->parent_id = $Pg->id;
				$Pg_keskustelu->created_date = date(DATE_W3C);
				$Pg_keskustelu->created_by = $_SESSION['user_id'];
				$Pg_keskustelu->site_id = $_SESSION['site_id'];
				$Pg_keskustelu->lang_id = 'fi';
				// $Pg_keskustelu->comments_id = $Topic_keskustelu->id;
				$Pg_keskustelu->setNextSeqNum();
				if ( !$Pg_keskustelu->save() ) throw new \Exception('Keskustelu-sivun tietojen tallentaminen epäonnistui: '.$Pg_keskustelu->_lastError);
				
				$Keskustelu_teksti = "<h1>{$Pg_keskustelu->title}</h1>";
				$Co_keskustelu = $this->_getPageContent( $Pg_keskustelu->id, $Pg_keskustelu->lang_id );
				$Co_keskustelu->content = pg_escape_string($Keskustelu_teksti);
				if ( empty($Co_keskustelu->date_created) ) {
					$Co_keskustelu->date_created = date(DATE_W3C);
					$Co_keskustelu->published = true;
					$Co_keskustelu->created_by = $_SESSION['user_id'];
				}
				if ( !$Co_keskustelu->save() ) throw new \Exception('Keskustelu-sivun oletussisällön tallentaminen epäonnistui: '.$Co_keskustelu->_lastError);
				
			}
			*/
			$res = array(
				"success" => true,
				"msg" => "Uusi sivu luotu",
				"page_id" => $Pg->id
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	
	}
	
	
	
	public function deletePage() {
		
		try {
			if ( !isset($_SESSION['user_id']) ) throw new \Exception('Tunnistautuminen epäonnistui.'); 
			$Pg = new \Lougis_cms_page($_POST['page_id']);
			if ( empty($Pg->created_date) ) throw new \Exception('Sivun poistaminen epäonnistui: Sivua ei voitu ladata!');
			if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun poistaminen epäonnistui: Virheellinen sivusto!');
			
			//ennakointi
			devlog($Pg, "delTeema");
			/*if ($Pg->page_type = "teema" ) {
				$Child_pages = $Pg->getEveryChildren();
				$Child_pages = $Child
			}*/
			
			
			
			//devlog($Pg, 'pyry');
			
			if ( !$Pg->delete() ) throw new \Exception('Sivun poistaminen epäonnistui: '.$Pg->_lastError);
			
			$res = array(
				"success" => true,
				"msg" => "Sivu poistettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			devlog($Pg, 'pyry');
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	private function _navTreeData( $Type = null ) {
	
		$Pages = array();
		$Pg = new \Lougis_cms_page();
		$Pg->site_id = $_SESSION['site_id'];
		$Pg->orderBy('seqnum ASC');
		$Pg->page_type = $Type;
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
		if($Type == 'toimiala' || $Type == 'teema' ) { return $Pages; }
		
		else return $this->_recurseNavTreeData( $Pages );
		
	}
	
	private function _recurseNavTreeData( $Pgs, $Parent = null ) {
		
		$Branch = array();
		foreach( $Pgs as $Pg ) {
			if ( empty($Parent) && empty($Pg->parent_id) ) {
				unset($Pgs[$Pg->id]);
				$Kids = $this->_recurseNavTreeData( $Pgs, $Pg->id );
				if ( $Kids ) $Pg->children = $Kids;
				array_push($Branch, $Pg);
			} elseif ( $Parent == $Pg->parent_id ) {
				unset($Pgs[$Pg->id]);
				$Kids = $this->_recurseNavTreeData( $Pgs, $Pg->id );
				if ( $Kids ) $Pg->children = $Kids;
				array_push($Branch, $Pg);
			} 
		}
		if ( count($Branch) < 1 ) return false;
		return $Branch;
		
	}
	
	public function parentComboData( ) {
		
		global $comboResult;

		$comboResult = array();
		$row = array(
			"page_id" => null,
			"title" => "&nbsp;",
			"level" => 0
		);
		array_push($comboResult, $row);
		
		$tree = $this->_navTreeData();
		$comboData = $this->_recurseParentCombo( $tree );
		
		return $this->jsonOut( $comboData );
	
	}
	
	private function _recurseParentCombo( $tree, $level = 0 ) {
		
		global $comboResult;
		
		foreach($tree as $Pg) {
			$pgTitle = "";
			for($i=0; $i < $level;$i++) {
				$pgTitle .= " &nbsp; &nbsp; ";
			}
			$pgTitle .= ( $level > 0 ) ? "\ _ _" : ""; 
			$pgTitle .= $Pg->nav_name;
			$row = array(
				"page_id" => $Pg->id,
				"title" => $pgTitle,
				"level" => $level
			);
			array_push($comboResult, $row);
			if ( isset($Pg->children) ) $this->_recurseParentCombo($Pg->children, ($level+1));
		}
		
		return $comboResult;
		
	}
	
	public function checkPagesJson() {
		
		$Site = new \Lougis_site( $_SESSION['site_id'] );
		
		$tree = $this->_navTreeData();
		$visible = $this->_recurseExtPageTree( $tree, true );
		$root = array(
			"text" => $Site->title,
			"expanded" => true,
			"children" => $visible
		);
		
		$this->jsonOut( $visible );
		
	}
	//navigaatio-valikko (2+ tasojen sivut)
	public function navTreeJson($Page_type = null) {
		
		$Site = new \Lougis_site( $_SESSION['site_id'] );
		$Page_type = $_POST['Page_type'];
		$tree = $this->_navTreeData($Page_type);
		$visible = $this->_recurseExtPageTree( $tree, false, 0 );
		$root = array(
			"text" => $Site->title,
			"expanded" => true,
			"children" => $visible
		);
		
		$this->jsonOut( $visible );
		
	}
	
	public function getTeemaPages() {
		
		$Pg = new \Lougis_cms_page();
		
		$parent_id = pg_escape_string($_POST['toimiala_id']);
		$sql = 'select cms_page.id as page_id, cms_page.title, cms_content.id as content_id, cms_page.id as content_page_id, cms_page.parent_id, cms_page.page_type, cms_content.content
				from lougis.cms_page, lougis.cms_content
				where cms_page."parent_id" = '.$parent_id.'
				and cms_content.page_id = cms_page.id
				and cms_page.page_type = \'teema\';';
		$Pg->query($sql);
		$Pages = array();
		while( $Pg->fetch() ) {
			$Pages[] = clone($Pg);		
		}
		
		$this->jsonOut( $Pages );
		
	}
	
	public function getToimialaPages() {
		
		$Pg = new \Lougis_cms_page();
		$Pg->page_type = "toimiala";
		$Pg->find();
		//lisää vielä käyttäjälle sallitut toimialat eli hae permissionit
		$Pages = array();
		while( $Pg->fetch() ) {
			$Pages[] = clone($Pg);		
		}	
		$this->jsonOut( $Pages );
		
	}
	
	private function _menuTreeData() {
	
		$Pages = array();
		$Pg = new \Lougis_cms_page();
		$Pg->site_id = $_SESSION['site_id'];
		$Pg->orderBy('seqnum ASC');
		$Pg->parent_id = null; //vain ensimmäisen tason sivut
		$Pg->find();
		while( $Pg->fetch() ) {
			$Pages[$Pg->id] = clone($Pg);
		}
		return $Pages;
		
	}
	//menu-valikko (=1.tason sivut)
	public function menuTreeJson() {
		
		$Site = new \Lougis_site( $_SESSION['site_id'] );
		
		$tree = $this->_navTreeData($Page_type);
		$visible = $this->_recurseExtPageTree( $tree, false, 0 );
		$root = array(
			"text" => $Site->title,
			"expanded" => true,
			"children" => $visible
		);
		
		$this->jsonOut( $visible );
		
	}
	
	private function _recurseExtPageTree( $PageTree, $Checkbox = false, $level = 0 ) {
		
		$Branch = array();
		foreach($PageTree as $Pg) {
			$Leaf = array(
				"text" => $Pg->nav_name,
				"page_id" => (int) $Pg->id
			);
			if ( isset($Pg->children) ) {
				$Leaf["expanded"] = ( $level < 1 ) ? true : false;
				$Leaf["children"] = $this->_recurseExtPageTree( $Pg->children, $Checkbox, $level+1 );
			} else {
				$Leaf["leaf"] = true;
			}
			if ( $Checkbox ) $Leaf["checked"] = false;
			array_push($Branch, $Leaf);
		}
		return $Branch;
	
	}
	
	
	public function saveTreeSort() {
		
		try {
			
			$treeData = json_decode($_POST['tree_data']);
			
			$this->_recurseSaveTreeSort( $treeData );
			
			$res = array(
				"success" => true,
				"msg" => "Rakenne tallennettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	private function _recurseSaveTreeSort( $branch, $ParentId = "NULL" ) {
		
		global $Idx;
		
		if ( $Idx === null ) $Idx = 10;
		
		foreach($branch as $leaf) {
			$Pg = new \Lougis_cms_page($leaf->page_id);
			$Pg->seqnum = $Idx;
			$Pg->parent_id = $ParentId;
			$Idx++;
			if ( !$Pg->save() ) {
				throw new \Exception('Puun tietojen tallentaminen epäonnistui: '.$Pg->_lastError);
			}
			if ( count($leaf->children) > 0 ) $this->_recurseSaveTreeSort( $leaf->children, $Pg->id );
		}
	
	}
	
}
?>