<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class News extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function getNewsArray() {
	
		global $Site;
		
		try {
			
			$res = array();
			
			$News = new \Lougis_news();
			$News->site_id = $Site->id;
			$News->orderBy('created_date');
			$News->find();
			while ( $News->fetch() ) {
				$Data = $News->toArray();
				$Data["news_id"] = intval($Data["id"]);
				$Data["seqnum"] = intval($Data["seqnum"]);
				$Data["created_by"] = intval($Data["created_by"]);
				unset($Data["id"]);
				//$Data["pages"] = $News->getPagesDataArray();
				$res[] = $Data;
			}
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	
	}
	
	
	
	public function newsTreeJson() {
		
		global $Site;
		
		try {
			
			$res = array();
			
			$News = new \Lougis_news();
			$News->site_id = $Site->id;
			$News->orderBy('created_date DESC');
			$News->find();
			while ( $News->fetch() ) {
				$Data = array();
				$Data["text"] = $News->title.' ('.date("d.m.Y", strtotime($News->created_date)).')';
				$Data["leaf"] = true;
				$Data["news_id"] = (int) $News->id;
				$Data["seqnum"] = (int) $News->seqnum;
				$res[] = $Data;
			}
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function getNewsJson() {
		
		global $Site;
		
		try {
			
			if ( !isset($_REQUEST['news_id']) || empty($_REQUEST['news_id']) ) throw new \Exception("Virhe tiedotteen latauksessa");
			$News = new \Lougis_news($_REQUEST['news_id']);
			if ( empty($News->title) || $News->site_id != $Site->id ) throw new \Exception("Tiedotetta ei voitu ladata");
			
			$data = $News->toArray("news[%s]");
			unset($data['news[created_date]']);
			unset($data['news[content]']);
			unset($data['news[id]']);
			
			$data['news[created_date]'] = date("d.m.Y", strtotime($News->created_date));
			$data['news[published]'] = ( $News->published == 't' ) ? true : false ;
			$data['content'] = $News->content;
			$data['news_id'] = $News->id;
			
			$data["pages"] = $News->getPagesIdArray();
			
			$res = array(
				"success" => true,
				"data" => $data
			);

			
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function saveNews() {
		
		global $Site, $User;
		
		try {
			devlog($_REQUEST, 'pyry');
			if ( isset($_REQUEST['news_id']) && !empty($_REQUEST['news_id']) ) {
				$News = new \Lougis_news($_REQUEST['news_id']);
				if ( empty($News->title) ) throw new \Exception("Tiedotetta ei voitu ladata!");
				$msg = "Tiedote tallennettu";
			} else {
				$News = new \Lougis_news();
				$msg = "Uusi tiedote tallennettu";
				$News->site_id = $Site->id;
				$News->lang_id = 'fi';
				$News->created_date = date(DATE_W3C);
				$News->created_by = $User->id;
				$News->seqnum = 0;
				\Lougis_news::growSeqnums();
			}
			$News->setFrom($_REQUEST['news']);
			if ( empty($News->created_date) ) $News->created_date = date(DATE_W3C);
			$News->content = $_REQUEST['news_content'];
			if ( !isset($_REQUEST['news']['published']) && $News->N == 1 ) $News->published = false;
			
			//if ( strlen($News->title) < 5 || strlen($News->title) > 250 ) throw new \Exception("Tiedotteen otsikko tulee olla vähintään 5 merkkiä ja maksimissaan 250.");
			if ( !$News->save() ) throw new \Exception('Tiedotteen tallentaminen epäonnistui: '.$News->_lastError->userinfo);
			if ( !$News->setPages( json_decode($_REQUEST['news_pages']) ) ) throw new \Exception('Tiedotteen linkkien tallennus epäonnistui: '.$Pg->_lastError);
			$res = array(
				"success" => true,
				"msg" => $msg,
				"news_id" => $News->id
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function saveNewsEnnakointi() {
	
		global $Site, $User;
		
		try {
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen epäonnistui.');
			
			
			//if ( strlen($_POST['news']['title']) < 5 || strlen($_POST['news']['title']) > 250 ) throw new \Exception("Tiedotteen otsikko tulee olla vähintään 5 merkkiä ja maksimissaan 250.");
			
			if ( isset($_POST['news_id']) && !empty($_POST['news_id']) ) {
				$News = new \Lougis_news($_POST['news_id']);
				if ( empty($News->title) ) throw new \Exception("Tiedotetta ei voitu ladata!");
				$msg = "Tiedote tallennettu";
			} else {
				$News = new \Lougis_news();
				$msg = "Uusi tiedote tallennettu";
				$News->site_id = $Site->id;
				$News->lang_id = 'fi';
				$News->created_date = date(DATE_W3C);
				$News->created_by = $User->id;
				$News->news_type = 'uutinen';
				$News->seqnum = 0;
				\Lougis_news::growSeqnums();
			}
			$News->setFrom($_POST['news']);
			if ( empty($News->created_date) ) $News->created_date = date(DATE_W3C);
			if ( !isset($_POST['news']['published']) && $News->N == 1 ) $News->published = false;
			
			//if ( strlen($News->title) < 5 || strlen($News->title) > 250 ) throw new \Exception("Tiedotteen otsikko tulee olla vähintään 5 merkkiä ja maksimissaan 250.");
			if ( !$News->save() ) throw new \Exception('Tiedotteen tallentaminen epäonnistui: '.$News->_lastError->userinfo);
			//if ( !$News->setPages( json_decode($_POST['news_pages']) ) ) throw new \Exception('Tiedotteen linkkien tallennus epäonnistui: '.$Pg->_lastError);
			
			//hae oikea parent page
			$parent = new \Lougis_cms_page();
			$parent->parent_id = $_POST['news']['parent_id'];
			$parent->page_type = "teema_uutiset";
			$parent->find(true);
			
			//luo cms_page
			$page = new \Lougis_cms_page();
			$page->site_id = 'everkosto';
			$page->lang_id = 'fi';
			$page->title = $_POST['news']['title'];
			$page->nav_name = $_POST['news']['title'];
			$page->created_by = $_SESSION['user_id'];
			$page->created_date = date(DATE_W3C);
			$page->published = true;
			$page->template = 'teema_uutiset.php';
			$page->visible = true;
			$page->restricted_access = true;
			$page->page_type = 'news';
			$page->news_id = $News->id;
			$page->parent_id = $parent->id;
			//if($_POST['news']['parent_id'] > 0) $page->parent_id = $_POST['news']['parent_id'];
			if($_POST['news']['page_id'] < 1) $page->setNextSeqNum(); //jos uusi niin annettaan seqnum
			if ( !$page->save() ) throw new \Exception("Sivun tallennus epäonnistui.");
			$PgArray = $page->toArray();
			
			//oikeudet päästä sivulle
			
			if($page->page_type == "news") {
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
			
			
			
			$res = array(
				"success" => true,
				"msg" => "Uutinen on luotu!"
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function saveNewsLinkki() {
	
		global $Site, $User;
		
		try {
		
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen epäonnistui.');
			
		//	if ( strlen($_POST['news']['title']) < 5 || strlen($_POST['news']['title']) > 250 ) throw new \Exception("Tiedotteen otsikko tulee olla vähintään 5 merkkiä ja maksimissaan 250.");
			
			if ( isset($_POST['news_id']) && !empty($_POST['news_id']) ) {
				$News = new \Lougis_news($_POST['news_id']);
				if ( empty($News->title) ) throw new \Exception("Linkkiä ei voitu ladata!");
				$msg = "Linkki tallennettu";
			} else {
				$News = new \Lougis_news();
				$msg = "Uusi linkki tallennettu";
				$News->site_id = $Site->id;
				$News->lang_id = 'fi';
				$News->created_date = date(DATE_W3C);
				$News->created_by = $User->id;
				$News->news_type = 'linkki';
				$News->seqnum = 0;
				\Lougis_news::growSeqnums();
			}
			$News->setFrom($_POST['news']);
			if ( empty($News->created_date) ) $News->created_date = date(DATE_W3C);
			if ( !isset($_POST['news']['published']) && $News->N == 1 ) $News->published = false;
			
			if ( strlen($News->title) < 5 || strlen($News->title) > 250 ) throw new \Exception("Linkin otsikko tulee olla vähintään 5 merkkiä ja maksimissaan 250.");
			if ( !$News->save() ) throw new \Exception('Linkin tallentaminen epäonnistui: '.$News->_lastError->userinfo);
			//if ( !$News->setPages( json_decode($_POST['news_pages']) ) ) throw new \Exception('Tiedotteen linkkien tallennus epäonnistui: '.$Pg->_lastError);
			
			//hae oikea parent page
			$parent = new \Lougis_cms_page();
			$parent->parent_id = $_POST['news']['parent_id'];
			$parent->page_type = "teema_linkit";
			$parent->find();
			$parent->fetch();
			
			//luo cms_page
			$page = new \Lougis_cms_page();
			$page->site_id = 'everkosto';
			$page->lang_id = 'fi';
			$page->title = $_POST['news']['title'];
			$page->nav_name = $_POST['news']['title'];
			$page->created_by = $_SESSION['user_id'];
			$page->created_date = date(DATE_W3C);
			$page->published = true;
			$page->template = 'teema_linkit.php';
			$page->visible = true;
			$page->restricted_access = true;
			$page->page_type = 'news';
			$page->news_id = $News->id;
			if($parent->id != null) $page->parent_id = $parent->id;
			//if($_POST['news']['parent_id'] > 0) $page->parent_id = $_POST['news']['parent_id'];
			if($_POST['news']['page_id'] < 1) $page->setNextSeqNum(); //jos uusi niin annettaan seqnum
			if ( !$page->save() ) throw new \Exception("Sivun tallennus epäonnistui.");
			$PgArray = $page->toArray();
			
			//oikeudet päästä sivulle
			
			if($page->page_type == "news") {
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
			
			
			$res = array(
				"success" => true,
				"msg" => $msg,
				"news_id" => $News->id
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function deleteNews() {
		
		global $Site;
		try {
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen epäonnistui.');
			
			$page_id = (int)$_POST['page_id'];
			$news_id = (int)$_POST['news_id'];
			if ( $page_id != null) {
				$page = new \Lougis_cms_page($page_id);
				if ( !$page->delete() ) throw new \Exception('Tiedotesivun poistaminen epäonnistui: '.$page->_lastError);
			}
			
			$News = new \Lougis_news($news_id);
			if ( empty($News->title) ) throw new \Exception('Tiedotteen poistaminen epäonnistui: Tiedotetta ei voitu ladata!');
			if ( $News->site_id != $Site->id ) throw new \Exception('Tiedotteen poistaminen epäonnistui: Virheellinen sivusto!');
			devlog($News, "e_news");
			//if ( !$News->delete() ) throw new \Exception('Tiedotteen poistaminen epäonnistui: '.$News->_lastError);
			$News->delete(); //ei tarvitse if:iä koska exceptionia ei tarvitse antaa vaikkei poistuis (db for.key restrict)
			$res = array(
				"success" => true,
				"msg" => "Tiedote poistettu onnistuneesti!"
			);
		
		} catch(\Exception $e) {
		
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		
		$this->jsonOut($res);
		
	}
	
	public function saveTreeSort() {
		
		try {
			
			$treeData = json_decode($_REQUEST['tree_data']);
			
			foreach($treeData as $idx => $news_id) {
				$News = new \Lougis_news($news_id);
				$News->seqnum = ($idx+1);
				if ( !$News->save() ) throw new \Exception('Järjestyksen tallentaminen epäonnistui: '.$Pg->_lastError);
			}
			
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
	
	public function getNewsHtml() {
		
		global $Site;
		
		$this->htmlHeader();
		
		try {
			
			if ( !isset($_REQUEST['nid']) || empty($_REQUEST['nid']) ) throw new \Exception("Virhe tiedotteen latauksessa");
			$News = new \Lougis_news($_REQUEST['nid']);
			if ( empty($News->title) || $News->site_id != $Site->id ) throw new \Exception("Tiedotetta ei voitu ladata");
			?>
			<span class="date"><?=date("d.m.Y", strtotime($News->created_date))?> - <?=$News->source?></span>
			<img class="close" src="/img/close.png" alt="" title="Sulje uutinen" data-nid="<?=$News->id?>" />
			<span class="clr"/>
			<h1 style="margin-top: -5px;"><?=$News->title ?></h1>
			<?=$News->getContentHtml(); ?>
			<? if ( !empty($News->source_url) ) { ?>
			<span class="link"><a href="<?=$News->source_url?>" target="_blank">Lue alkuperäinen artikkeli</a></span>
			<?
			}
		
		} catch(\Exception $e) {
			
			?>
			<b><?=$e->getMessage()?></b>
			<?
			
		}
		
		
	}
	
}
?>