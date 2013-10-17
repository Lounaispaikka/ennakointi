<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class Charts extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
        
	public function buildIframeCode() {
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			$code = $Chart->getIframeCode($_REQUEST['width'], $_REQUEST['height']);
			
			$res = array(
				"success" => true,
				"code" => $code
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function uploadData() {
	
		global $Site, $User;
		
		try {
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception("KÃ€yttÃ€jÃ€n tunnistaminen");
			
			if ( $_FILES['datafile']['type'] != 'text/csv' ) throw new \Exception("Virheellinen tiedostotyyppi! Tiedoston tulee olla CSV-tiedosto (tiedostopääte .csv)");
			unset($_SESSION['new_chart_id']); //väliaikainen testaamiseen
			if ( isset($_SESSION['new_chart_id']) ) {
				$Chart = new \Lougis_chart($_SESSION['new_chart_id']);
			} else {
				$Chart = new \Lougis_chart();
				$Chart->setNextKey();
			}

			if ( !$Chart->buildJsonDataFromCsv($_FILES['datafile']) ) throw new \Exception("Dataa ei voitu lukea");
			$Chart->created_date = date(DATE_W3C);
			$Chart->created_by = $User->id;
			if ( !$Chart->save() ) throw new \Exception("Virhe tilastoa luotaessa. Tilastoa ei voitu tallentaa.");
			
			$_SESSION['new_chart_id'] = $Chart->id;
			
			$res = array(
				"success" => true,
				"chart" => $Chart->dbToChartArray()
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonHtmlOut($res);
	
	}
	
	
	//update data to database from jquery chart editor. 
	public function updateDbData() {
		
			
		try {
			
			$data = json_encode($_POST['chart_data']);
			$Chart = new \Lougis_chart($_POST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Chart->updated_date = date(DATE_W3C);
			$Chart->data_json = $data;
			
			if ( count($data) == 0 ) throw new \Exception("Taulukko on tyhjä. Tyhjää taulukkoa ei voi tallentaa");
			
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			
			$res = array(
				"success" => true,
				"chart" => $Chart
				//"msg" => "Taulukko tallennettu."
			);
		
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	//Save configs and create cms_page
	public function saveHighchartConfig() {
		
		try {
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen epäonnistui.');
			
			$ChartData = $_REQUEST['chart'];
			
			$Chart = new \Lougis_chart($ChartData['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Chart->title = $ChartData['title'];
			
			//config array
			$config = array();
			$config["type"] = $ChartData['config']['type'];
			$config["x_title"] = $ChartData['config']['x_title'];
			$config["y_title"] = $ChartData['config']['y_title'];
			
			//if chart type == pie
			if ( $config["type"] == 'pie' ) {
				$config["plotOptions"]["pie"]["allowPointSelect"] = true;
				$config["plotOptions"]["pie"]["cursor"] = 'pointer';
				$config["series"]["type"] = 'pie';
			}
			
			$Chart->config_json = json_encode($config);
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			$ChartData['page_id'] = $Chart->page_id;
			$ChartData['chart_id'] = $Chart->id;
			//create cms_page
			if(!$this->saveCmsPageForChart($ChartData)) throw new \Exception("Tekninen virhe! Tilastosivua ei voida tallentaa.");
			
			//$Chart->buildHighchart();
			
			/* $res = array(
				"success" => true,
				"msg" => $Msg,
				"conf" => $ChartConf
			); */
			
			$res = array(
				"success" => true
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function getHighchart() {
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			$ChartGraph = $Chart->buildHighchart();
			$ChartConf = $Chart->config_json;
			
			
			$res = array(
				"success" => true,
				"chart" => $ChartGraph,
				"conf" => $ChartConf,
			);
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	}
	
	//Create cms page for chart
	public function saveCmsPageForChart($chartData) {
		
		try {
			//get parent page
			$parent = new \Lougis_cms_page();
			$parent->parent_id = $chartData['parent_id'];
			$parent->page_type = "teema_tilastot";
			$parent->find(true);
			
			//update cms_page
			$page = new \Lougis_cms_page();
			$page->page_type = 'chart';
			$page->chart_id = $chartData['chart_id'];
			$page->find(true);
			if($page->id != null) {
				$page->title = $chartData['title'];
				$page->nav_name = $chartData['title'];
				if ( !$page->save() ) throw new \Exception("Sivun tallennus epäonnistui.");
			}
			else {
				//create new cms_page
				$page = new \Lougis_cms_page();
				$page->site_id = 'everkosto';
				$page->lang_id = 'fi';
				$page->title = $chartData['title'];
				$page->nav_name = $chartData['title'];
				$page->created_by = $_SESSION['user_id'];
				$page->created_date = date(DATE_W3C);
				$page->published = true;
				$page->template = 'teema_tilastot.php';
				$page->visible = true;
				$page->restricted_access = true;
				$page->page_type = 'chart';
				$page->chart_id = $chartData['chart_id'];
				if($parent->id != null) $page->parent_id = $parent->id;
				$page->setNextSeqNum(); //jos uusi niin annettaan seqnum
				if ( !$page->save() ) throw new \Exception("Sivun tallennus epäonnistui.");
			}
			
			$PgArray = $page->toArray();
			
			//create permissions
			if($page->page_type == "chart") {
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
			
			/* //update chart page_id
			$Chart = new \Lougis_chart($chartData['id']);
			$Chart->page_id = $page->id;
			if ( !$Chart->save() ) throw new \Exception("Tilaston tallennus epäonnistui."); */
			
			
		} catch(\Exception $e) {
			
			return false;
			
		}
		return true;
		
	}
	
	//Delete chart (and cms_page)
	public function deleteChart() {
		$res = array(
			"success" => false,
			"msg" => "Delete function not in use"
		);
		$this->jsonOut($res);
		return 0;
		try {
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen epäonnistui.');

			$chart_id = (int)$_POST['chart_id'];
			$Chart = new \Lougis_chart($chart_id);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Tilastoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
	
			$Pg = new \Lougis_cms_page();
			$Pg->chart_id = $chart_id;
			$Pg->find(true);
			if( $Pg->id != null) {
				if ( empty($Pg->created_date) ) throw new \Exception('Sivun poistaminen epäonnistui: Sivua ei voitu ladata!');
				if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun poistaminen epäonnistui: Virheellinen sivusto!');
				if ( !$Pg->delete() ) throw new \Exception('Sivun poistaminen epäonnistui: '.$Pg->_lastError);
			}
			$Chart->delete(); //try to delete chart also. If chart belongs to many pages then delete is restricted
			
			$res = array(
				"success" => true,
				"msg" => 'Tilasto "'.$Chart->title.'" poistettu.'
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
}
?>