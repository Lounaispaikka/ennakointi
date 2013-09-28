/*
* 
* FUNKTIO: Lisää uusi uusi tilasto
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
	//dialog width and height according to window size
	var wWidth = $(window).width();
    var dWidth = wWidth * 0.8;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.8;
	
	$('#addChartDialog_file').dialog({
		autoOpen: false,
		modal: true,
		width: dWidth,
		height: dHeight,
		buttons: [
			{
				text: "Sulje",
				click: function() {
					$(this).dialog("close");
				}
				
			},
			{
				text: "Lataa tiedosto",
				click: function() {
					$('#chartform_upload').submit();
				}
			}
        ]
	});
	$('#addChartDialog_file').dialog('open');
	
	uploadCsv(parent_id);
	return false;
}

function uploadCsv(parent_id) {
// step 1
	//Tiedoston lataus
	
	$('#chartOhje').empty();
	$('#chartOhje').append('Valitse koneeltasi ladattava CSV-tiedosto ja klikkaa "Seuraava &raquo;"');
	$('#chartform_upload').empty();
	

	$('#chartform_upload').dform({
			"method" : "post",
			"html" :
				[
					{
						"name" : "datafile",
						"type" : "file",
						"class" : "lomake"
					}
				]
	});	
	
	// bind form using 'ajaxForm' 
    $('#chartform_upload').ajaxForm({
		success: 	callback1,
		url:       "/run/lougis/charts/uploadData/" ,
        type:      "post", 
        dataType:  "json"
	});
	 
	 // post-submit callback 
	function callback1(responseText)  { 
		createGrid(responseText.chart.data, responseText.chart.id, parent_id);
		console.log("chup");
		$('#addChartDialog_file').dialog('close');
	}
	
	return false;
}

// step 2
	
function createGrid(celldata, chart_id, parent_id) {
	
	//dialog width and height according to window size
	var wWidth = $(window).width();
    var dWidth = wWidth * 0.8;
    var wHeight = $(window).height();
    var dHeight = wHeight * 0.8;
	
	$('#datagrid').empty();
	$('#chartform_table').empty();
	$('#chartform_config').empty();
	$('#chartform_preview').empty();
	
	$("#addChartDialog").tabs().dialog({
		modal: true,
		width: dWidth,
		height: dHeight,
		buttons: [
			{
				text: "Peruuta",
				click: function() {
					delChart(chart_id);
					$(this).dialog("close");
					//window.location.reload(); //fix because handsontable not loading again without reload
				}
				
			},
			{
				text: "Tallenna",
				click: function() {
					var active = $(this).tabs( "option", "active" ); //get active tab
					//save data from current tab
					switch(active)
					{
						case 0:
							console.log("taulukko");
							saveGrid();
							break;
						case 1:
							console.log("asetukset");
							$("#chartform_config").submit();
							break;
						case 2:
							console.log("esikatselu");
							saveGrid();
							$("#chartform_config").submit();
							$(this).dialog("close");
							break;
					}
				}
			}
        ],
		open: function(){
			$('.ui-dialog-titlebar').hide(); // hide the default dialog titlebar
			$("#addChartDialog").tabs( "option", "disabled", [ 1, 2 ] );
		},
		close: function(){
			$('.ui-dialog-titlebar').show(); // in case you have other ui-dialogs on the page, show the titlebar 
		},		
	}).parent().draggable({handle: ".ui-tabs-nav"}); // the ui-tabs element (#tabdlg) is the object parent, add these allows the tab to drag the dialog around with it
	// stop the tabs being draggable (optional)
	$('.ui-tabs-nav li').mousedown(function(e){
    	e.stopPropagation();
	});
	
	$('#addChartDialog').dialog('open');
	
		console.log("createGrid");
		$('#chartOhje').append('Muokkaa taulukkoa tarvittaessa.');
		
		var cellArray = $.parseJSON(celldata);
		
		var chart = [];
		var fields = [];
		var data = [];
		
		//arrayn ensimm�inen rivi sopivaan muotoon
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
		
		var savedChart = {};
		
		//$('.next-btn').click(function () {
		function saveGrid() {	
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
			//nyt fields samaan muotoon kuin alunperin, tähän korjaus jatkossa? tyyppien k�sittely puuttuu, mutta tarvitaanko edes?
			$.ajax({
				url: "/run/lougis/charts/updateDbData/",
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
						configureChart(savedChart ,chart_id, parent_id);
						$("#addChartDialog").tabs( "enable" );
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
		}
		
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
function configureChart(chartObj, chart_id, parent_id) {
	console.log(chartObj);
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
					"type": "hidden",
					"name": "chart[parent_id]",
					"value": parent_id
				},
				{
					"type": "text",
					"name": "chart[title]",
					"caption": "Tilaston otsikko",
					"class" : "lomake"
				},
				{
					"type": "select",
					"name": "chart[config][type]",
					"caption": "Tyyppi",
					"options": { 
						"bar" : "Pylväs (vaaka)",
						"column" : "Pylväs (pysty)",
						"line" : "Käyrä",
						"pie" : "Piirakka"
					},
					"class" : "lomake"
				},
				{
					"type": "text",
					"name": "chart[config][y_title]",
					"caption": "Y-akselin otsikko"	,
					"class" : "lomake"
				},
				{
					"type": "text",
					"name": "chart[config][x_title]",
					"caption": "X-akselin otsikko"	,
					"class" : "lomake"
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
	$('#chartform_config').ajaxForm(options); 
	 
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

//create chart graph
//both preview and template use this
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
		credits: {
            enabled: false
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
		url: "/run/lougis/charts/getHighchart/",
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
					console.log("piechart");
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
										console.log(res.chart);

					options.xAxis.categories = res.chart.xAxis.categories;
					options.xAxis.title.text = configs.x_title;
					$.each(res.chart.series, function(k,v) {
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

//delete chart
function delChart(chart_id) {
	var req = $.ajax({
		url: "/run/lougis/charts/deleteChart/",
		data: {
			"chart_id": chart_id
			},
		dataType: 'json',
		type: 'POST'
	})
	.done(function (res) {
		console.log(res.msg);
		window.alert(res.msg);
	})
	.fail(function (res) {
		console.log(res.msg);
		window.alert(res.msg);
	});
	
}