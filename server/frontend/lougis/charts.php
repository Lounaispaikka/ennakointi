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
        //Indikaattorisivun käyttöön indikaattorina julkaistavat tilastot
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
	
	public function saveChartConfig() {
		
		try {
			
			$ChartData = $_REQUEST['chart'];
			
			$Chart = new \Lougis_chart($ChartData['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
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
	
	//highchart configs
	public function saveHighchartConfig() {
		
		try {
		
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
			//käsittele config_json json muodosta käytettävksi
			
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
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Chart->updated_date = date(DATE_W3C);
			$data = json_decode($_REQUEST['data']);
			if ( count($data) == 0 ) throw new \Exception("Taulukko on tyhjä. Tyhjää taulukkoa ei voi tallentaa");
			//json file päivitys
			if ( !$Chart->updateData( $data ) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			//tietokantaan päivitys
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
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
	
	//update data to database from jquery chart editor
	public function updateDbData() {
		
			devlog($_REQUEST['chart_data'], "e_chart_data");
			
		try {
			$data = json_encode($_REQUEST['chart_data']);
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			$Chart->updated_date = date(DATE_W3C);
			$Chart->data_json = $data;
			
			if ( count($data) == 0 ) throw new \Exception("Taulukko on tyhjä. Tyhjää taulukkoa ei voi tallentaa");
			
			if ( !$Chart->save() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu tallentaa. Ota yhteyttä ylläpitoon.");
			
			$res = array(
				"success" => true,
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
			
			//if ( $_FILES['datafile']['type'] != 'text/csv' ) throw new \Exception("Virheellinen tiedostotyyppi! Tiedoston tulee olla CSV-tiedosto (tiedostopääte .csv)");
			unset($_SESSION['new_chart_id']); //väliaikainen testaamiseen
			if ( isset($_SESSION['new_chart_id']) ) {
				$Chart = new \Lougis_chart($_SESSION['new_chart_id']);
			} else {
				$Chart = new \Lougis_chart();
				$Chart->setNextKey();
			}
			$fila = implode("|", $_FILES['datafile']);
		
			//if ( !$Chart->addUploadedDatafile($_FILES['datafile']) ) throw new \Exception("Datatiedostoa ei voitu tallentaa palvelimelle.". $fila );
			//if ( !$Chart->buildJsonFileFromCsv() ) throw new \Exception("Dataa ei voitu lukea");
			if ( !$Chart->buildJsonDataFromCsv($_FILES['datafile']) ) throw new \Exception("Dataa ei voitu lukea");
			$Chart->created_date = date(DATE_W3C);
			$Chart->created_by = $User->id;
			if ( !$Chart->save() ) throw new \Exception("Tilastoa ei voitu tallentaa");
			
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
			
			//if ( $_FILES['datafile']['type'] != 'text/csv' ) throw new \Exception("Virheellinen tiedostotyyppi! Tiedoston tulee olla CSV-tiedosto (tiedostopääte .csv)");
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
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei löytynyt!");
			
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
			if ( empty($Chart->created_date) ) throw new \Exception("Tilastoa ei löytynyt!");
			
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
	
	public function deleteChart() {
		
		
		try {
			
			$Chart = new \Lougis_chart($_REQUEST['chart_id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu ladata. Ota yhteyttä ylläpitoon.");
			
			if ( !$Chart->delete() ) throw new \Exception("Tekninen virhe! Taulukkoa ei voitu poistaa. Ota yhteyttä ylläpitoon.");
			$ChAr = array();
			$ChAr = $Chart->toArray();
			if( $ChAr['page_id'] > 0) {
				//delete page also
				$Pg = new \Lougis_cms_page($ChAr['page_id']);
				if ( empty($Pg->created_date) ) throw new \Exception('Sivun poistaminen epäonnistui: Sivua ei voitu ladata!');
				if ( $Pg->site_id != $_SESSION['site_id'] ) throw new \Exception('Sivun poistaminen epäonnistui: Virheellinen sivusto!');
				if ( !$Pg->delete() ) throw new \Exception('Sivun poistaminen epäonnistui: '.$Pg->_lastError);
			}
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
			if ( !$Pg->save() ) throw new \Exception("Sivun tallennus epäonnistui.");
			$PgArray = $Pg->toArray();
			
			if($_REQUEST['chart']['page_id'] > 0) devlog($_REQUEST['chart']);
			
			//chart
			$Chart = new \Lougis_chart($_REQUEST['chart']['id']);
			if ( empty($Chart->created_date) ) throw new \Exception("Tilaston avaus epäonnistui.");
			$Chart->setFrom($_REQUEST['chart']);
			$Chart->page_id = $PgArray['id']; //page_id of created or updated page
			$Chart->updated_date = date(DATE_W3C);
			if ( empty($Chart->title) ) throw new \Exception("Tilaston otsikko on pakollinen!");
			if ( !$Chart->updateJsonFileFields($_REQUEST['fields']) ) throw new \Exception("Tilastotiedon tallennus epäonnistui!");
			if ( !$Chart->save() ) throw new \Exception("Tilaston tallennus epäonnistui.");
			
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