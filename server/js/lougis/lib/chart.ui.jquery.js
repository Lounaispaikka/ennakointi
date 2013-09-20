/*
* 
* FUNKTIO: Lis‰‰ uusi tilasto
*
*/
function openAddChartDialog(parent_id){
	
	/*//Tab dialogin luonti
	$("#addChartDialog").tabs().dialog({
		autoOpen: false,
		width: 600,
		draggable: false,
		modal: true,
		open: function(){
			$('.ui-dialog-titlebar').hide(); // hide the default dialog titlebar
		},
		close: function(){
			$('.ui-dialog-titlebar').show(); // in case you have other ui-dialogs on the page, show the titlebar 
		},		
	}).parent().draggable({handle: ".ui-tabs-nav"}); // the ui-tabs element (#tabdlg) is the object parent, add these allows the tab to drag the dialog around with it
	// stop the tabs being draggable (optional)
	$('.ui-tabs-nav li').mousedown(function(e){
    	e.stopPropagation();
	});
	
	
	$(".cancel-btn").click(function() {
		//sulkee kaikki dialogit
		$(".ui-dialog-content").dialog("close");
		return false;		
	});
	
	*/
	
	$('#addChartDialog').dialog({
		autoOpen: false,
		modal: true,
		width: 600
	});
	$('#addChartDialog').dialog('open');
	
	newChart(parent_id);
	return false;
}

function newChart(parent_id) {
// step 1
	//Tiedoston lataus
	$('#datagrid').empty();
	$('#chartOhje').empty();
	$('#chartOhje').append('Valitse koneeltasi ladattava CSV-tiedosto ja klikkaa "Seuraava &raquo;"');
	$('#chartform_upload').empty();
	$('#chartform_table').empty();
	$('#chartform_config').empty();
	$('#chartform_preview').empty();

	$('#chartform_upload').dform({
			"method" : "post",
			"html" :
				[
					{
						"name" : "datafile",
						"type" : "file"
					},
					
					{
						"type" : "submit",
						"value" : "Seuraava",
						"class": "next-btn"
					},
					{
						"type" : "button",
						"html" : "Peruuta",
						"class": "cancel-btn"
					}
				]
	});	
	$(".cancel-btn").click(function() {
		$('#addChartDialog').dialog('close');
		
	});
	
	
	// bind form using 'ajaxForm' 
    $('#chartform').ajaxForm({
		success: 	callback1,
		url:       "/run/lougis/charts/uploadData/" ,
        type:      "post", 
        dataType:  "json"
	});
	 
	 // post-submit callback 
	function callback1(responseText)  { 
		createGrid(responseText.chart.data, responseText.chart.id);
		console.log("fuck");
		//jQuery('#addChartDialog').dialog('close');
	}
	
	return false;
}

// step 2
	
function createGrid(celldata, chart_id) {
	console.log("createGrid");
		$('#chartOhje').append('Muokkaa taulukkoa tarvittaessa.');
		
		var cellArray = $.parseJSON(celldata);
		
		var chart = [];
		var fields = [];
		var data = [];
		
		//arrayn ensimm‰inen rivi sopivaan muotoon
		$.each(cellArray.fields, function(index, obj) {
			$.each(obj, function(attr, value) {
				if(attr === "name") {
					fields.push(value);
				}
			});
		});
		chart.push(fields);
		
		//datarivit arrayhyn
		$.each(cellArray.data, function(index, obj) {
			chart.push(obj);
		});
		

		
		var container = $("#datagrid");
		container.handsontable({
			data: chart,
			minSpareRows: 1,
			colHeaders: true,
			contextMenu: true
		});
		var tabledata = container.data('handsontable');
		
		
		$('#chartform_table').dform({
			"method" : "post",
			"html" :
				[
					{
						"type" : "submit",
						"value" : "Tallenna",
						"class": "next-btn"
					},
					{
						"type" : "button",
						"html" : "Peruuta",
						"class": "cancel-btn"
					}
				]
		});	
		
		
		var savedChart = {};
		
		$('.next-btn').click(function () {
			
			var datacells = tabledata.getData();

			//first row to fields
			
			fields = datacells[0];
			var i=0;
			$.each(cellArray.fields, function(index, obj) {
				$.each(obj, function(attr, value) {
					if(attr === "name") {
						obj[attr] = fields[i];
					}
				});
				i++;
			});
			
			savedChart["fields"] = cellArray.fields;

			
			//data rows to data except first(=heading) and last(=new empty row)
			for(var i=1; i<datacells.length-1; i++) {
				data.push(datacells[i]);
			}
			
			savedChart["data"] = data;
			

			var jsonstring = JSON.stringify(savedChart);
			var jsonpa = JSON.parse(jsonstring);
			console.log("js",jsonpa);
			//nyt fields samaan muotoon kuin alunperin, t‰h‰n korjaus jatkossa? tyyppien k‰sittely puuttuu, mutta tarvitaanko edes?
			$.ajax({
				url: "/run/lougis/charts/updateDbData/", //t‰h‰n tallennusfunktio
				data: {
					"chart_data": jsonpa,
					"chart_id": chart_id
					},
				dataType: 'json',
				type: 'POST',
				success: function (res) {
					console.log(res);
					 if (res.success === true) {
						console.log('Data saved');
						configureChart(savedChart ,chart_id);
					}
					else {
						console.log('Save error');
					} 
				},
				error: function () {
					console.log('Save error. POST method is not allowed on GitHub Pages. Run this example on your own server to see the success message.');
				}
			});
			
			
			return false;
		});
		
		return false;
}
	//Perusti
	//form
	/*
	perus:
	tilston otsikko, lyhyt kuvaus, kuvaus
	
	kaavio:
	kaavion akselit
	sarakeotsikot: x vai y-akseli
	x ja y akselin otsikot ja tyyppi (kategoria vai numeerinen)
	*/
	//Tilastokuvaajan luominen
function configureChart(chartObj, chart_id) {
	console.log(chartObj);
	//pikafixin‰ extjs chart creator
	
	$('#chartform_config').dform({
		"method" : "post",
		"html" :
			[
				{
					"type": "hidden",
					"name": "chart[id]",
					"value": chart_id
				},
				{
					"type": "text",
					"name": "chart[title]",
					"caption": "Tilaston otsikko"	
				},
				{
					"type": "select",
					"name": "chart[config][type]",
					"caption": "Tyyppi",
					"options": { 
						"bar" : "Pylv‰s (vaaka)",
						"column" : "Pylv‰s (pysty)",
						"line" : "K‰yr‰",
						"pie" : "Piirakka"
					}
				},
				{
					"type": "text",
					"name": "chart[config][y_title]",
					"caption": "Y-akselin otsikko"	
				},
				{
					"type": "text",
					"name": "chart[config][x_title]",
					"caption": "X-akselin otsikko"	
				},
				{
					"type" : "submit",
					"value" : "Tallenna",
					"class": "next-btn",
					"id": "saveHc"
				},
				{
					"type" : "button",
					"html" : "Peruuta",
					"class": "cancel-btn"
				}
			]
	});
	
	$(".cancel-btn").click(function() {
		$('#addChartDialog').dialog('close');
		return false;
	});
	
	//form submit
	var options = { 
		success:       showResponse, 
		url:       "/run/lougis/charts/saveHighchartConfig/" ,
		type:      "post", 
		dataType:  "json" 
	}; 
	// bind form using 'ajaxForm' 
	$('#chartform').ajaxForm(options); 
	 
	// post-submit callback 
	function showResponse(responseText)  { 
		//$('#chartform').empty();
		createChartGraph(chart_id);
	} 

	
	
	/*
	
	
	
	kaavion legenda
	
	kuvaaja
	*/
	

// step 3

}

function createChartGraph(chart_id) {
		
	
	
	
	//global options
	
	Highcharts.setOptions({
		chart: {
			backgroundColor: {
				linearGradient: [0, 0, 500, 500],
				stops: [
					[0, 'rgb(255, 255, 255)'],
					[1, 'rgb(240, 240, 255)']
					]
			},
			borderWidth: 2,
			plotBackgroundColor: 'rgba(255, 255, 255, .9)',
			plotShadow: true,
			plotBorderWidth: 1
		}
	});
	var options = {
		chart: {
			renderTo: 'chart_container',
		},
		title: {
			text: ''
		},
		xAxis: {
			categories: [],
			title: {
				text: ''
			}
		},
		yAxis: {
			title: {
				text: ''
			}
		},
		series: []
	};
	
	//get chart data
	$.ajax({
		url: "/run/lougis/charts/getHighchart/", //t‰h‰n tallennusfunktio
		data: {
			"chart_id": chart_id
			},
		dataType: 'json',
		type: 'POST',
		success: function (res) {
			if (res.success === true) {
				//set chart options
				console.log(res.conf);
				var configs = $.parseJSON(res.conf); //parse json to object
				options.chart.type = configs.type;
				
				options.yAxis.title.text = configs.y_title;
				console.log(configs);
				console.log(res.chart);
				//pie
				if(configs.type == 'pie') {
					options.plotOptions = {};
					options.plotOptions.pie = {};
					options.plotOptions.pie.allowPointSelect = configs.plotOptions.pie.allowPointSelect;
					options.plotOptions.pie.cursor = configs.plotOptions.pie.cursor
					var obj = {};
					obj.type = configs.series.type;
					//each data to pushed to series
					obj.data = [];
					$.each(res.chart.pie_series, function(key, value) {
						var array = [];
						array.push(key);
						array.push(value);
						obj.data.push(array);
					});
					options.series.push(obj);
				
				}
				//bar, column or line
				else {
					options.xAxis.categories = res.chart.xAxis.categories;
					options.xAxis.title.text = configs.x_title;
					$.each(res.chart.pie_series, function(k,v) {
						options.series.push(res.chart.series[k]);
					});
						
				}
				var chart1 = new Highcharts.Chart(options);

			}
			else {
				console.log('Error??==#"#"4');
			} 
		},
		error: function () {
			console.log('Save error. POST method is not allowed on GitHub Pages. Run this example on your own server to see the success message.');
		}
	});
	
}