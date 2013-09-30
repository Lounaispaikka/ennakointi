<?php
/**
 * Table Definition for lougis.chart
 */
require_once 'DB/DataObject.php';

class Lougis_chart extends \Lougis\DB_DataObject_Wrapper 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'lougis.chart';                    // table name
    public $id;                              // int4(4)  not_null default_nextval%28lougis.chart_id_seq%29 primary_key
    public $title;                           // varchar(-1)  
    public $description;                     // text(-1)  
    public $original_filename;               // varchar(-1)  
    public $created_date;                    // timestamptz(8)  not_null
    public $created_by;                      // int4(4)  
    public $published;                       // bool(1)  not_null default_false
    public $updated_date;                    // timestamptz(8)  not_null default_now%28%29
    public $short_description;               // varchar(-1)  
    public $parent;                          // varchar(-1)  
    public $comments_id;                     // int4(4)  
    public $page_id;                         // int4(4)  
    public $config_json;                     // varchar(-1)  
    public $data_json;                       // varchar(-1)  

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('Lougis_chart',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
    
    public $jsonData;
    
    public $jsonDataString;
    
    public $chartConfig;
    
    public $chartConfigString;
    
    public function delete() {
	    
	    if ( !empty($this->created_date) && !empty($this->id) ) {
		    $path = $this->getChartFolder();
		    exec("rm -rf ".$path);
	    }
	    return parent::delete();
	    
    }
    
    public function toChartArray( $Data = true, $Config = true, $Request = false ) {
	    
	    $Ar = $this->toArray();
	    if ( $Data ) $Ar['data'] = $this->getChartJsonData();
	    if ( $Config ) $Ar['config'] = $this->getChartConfig();
	    if ( $Request ) $Ar['request'] = $this->getChartConfigRequest();
	    return $Ar;
	    
    }
	
	 public function dbToChartArray( $Data = true, $Config = true, $Request = false ) {
	    
	    $Ar = $this->toArray();
	    if ( $Data ) $Ar['data'] = $this->data_json;
	    if ( $Config ) $Ar['config'] = $this->config_json;
	    //if ( $Request ) $Ar['request'] = $this->getChartConfigRequest();
	    return $Ar;
	    
    }
    
    public function getChartFolder() {
    	
    	$path = PATH_CHARTS.$this->id.'/';
    	if ( !file_exists($path) ) mkdir($path, 0775, true);
    	return $path;
    
    }
    public function getDownloadFolder() {
    	
    	$path = '../../chart_csv/'.$this->id.'/';
    	return $path;
    
    }
    
    public function getChartConfigPath() {
	    return $this->getChartFolder().'chart.json';
    }
    
    public function getChartConfigRequestPath() {
	    return $this->getChartFolder().'chart-request.json';
    }
    
    public function getFileOriginalPath() {
	    return $this->getChartFolder().'original.csv';
    }
    
    public function getFileCsvPath() {
	    return $this->getChartFolder().'data.csv';
    }
    public function getDownloadCsvPath() {
	    return $this->getDownloadFolder().'data.csv';
    }
    
    public function getFileJsonPath() {
	    return $this->getChartFolder().'data.json';
    }
    
    public function getIframeCode($Width = 500, $Height = 300) {
	    
	    $url = $this->getIframeUrl($Width, $Height);
	    $iframeCode = '<iframe src="'.$url.'" border="0" style="width:'.$Width.'px;height:'.$Height.'px;margin:0;padding:0;border:0; overflow:hidden;"></iframe>';
	    return $iframeCode;
	    
    }
    public function getIframeUrl($Width = 500, $Height = 300) {
	    
	    return 'http://'.$_SERVER['HTTP_HOST'].'/tilastot/iframe/?id='.$this->id.'&w='.$Width.'&h='.$Height;
	    
    }
    
	//build highchart
	public function buildTransposedHighchart(/* $chartdata */) {
		
		//get this->data_json data as array
		$data_json = (array)json_decode($this->data_json, true);
		devlog($data_json["fields"], "e_hc");
		//transpose data_json
		$arr = array();
		$arr = $data_json["data"];
		
		$fields = array();
		$fields = $data_json["fields"];
		
		$transposed_data = array();
		function transpose($array) {
			array_unshift($array, null);
			return call_user_func_array('array_map', $array);
		}
		$transposed_data = transpose($arr);
		devlog($transposed_data, "e_hc");
		//switch values to int, float or string if necessary
		foreach ( $transposed_data as $k=>$serie ) {
			foreach ( $serie as $key=>$val ) {
				
				switch(true) {
					case ctype_digit($val): //jos stringiss‰ on vain numeroita
						$transposed_data[$k][$key] = (int)$val;
						break;
					case is_numeric($val): //jos string on numeerinen
						$transposed_data[$k][$key] = (float)$val;
						break;
					default: //jos string
						$transposed_data[$k][$key] = $val;
				}
			}		
		}
	
		$highchart = array();
		
		//for pie-chart use
		foreach($transposed_data[0] as $key=>$value) {
			$highchart["pie_series"][$value] = $transposed_data[1][$key];
		}
		devlog($transposed_data[0], "e_hc");
		devlog($transposed_data[1], "e_hc");
		devlog($highchart["pie_series"], "e_hc");
		
		
		//x-axis
		$highchart["xAxis"]["categories"] = $transposed_data[0];
		//series
		for($i = 1; $i < count($transposed_data); $i++) {
			//$highchart["series"][$i-1]["name"] = "nimi".$i;
			$highchart["series"][$i-1]["name"] = $fields[$i]["name"];
			$highchart["series"][$i-1]["data"] = $transposed_data[$i];		
			
		}
		
			
		return $highchart;
		
	}
	
	//build highchart
	public function buildHighchart() {
		
		//get this->data_json data as array
		$data_json = (array)json_decode($this->data_json, true);

		$category = $data_json['category'];
		$series = $data_json['series'];
		
		$category_name = array_shift($category);
		
		
		//switch values to int, float or string if necessary
		foreach ( $series as $k=>$serie ) {
			foreach ( $serie as $key=>$val ) {
				$val = trim($val); //remove line endings etc
				switch(true) {
					case ctype_digit($val): //if only int
						$series[$k][$key] = (int)$val;
						break;
					case preg_match("/^(\d+(?:[\.\,]\d{2})?)$/", $val): //float with commas or dots
						$series[$k][$key] = floatval(str_replace(',', '.', $val));
						break;
					default: //string value
						$series[$k][$key] = $val;
				}
			}		
		}
		
		//x-axis
		$highchart["xAxis"]["categories"] = $category;
		$highchart["xAxis"]["title"] = $category_name;
		//series
		for($i = 0; $i < count($series); $i++) {
			$highchart["series"][$i]["name"] = $series[$i][0];
			for($j = 1; $j < count($series[$i]); $j++) {
				$highchart["series"][$i]["data"][$j-1] = $series[$i][$j];
			}
		}
		
		devlog($highchart, "e_hc");
		return $highchart;
		
	}
	
	
    public function buildExtJsonChart( $chartData ) {
	    
	    $ext = array(
	    	"xtype" => "chart",
	    	"animate" => true,
	    	"shadow" => true
	    );
	    
	    if ( count($chartData["axes"]["y"]["fields"]) < 1 ) throw new \Exception("Virheellinen Y-akseli");
	    if ( count($chartData["axes"]["x"]["fields"]) < 1 ) throw new \Exception("Virheellinen X-akseli");
	    
	    if ( $chartData["legend"]["visible"] == 0 ) { 
	    	unset($chartData["legend"]);
	    } else {
	    	$ext["legend"] = $chartData["legend"];
	    }
	    /*
	    devlog($ext["legend"], 'pyry');
	    devlog($chartData["legend"], 'pyry');
	    $ext["legend"] = array_merge($ext["legend"], $chartData["legend"]);
	    devlog($ext["legend"], 'pyry');
		*/  
	    $yAxis = array(
	    	"type" => $chartData["axes"]["y"]["type"],
	    	"position" => "left",
	    	"title" => $chartData["axes"]["y"]["title"]
	    );
	    $yAxis["fields"] = ( count($chartData["axes"]["y"]["fields"]) < 2 ) ? array_shift($chartData["axes"]["y"]["fields"]) : array_values($chartData["axes"]["y"]["fields"]);
	    $xAxis = array(
	    	"type" => $chartData["axes"]["x"]["type"],
	    	"position" => "bottom",
	    	"title" => $chartData["axes"]["x"]["title"]
	    );
	    $xAxis["fields"] = ( count($chartData["axes"]["x"]["fields"]) < 2 ) ? array_shift($chartData["axes"]["x"]["fields"]) : array_values($chartData["axes"]["x"]["fields"]);
	    $ext["axes"] = array( $yAxis, $xAxis );
	    $ext["series"] = array();
	    
	    if ( count($chartData["series"]) < 1 ) throw new \Exception("Kaavion kuvaaja(t) puuttuu.");
	    
	    foreach( $chartData["series"] as $serieData ) {
		    $serie = array(
		    	"smooth" => true
		    );
		    if ( count($serieData["xField"]) < 1 || count($serieData["yField"]) < 1 ) throw new \Exception("Kuvaajan kentat puuttuvat.");
		    $serie = array_merge($serie, $serieData);
		    
			$serie["yField"] = array_values($serieData["yField"]);
		    if ( count($serie["yField"]) == 1 ) $serie["yField"] = $serie["yField"][0];
		    
			$serie["xField"] = array_values($serieData["xField"]);
		    if ( count($serie["xField"]) == 1 ) $serie["xField"] = $serie["xField"][0];
		    /*
		    if ( $serie["type"] == "stack" ) {
			    $serie["type"] = "column";
			    $serie["stacked"] = "true";
		    }
		    */
		    if ( empty($serieData["title"])  ) {
		    	$fields = $this->getChartFields();
		    	switch( true ) {
			    	case $ext["axes"][0]["type"] == "Numeric":
			    		$serie["title"] = $this->getSerieFieldTitles($fields, $ext["axes"][0]["fields"]);
			    	break;
			    	case $ext["axes"][1]["type"] == "Numeric":
			    		$serie["title"] = $this->getSerieFieldTitles($fields, $ext["axes"][1]["fields"]);
			    	break;
		    	}
		    	if ( is_array($serie["title"]) && count($serie["title"]) == 1 ) $serie["title"] = $serie["title"][0];
		    }
		    devlog($serie);
		    array_push($ext["series"], $serie);
	    }
	    return $ext;
	    
    }
    
    public function getSerieFieldTitles( $chartFields, $serieFields ) {
	    
	    if ( is_array($serieFields) ) {
		
		    $serieTitles = array();
		    foreach($serieFields as $sField) {
			    foreach($chartFields as $cField) {
				    if ( $cField->dataindex == $sField ) {
					    $serieTitles[] = $cField->name;
					    break;
				    }
			    }
		    }   
		    return $serieTitles;
		    
	    } else {
		    
		    foreach($chartFields as $cField) {
			    if ( $cField->dataindex == $serieFields ) {
				    return $cField->name;
			    }
		    }
		    
	    }
	    return $serieFields;
	    
    }
    
    public function saveChartConfig( $ExtConfig, $configRequest = null ) {
	    
	    if ( !empty($configRequest) ) {
		    $reqfile = $this->getChartConfigRequestPath();
		    file_put_contents($reqfile, json_encode($configRequest));
	    }
	    $jsonfile = $this->getChartConfigPath();
		return file_put_contents($jsonfile, json_encode($ExtConfig));
		
    }
	
	//save highchart config
	public function saveHighchartConfig( $ExtConfig, $configRequest = null ) {
	    
	    if ( !empty($configRequest) ) {
		    $reqfile = $this->getChartConfigRequestPath();
		    file_put_contents($reqfile, json_encode($configRequest));
	    }
	    $jsonfile = $this->getChartConfigPath();
		return file_put_contents($jsonfile, json_encode($ExtConfig));
		
    }
    
    public function getChartConfig() {
	    
	    if ( empty($this->chartConfig) ) $this->chartConfig = json_decode($this->getChartConfigJsonString());
	    return $this->chartConfig;
	    
    }
    
    public function getChartConfigRequest() {
	    
	    $reqfile = $this->getChartConfigRequestPath();
	    if ( !file_exists($reqfile) ) return null;
	    $reqstring = file_get_contents($reqfile);
	   	$obj = json_decode($reqstring);
	   	$rar = $this->loopObjectToRequest( $obj );
	   	unset($rar['save']);
	   	return $rar;
	   	
    }
    
    private function loopObjectToRequest( $obj, $preName = null ) {
	    
	    global $rar;
	    
	    if ( $rar == null ) $rar = array();
	    foreach( $obj as $key => $var ) {
	    	$akey = ( empty($preName) ) ? $key : $preName.'['.$key.']';
		    switch(true) {
			    case is_object($var) || is_array($var):
			    	$this->loopObjectToRequest($var, $akey);
			    break;
			    default:
			    	$rar[$akey] = $var;
			    break;
		    }
	    }
	    return $rar;
	    
    }
    
    public function getChartConfigJsonString() {
	    
	    if ( empty($this->chartConfigString) ) {
		    if ( !file_exists($this->getChartConfigPath()) ) return null;
		    $this->chartConfigString = file_get_contents($this->getChartConfigPath());
	    }
	    return $this->chartConfigString;
	    
    }
     
    public function addUploadedDatafile( $fileArray ) {
		
    	$original = $this->getFileOriginalPath();
    	$csvfile = $this->getFileCsvPath();
		$upload_dir = $this->getChartFolder();
	
		if ( move_uploaded_file($fileArray['tmp_name'], $original) ) {
			$this->original_filename = $fileArray['name'];
			return copy($original, $csvfile);
		} 
		else {
			return $false;
		}
		
    
    }
  
    public function getChartJsonData() {
	    
	    if ( empty($this->jsonData) ) $this->jsonData = json_decode($this->getChartJsonDataString());
	    return $this->jsonData;
	    
    }
    
    public function getChartJsonDataString() {
	    
	    if ( empty($this->jsonDataString) ) {
		    if ( !file_exists($this->getFileJsonPath()) ) throw new \Exception("Chart json file does not exist");
		    $this->jsonDataString = file_get_contents($this->getFileJsonPath());
	    }
	    return $this->jsonDataString;
    }
    
    public function getChartFields() {
	    
		$json = $this->getChartJsonData();
		return $json->fields;
	    
    }
    
    
    public function updateData( $dataArray ) {
	    
	    $jsonfile = $this->getFileJsonPath();
		$json = $this->getChartJsonData();
		$json->data = $dataArray;
		return file_put_contents($jsonfile, json_encode($json));
	    
    }
    
    public function updateJsonFileFields( $fieldsArray ) {
	    
	    $jsonfile = $this->getFileJsonPath();
		$json = $this->getChartJsonData();
		$json->fields = $fieldsArray;
		return file_put_contents($jsonfile, json_encode($json));
	    
    }
	
	public function buildJsonFileFromCsv(  ) {
		
		 
		$CsvFile = $this->getFileCsvPath();
		$fa = file($CsvFile);
                	
		$headers = explode(';', $fa[0]);
		$firstdata = explode(';', $fa[1]);
	
		$datatypes = array();
		
		
	// 27.3.2013 vg
	//T‰m‰ pit‰isi tehd‰ uudestaan. Nyt k‰y vain ensimm‰isen rivin l‰pi. Pit‰isi tiet‰‰ onko jollain rivill‰ float tms vaikka ekalla olisikin int tai tyhj‰
		foreach($firstdata as $cell) {
			$data = trim($cell);
			if ( strpos($data, ',') !== false ) $data = str_replace(',', '.', $data);
			switch(true) {
				//jos solu on tyhj‰. 27.3.2013 vg
				case $data == '';
					$datatypes[] = 'float';
				break;
				case is_numeric($data):
                    if ( strpos($data, '.') !== false ) {
						$datatypes[] = 'float';
					} else {
						$datatypes[] = 'int';
					}        
				break;
				case isDate($data):
					$datatypes[] = 'datetime';
				break;	
				default:
					$datatypes[] = 'string';
				break;
			}
		}
				//devlog($datatypes);
		
		$dataConfig = array();
		for($i = 0;$i < count($headers); $i++) {
			//Pist‰‰ arvauksena "vuosi". 27.3.2013 vg
			if( ($i == 0) && $headers[$i] == '') $headers[$i] = 'Vuosi';
			$dataConfig[] = array(
				"name" => trim(utf8_encode($headers[$i])),
				"type" => $datatypes[$i],
				"dataindex" => "c".$i
			);
		}
		
		$dataContent = array();
		for($i = 1;$i < count($fa); $i++) {
			$rawRow = explode(';', $fa[$i]);
                        $rawRow = array_map('trim', $rawRow);
			//$rawRow = str_getcsv($fa[$i]);
			$dataRow = array();
			foreach($datatypes as $index => $type) {
                                //devlog($type);
				switch($type) {
					case 'string':
						array_push($dataRow, strval($rawRow[$index]));
					break;
					case 'int':
						//array_push($dataRow, intval($rawRow[$index])); poistettu, koska jos joissain sarakkeen kentiss‰ on float niin katkaisee desimaalin 27.3.2013 vg
						array_push($dataRow, $rawRow[$index]);
					break;
                                        case 'float':
						$floatval = ( strpos($rawRow[$index], ',') !== false ) ? str_replace(',', '.', $rawRow[$index]) : $rawRow[$index];
						array_push($dataRow, floatval($floatval));
					break;
					case 'datetime':
						array_push($dataRow, strtotime($rawRow[$index]));
					break;
                                        default:
                                                array_push($dataRow, strval($rawRow[$index]));
                                        break;
				}
			}
			array_push($dataContent, $dataRow);
		}
		
		$json = array(
			"fields" => $dataConfig,
			"data" => $dataContent
		);
		$jsonfile = $this->getFileJsonPath();
		$encoded_json = json_encode($json);
		
		//json to db
		$this->data_json = $encoded_json;
		
		return file_put_contents($jsonfile, json_encode($json));
	    
	    
    }  
   /* transposed shit
	public function buildJsonDataFromCsv($fileArray) {
	    
	    //use directly temp file, no need to save the file to server
		$fa = file($fileArray['tmp_name']);
        
		$headers = explode(';', $fa[0]);
		$firstdata = explode(';', $fa[1]);
		
		$datatypes = array();
		devlog($firstdata);
		
	// 27.3.2013 vg
	//T‰m‰ pit‰isi tehd‰ uudestaan. Nyt k‰y vain ensimm‰isen rivin l‰pi. Pit‰isi tiet‰‰ onko jollain rivill‰ float tms vaikka ekalla olisikin int tai tyhj‰
		foreach($firstdata as $cell) {
			$data = trim($cell);
			if ( strpos($data, ',') !== false ) $data = str_replace(',', '.', $data);
			switch(true) {
				//jos solu on tyhj‰. 27.3.2013 vg
				case $data == '';
					$datatypes[] = 'float';
				break;
				case is_numeric($data):
                    if ( strpos($data, '.') !== false ) {
						$datatypes[] = 'float';
					} else {
						$datatypes[] = 'int';
					}        
				break;
				case isDate($data):
					$datatypes[] = 'datetime';
				break;	
				default:
					$datatypes[] = 'string';
				break;
			}
		}
				//devlog($datatypes);
		
		$dataConfig = array();
		for($i = 0;$i < count($headers); $i++) {
			//Pist‰‰ arvauksena "vuosi". 27.3.2013 vg
			if( ($i == 0) && $headers[$i] == '') $headers[$i] = 'Vuosi';
			$dataConfig[] = array(
				"name" => trim(utf8_encode($headers[$i])),
				"type" => $datatypes[$i],
				"dataindex" => "c".$i
			);
		}
		
		$dataContent = array();
		for($i = 1;$i < count($fa); $i++) {
			$rawRow = explode(';', $fa[$i]);
            $rawRow = array_map('trim', $rawRow);
			$dataRow = array();
			foreach($datatypes as $index => $type) {
				switch($type) {
					case 'string':
						array_push($dataRow, strval($rawRow[$index]));
					break;
					case 'int':
						array_push($dataRow, $rawRow[$index]);
					break;
                    case 'float':
						$floatval = ( strpos($rawRow[$index], ',') !== false ) ? str_replace(',', '.', $rawRow[$index]) : $rawRow[$index];
						array_push($dataRow, floatval($floatval));
					break;
					case 'datetime':
						array_push($dataRow, strtotime($rawRow[$index]));
					break;
					default:
						array_push($dataRow, strval($rawRow[$index]));
					break;
				}
			}
			array_push($dataContent, $dataRow);
		} 
		/*
		$dataContent = array();
		//all rows
		for($i = 1;$i < count($fa); $i++) {
			$rawRow = explode(';', $fa[$i]);
            $rawRow = array_map('trim', $rawRow);
			$dataRow = array();
			$datatypes = array();
			//each row
			foreach($rawRow as $cellData) {
				switch(true) {
					case is_int($cellData):
						array_push($dataRow, intval($cellData));
						break;
					case is_float($cellData):
						array_push($dataRow, floatval($cellData));
						break;
					case isDate($cellData):
						array_push($dataRow, strtotime($cellData));
						break;
					default:
						array_push($dataRow, strval($cellData));
						break;
				}
			}
			array_push($dataContent, $dataRow);
			devlog($dataContent);
		}
		*/
			/*
		//$jsonfile = $this->getFileJsonPath();
		$json = array(
			"fields" => $dataConfig,
			"data" => $dataContent
		);
		
		$encoded_json = json_encode($json);
		
		//json to db
		$this->data_json = $encoded_json;
		
		//return file_put_contents($jsonfile, json_encode($json));
	    return $encoded_json;
	    
    } 
	*/
	
	//create json data from uploaded csv data
	public function buildJsonDataFromCsv($fileArray) {
	    
	    //use directly temp file, no need to save the file to server
		$fa = file($fileArray['tmp_name']);
        
		$firstRow = explode(';', $fa[0]);
		
		$dataContent = array();
		$series = array();
		//set all the rest rows to serie rows
		for($i = 1;$i < count($fa); $i++) {
			$serieRow = explode(';', $fa[$i]);
			array_push($series, $serieRow);
		}
		$datatypes = array();
		devlog($firstRow, "e_chdata");
		devlog($series, "e_chdata");

		$json = array(
			"category" => $firstRow,
			"series" => $series
		);
		
		$encoded_json = json_encode($json);
		
		//json to db
		$this->data_json = $encoded_json;
		
		//return file_put_contents($jsonfile, json_encode($json));
	    return $encoded_json;
	    
    } 
	
	/*
	* Requires utility/parsecsv/parsecsv.lib.php -class
	*
	* Outputs csv file from chart data
	*
	*/
	public function parseCsvToFile() {
		
		require_once(PATH_SERVER.'utility/parsecsv/parsecsv.lib.php');
		
		//lis‰‰ viel‰ k‰yttˆoikeuden tarksitus
		
		$data = $this->data_json;
		$data = json_decode($data, true); //return as array
		
		//csv array headings
		$headings = array();
		foreach($data['fields'] as $field_row) {
			$headings[] = $field_row['name'];
		}
		
		//generate filename
		if($this->title !== null) {
			$filename = preg_replace('/[^A-Za-z0-9\. -]/', '', $this->title);
			$filename = str_replace(' ', '_', $filename);
			$filename = $filename.'.csv';
		}
		else $filename = "data.csv";
		
		$csv = new parseCSV();
		return $csv->output($filename, $data['data'], $headings, ';');
		
	}
}
