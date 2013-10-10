<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');

class Charts extends \Lougis\abstracts\Frontend {

	public function __construct() {
	
	}
	
	public function getChartsJson() {
	
		global $Site;
		
		try {
			/*
			$ChartPages = array();
			$Pg = new \Lougis_cms_page(); //all charts are sub of pages
			$Pg->page_type = 'chart'; //pages where type is chart
			$Pg->orderBy("title");
			$Pg->find();
			while( $Pg->fetch() ) {
				$ChartPages[] = array(
					"text" => $Pg->title,
					"leaf" => true,
					"expanded" => false,
					"chart_id" => intval($Pg->id)
				);
			}
			$res = $ChartPages;
			*/
			$Charts = array();
			$Ch = new \Lougis_chart();
			$Ch->orderBy("title");
			$Ch->whereAdd("title IS NOT NULL");
			$Ch->find();
			while( $Ch->fetch() ) {
				$Charts[] = array(
					"text" => $Ch->title,
					"leaf" => true,
					"expanded" => false,
					"chart_id" => intval($Ch->id)
				);
			}
			$res = $Charts;
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	
	}
        //Indikaattorisivun k�ytt��n indikaattorina julkaistavat tilastot
        public function getPublishedChartsJson() {
	
		global $Site;
		
		try {
			
			
			$Charts = array();
			$Ch = new \Lougis_chart();
			$Ch->orderBy("title");
			$Ch->whereAdd("title IS NOT NULL");
                        $Ch->whereAdd("published IS TRUE");
			$Ch->find();
			while( $Ch->fetch() ) {                            
				$Charts[] = array(
					"text" => $Ch->title,
					"leaf" => true,
					"expanded" => false,
					"chart_id" => intval($Ch->id),
				);
			}
			$res = $Charts;
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	
	}
        
       
	public function buildIframeCode() {
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteytt� yll�pitoon.");
			
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
	
	public function saveChartConfig() {
		
		try {
			
			$ChartData = $_REQUEST['chart'];
			
			$Chart = new \Lougis_chart($ChartData['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteytt� yll�pitoon.");
			$Msg = ( $_REQUEST['save'] == 'true' ) ? "Kaavio tallennettu." : null;
			$ChartConf = $Chart->buildExtJsonChart($ChartData);
			if ( $_REQUEST['save'] == 'true' ) $Chart->saveChartConfig($ChartConf, $_REQUEST);
			
			$res = array(
				"success" => true,
				"msg" => $Msg,
				"conf" => $ChartConf
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
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen ep�onnistui.');
			
			$ChartData = $_REQUEST['chart'];
			
			$Chart = new \Lougis_chart($ChartData['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteytt� yll�pitoon.");
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
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteytt� yll�pitoon.");
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
			//k�sittele config_json json muodosta k�ytett�vksi
			
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
	
	public function updateData() {
		
		
		try {

			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteytt� yll�pitoon.");
			$Chart->updated_date = date(DATE_W3C);
			$data = json_decode($_REQUEST['data']);
			if ( count($data) == 0 ) throw new \Exception("Taulukko on tyhj�. Tyhj�� taulukkoa ei voi tallentaa");
			//json file p�ivitys
			if ( !$Chart->updateData( $data ) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteytt� yll�pitoon.");
			//tietokantaan p�ivitys
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteytt� yll�pitoon.");
			$res = array(
				"success" => true,
				"msg" => "Taulukko tallennettu."
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
				if ( !$page->save() ) throw new \Exception("Sivun tallennus ep�onnistui.");
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
				if ( !$page->save() ) throw new \Exception("Sivun tallennus ep�onnistui.");
			}
			
			$PgArray = $page->toArray();
			
			//create permissions
			if($page->page_type == "chart") {
				$pa = $page->getParentIdArray(); //sivun sukupolvet id arrayna
				
				$group = new \Lougis_group();
				$group->page_id = $pa[2]; //arrayn indeksiss� 2 on oikea parent, eli t�m�n toimialan id.
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
			if ( !$Chart->save() ) throw new \Exception("Tilaston tallennus ep�onnistui."); */
			
			
		} catch(\Exception $e) {
			
			return false;
			
		}
		return true;
		
	}
	
	
	//update data to database from jquery chart editor. 
	public function updateDbData() {
		
			
		try {
			
			$data = json_encode($_POST['chart_data']);
			$Chart = new \Lougis_chart($_POST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteytt� yll�pitoon.");
			$Chart->updated_date = date(DATE_W3C);
			$Chart->data_json = $data;
			
			if ( count($data) == 0 ) throw new \Exception("Taulukko on tyhj�. Tyhj�� taulukkoa ei voi tallentaa");
			
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteytt� yll�pitoon.");
			
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
	
	public function uploadData() {
	
		global $Site, $User;
		
		try {
			
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception("Käyttäjän tunnistaminen");
			
			//if ( $_FILES['datafile']['type'] != 'text/csv' ) throw new \Exception("Virheellinen tiedostotyyppi! Tiedoston tulee olla CSV-tiedosto (tiedostop��te .csv)");
			unset($_SESSION['new_chart_id']); //v�liaikainen testaamiseen
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
	
	/* public function uploadData() {
	
		global $Site, $User;
		
		try {
			
			//if ( $_FILES['datafile']['type'] != 'text/csv' ) throw new \Exception("Virheellinen tiedostotyyppi! Tiedoston tulee olla CSV-tiedosto (tiedostop��te .csv)");
			//unset($_SESSION['new_chart_id']);
			if ( isset($_SESSION['new_chart_id']) ) {
				$Chart = new \Lougis_chart($_SESSION['new_chart_id']);
			} else {
				$Chart = new \Lougis_chart();
				$Chart->setNextKey();
			}
			if ( !$Chart->addUploadedDatafile($_FILES['datafile']) ) throw new \Exception("Datatiedostoa ei voitu tallentaa palvelimelle.");
			if ( !$Chart->buildJsonFileFromCsv() ) throw new \Exception("Dataa ei voitu lukea");
			$Chart->created_date = date(DATE_W3C);
			$Chart->created_by = $User->id;
			if ( !$Chart->save() ) throw new \Exception("Tilastoa ei voitu tallentaa");
			
			$_SESSION['new_chart_id'] = $Chart->id;
			
			$res = array(
				"success" => true,
				"chart" => $Chart->toChartArray()
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonHtmlOut($res);
	
	} */
	
	public function getChartObj() {
                global $Site, $User;
                
                try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei l�ytynyt!");
			
			$res = array(
				"success" => true,
				//"chart" => $Chart->toChartArray(true, true, true)
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
                
        }
	public function getChartInfo() {
		
		global $Site, $User;
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei l�ytynyt!");
			
			$res = array(
				"success" => true,
				"chart" => $Chart->toChartArray(true, true, true)
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
		
	}
	
	public function getChartTitle() {
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei l�ytynyt!");
			
			$res = array(
				"success" => true,
				"title" => $Chart->title
			);
			
		} catch(\Exception $e) {
			
			$res = array(
				"success" => false,
				"msg" => $e->getMessage()
			);
			
		}
		$this->jsonOut($res);
	}
	
	//Delete chart (and cms_page)
	public function deleteChart() {
		
		try {
			//user auth
			$SessionUser = new \Lougis_session();
			$SessionUser->get($_SESSION['user_id']);
			if(!$SessionUser->isLogged()) throw new \Exception('Tunnistautuminen ep�onnistui.');

			$chart_id = (int)$_POST['chart_id'];
			$Chart = new \Lougis_chart($chart_id);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Tilastoa ei voitu ladata. Ota yhteytt� yll�pitoon.");
	
			$Pg = new \Lougis_cms_page();
			$Pg->chart_id = $chart_id;
			$Pg->find(true);
			if( $Pg->id != null) {
				if ( empty($Pg->created_date) ) throw new \Exception('Sivun poistaminen ep�onnistui: Sivua ei voitu ladata!');
				if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun poistaminen ep�onnistui: Virheellinen sivusto!');
				if ( !$Pg->delete() ) throw new \Exception('Sivun poistaminen ep�onnistui: '.$Pg->_lastError);
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
	
	public function saveChartInfo() {
		
		global $Site, $User;
		
		try {
			
			//page
			$PgArray = array();
		
			if($_REQUEST['chart']['page_id'] > 0) $Pg = new \Lougis_cms_page($_REQUEST['chart']['page_id']);
			else $Pg = new \Lougis_cms_page();	
			$Pg->site_id = 'everkosto';
			$Pg->lang_id = 'fi';
			$Pg->title = $_REQUEST['chart']['title'];
			$Pg->nav_name = $_REQUEST['chart']['title'];
			$Pg->created_by = $_SESSION['user_id'];
			$Pg->created_date = date(DATE_W3C);
			$Pg->template = 'indikaattorit_uusi.php';
			$Pg->published = true;
			$Pg->visible = true;
			$Pg->restricted_access = false;
			$Pg->page_type = 'chart';
			if($_REQUEST['chart']['parent_id'] > 0) $Pg->parent_id = $_REQUEST['chart']['parent_id'];
			if($_REQUEST['chart']['page_id'] < 1) $Pg->setNextSeqNum(); //jos uusi niin annettaan seqnum
			if ( !$Pg->save() ) throw new \Exception("Sivun tallennus ep�onnistui.");
			$PgArray = $Pg->toArray();
			
			if($_REQUEST['chart']['page_id'] > 0) devlog($_REQUEST['chart']);
			
			//chart
			$Chart = new \Lougis_chart($_REQUEST['chart']['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilaston avaus ep�onnistui.");
			$Chart->setFrom($_REQUEST['chart']);
			$Chart->page_id = $PgArray['id']; //page_id of created or updated page
			$Chart->updated_date = date(DATE_W3C);
			if ( empty($Chart->title) ) throw new \Exception("Tilaston otsikko on pakollinen!");
			if ( !$Chart->updateJsonFileFields($_REQUEST['fields']) ) throw new \Exception("Tilastotiedon tallennus ep�onnistui!");
			if ( !$Chart->save() ) throw new \Exception("Tilaston tallennus ep�onnistui.");
			
			if ( isset($_SESSION['new_chart_id']) ) unset($_SESSION['new_chart_id']);
		
			
			$res = array(
				"success" => true,
				"msg" => "Tilaston perustiedot tallennettu",
				"chart" => $Chart->toChartArray()
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