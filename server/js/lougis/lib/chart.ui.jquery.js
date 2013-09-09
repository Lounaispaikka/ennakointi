/*
* 
* FUNKTIO: Lis‰‰ uusi tilasto
*
*/
function openAddChartDialog(parent_id) {
	
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
	$('#chartform').empty();
	$('#chartform').dform({
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
		return false;
	});
	
	//form submit
    var options = { 
        success:       showResponse, 
        url:       "/run/lougis/charts/uploadData/" ,
        type:      "post", 
        dataType:  "json" 
    }; 
	// bind form using 'ajaxForm' 
    $('#chartform').ajaxForm(options); 
	 
	// post-submit callback 
	function showResponse(responseText)  { 
		console.log(responseText);
		console.log(responseText.chart);
		$('#chartform').empty();
		createGrid(responseText.chart.data, responseText.chart.id);
		//jQuery('#addChartDialog').dialog('close');
	} 

// step 2
	
	function createGrid(celldata, chart_id) {
		$('#chartOhje').empty();
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
		
		
		$('#chartform').dform({
			"method" : "post",
			"html" :
				[
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
		
		
		var savedChart = [];
		
		$('.next-btn').click(function () {
			
			var datacells = tabledata.getData();
		/* 	
			//first row to fields
			fields = datacells[0];
			$.each(cellArray.fields, function(index, obj) {
				console.log(index + '; ' + obj);
			});
			console.log(cellArray.fields); */
			//first row to fields
			fields = datacells[0];
			console.log(datacells);
			var i=0;
			$.each(cellArray.fields, function(index, obj) {
				$.each(obj, function(attr, value) {
					if(attr === "name") {
						obj[attr] = fields[i];
					}
				});
				i++;
			});
			console.log(cellArray.fields);
			
			savedChart["fields"] = cellArray.fields;

			
			//data rows to data except first(=heading) and last(=new empty row)
			for(var i=1; i<datacells.length-1; i++) {
				data.push(datacells[i]);
			}
			
			savedChart["data"] = data;
			
			
			console.log("ch", savedChart);
			
			$.ajax({
				url: "/run/lougis/charts/updateDbData/", //t‰h‰n tallennusfunktio
				data: {
					"chart_data": savedChart,
					"chart_id": chart_id
					},
				dataType: 'json',
				type: 'POST',
				success: function (res) {
					if (res.result === 'ok') {
						console.log('Data saved');
					}
					else {
						console.log('Save error');
					}
				},
				error: function () {
					console.log('Save error. POST method is not allowed on GitHub Pages. Run this example on your own server to see the success message.');
				}
			});
			//katso ett‰ data saadaan arrayhin samassa muodossa kuin data_json -taulu
			
			
			return false;
		});
		
		return false;
	}
	//Perusti
	//form

// step 3

}
