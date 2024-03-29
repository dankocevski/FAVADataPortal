<?php
	header('Content-Type:text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us">
<head>
    <title>Fermi All-Sky Variability Analysis (FAVA)</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- D3 Plotting Library -->
    <!-- // <script type="text/javascript" src="/js/lib/dummy.js"></script> -->
    <!-- <link rel="stylesheet" type="text/css" href="/css/result-light.css"> -->
    <!-- <script type="text/javascript" src="http://d3js.org/d3.v3.js"></script> -->
    <!-- <script type="text/javascript" src="http://d3js.org/d3.v3.min.js"></script> -->
    <!-- <script type="text/javascript" src="http://d3js.org/d3.geo.projection.v0.js"></script> -->
    <!-- <script type="text/javascript" src="http://d3js.org/topojson.v1.js"></script> -->
    <script type="text/javascript" src="./js/d3.v3.js"></script>
    <script type="text/javascript" src="./js/d3.geo.projection.v0.js"></script>
    <script type="text/javascript" src="./js/topojson.v1.js"></script>

    <!-- jQuery -->
    <script type="text/javascript" src="./js/jquery-1.10.2.min.js"></script>

    <!-- Bootstrap core js -->
    <script src="./js/bootstrap.min.js"></script>

    <!-- Bootstrap core CSS -->
    <link href="./css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap theme -->
    <link href="./css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- NASA theme -->
    <link rel="stylesheet" href="./css/NASA.css">

    <!-- Highcharts -->
	<script src="./js/Highcharts/highcharts.js"></script>
	<script src="./js/Highcharts/highcharts-more.js"></script>
	<script src="./js/Highcharts/modules/exporting.js"></script>

	<!-- Javascript SVG parser and renderer on Canvas -->
	<script type="text/javascript" src="http://gabelerner.github.io/canvg/rgbcolor.js"></script> 
	<script type="text/javascript" src="http://gabelerner.github.io/canvg/StackBlur.js"></script>
	<script type="text/javascript" src="http://gabelerner.github.io/canvg/canvg.js"></script> 


	<!-- Test -->

	<style type="text/css">

		.stroke {
		  fill: none;
		  stroke: #000;
		  stroke-width: 3px;
		}

		.fill {
		  fill: #fff;
		}

		.graticule {
		  fill: none;
		  /* stroke: #777; */
		  stroke: #C0C0C0;
		  stroke-width: 0.5px;
		  stroke-opacity: 1;
		}

		.graticule.outline {
		  stroke-width: 2px;
		}

		.coordinateLabel {
		  text-anchor: end;
		  /*fill: red;*/
		  /*fill: #777;*/
		  fill: #C0C0C0;
		  opacity: 1; 
		  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;  
		  font-size: 12px;
		}

		svg.map {
		  border:1px solid black;
		  margin-top: 10px;
		  margin-left: 10px;
		}  
		   

		/*.tooltip {
		  position: absolute;
		  width: 200px;
		  height: 28px;
		  pointer-events: none;
		  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;  
		  font-size: 12px;
		}   */ 

		/* The selection tool tip */
		.tooltip_map {
		    border: solid;
		    border-radius: 5px;
		    background-color: rgba(240,240,240,.95);
		    border: 2px solid rgba(0, 0, 0, .9);
		    position: absolute;
		    width: auto;
		    height: auto;    		    
		    top: 100px;
		    box-shadow: 0 1px 2px rgba(0,0,0,.4), 0 1px 0 rgba(255,255,255,.5) inset;
		    text-align: left;
		    color: #000;
		    padding: 10px 10px 10px 10px;
		    font-size: 14px;
		    line-height: 125%;
		}

		/* The selection tool tip */
		.tooltipFixed {
		    border: solid;
		    border-radius: 5px;
		    background-color: rgba(240,240,240,.90);
		    border: 2px solid rgba(0, 0, 0, .9);
		    position: absolute;
		    width: auto;    
		    top: 100px;
		    box-shadow: 0 1px 2px rgba(0,0,0,.4), 0 1px 0 rgba(255,255,255,.5) inset;
		    text-align: left;
		    color: #000;
		    padding: 10px 10px 5px 10px;
		    font-size: 14px;
		    line-height: 125%;
		}

		#cursorcoords {
		  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;  
		  font-size: 12px;  
		  position: relative;
		  left: 17.5px;
		  top: 707.5px;
		}

		/*.overlay {
		  fill: none;
		  pointer-events: all;
		}*/

		.buttons {
		  position: absolute;
		  left: 390px;
		  top: 2767px;
/*		  position: relative;
		  left: 0px;
		  top: 100px;
		  float:left;	*/  
		}

		.CandidateSource {
		/*  fill: lightsteelblue;
		  stroke: steelblue;*/
		}

		text {
		  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;  
		}

		#footer { 
			float:left;
			padding:25px 0 10px 10px;
			width:99%}
		}


	</style>

</head>


<body id="body-plain">

	<script type="text/javascript">

		// Test

		// Defining some global variables
		var raCenter;
		var decCenter;
		var searchRadius = 24;

		var mapWidth = 1250,
		    mapHeight = 700;

		var data_flare = []
		var data_3FGL = []
		var data_2FAV = []

		var t;
		var s = 1;

		var weekNumber;
		var flareNumber;
		var flareID;

		// Lightcurve data
        var data_lightCurve;


		function median(values) {

			values.sort( function(a,b) {return a - b;} );

			var half = Math.floor(values.length/2);

			if (values.length % 2) {
				return values[half];
			} else {
				return (values[half-1] + values[half]) / 2.0;
			}
		}

		function standardDeviation(values){

			var avg = average(values);

			var squareDiffs = values.map(function(value){
				var diff = value - avg;
				var sqrDiff = diff * diff;
				return sqrDiff;
			});

			var avgSquareDiff = average(squareDiffs);

			var stdDev = Math.sqrt(avgSquareDiff);
			return stdDev;
		}

		function average(data){

			var sum = data.reduce(function(sum, value){
				return sum + value;
			}, 0);

			var avg = sum / data.length;
			return avg;
		}

		function RadiansPrintD (rad) { 
			var sign2 = "";
			if ( rad < 0.0 ) { sign2 = "-"; rad = 0.0 - rad; }

			var hh = rad * toDegrees;
			hh = hh + 0.00005; // rounding
			var h = Math.floor(hh);
			hh = hh - h; // fraction
			hh = hh * 10;
			var f1 = Math.floor (hh); // Crude but easy way to get leading zeroes in fraction
			hh = hh - f1;
			hh = hh * 10;
			var f2 = Math.floor (hh); 
			hh = hh - f2;
			hh = hh * 10;
			var f3 = Math.floor (hh);
			hh = hh - f3;
			hh = hh * 10;
			var f4 = Math.floor (hh); 
			ret = sign2 + h + "." +f1+f2+f3+f4 + "&deg;";
			return ret;
		}

		function fillSideTable(data) {

			sourceRecord = data[0];

			// Source Information
			document.getElementById('table_week').innerHTML = sourceRecord['week'];
			document.getElementById('table_flare').innerHTML = parseInt(sourceRecord['num']).toString();

			// Analysis Duration
			document.getElementById('StartTime').innerHTML = sourceRecord['tmin'];
			document.getElementById('EndTime').innerHTML = sourceRecord['tmax'];			
			// document.getElementById('StartDate').innerHTML = sourceRecord['dateStart'];
			// document.getElementById('EndDate').innerHTML = sourceRecord['dateStop'];

			// Localization Information
			document.getElementById('table_ra').innerHTML = sourceRecord['best_ra'] + '&deg;';
			document.getElementById('table_dec').innerHTML = sourceRecord['best_dec'] + '&deg;';
			document.getElementById('table_r95').innerHTML = sourceRecord['best_r95'] + '&deg;';

			// calculateGalacticCoordinates();

			if (sourceRecord['bestPositionSource'] === 'low') {
				document.getElementById('table_source').innerHTML = 'Likelihood (LE)';	
				document.getElementById('table_galb').innerHTML = sourceRecord['le_gall'] + '&deg;';
				document.getElementById('table_gall').innerHTML = sourceRecord['le_galb'] + '&deg;';
			} else if (sourceRecord['bestPositionSource'] === 'high')  {
				document.getElementById('table_source').innerHTML = 'Likelihood (HE)';	
				document.getElementById('table_galb').innerHTML = sourceRecord['he_gall'] + '&deg;';
				document.getElementById('table_gall').innerHTML = sourceRecord['he_galb'] + '&deg;';
			} else {
				document.getElementById('table_source').innerHTML = 'FAVA';					
				document.getElementById('table_galb').innerHTML = sourceRecord['gall'] + '&deg;';
				document.getElementById('table_gall').innerHTML = sourceRecord['galb'] + '&deg;';
			}

			// document.getElementById('table_galb').innerHTML = sourceRecord['gall'] + '°';
			// document.getElementById('table_gall').innerHTML = sourceRecord['galb'] + '°';

			// // Low Energy FAVA
			// document.getElementById('StartTimeLikelihoodLow').innerHTML = sourceRecord['tmin'];
			// document.getElementById('EndTimeLikelihoodLow').innerHTML = sourceRecord['tmax'];
			document.getElementById('sigmaLow').innerHTML = parseFloat(sourceRecord['sigma']).toFixed(2)+ '&sigma;';

			// // High Energy FAVA
			// document.getElementById('StartTimeLikelihoodHigh').innerHTML = sourceRecord['tmin'];
			// document.getElementById('EndTimeLikelihoodHigh').innerHTML = sourceRecord['tmax'];
			document.getElementById('sigmaHigh').innerHTML = parseFloat(sourceRecord['he_sigma']).toFixed(2) + '&sigma;';

			// Low Energy Likelihood
			document.getElementById('le_ts').innerHTML = sourceRecord['le_ts'];
			document.getElementById('le_tssigma').innerHTML = sourceRecord['le_tssigma'] + '&sigma;';
			document.getElementById('le_ra').innerHTML = sourceRecord['le_ra'] + '&deg;';
			document.getElementById('le_dec').innerHTML = sourceRecord['le_dec'] + '&deg';
			document.getElementById('le_r95').innerHTML = sourceRecord['le_r95'] + '&deg;';
			document.getElementById('le_flux').innerHTML = sourceRecord['le_flux'] + ' &plusmn; ' + sourceRecord['le_fuxerr'];
			document.getElementById('le_index').innerHTML = sourceRecord['le_index'] + ' &plusmn; ' + sourceRecord['le_indexerr'];


			// High Energy Likelihood
			document.getElementById('he_ts').innerHTML = sourceRecord['he_ts'];
			document.getElementById('he_tssigma').innerHTML = sourceRecord['he_tssigma'] + '&sigma;';
			document.getElementById('he_ra').innerHTML = sourceRecord['he_ra'] + '&deg';
			document.getElementById('he_dec').innerHTML = sourceRecord['he_dec'] + '&deg';
			document.getElementById('he_r95').innerHTML = sourceRecord['he_r95'] + '&deg';
			document.getElementById('he_flux').innerHTML = sourceRecord['he_flux'] + ' &plusmn; ' + sourceRecord['he_fuxerr'];
			document.getElementById('he_index').innerHTML = sourceRecord['he_index'] + ' &plusmn; ' + sourceRecord['he_indexerr'];

			// Associations
			// document.getElementById('favasrc').innerHTML = sourceRecord['favasrc'];
			document.getElementById('fglassoc').innerHTML = sourceRecord['fglassoc'];
			document.getElementById('assoc').innerHTML = sourceRecord['assoc'];			
		}

		// Call the flare database
		function queryFlareDB() {

			console.log('Querying the flare database...')

			// Get the week number
			// var weekNumber = document.getElementById('WeekLow').innerHTML;
			// var weekNumber_urlEncoded = encodeURIComponent(weekNumber);
			var weekNumber_urlEncoded = encodeURIComponent(weekNumber);

			// Set the request type
			var typeOfRequest = 'SourceReport';
			var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);

			// Set the request type
			var flare_urlEncoded = encodeURIComponent(flareNumber);

			// Setup the URL
			var URL = "queryDB_2FAV.php?typeOfRequest=" + typeOfRequest_urlEncoded + "&week=" + weekNumber_urlEncoded + "&flare=" + flare_urlEncoded;
			console.log(URL);

			// Perform an ajax request
			$.ajax({url: URL, success: function(responseText){

				// Parse the returned data
				data_flare = JSON.parse(responseText);

				// Fill the side table information
				fillSideTable(data_flare);

				// Set the ts map image links
				var tmin = data_flare[0]['tmin'];
				var tmax = data_flare[0]['tmax'];
				var gall = data_flare[0]['gall'];
				var galb = data_flare[0]['galb'];

				var URL_low;
				var URL_high;

				if (parseInt(tmin) < 568568618 == true) {
					URL_low = "https://s3df.slac.stanford.edu/data/fermi/FAVA/maps/" + tmin + '_' + tmax + '/tsmaps/png/tsmap_leFAVF_' + tmin + '_' + tmax + '_' + gall + '_' + galb + '.png';
					URL_high = "https://s3df.slac.stanford.edu/data/fermi/FAVA/maps/" + tmin + '_' + tmax + '/tsmaps/png/tsmap_heFAVF_' + tmin + '_' + tmax + '_' + gall + '_' + galb + '.png';
				} else {
					URL_low = "https://s3df.slac.stanford.edu/data/fermi/FAVA/maps/" + tmin + '_' + tmax + '/tsmaps/png/tsmap_leFAVF_' + tmin + '_' + tmax + '_' + gall + '_' + galb + '.png';
					URL_high = "https://s3df.slac.stanford.edu/data/fermi/FAVA/maps/" + tmin + '_' + tmax + '/tsmaps/png/tsmap_heFAVF_' + tmin + '_' + tmax + '_' + gall + '_' + galb + '.png';
				}


				$("#lowEnergyTSMap").attr("src",URL_low);
				$("#highEnergyTSMap").attr("src",URL_high);

				// Get the light curve data
				queryDB_Lightcurve();

				// Set the map coordinates
				raCenter = data_flare[0]['best_ra'];
				decCenter = data_flare[0]['best_dec'];
				flareID = data_flare[0]['flareID']
				// searchRadius = 12;

				// Draw the map
				drawMap()

				// // Replace source
				// $('img').error(function(){
				//         $(this).attr('src', 'missing.png');
				// });


				// Or, hide them
				$("#lowEnergyTSMap").error(function(){
				        $(this).hide();
				});
				$("#highEnergyTSMap").error(function(){
				        $(this).hide();
				});	

			}});			
		}

		// Call the database
		function queryDB_Lightcurve() {

			// Setting some variables
	        var time = [];

	        var nev = [];
	        var avnev = [];
	        var relflux = [];
	        var e_relflux = [];
	        var sigma = [];
	        var relflux_low = [];
	        var he_relflux_high = [];

	        var he_nev = [];
	        var he_avnev = [];
	        var he_relflux = [];
	        var e_he_relflux = [];
	        var he_sigma = [];
	        var he_relflux_low = [];
	        var he_relflux_high = [];

	        var relflux_withMET = [];
	        var e_relflux_withMET = [];
	        var he_relflux_withMET = [];
	        var e_he_relflux_withMET = [];

	        var sigma_withMET = [];
	        var he_sigma_withMET = [];

		    // Setup the URL
		    var ra = parseFloat(document.getElementById('table_ra').innerHTML);
   		    var dec = parseFloat(document.getElementById('table_dec').innerHTML);

		    var ra_urlEncoded = encodeURIComponent(ra);
		    var dec_urlEncoded = encodeURIComponent(dec);
	        var URL = "queryDB_Lightcurve.php?ra=" + ra_urlEncoded + "&dec=" + dec_urlEncoded;

	        console.log('Querying the lightcurve database...')
	        console.log(URL);

   			var contentPlaceholderLowElement = document.getElementById('contentPlaceholderLow');
			contentPlaceholderLowElement.style.display = "block";
			contentPlaceholderLowElement.innerHTML = '<img src="img/animatedCircle_black.gif" height="50">'

			var contentPlaceholderHighElement = document.getElementById('contentPlaceholderHigh');
			contentPlaceholderHighElement.style.display = "block";
			contentPlaceholderHighElement.innerHTML = '<img src="img/animatedCircle_black.gif" height="50">'


			$.ajax({url: URL, success: function(responseText){

                data_lightCurve = JSON.parse(responseText);

                // console.log('Lightcurve data recieved.')

                // time = data_lightCurve['time']

                // console.log(time);

                // nev = data_lightCurve['nev']
                // avnev = data_lightCurve['avnev']
                // relflux = data_lightCurve['relflux']
                // e_relflux = data_lightCurve['e_relflux']     
                // sigma = data_lightCurve['sigma']

                // console.log(sigma);

                // he_nev = data_lightCurve['he_nev']
                // he_avnev = data_lightCurve['he_avnev']
                // he_relflux = data_lightCurve['he_relflux']
                // e_he_relflux = data_lightCurve['e_he_relflux']     
                // he_sigma = data_lightCurve['he_sigma']

                // relflux_withMET = data_lightCurve['relflux_withMET']
                // e_relflux_withMET = data_lightCurve['e_relflux_withMET']
                // he_relflux_withMET = data_lightCurve['he_relflux_withMET']
                // e_he_relflux_withMET = data_lightCurve['e_he_relflux_withMET']

                // sigma_withMET = data_lightCurve['sigma_withMET']
                // he_sigma_withMET = data_lightCurve['he_sigma_withMET']

				$.each(data_lightCurve, function(i, datum) {

       				time.push(parseInt(datum.time));
       				nev.push(datum.nev);
       				avnev.push(datum.avnev);
       				relflux.push( (datum.nev-datum.avnev)/datum.avnev )
       				sigma.push(parseFloat(datum.sigma));

					relflux_low = (datum.nev-datum.avnev)/datum.avnev - (Math.sqrt( datum.nev)/datum.avnev)
					relflux_high = (datum.nev-datum.avnev)/datum.avnev + (Math.sqrt( datum.nev)/datum.avnev)
					e_relflux.push( [relflux_low,relflux_high] )

       				he_nev.push(datum.he_nev);
       				he_avnev.push(datum.he_avnev);
       				he_relflux.push( (datum.he_nev-datum.he_avnev)/datum.he_avnev)
       				he_sigma.push(parseFloat(datum.he_sigma));

					he_relflux_low = (datum.he_nev-datum.he_avnev)/datum.he_avnev - (Math.sqrt(datum.he_nev)/datum.he_avnev)
					he_relflux_high = (datum.he_nev-datum.he_avnev)/datum.he_avnev + (Math.sqrt(datum.he_nev)/datum.he_avnev)
					e_he_relflux.push( [he_relflux_low,he_relflux_high] )

					relflux_withMET.push( [parseInt(datum.time), (datum.nev-datum.avnev)/datum.avnev] )
					e_relflux_withMET.push( [parseInt(datum.time), relflux_low, relflux_high ])

					he_relflux_withMET.push( [parseInt(datum.time), (datum.he_nev-datum.he_avnev)/datum.he_avnev] )
					e_he_relflux_withMET.push( [parseInt(datum.time), he_relflux_low, he_relflux_high ])

					sigma_withMET.push( [parseInt(datum.time), parseFloat(datum.sigma)])
					he_sigma_withMET.push( [parseInt(datum.time), parseFloat(datum.he_sigma)] )

    			});

				document.getElementById('contentPlaceholderLow').style.display = 'none';
				document.getElementById('contentPlaceholderHigh').style.display = 'none';

				// document.getElementById('lowEnergyExport').style.cssText = 'float: right; margin:5px 0px 0px 2px; display: block';
				// document.getElementById('highEnergyExport').style.cssText = 'float: right; margin:5px 0px 0px 2px; display: block';
				document.getElementById('DownloadPanel').style.display = 'block';


				document.getElementById('RelativeFluxGT100MeV').style.display = 'block';
				document.getElementById('RelativeFluxGT100MeV_Significance').style.display = 'block';
				document.getElementById('RelativeFluxGT800MeV').style.display = 'block';
				document.getElementById('RelativeFluxGT800MeV_Significance').style.display = 'block';

				var lineWidth = 2
				var symbolShape = "square"
				var symbolRadius = 3
				
				Highcharts.setOptions({
				    chart: {
				        style: {
				            fontFamily: 'Helvetica Neue'
				        }
				    }
				});

				// RelativeFluxGT100MeV
				var chart;
				$('#RelativeFluxGT100MeV').highcharts({
					chart: {
						zoomType: 'xy',
						plotBorderWidth: lineWidth,
						plotBorderColor: '#000000'
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: [{

						plotLines: [{
						    color: 'red', // Color value
						    dashStyle: 'Dash', // Style of the plot line. Default to solid
						    value: parseFloat(document.getElementById('StartTime').innerHTML) + ((parseFloat(document.getElementById('EndTime').innerHTML)-parseFloat(document.getElementById('StartTime').innerHTML))/2), // Value of where the line will appear
						    width: 1, // Width of the line 
						    zIndex:0   
						}],

						categories: time,
						labels: {

							// formatter: function() {
							// 	return this.value + ' s';
							// },
							useHTML: true,     
							style: {
								fontSize: '14px',
								color: '#000000',
								paddingTop: '10px',
							}
						},

						min: 239859818,
						floor: 239859818,
						tickInterval: 30240000,
						// tickPixelInterval: 100,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,
			
						lineColor: '#000000',
						gridLineWidth: 0.5,
			
						title: {
							text: 'MET',
							style: {
								color: '#000000',
							}
						}
					}],
					yAxis: [{ 

						plotLines:[{
							value:0,
							color: '#000000',
							width:1,
							zIndex:0,
						}],

						labels: {

							// formatter: function() {
							// 	return this.value + '°C';
							// },

							style: {
								fontSize: '14px',
								color: '#000000',
							}
						},
			



						// tickColor: '#000000',
						// tickPosition: 'inside',
						// tickWidth: lineWidth,
						// minorTickWidth: lineWidth,
						// minorTickInterval: 'auto',
						// minorTickPosition: 'inside',
						// minorGridLineWidth: 0,
						// gridLineColor: '#ffffff',
						// gridLineWidth: 0.5,

						// tickInterval: 50,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,

						lineColor: '#000000',
						gridLineWidth: 0.5,


						lineColor: '#000000',
						lineWidth: lineWidth,
						title: {
							text: 'Relative Flux > 100 MeV',
							style: {
								color: '#000000',
							}
						}
					}],

					// tooltip: {
					// 	shared: true
					// },

					legend: {
						enabled: false
					},

					series: [{
						name: 'Relative Flux > 100 MeV',
						color: '#000000',
						type: 'scatter',
						data: relflux_withMET,
						marker: {
		            		radius: symbolRadius,
		            		symbol: symbolShape
		        		},

						tooltip: {
							pointFormat: '<span style="font-weight: normal; color: {series.color}"></span>x: <b>{point.x:.2f}</b><br>y: <b>{point.y:.2f} {point.low}</b> '
						}

					}, {
						name: 'Error',
						type: 'errorbar',
						color: '#000000',
						data: e_relflux_withMET,
						marker: {
		            		radius: symbolRadius,
		            		symbol: symbolShape
		        		},	
		        		stickyTracking: false,		
						tooltip: {
							followPointer: false,
							pointFormat: 'y-max: <b>{point.high:0.2f}</b><br>y-min: <b>{point.low:0.2f}</b>'
						}
					}]
				});

				// RelativeFluxGT100MeV_Significance	
				var chart;
				$('#RelativeFluxGT100MeV_Significance').highcharts({
					chart: {
						zoomType: 'xy',
						plotBorderWidth: lineWidth,
						plotBorderColor: '#000000'
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: [{

						plotLines: [{
						    color: 'red', // Color value
						    dashStyle: 'Dash', // Style of the plot line. Default to solid
						    value: parseFloat(document.getElementById('StartTime').innerHTML) + ((parseFloat(document.getElementById('EndTime').innerHTML)-parseFloat(document.getElementById('StartTime').innerHTML))/2), // Value of where the line will appear
						    width: 1, // Width of the line 
						    zIndex:0   
						}],

						categories: time,
						labels: {
							// formatter: function() {
							// 	return this.value + ' s';
							// },
							useHTML: true,     
							style: {
								color: '#000000',
								fontSize: '14px',
								paddingTop: '10px',
							}
						},

						min: 239859818,
						floor: 239859818,
						tickInterval: 30240000,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,
						padding: 100,

						lineColor: '#000000',
						gridLineWidth: 0.5,
			
						title: {
							text: 'MET',
							style: {
								color: '#000000',
							}
						}
					}],
					yAxis: [{ // Primary yAxis

						plotLines:[{
							value:0,
							color: '#000000',
							width:1,
							zIndex:0,
						}],

						labels: {
							// formatter: function() {
							// 	return this.value + '°C';
							// },
							style: {
								color: '#000000',
								fontSize: '14px'
							}
						},
			
						// tickInterval: 50,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,

						lineColor: '#000000',
						gridLineWidth: 0.5,
			
						title: {
							text: 'Significance',
							style: {
								color: '#000000',
							}
						}
					}],

					// tooltip: {
					// 	shared: true
					// },

					legend: {
						enabled: false
						// floating: true,
						// layout: 'vertical',
						// align: 'center',
						// verticalAlign: 'top',
						// borderWidth: 0
					},

					series: [{
						name: 'Sigma',
						color: '#000000',
						type: 'scatter',
						data: sigma_withMET,
						marker: {
	                		radius: symbolRadius,
	                		symbol: symbolShape
	            		},
						tooltip: {
							useHTML:true,
							pointFormat: '<span style="font-weight: normal; color: {series.color}"></span>x: <b>{point.x:.2f}</b><br>y: <b>{point.y:.2f}\u03C3</b> '
						}
					}]
				});

				// RelativeFluxGT800MeV
				var chart;
				$('#RelativeFluxGT800MeV').highcharts({
					chart: {
						zoomType: 'xy',
						plotBorderWidth: lineWidth,
						plotBorderColor: '#000000'
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: [{

						plotLines: [{
						    color: 'red', // Color value
						    dashStyle: 'Dash', // Style of the plot line. Default to solid
						    value: parseFloat(document.getElementById('StartTime').innerHTML) + ((parseFloat(document.getElementById('EndTime').innerHTML)-parseFloat(document.getElementById('StartTime').innerHTML))/2), // Value of where the line will appear
						    width: 1, // Width of the line 
						    zIndex:0   
						}],

						categories: time,
						labels: {

							// formatter: function() {
							// 	return this.value + ' s';
							// },
							useHTML: true,     
							style: {
								fontSize: '14px',
								color: '#000000',
								paddingTop: '10px',
							}
						},

						min: 239859818,
						floor: 239859818,
						tickInterval: 30240000,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,
			
						lineColor: '#000000',
						gridLineWidth: 0.5,
			
						title: {
							text: 'MET',
							style: {
								color: '#000000',
							}
						}
					}],
					yAxis: [{ // Primary yAxis
						    plotLines:[{
								value:0,
								color: '#000000',
								width:1,
								zIndex:0,
						    }],

						labels: {

							// formatter: function() {
							// 	return this.value + '°C';
							// },

							style: {
								fontSize: '14px',
								color: '#000000',
							}
						},
			
						// tickInterval: 50,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,

						lineColor: '#000000',
						gridLineWidth: 0.5,

			
						lineColor: '#000000',
						lineWidth: lineWidth,
						title: {
							text: 'Relative Flux > 800 MeV',
							style: {
								color: '#000000',
							}
						}
					}],

					// tooltip: {
					// 	shared: true
					// },

					legend: {
						enabled: false
					},

					series: [{
						name: 'Relative Flux > 800 MeV',
						color: '#000000',
						type: 'scatter',
						data: he_relflux_withMET,
						marker: {
		            		radius: symbolRadius,
		            		symbol: symbolShape
		        		},

						tooltip: {
							pointFormat: '<span style="font-weight: normal; color: {series.color}"></span>x: <b>{point.x:.2f}</b><br>y: <b>{point.y:.2f} {point.low}</b> '
						}

					}, {
						name: 'Error',
						type: 'errorbar',
						color: '#000000',
						data: e_he_relflux_withMET,
						marker: {
		            		radius: symbolRadius,
		            		symbol: symbolShape
		        		},	
		        		stickyTracking: false,		
						tooltip: {
							followPointer: false,
							pointFormat: 'y-max: <b>{point.high:0.2f}</b><br>y-min: <b>{point.low:0.2f}</b>'
						}
					}]
				});

				// RelativeFluxGT800MeV_Significance	
				var chart;
				$('#RelativeFluxGT800MeV_Significance').highcharts({
					chart: {
						zoomType: 'xy',
						plotBorderWidth: lineWidth,
						plotBorderColor: '#000000'
					},
					title: {
						text: null
					},
					credits: {
						enabled: false
					},
					xAxis: [{

						plotLines: [{
						    color: 'red', // Color value
						    dashStyle: 'Dash', // Style of the plot line. Default to solid
						    value: parseFloat(document.getElementById('StartTime').innerHTML) + ((parseFloat(document.getElementById('EndTime').innerHTML)-parseFloat(document.getElementById('StartTime').innerHTML))/2), // Value of where the line will appear
						    width: 1, // Width of the line 
						    zIndex:0   
						}],

						categories: time,
						labels: {
							// formatter: function() {
							// 	return this.value + ' s';
							// },
							useHTML: true,     
							style: {
								color: '#000000',
								fontSize: '14px',
								paddingTop: '10px',
							}
						},

						min: 239859818,
						floor: 239859818,
						tickInterval: 30240000,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,
						padding: 100,

						lineColor: '#000000',
						gridLineWidth: 0.5,
			
						title: {
							text: 'MET',
							style: {
								color: '#000000',
							}
						}
					}],
					yAxis: [{ // Primary yAxis

							plotLines:[{
								value:0,
								color: '#000000',
								width:1,
								zIndex:0,
						    }],

						labels: {
							// formatter: function() {
							// 	return this.value + '°C';
							// },
							style: {
								color: '#000000',
								fontSize: '14px'
							}
						},
			
						// tickInterval: 50,
						tickColor: '#000000',
						tickPosition: 'inside',
						tickWidth: lineWidth,
						minorTickWidth: lineWidth,
						minorTickInterval: 'auto',
						minorTickPosition: 'inside',
						minorGridLineWidth: 0,

						lineColor: '#000000',
						gridLineWidth: 0.5,
			
						title: {
							text: 'Significance',
							style: {
								color: '#000000',
							}
						}
					}],

					// tooltip: {
					// 	shared: true
					// },

					legend: {
						enabled: false
						// floating: true,
						// layout: 'vertical',
						// align: 'center',
						// verticalAlign: 'top',
						// borderWidth: 0
					},

					series: [{
						name: 'Sigma',
						color: '#000000',
						type: 'scatter',
						data: he_sigma_withMET,
						marker: {
	                		radius: symbolRadius,
	                		symbol: symbolShape
	            		},
						tooltip: {
							useHTML:true,
							pointFormat: '<span style="font-weight: normal; color: {series.color}"></span>x: <b>{point.x:.2f}</b><br>y: <b>{point.y:.2f}\u03C3</b> '
						}
					}]
				});

				// Calculate the analysis values
				var startTime = Math.min.apply(null, time);
				var endTime = Math.max.apply(null, time);
				console.log(sigma);
				var maximumSigmaLow = Math.max.apply(null, sigma);
				console.log(maximumSigmaLow);
				i = sigma.indexOf(maximumSigmaLow);
				maximumSigmaTimeLow = time[i];
				maximumSigmaLow = maximumSigmaLow.toFixed(2);

				var stdevLow = standardDeviation(relflux);
				stdevLow = stdevLow.toFixed(2);
				var medianLow = median(relflux);
				medianLow = medianLow.toFixed(2);


				var maximumSigmaHigh = Math.max.apply(null, he_sigma);
				i = he_sigma.indexOf(maximumSigmaHigh);
				maximumSigmaTimeHigh = time[i];
				maximumSigmaHigh = maximumSigmaHigh.toFixed(2);

				var stdevHigh = standardDeviation(he_relflux);
				stdevHigh = stdevHigh.toFixed(2);
				var medianHigh = median(he_relflux);
				medianHigh = medianHigh.toFixed(2);

				// document.getElementById('StartTimeLow').innerHTML = startTime;
				// document.getElementById('EndTimeLow').innerHTML = endTime;
				document.getElementById('maximumSigmaLow').innerHTML = maximumSigmaLow + '&sigma;';
				document.getElementById('maximumSigmaTimeLow').innerHTML = maximumSigmaTimeLow;
				document.getElementById('medianLow').innerHTML = medianLow;
				document.getElementById('standardDeviationLow').innerHTML = stdevLow;
				// document.getElementById('StartTimeHigh').innerHTML = startTime;
				// document.getElementById('EndTimeHigh').innerHTML = endTime;
				document.getElementById('maximumSigmaHigh').innerHTML = maximumSigmaHigh + '&sigma;';
				document.getElementById('maximumSigmaTimeHigh').innerHTML = maximumSigmaTimeHigh;
				document.getElementById('medianHigh').innerHTML = medianHigh;
				document.getElementById('standardDeviationHigh').innerHTML = stdevHigh;

				// calculateGalacticCoordinates();

				// var submitButtonElement = document.getElementById('submitButton');
				// submitButtonElement.innerHTML = "Submit";


			}});
		}

		// Convert RA and Dec to galactic coordinates
		function calculateGalacticCoordinates() {

			// Adopted from http://www.robertmartinayers.org/tools/coordinates.html
			// Copyright Robert Martin Ayers, 2009, 2011, 2014.  All rights reserved.

			// Define some constants			
			pi = 3.1415926536
			toDegrees = 180.0/pi;
			degrees2arcseconds = 3600.;
			hours2degrees = 360/24.

			// From J2000 to "galactic coordinates"
			// Spherical Astronomy by Green, equation 14.55, page 355
			var JtoG = new Array (
			-0.054876, -0.873437, -0.483835,
			 0.494109, -0.444830,  0.746982,
			-0.867666, -0.198076,  0.455984 );

			var radec = new Array (99.0, 99.0);

			var ra;
			var dec;

			// Getting the user ra and dec
			// if (document.getElementById('table_ra').value.length == 0) {
			// 	ra = document.getElementById('table_ra').placeholder;
			// 	dec = document.getElementById('table_dec').placeholder;
			// } else {
			// 	ra = document.getElementById('table_ra').value;
			// 	dec = document.getElementById('table_dec').value;
			// }

			ra = parseFloat(document.getElementById('table_ra').innerHTML);
			dec = parseFloat(document.getElementById('table_dec').innerHTML);

			// Converting the user supplied ra and dec from degrees to arcseconds
			globalJRA = parseFloat(ra) * hours2degrees * degrees2arcseconds;
			globalJDec = parseFloat(dec) * degrees2arcseconds;


			// Make sure that the coordinates make sense
			// if ( (globalJRA >= 1296000) || (globalJRA < 0) ) 
			// {  
			//     return 0; // Acting like exit 
			// }
			// if ( (globalJDec > 324000) || (globalJDec < -324000) ) 
			// { 
			//     return 0; // Acting like exit 
			// }

			var radec1 = new Array ( (globalJRA/3600.0) / toDegrees, 
			(globalJDec/3600.0) / toDegrees );

			radec = radec1;
			matrix = JtoG;

			var r0 = new Array ( 
			Math.cos(radec[0]) * Math.cos(radec[1]),
			Math.sin(radec[0]) * Math.cos(radec[1]),
			Math.sin(radec[1]) );

			var s0 = new Array (
			r0[0]*matrix[0] + r0[1]*matrix[1] + r0[2]*matrix[2], 
			r0[0]*matrix[3] + r0[1]*matrix[4] + r0[2]*matrix[5], 
			r0[0]*matrix[6] + r0[1]*matrix[7] + r0[2]*matrix[8] ); 

			var r = Math.sqrt ( s0[0]*s0[0] + s0[1]*s0[1] + s0[2]*s0[2] ); 

			var result = new Array ( 0.0, 0.0 );
			result[1] = Math.asin ( s0[2]/r ); // New dec in range -90.0 -- +90.0 
			// or use sin^2 + cos^2 = 1.0  
			var cosaa = ( (s0[0]/r) / Math.cos(result[1] ) );
			var sinaa = ( (s0[1]/r) / Math.cos(result[1] ) );
			result[0] = Math.atan2 (sinaa,cosaa);

			if ( result[0] < 0.0 ) {
				result[0] = result[0] + pi + pi;
			}

			document.getElementById('table_galb').innerHTML = RadiansPrintD(result[0]);
			document.getElementById('table_gall').innerHTML = RadiansPrintD(result[1]);
		}

		function update() {

			document.getElementById('RelativeFluxGT100MeV').style.display = 'none';
			document.getElementById('RelativeFluxGT100MeV_Significance').style.display = 'none';
			document.getElementById('RelativeFluxGT800MeV').style.display = 'none';
			document.getElementById('RelativeFluxGT800MeV_Significance').style.display = 'none';

			var submitButtonElement = document.getElementById('submitButton');
			submitButtonElement.innerHTML = "Loading...";

			var contentPlaceholderLowElement = document.getElementById('contentPlaceholderLow');
			contentPlaceholderLowElement.style.display = "block";
			contentPlaceholderLowElement.innerHTML = '<img src="img/animatedCircle_black.gif" height="50">'

			var contentPlaceholderHighElement = document.getElementById('contentPlaceholderHigh');
			contentPlaceholderHighElement.style.display = "block";
			contentPlaceholderHighElement.innerHTML = '<img src="img/animatedCircle_black.gif" height="50">'


			// Call the database
			// queryDB()
		}

	  	function drawMap() {

		    // console.log(data_json[0])
		    // data = [
		    //   {
		    //     name: "Candidate Source",
		    //     ra: raCenter,
		    //     dec: decCenter,
		    //     error: 10.5    
		    //   }
		    // ]

		    var width = mapWidth,
		        height = mapHeight;

		    var projection = d3.geo.modifiedStereographic()
		        .coefficients("gs48")
		        .clipAngle(55)
		        .scale(2000)
		        .translate([width / 2, height / 2])
		        .rotate([ raCenter, decCenter*-1])
		        .precision(.1);

		    // var projection = d3.geo.aitoff()
		    //     .scale(width / 2.02 / Math.PI)
		    //     .translate([width / 2, height / 2])
		    //     .precision(0.1);

		    // var projection = d3.geo.satellite()
		    //     .translate([width / 2, height / 2])
		    //     .distance(0)
		    //     .scale(1000)
		    //     // .rotate([0,0])
		    //     // .center([45 + 4.575, 0 + 2.85])
		    //     .center([0 , 80])      
		    //     .tilt(0)
		    //     // .clipAngle(Math.acos(1 / 1.1) * 180 / Math.PI - 1e-6)
		    //     .precision(.1);

		    // var dx = 100,  
		    //     dy = 100;

		    // var projection = d3.geo.orthographic()
		    //     .scale(5000)
		    //     .translate([width / 2, height / 2])
		    //     // .clipAngle(45)
		    //     .center([1 , 85])      
		    //     // .clipExtent( [[dx, dy], [width-dx,height-dy]] )
		    //     .precision(.1);



		    // add the tooltip area to the webpage
		    var tooltip_map = d3.select("body").append("div")
		        .attr("class", "tooltip_map")
		        .style("opacity", 0)

		    // add the fixed tooltip area to the webpage
		    var tooltipFixed = d3.select("body").append("div")
		        .attr("class", "tooltipFixed")
		        .style("opacity", 0)

		    var zoom = d3.behavior.zoom()
		        .scaleExtent([1, 50])
		        .on("zoom", zoomed)

		    var path = d3.geo.path()
		        .projection(projection);
		        
		    var svg = d3.select("#FlareMap").append("svg")
		        .attr('class', 'map')
		        .attr('xmlns', 'http //www.w3.org/2000/svg')
		        .attr("width", width)
		        .attr("height", height)
		        .call(zoom)
		        // .on("dblclick.zoom", null);
		        .on("mousedown", function(d) {
		          tooltip_map.transition()
		             .duration(500)
		             .style("opacity", 0)                 // Fade the tooltip
		             .each("end", function(d) {           // Move the tooltip out of the way once it's faded away
		                tooltip_map.style("left", "0px")
		                tooltip_map.style("top", "0px")
		             } );
		          })

		    // Set the labels to be visible by default
		    labelsVisible = true;


		    var customSymbolTypes = d3.map({
		      'thin-x': function(size) {
		        size = Math.sqrt(size);
		        return 'M' + (-size/2) + ',' + (-size/2) +
		          'l' + size + ',' + size +
		          'm0,' + -(size) + 
		          'l' + (-size) + ',' + size;
		      },
		      'smiley': function(size) {
		        size = Math.sqrt(size);
		        var pad = size/5;
		        var r = size/8;
		        return 'M' + ((-size/2)+pad) + ',' + (-size/2) +
		        ' m' + (-r) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (r * 2) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (-(r * 2)) + ',0' +
		          
		        'M' + ((size/2)-pad) + ',' + (-size/2) +
		        ' m' + (-r) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (r * 2) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (-(r * 2)) + ',0' +
		          
		        'M' + (-size/2) + ',' + ((size/2)-(2*pad)) +
		        'q' + (size/2) + ',' + (pad*2) + ' ' + size + ',0';
		      }
		    });

		    d3.svg.customSymbol = function() {
		      var type,
		          size = 64;
		      function symbol(d,i) {
		        return customSymbolTypes.get(type.call(this,d,i))(size.call(this,d,i));
		      }
		      symbol.type = function(_) {
		        if (!arguments.length) return type;
		        type = d3.functor(_);
		        return symbol;
		      };
		      symbol.size = function(_) {
		        if (!arguments.length) return size;
		        size = d3.functor(_);
		        return symbol;
		      };
		      return symbol;
		    };

		    function getSymbol(type, size) {
		      size = size || 64;
		      if (d3.svg.symbolTypes.indexOf(type) !== -1) {
		        return d3.svg.symbol().type(type).size(size)();
		      } else {
		        return d3.svg.customSymbol().type(type).size(size)();
		      }
		    }

		    // var data = ['circle', 'diamond', 'thin-x', 'smiley', 'square'];

		    // var symb = svg.selectAll('.symb')
		    //   .data(data)
		    //   .enter().append('path')
		    //     .attr('transform', function(d,i) {
		    //       return 'translate(' + (30 + (i * 30)) + ',150)';
		    //     })
		    //     .attr('d', function(d) {
		    //       return getSymbol(d, 128);
		    //     })
		    //     .attr('fill', 'transparent')
		    //     .attr('stroke', '#333');        

		    g = svg.append("g");

		    g.append("defs").append("path")
		            .datum({
		                type: "Sphere"
		            })
		            .attr("id", "sphere")
		            .attr("d", path);

		    g.append("use")
		            .attr("class", "stroke")
		            .attr("xlink:href", "#sphere");

		    g.append("use")
		            .attr("class", "fill")
		            .attr("xlink:href", "#sphere"); 


		    // Graticule options & label format
		    var decimal_labels = true,
		        showGrat = false,
		        showGratLabel = true,
		        step_ra = 1,
		        step_dec = 1;

		    // Draw the coordinate grid and associated labels
		    labelGraticule(true,true);

		     // Current cursor position
		     //  -- When entering the map --
		    g.on('mouseenter', function () {

		        g.on('mousemove', function () {

		            // Get (x,y) mouse coordinates (==svg coords)
		            var coordinates = d3.mouse(this);
		            x = coordinates[0];
		            y = coordinates[1];

		            // Calculate (long,lat) mouse coordinates (==world coords)
		            var inverse_coordinates = projection.invert([x, y]);
		            var c_long, c_lat;

		            if (inverse_coordinates !== undefined) {
		                c_long = -1 * inverse_coordinates[0]; 
		                // *-1 is to get "conventional" RA-axis representation, i.e. 180° <-- 0°/360° <-- 180°
		                // By default, D3.js orientation is: 180° --> 360°/0° --> 180°

		                // x-axis wrapping
		                if (c_long < 0) {
		                    c_long = c_long + 360.0;
		                }
		                c_long = parseFloat(c_long).toFixed(2);

		                c_lat = parseFloat(inverse_coordinates[1]).toFixed(2);

		                    d3.select("#cursorcoords")
		                        .html("RA: "+c_long+"&deg;, Dec: "+c_lat+"&deg;");
		            }
		        });
		    });

		    // Current cursor position
		    // -- When leaving the map --
		    g.on('mouseleave', function () {
		            d3.select("#cursorcoords")
		                .html("RA: -- ;, Dec: -- ");
		    });


		    // CandidateSource
		    var candidateSource = g.selectAll("CandidateSource")
		      .data(data_flare)
		      .enter().append("circle")
		      .attr("class", "CandidateSource")
		      .attr("r", 4)
		      .style("fill", "blue") 
		      .style("opacity", .75)
		      // .style("stroke-width", "1.5px")
		      .attr("transform", function(d) {
		        return "translate(" + projection([
		          d.best_ra * -1,
		          d.best_dec
		        ]) + ")";
		      })

		      .on("mouseover", function(d) {

		      		// Generate the simbad and ned links
					var simbadLink = "<a href=\"http://simbad.u-strasbg.fr/simbad/sim-coo?Coord=" + d.best_ra + "+" + d.best_dec + "&Radius=" + d.best_r95 * 60 + "\" target=\"_blank\">Simbad Search</a>";
					var nedLink = "<a href=\"http://ned.ipac.caltech.edu/cgi-bin/nph-objsearch?search_type=Near+Position+Search&lon=" + d.best_ra + "d&lat=" + d.best_dec + "&Radius=" + d.best_r95 * 60 + "\" target=\"_blank\">NED Search</a>";

					// Generate the tooltip contents
					var innerHTML =  'FAVA_' + d.flareID + '<BR>MET: ' + d.tmin + '<BR>RA: ' + d.best_ra + ', Dec: ' + d.best_dec + '<BR>Error: +/-' + d.best_r95 + '<BR>Source: ' + document.getElementById('table_source').innerHTML + '<BR>' + simbadLink + ' | ' + nedLink; 


					// Select the tooltip area to the webpage
					var tooltip_map = d3.select(".tooltip_map")

				    var offsetTop = $("#FlareMap").offset().top;
				    var offsetLeft = $("#FlareMap").offset().left;

					tooltip_map.transition()
					   .duration(200)
					   .style("opacity", .9);
					tooltip_map.html( innerHTML )
					   .style("left", event.pageX - 85 + "px")
					   .style("top", event.pageY - 150 + "px")

					})

		      // .on("mouseout", function(d) {
		      //     tooltip_map.transition()
		      //        .duration(500)
		      //        .style("opacity", 0)                 // Fade the tooltip
		      //        .each("end", function(d) {           // Move the tooltip out of the way once it's faded away
		      //           tooltip_map.style("left", "0px")
		      //           tooltip_map.style("top", "0px")
		      //        } );
		      //     })


		    // CandidateSource Error  
		    g.selectAll("CandidateSourceError")
		      .data(data_flare)
		      .enter().append("circle", "error")
		      .attr("class", "CandidateSourceError")
		      .style("fill", "none")
		      .style("stroke", "red")
		      .style("stroke-dasharray", ("3, 3"))
		      .attr("r", function(d) { return d.best_r95*34 })
		      .attr("transform", function(d) {
		        return "translate(" + projection([
		          d.best_ra * -1,
		          d.best_dec
		        ]) + ")";
		      });

		    // CandidateSource Label 
		    g.selectAll("Labels")
		      .data(data_flare)
		      .enter().append("text")
		      .attr("class", "Labels")
		      .style("text-anchor", "right")
		      .style("opacity", .9)
		      .attr("dx", "0.5em")
		      .attr("dy", "-0.5em")      
		      // .style("font-size", "1em")
		      // .attr("x", function(d) { return projection([d.location.longitude,d.location.latitude])[0]+10 })
		      // .attr("y", function(d) { return projection([d.location.longitude,d.location.latitude])[1]-10 })
		      .attr("transform", function(d) {
		        return "translate(" + projection([
		          d.best_ra * -1,
		          d.best_dec
		        ]) + ")";
		      })
		      .text(function(d) { return 'FAVA_' + d.flareID} )



		    // Handles zoom/pan
		    function zoomed() {

					// Select the tooltip area to the webpage
					var tooltip_map = d3.select(".tooltip_map")

		    		// Hide any tooltips
					tooltip_map.style("left", "0px")
					tooltip_map.style("top", "0px")
					tooltip_map.style("opacity", 0)   


		            g.attr("transform", "translate(" + zoom.translate() + ")" + "scale(" + zoom.scale() + ")" );

		            t = zoom.translate(),
		            s = zoom.scale();
		            t[0] = Math.min(width / 2 * (s - 1), Math.max(width / 2 * (1 - s), t[0]));
		            t[1] = Math.min(height / 2 * (s - 1), Math.max(height / 2 * (1 - s), t[1]));
		            zoom.translate(t);
		            
		            // text scaling
		            g.selectAll("text").attr("font-size", function () {
		                return 1/s+"em";        
		            });
		            
		            // text label scaling
		            g.selectAll("text.Label").attr("font-size", function () {
		                return 1/s+"em";        
		            });

		            // stroke-width scaling
		            g.style("stroke-width", 1/s).attr("transform", "translate(" + t + ")scale(" + s + ")");

		            // g.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");

		            g.selectAll("circle.CandidateSource").attr("stroke-width", function () { return 2/s; });


		            // Filled Circles
		            g.selectAll("circle")
		                .filter(function(d) {
		                    if (d3.select(this).style("fill") == "none") {
		                        d3.select(this).attr("r", function(d){
		                            return d3.select(this).attr("r")
		                        })
		                    } else {
		                        d3.select(this).attr("r", function(d){
		                            return (4. / zoom.scale());
		                        })                    
		                    }
		                })

		           // 2FAV Sources
		            g.selectAll("path._2FAVSources").attr('d', function(d) {
		                return getSymbol( 'thin-x', (75.0/zoom.scale())); 
		                // return getSymbol( 'thin-x', (75.0/(d3.event.scale*2)));        
		            })
		                
		            // Error Circles  
		            g.selectAll("circle.CandidateSourceError").style("stroke-dasharray", (3/zoom.scale()  +"," + 3/zoom.scale()))          
		            g.selectAll("circle._2FAVSourceError").style("stroke-dasharray", (3/zoom.scale()  +"," + 3/zoom.scale()))

		            g.select("#graticule").style("stroke-width", 1 / s);    

		    }

		        
		    // from astro.coordinates.js of slowe-astro.js
		    function deg2dms(a,bounds){
		        var d = parseFloat(a);

		        var sign = (d < 0) ? -1 : 1;
		        a = Math.abs(d);
		        var deg = Math.floor(a);
		        var min = Math.floor((a-deg)*60);
		        var sec = (a-deg-min/60)*3600;
		        var second = (sec < 10) ? "0"+sec : ""+sec;
		        if(second.length > 5) second = second.substring(0,5);
		        return { degrees: d, dms: [sign*deg, min, sec], string: ((sign < 0) ? "-" : "+")+((deg < 10) ? "0"+deg : deg)+':'+((min < 10) ? "0"+min : min)+':'+second,  shortstring: ((sign < 0) ? "-" : "+")+((deg < 10) ? "0"+deg : deg)+':'+((min < 10) ? "0"+min : min) };
		    }

		    function deg2hms(a,bounds){
		        var d = parseFloat(a);

		        a = d/15.0;
		        var hrs = Math.floor(a);
		        var min = Math.floor((a-hrs)*60);
		        var sec = Math.round(100*(a-hrs-min/60)*3600)/100;
		        if(sec < 0.000001) sec = 0;
		        return { degrees: d, hms: [hrs, min, sec], string: ((hrs < 10) ? "0"+hrs : hrs)+':'+((min < 10) ? "0"+min : min)+':'+((sec < 10) ? "0"+sec : ""+sec), shortstring: ((hrs < 10) ? "0"+hrs : hrs)+':'+((min < 10) ? "0"+min : min) };
		    }

		    function dms2deg(inp,bounds){
		        if(typeof inp==="number" || (!inp.indexOf(':') > 0 && !inp.indexOf(' ') > 0)) return deg2dms(inp,bounds);
		        var bits = (inp.indexOf(':') > 0) ? inp.split(':') : ((inp.indexOf(' ') > 0) ? inp.split(' ') : [inp,"0","0"]);
		        var deg = parseFloat(bits[0]);
		        var sign = (deg < 0) ? -1 : 1;
		        deg = Math.abs(deg);
		        var min = parseFloat(bits[1]);
		        var sec = parseFloat(bits[2]);
		        if(sec < 0.000001) sec = 0;
		        var t = sign*(deg + min/60 + sec/3600);
		        return { degrees: t, dms: [deg, min, sec], string: inp };
		    }

		    function hms2deg(inp,bounds){
		        if(typeof inp==="number" || (!inp.indexOf(':') > 0 && !inp.indexOf(' ') > 0)) return deg2hms(inp,bounds);
		        var bits = (inp.indexOf(':') > 0) ? inp.split(':') : ((inp.indexOf(' ') > 0) ? inp.split(' ') : [inp,"0","0"]);
		        var hrs = parseFloat(bits[0]);
		        var min = parseFloat(bits[1]);
		        var sec = parseFloat(bits[2]);
		        var t = (hrs + min/60 + sec/3600);
		        return { degrees: t*15, hms: [hrs, min, sec], string: inp };
		    }

		    // Draw graticule with/without labels
		    function labelGraticule(showGrat,showGratLabel) {
		        // showGrat: display graticule lines
		        // showGratLabel: display graticule labels

		        // Remove previous graticule (and labels) [if exist(s)]
		        if (! g.select("#graticule").empty()) g.select("#graticule").remove();
		        if (! g.selectAll("text").empty()) g.selectAll("text").remove();
		            
		        // Label format (implies graticule steps for more readability)
		        decimal_labels ? step_ra = 5 : step_ra = 5;
		        decimal_labels ? step_dec = 5 : step_dec = 5;
		        
		        // Create graticule
		        graticule = d3.geo.graticule()
		        .extent( [[-180, -85], [180, 85 + 1e-6]] )  // Limit the grid to +/- 85° in dec 
		        .step([step_ra,step_dec])      // graticule steps (default 10,10)
		        .precision(1.0)                // graticule precision (° ; default 2.5°) // NB: only useful on x-axis: building of parallels thanks to arcs of great circles
		        
		        
		        // Insert graticule
		        if (showGrat){
		            g.insert("path")
		                .datum(graticule)
		                .attr("class", "graticule")
		                .attr("id", "graticule")
		                .attr("d", path)
		                .call(zoom);
		        }
		        
		        // Insert labels (only if graticule is displayed)
		        if (showGrat && showGratLabel){
		            
		            g.selectAll('text')
		            .data(graticule.lines())
		            .enter()
		            .append("text")
		            .html(function(d) {    // label text

		                if ((d.coordinates[0][0] == d.coordinates[1][0])) { // meridian
		                    if (-1*d.coordinates[0][0] >= 0) {
		                        return (decimal_labels ? -1*d.coordinates[0][0].toString()+'&deg;' : deg2hms(-1*d.coordinates[0][0]).shortstring);
		                    }
		                    else {
		                         return (decimal_labels ? (-1*d.coordinates[0][0]+360).toString()+'&deg;' : deg2hms(-1*d.coordinates[0][0]+360).shortstring);               
		                    }
		                }
		                else if (d.coordinates[0][1] == d.coordinates[1][1] && d.coordinates[0][1] != 0) {   // parallele (NB: special placement for O°)
		                    return (decimal_labels ? d.coordinates[0][1].toString()+'&deg;' : deg2dms(d.coordinates[0][1]).shortstring);
		                }
		            })

		            .attr("class","coordinateLabel")
		            .attr("style", function(d) { return "text-anchor: start"; })
		            // .attr("dx", function(d) { return (d.coordinates[0][1] == d.coordinates[1][1]) ? 15 : 15; })
		            // .attr("dy", function(d) { return (d.coordinates[0][1] == d.coordinates[1][1]) ? -15 : -15; })
		            .attr("dx", function(d) { return (d.coordinates[0][0] == d.coordinates[1][0]) ? 8 : 10; })      // RA label dy offset : Dec label dx offset
		            .attr("dy", function(d) { return (d.coordinates[0][1] == d.coordinates[1][1]) ? 17.5 : -10; })     // Dec label dy offset : RA label dx offset
		            .attr('transform', function(d) {       // label placement

		                <?php
		                  if ( (isset($_GET['ra'])) ) {             
		                    $ra = floatval($_GET['ra']);
		                    $ra = htmlspecialchars($ra, ENT_QUOTES, 'UTF-8');
		                    $raSetString = "var raCenter = $ra;";
		                    echo $raSetString;
		                  }

		                  if ( (isset($_GET['dec'])) ) {             
		                    $dec = floatval($_GET['dec']);
		                    $dec = htmlspecialchars($dec, ENT_QUOTES, 'UTF-8');
		                    $decSetString = "var decCenter = $dec;";
		                    echo $decSetString;
		                  }
		                ?>    

		                // RA Label
		                if (d.coordinates[0][0] == d.coordinates[1][0]){
		                	decLabel = (parseFloat(decCenter) + 6).toString()
		                    return ('translate(' + projection([d.coordinates[0][0],decLabel])[0] + ',' + projection([d.coordinates[0][0],decLabel])[1] + ') rotate(-90)')
		                }

		                // Dec Label
		                else if (d.coordinates[0][1] == d.coordinates[1][1]) {
		                  return ('translate(' + projection([-1*raCenter-20,d.coordinates[0][1]])[0] + ',' + projection([-1*raCenter-20,d.coordinates[0][1]])[1] + ') rotate(0)')

		                }
		            });

		        // Special labels: dec=-90,0,90° and ra=180°
		        g.append("html")
		            .text(function(){ return (decimal_labels ? '-90&deg;' : '-90:00'); })
		            .attr("class","coordinateLabel")
		            .attr("style","text-anchor: middle;")
		            .attr("dx",0)
		            .attr("dy",10)
		            .attr("transform", function() {
		                return ('translate(' + projection([-180,-90])[0] + ',' + projection([-180,-90])[1] + ')')
		            });

		         g.append("html")
		            .text(function(){ return (decimal_labels ? '0&deg;' : '+00:00'); })
		            .attr("class","coordinateLabel")
		            .attr("style","text-anchor: end;")
		            .attr("dx",25)
		            .attr("dy",17.5)
		            .attr("transform", function() {
		                return ('translate(' + projection([-1*raCenter-10,0])[0] + ',' + projection([0,0])[1] + ')')
		            });

		        g.append("html")
		            .text(function(){ return (decimal_labels ? '90&deg;' : '+90:00'); })
		            .attr("class","coordinateLabel")
		            .attr("style","text-anchor: middle;")
		            .attr("dx",0)
		            .attr("dy",-5)
		            .attr("transform", function() {
		                return ('translate(' + projection([-180,90])[0] + ',' + projection([-180,90])[1] + ')')
		            });

		        // g.append("text")
		        //     .text(function(){ return (decimal_labels ? '180°' : '12:00'); })
		        //     .attr("class","coordinateLabel")
		        //     .attr("style","text-anchor: middle;")
		        //     .attr("dx",0)
		        //     .attr("dy",-2)
		        //     .attr("transform", function() {
		        //         return ('translate(' + projection([180,0])[0] + ',' + projection([180,0])[1] + ') rotate(-90)')
		        //             // Warning : When rotating the text, its own coordinates also rotates in the same way.
		        //             // That directly impacts the translation that comes juster after (axis-direction change).
		        //     });
		        }
		    }


		    function interpolateZoom (translate, scale) {
		        var self = this;
		        return d3.transition().duration(350).tween("zoom", function () {
		            var iTranslate = d3.interpolate(zoom.translate(), translate),
		                iScale = d3.interpolate(zoom.scale(), scale);
		            return function (t) {
		                zoom
		                    .scale(iScale(t))
		                    .translate(iTranslate(t));
		                zoomed();
		            };
		        });
		    }

		    function zoomClick() {
		        console.log('zoomClicked!')

		        var clicked = d3.event.target,
		            direction = 1,
		            factor = 1.0,
		            target_zoom = 1,
		            center = [width / 2, height / 2],
		            extent = zoom.scaleExtent(),
		            translate = zoom.translate(),
		            translate0 = [],
		            l = [],
		            view = {x: translate[0], y: translate[1], k: zoom.scale()};

		        d3.event.preventDefault();
		        direction = (this.id === 'zoom_in') ? 1 : -1;

		        if (direction == 1) {
		            target_zoom = zoom.scale() * (1 + factor);
		        } else {
		            target_zoom = zoom.scale() / (1 + factor);
		        }

		        if (target_zoom < extent[0] || target_zoom > extent[1]) { return false; }

		        translate0 = [(center[0] - view.x) / view.k, (center[1] - view.y) / view.k];
		        view.k = target_zoom;
		        l = [translate0[0] * view.k + view.x, translate0[1] * view.k + view.y];

		        view.x += center[0] - l[0];
		        view.y += center[1] - l[1];

		        interpolateZoom([view.x, view.y], view.k);

		    }


		    // function toggleLabels() {

		    //     console.log('toggleLabels clicked!')

		    //     // text label scaling
		    //     // g.selectAll("text.Label")
		    //     //     .style("opacity", 0);

		    //     // text label scaling
		    //     g.selectAll("text, #Labels")
		    //         .transition()
		    //         .duration(200)
		    //         .style("opacity", function () {
		    //             return 0;        
		    //         });


		    // }

		    function fake() {
		    	return
		    }


		    function toggleLabels() {

		        console.log('toggleLabels clicked!')

		        if (labelsVisible == true) {

		            labelsVisible = false;

		            // text label scaling
		            g.selectAll("text, #Labels")
		                .transition()
		                .duration(200)
		                .style("opacity", function () {
		                    return 0;        
		                });

		        } else {

		            labelsVisible = true;

		            // text label scaling
		            g.selectAll("text, #Labels")
		                .transition()
		                .duration(200)
		                .style("opacity", function () {
		                    return 0.9;        
		                });
		        }






		    }


		    function exportMap() {

				var svg = document.querySelectorAll('svg')[4]
				var svgData = new XMLSerializer().serializeToString( svg );

				var canvas = document.createElement( "canvas" );
				var ctx = canvas.getContext( "2d" );

				var img = document.createElement( "img" );
				img.setAttribute( "src", "data:image/svg+xml;base64," + btoa( svgData ) );


				ctx.drawImage( img, 0, 0 );
				var img_new  = canvas.toDataURL("image/png");
				document.write('<img src="'+img_new+'"/>');

		    }



		    // function toggleLabels() {

		    //     console.log('toggleLabels clicked!')

		    //     // text label scaling
		    //     // g.selectAll("text.Label")
		    //     //     .style("opacity", 0);

		    //     // text label scaling
		    //     g.selectAll("text, #Labels")

		    //         .filter(function(d) {
		    //             if (d3.select(this).style("opacity") == 0) {
		    //                 d3.select(this).transition()
		    //                 .duration(200)
		    //                 .style("opacity", function () {
		    //                     return 0.9;        
		    //                 })
		    //             } else {
		    //                 d3.select(this).transition()
		    //                 .duration(200)
		    //                 .style("opacity", function () {
		    //                     return 0;        
		    //                 })
		    //             }
		    //         }

		    // }

		    function zoomStart(){
		        console.log("ZOOM START");
		    }
		    function zoomEnd(){
		        console.log("ZOOM END");
		    }

		    // Add the buttons
		    d3.selectAll('.button, #zoom_in').on('click', zoomClick);
		    d3.selectAll('.button, #zoom_out').on('click', zoomClick);
		    d3.selectAll('.button, #LabelToggle').on('click', toggleLabels);
		    d3.selectAll('.button, #ExportButton').on('click', exportMap);


		    // Update the data
		    queryDB_2FAV(raCenter, decCenter, searchRadius)
		    queryDB_3FGL(raCenter, decCenter, searchRadius)
		}

		function updateFlareData() {

		    var svg = d3.select("svg")
		    var g = svg.select('g')

		    var width = mapWidth,
		        height = mapHeight;

		    var projection = d3.geo.modifiedStereographic()
		        .coefficients("gs48")
		        .clipAngle(55)
		        .scale(2000)
		        .translate([width / 2, height / 2])
		        .rotate([ raCenter, decCenter*-1])
		        .precision(.1);


		    // Set the labels to be visible by default
		    labelsVisible = true;

		    // Select the tooltip area to the webpage
		    var tooltip_map = d3.select(".tooltip_map")

		    // Create a custom symbol type
		    var customSymbolTypes = d3.map({
		      'thin-x': function(size) {
		        size = Math.sqrt(size);
		        return 'M' + (-size/2) + ',' + (-size/2) +
		          'l' + size + ',' + size +
		          'm0,' + -(size) + 
		          'l' + (-size) + ',' + size;
		      },
		      'smiley': function(size) {
		        size = Math.sqrt(size);
		        var pad = size/5;
		        var r = size/8;
		        return 'M' + ((-size/2)+pad) + ',' + (-size/2) +
		        ' m' + (-r) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (r * 2) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (-(r * 2)) + ',0' +
		          
		        'M' + ((size/2)-pad) + ',' + (-size/2) +
		        ' m' + (-r) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (r * 2) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (-(r * 2)) + ',0' +
		          
		        'M' + (-size/2) + ',' + ((size/2)-(2*pad)) +
		        'q' + (size/2) + ',' + (pad*2) + ' ' + size + ',0';
		      }
		    });

		    d3.svg.customSymbol = function() {
		      var type,
		          size = 64;
		      function symbol(d,i) {
		        return customSymbolTypes.get(type.call(this,d,i))(size.call(this,d,i));
		      }
		      symbol.type = function(_) {
		        if (!arguments.length) return type;
		        type = d3.functor(_);
		        return symbol;
		      };
		      symbol.size = function(_) {
		        if (!arguments.length) return size;
		        size = d3.functor(_);
		        return symbol;
		      };
		      return symbol;
		    };

		    function getSymbol(type, size) {
		      size = size || 64;
		      if (d3.svg.symbolTypes.indexOf(type) !== -1) {
		        return d3.svg.symbol().type(type).size(size)();
		      } else {
		        return d3.svg.customSymbol().type(type).size(size)();
		      }
		    }


		    // 2FAV Sources
		    g.selectAll("_2FAVSources")
				.data(data_2FAV)
				.enter().append("path")  
				.attr("class", "_2FAVSources")
				.style("opacity", .9)
				.style("fill", '#777')
				.attr("transform", function(d) {
		      		if (d.flareID === flareID) {
		      			return
		      		} else {
						return "translate(" + projection([
							d.fava_ra * -1,
							d.fava_dec
						]) + ") rotate(45)";
					}
		        })
		      // .attr("d", d3.svg.symbol().type("cross").size(25))
		        .attr('d', function(d) {
		        	if (d.flareID === flareID) {
		      			return
		      		} else {
		          		return getSymbol( 'thin-x', 50);
		          	}
		        })
		        .attr('fill', 'transparent')
		        .attr('stroke', '#777')
		      .on("mouseover", function(d) {

					// var lightcurveLink = "<a href=\"http://http://fermi.gsfc.nasa.gov/ssc/data/access/lat/4yr_catalog/3FGL-table/data/3FGL_lc_v5/" + d.Source_Name.replace(' ', '_').replace('+','p').replace('.','d').replace('-','m') + "_lc.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">Light Curve</a>";
					// var spectrum = "<a href=\"http://http://fermi.gsfc.nasa.gov/ssc/data/access/lat/4yr_catalog/3FGL-table/data/3FGL_spec_v5/"  + d.Source_Name.replace(' ', '_').replace('.','d').replace('+','p') + "_spec.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">Spectrum</a>";
					// var innerHTML =  d.Source_Name + '<BR>RA: ' + d.RAJ2000 + ', Dec: ' + d.DEJ2000 + '<BR>Association: ' + d.ASSOC1 + '<BR>' + lightcurveLink  + ' | ' + spectrum;
					var sourceReportLink = "<a href=\"SourceReport.php?week=" + d.week + '&flare=' + d.num + "\">FAVA Source Report</a>";
					var innerHTML =  'FAVA_' + d.flareID + '<BR>RA: ' + d.fava_ra + ', Dec: ' + d.fava_dec + '<BR>Week: ' + d.week + '<BR>MET: ' + d.tmin + '<BR>' + sourceReportLink;

				    var offsetTop = $("#FlareMap").offset().top;
				    var offsetLeft = $("#FlareMap").offset().left;

					tooltip_map.transition()
					   .duration(200)
					   .style("opacity", .9);
					tooltip_map.html( innerHTML )
					   // .style("left", projection([d.fava_ra * -1,d.fava_dec])[0] + offsetLeft - 60 + "px")
					   // .style("top", projection([d.fava_ra * -1,d.fava_dec])[1] + offsetTop - 90 + "px")
					   .style("left", event.pageX - 87.5 + "px")
					   .style("top", event.pageY - 135 + "px")

					})

		      // .on("mouseout", function(d) {
		      //     tooltip_map.transition()
		      //        .duration(500)
		      //        .style("opacity", 0)                 // Fade the tooltip
		      //        .each("end", function(d) {           // Move the tooltip out of the way once it's faded away
		      //           tooltip_map.style("left", "0px")
		      //           tooltip_map.style("top", "0px")
		      //        } );
		      //     })

		    // 2FAV Source Error
		    g.selectAll("_2FAVSourcesError")
		      .data(data_2FAV)
		      .enter().append("circle", "error")
		      .attr("class", "_2FAVSourceError")  
		      .style("fill", "none")
		      .style("stroke", "red")
		      .style("stroke-dasharray", ("3, 3"))
		      .style("opacity", .5)  
		      // .attr("r", function(d) { return d.error })
		      .attr("r", function(d) { 
		      		if (d.flareID === flareID) {
		      			return
		      		} else {
		      			return d.le_r95*34   
		      		}
		      	})
		      .attr("transform", function(d) {
		      		if (d.flareID === flareID) {
		      			return
		      		} else {
				        return "translate(" + projection([
				          d.fava_ra * -1,
				          d.fava_dec
				        ]) + ")";
		    		}
		      });

		    // 2FAV Source Labels
		    g.selectAll("Labels")
		      .data(data_2FAV)
		      .enter().append("text")
		      .attr("class", "Labels")
		      .style("text-anchor", "right")
		      .style("opacity", 0.9)
		      .style("fill", '#777')
		      .attr("text-anchor", 'left') 
		      .attr("dx", "0.5em")
		      .attr("dy", "-0.5em")
		      .attr("transform", function(d) {
	      		if (d.flareID === flareID) {
	      			return
	      		} else {
			        return "translate(" + projection([
			          d.fava_ra * -1,
			          d.fava_dec
			        ]) + ")";
	     		}
		      })
		      .text(function(d) { return 'FAVA_' + d.flareID} )
		}

		function update3FGLData() {

		    var svg = d3.select("svg")
		    var g = svg.select('g')

		    var width = mapWidth,
		        height = mapHeight;

		    var projection = d3.geo.modifiedStereographic()
		        .coefficients("gs48")
		        .clipAngle(55)
		        .scale(2000)
		        .translate([width / 2, height / 2])
		        .rotate([ raCenter, decCenter*-1])
		        .precision(.1);


		    // Set the labels to be visible by default
		    labelsVisible = true;

		    // Select the tooltip area to the webpage
		    var tooltip_map = d3.select(".tooltip_map")
		    var tooltipFixed = d3.select(".tooltipFixed")

		    // Create a custom symbol type
		    var customSymbolTypes = d3.map({
		      'thin-x': function(size) {
		        size = Math.sqrt(size);
		        return 'M' + (-size/2) + ',' + (-size/2) +
		          'l' + size + ',' + size +
		          'm0,' + -(size) + 
		          'l' + (-size) + ',' + size;
		      },
		      'smiley': function(size) {
		        size = Math.sqrt(size);
		        var pad = size/5;
		        var r = size/8;
		        return 'M' + ((-size/2)+pad) + ',' + (-size/2) +
		        ' m' + (-r) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (r * 2) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (-(r * 2)) + ',0' +
		          
		        'M' + ((size/2)-pad) + ',' + (-size/2) +
		        ' m' + (-r) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (r * 2) + ',0' +
		        ' a' + r + ',' + r + ' 0 1,0' + (-(r * 2)) + ',0' +
		          
		        'M' + (-size/2) + ',' + ((size/2)-(2*pad)) +
		        'q' + (size/2) + ',' + (pad*2) + ' ' + size + ',0';
		      }
		    });

		    d3.svg.customSymbol = function() {
		      var type,
		          size = 64;
		      function symbol(d,i) {
		        return customSymbolTypes.get(type.call(this,d,i))(size.call(this,d,i));
		      }
		      symbol.type = function(_) {
		        if (!arguments.length) return type;
		        type = d3.functor(_);
		        return symbol;
		      };
		      symbol.size = function(_) {
		        if (!arguments.length) return size;
		        size = d3.functor(_);
		        return symbol;
		      };
		      return symbol;
		    };

		    function getSymbol(type, size) {
		      size = size || 64;
		      if (d3.svg.symbolTypes.indexOf(type) !== -1) {
		        return d3.svg.symbol().type(type).size(size)();
		      } else {
		        return d3.svg.customSymbol().type(type).size(size)();
		      }
		    }

		   // Circles
		    g.selectAll("_3FGLCatalog")
		      .data(data_3FGL)
		      .enter().append("circle", "_3FGLCatalog")
		      .attr("r", 4)
		      .style("fill", "green")  
		      .style("opacity", .75)
		      .attr("transform", function(d) {
		        return "translate(" + projection([
		          d.RAJ2000 * -1,
		          d.DEJ2000
		        ]) + ")";
		      })
		      .on("mouseover", function(d) {

					var lightcurveLink_3FGL = "<a href=\"http://fermi.gsfc.nasa.gov/ssc/data/access/lat/4yr_catalog/3FGL-table/data/3FGL_lc_v5/" + d.Source_Name.replace(' ', '_').replace('+','p').replace('.','d').replace('-','m') + "_lc.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">3FGL Light Curve</a>";
					var lightcurveLink_FAVA = "<a href=\"LightCurve.php?ra=" + d.RAJ2000 + "&dec=" + d.DEJ2000 + "\">FAVA Light Curve</a>";
					var spectrum = "<a href=\"http://fermi.gsfc.nasa.gov/ssc/data/access/lat/4yr_catalog/3FGL-table/data/3FGL_spec_v5/"  + d.Source_Name.replace(' ', '_').replace('.','d').replace('+','p').replace('-','m') + "_spec.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">3FGL Spectrum</a>";
					var innerHTML =  d.Source_Name + '<BR>RA: ' + d.RAJ2000 + ', Dec: ' + d.DEJ2000 + '<BR>Association: <a href=\"http://www.google.com/search?q=\'' + d.ASSOC1 + '\'\">' + d.ASSOC1 + '</a><BR>' + lightcurveLink_3FGL  + ' | ' + spectrum + "<BR>" + lightcurveLink_FAVA;
					// var innerHTML =  d.Source_Name + '<BR>RA: ' + d.RAJ2000 + ', Dec: ' + d.DEJ2000 + '<BR>Association: ' + d.ASSOC1 + '<BR>Class: ' + d.Type;

				    var offsetTop = $("#FlareMap").offset().top;
				    var offsetLeft = $("#FlareMap").offset().left;

					tooltip_map.transition()
					   .duration(200)
					   .style("opacity", .9);
					tooltip_map.html( innerHTML )
					   // .style("left", projection([d.RAJ2000 * -1,d.DEJ2000])[0] + offsetLeft - 90 + "px")
					   // .style("top", projection([d.RAJ2000 * -1,d.DEJ2000])[1] + offsetTop - 90 + "px")
						.style("left", event.pageX - 120 + "px")
						.style("top", event.pageY - 135 + "px")
				})

		      // .on("mouseout", function(d) {
		      //     tooltip_map.transition()
		      //        .duration(500)
		      //        .style("opacity", 0)                 // Fade the tooltip
		      //        .each("end", function(d) {           // Move the tooltip out of the way once it's faded away
		      //           tooltip_map.style("left", "0px")
		      //           tooltip_map.style("top", "0px")
		      //        } );
		      //     })

		      // .on("dblclick", function(d) {

		      //     // var lightcurveLink = "<a href=\"http://http://fermi.gsfc.nasa.gov/ssc/data/access/lat/4yr_catalog/3FGL-table/data/3FGL_lc_v5/" + d.Source_Name.replace(' ', '_').replace('+','p').replace('.','d').replace('-','m') + "_lc.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">Light Curve</a>";
		      //     // var spectrum = "<a href=\"http://http://fermi.gsfc.nasa.gov/ssc/data/access/lat/4yr_catalog/3FGL-table/data/3FGL_spec_v5/"  + d.Source_Name.replace(' ', '_').replace('.','d').replace('+','p') + "_spec.png\" onclick=\"window.open(this.href,'targetWindow','width=800px, height=600px'); return false;\">Spectrum</a>";
		      //     // var innerHTML =  d.Source_Name + '<BR>RA: ' + d.RAJ2000 + ', Dec: ' + d.DEJ2000 + '<BR>Association: ' + d.ASSOC1 + '<BR>' + lightcurveLink  + ' | ' + spectrum;
		      //     var innerHTML =  d.Source_Name + '<BR>RA: ' + d.RAJ2000 + ', Dec: ' + d.DEJ2000 + '<BR>Association: ' + d.ASSOC1 + '<BR>Class: ' + d.Type;
		        
		      //     tooltipFixed.transition()
		      //          .duration(200)
		      //          .style("opacity", .9);
		      //     tooltipFixed.html( innerHTML )
		      //          .style("left", (projection([d.RAJ2000 * -1,d.DEJ2000])[0]-70) + "px")
		      //          .style("top", (projection([d.RAJ2000 * -1,d.DEJ2000])[1]-100) + "px");
		      //          // .style("left", event.clientX + 10 + "px")
		      //          // .style("top", event.clientY - 20 + "px");
		      // })

		    g.selectAll("Labels")
		      .data(data_3FGL)
		      .enter().append("text")
		      .attr("class", "Labels")
		      .style("text-anchor", "right")
		      .style("opacity", 1)
		      .attr("dx", "0.5em")
		      .attr("dy", "-0.5em")
		      .attr("transform", function(d) {
		        return "translate(" + projection([
		          d.RAJ2000 * -1,
		          d.DEJ2000
		        ]) + ")";
		      })
		      .text(function(d) { return d.Source_Name } )
		}

		// Call the flare database
		function queryDB_2FAV(ra, dec, radius) {

		    console.log('Querying the flare database...')

		    // Encode the URL parameters
		    var ra_urlEncoded = encodeURIComponent(ra);
		    var dec_urlEncoded = encodeURIComponent(dec);
		    var radius_urlEncoded = encodeURIComponent(radius);

            <?php
				if ( (isset($_GET['threshold'])) ) {
					$thresholdRequest = intval($_GET['threshold']);
					$thresholdRequest = htmlspecialchars($thresholdRequest, ENT_QUOTES, 'UTF-8');
					echo "var thresholdRequest = '$thresholdRequest';";
	            } else {
	            	echo "var thresholdRequest = '6';";

	            }
            ?>  

			var thresholdRequest_urlEncoded = encodeURIComponent(thresholdRequest);

		    // Set the request type
		    var typeOfRequest = 'MapData';
		    var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);

		    // Setup the URL
		    var URL = "queryDB_2FAV.php?typeOfRequest=" + typeOfRequest_urlEncoded + "&ra=" + ra_urlEncoded + "&dec=" + dec_urlEncoded + "&radius=" + radius_urlEncoded + "&threshold=" + thresholdRequest_urlEncoded;
		    console.log(URL);

		    // Perform an ajax request
		    $.ajax({url: URL, success: function(responseText){

		        data_2FAV = JSON.parse(responseText);

		        // drawMap();
		        updateFlareData();

		    }});
		}

		// Call the 3FGL database
		function queryDB_3FGL(ra, dec, radius) {

		    console.log('Querying the 3FGL database...')

		    // Encode the URL parameters
		    var ra_urlEncoded = encodeURIComponent(ra);
		    var dec_urlEncoded = encodeURIComponent(dec);
		    var radius_urlEncoded = encodeURIComponent(radius);

		    // Set the request type
		    var typeOfRequest = 'MapData';
		    var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);

		    // Setup the URL
		    var URL = "queryDB_3FGL.php?typeOfRequest=" + typeOfRequest_urlEncoded + "&ra=" + ra_urlEncoded + "&dec=" + dec_urlEncoded + "&radius=" + radius_urlEncoded;
		    console.log(URL);

		    // Perform an ajax request
		    $.ajax({url: URL, success: function(responseText){

		        // Get the data
		        data_3FGL = JSON.parse(responseText);

		        // drawMap();
		        update3FGLData();


		    }});
		}


		function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel) {
		    //If JSONData is not an object then JSON.parse will parse the JSON string in an Object
		    var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
		    
		    var CSV = '';    
		    //Set Report title in first row or line
		    
		    CSV += ReportTitle + '\r\n\n';

		    //This condition will generate the Label/Header
		    if (ShowLabel) {
		        var row = "";
		        
		        //This loop will extract the label from 1st index of on array
		        for (var index in arrData[0]) {
		            
		            //Now convert each value to string and comma-seprated
		            row += index + ',';
		        }

		        row = row.slice(0, -1);
		        
		        //append Label row with line break
		        CSV += row + '\r\n';
		    }
		    
		    //1st loop is to extract each row
		    for (var i = 0; i < arrData.length; i++) {
		        var row = "";
		        
		        //2nd loop will extract each column and convert it in string comma-seprated
		        for (var index in arrData[i]) {
		            row += '"' + arrData[i][index] + '",';
		        }

		        row.slice(0, row.length - 1);
		        
		        //add a line break after each row
		        CSV += row + '\r\n';
		    }

		    if (CSV == '') {        
		        alert("Invalid data");
		        return;
		    }   
		    
		    //Generate a file name
		    var fileName = "MyReport_";
		    //this will remove the blank-spaces from the title and replace it with an underscore
		    fileName += ReportTitle.replace(/ /g,"_");   
		    
		    //Initialize file format you want csv or xls
		    var uri = 'data:text/csv;charset=utf-8,' + escape(CSV);
		    
		    // Now the little tricky part.
		    // you can use either>> window.open(uri);
		    // but this will not work in some browsers
		    // or you will not get the correct file extension    
		    
		    //this trick will generate a temp <a /> tag
		    var link = document.createElement("a");    
		    link.href = uri;
		    
		    //set the visibility hidden so it will not effect on your web-layout
		    link.style = "visibility:hidden";
		    link.download = fileName + ".csv";
		    
		    //this part will append the anchor tag and remove it after automatic click
		    document.body.appendChild(link);
		    link.click();
		    document.body.removeChild(link);
		}


		$(function() {

		    $('#Download').click(function(){
		        var data = data_lightCurve;
		        if(data == '')
		            return;
		        
		        JSONToCSVConvertor(data, "# FAVA Relative Flux Lightcurve Data", true);
		    });


			document.getElementById('RelativeFluxGT100MeV').style.display = 'none';
			document.getElementById('RelativeFluxGT100MeV_Significance').style.display = 'none';
			document.getElementById('RelativeFluxGT800MeV').style.display = 'none';
			document.getElementById('RelativeFluxGT800MeV_Significance').style.display = 'none';

			// Use php to read the url parameter and set it in the analysis run info box
	        <?php
				if ( (isset($_GET['week'])) ) {				
					$week = intval($_GET['week']);
					$week = htmlspecialchars($week, ENT_QUOTES, 'UTF-8');
				 //    $weekLowSetString = "document.getElementById('WeekLow').innerHTML = $week;";
				 //    $weekHighSetString = "document.getElementById('WeekHigh').innerHTML = $week;";
	                $weekNumberSetString = "weekNumber = $week;";
	                echo $weekNumberSetString;

	            } 

				if ( (isset($_GET['flare'])) ) {				
					$flare = intval($_GET['flare']);
					$flare = htmlspecialchars($flare, ENT_QUOTES, 'UTF-8');
	                $flareNumberSetString = "flareNumber = $flare;";
	                echo $flareNumberSetString;
	            } 
	        ?>

			// Call the database
			queryFlareDB()


		});


	</script>


	<!-- main starts here -->		
	<div id="main">	

	    <!-- Start NASA Container -->
	    <div id="nasa-container" style="margin:8px 0 0 10px">

	        <!-- Start NASA Banner -->
	        <div id="nasa-banner-plain">

	            <!-- Left - Logo -->
	            <div class="nasa-logo">
	                <a href="http://www.nasa.gov/"><img src="http://fermi.gsfc.nasa.gov/ssc/inc/img/nasa_logo.gif" width="140" height="98" border="0" alt="NASA Logo"></a>
	            </div>
	        
	            <!-- Middle - Affiliations -->
	            <div id="nasa-affiliation">
	                <h1><a href="http://www.nasa.gov/">National Aeronautics and Space Administration</a></h1>
	                <h2><a href="http://www.nasa.gov/goddard">Goddard Space Flight Center</a></h2>
	            </div>
	            
	            <!-- Right - Search and Links -->
	            <div id="nasa-search-links">
	                <div id="header-links">
	                    <a href="/ssc/">FSSC</a> &bull; <a href="http://heasarc.gsfc.nasa.gov/">HEASARC</a> &bull; <a href="http://science.gsfc.nasa.gov/">Sciences and Exploration</a>
	                </div>
	            </div>

	        </div>
	        <!-- End NASA Banner -->

	        <!-- Start Mission Banner Graphic -->
<!-- 	        <div id="mission-banner-plain">
	            <a href="/ssc/"><img src="http://fermi.gsfc.nasa.gov/ssc/inc/img/fssc_banner.jpg" width="952" height="100" alt="Fermi Science Support Center" /></a>
	        </div> -->
	        <!-- End Mission Banner Graphic -->

	    <!-- End NASA Container -->
	    </div>

		<!-- Header starts here -->
		<div>
			<div style="float: left; padding-top:12px; padding-left:25px"><img middle; style="width: 100%; height: 100%" src="./img/Fermi_Small.png"></div>
			<div style="margin-left: 25px;padding-left: 75px; padding-bottom:20px; padding-top: 5px">
				<H2>Fermi All-sky Variability Analysis (FAVA) - Source Report</H2>
			</div>
		</div>
		<!-- Header ends here -->


		<!-- sidebar start here -->
	    <div style="width:300px; margin-left:25px; float:left;" id="coordinateInput">

			<!-- Position information start here -->		
			<div class="panel panel-default">
				<div class="panel-heading">
			        <h3 class="panel-title">Analysis Results</h3>
			    </div>
			    <div class="panel-body">

					<div class="table-responsive">

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Source Information</th><th></th>
			                </tr>
			              </thead>
			              <tbody>
								<tr><td>Week: </td><td id="table_week" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Flare: </td><td id="table_flare" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table>  

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Analysis Duration</th><th></th>
			                </tr>
			              </thead>
   			              	<tbody>

								<tr><td>Start Time: </td><td id="StartTime" align="right" style="padding-right:18px"></td></tr>
								<tr><td>End Time: </td><td id="EndTime" align="right" style="padding-right:18px"></td></tr>		              
							</tbody>
			            </table>  


			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Best Localization</th><th></th>
			                </tr>
			              </thead>
			              <tbody>
								<tr><td>RA: </td><td id="table_ra" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Dec: </td><td id="table_dec" align="right" style="padding-right:18px"></td></tr>
								<tr><td>r95: </td><td id="table_r95" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Source: </td><td id="table_source" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Galactic l: </td><td id="table_galb" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Galactic b: </td><td id="table_gall" align="right" style="padding-right:18px"></td></tr>

			              </tbody>
			            </table>  

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>FAVA Analysis <BR>(100 MeV - 300 GeV)</th><th></th>
			                </tr>
			              </thead>
			              <tbody>

								<tr><td>Significance: </td><td id="sigmaLow" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Maximum Variation: </td><td id="maximumSigmaLow" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Time of Max Variation: </td><td id="maximumSigmaTimeLow" align="right" style="padding-right:18px"></td></tr>	
								<tr><td>Median Variation: </td><td id="medianLow" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Standard Deviation: </td><td id="standardDeviationLow" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table>  

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>FAVA Analysis <BR>(800 MeV - 300 GeV)</th><th></th>
			                </tr>
			              </thead>
			              <tbody>

								<tr><td>Significance: </td><td id="sigmaHigh" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Maximum Variation: </td><td id="maximumSigmaHigh" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Time of Max Variation: </td><td id="maximumSigmaTimeHigh" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Median Variation: </td><td id="medianHigh" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Standard Deviation: </td><td id="standardDeviationHigh" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table> 

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Likelihood Analysis <BR>(100 MeV - 800 MeV)</th><th></th>
			                </tr>
			              </thead>
			              <tbody>

								<tr><td>Test Statistic (TS): </td><td id="le_ts" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Significance: </td><td id="le_tssigma" align="right" style="padding-right:18px"></td></tr>
								<tr><td>RA: </td><td id="le_ra" align="right" style="padding-right:18px"></td></tr>	
								<tr><td>Dec: </td><td id="le_dec" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Error (95%): </td><td id="le_r95" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Flux: </td><td id="le_flux" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Photon Index: </td><td id="le_index" align="right" style="padding-right:18px"></td></tr>

			              </tbody>
			            </table> 

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Likelihood Analysis <BR>(800 MeV - 300 GeV)</th><th></th>
			                </tr>
			              </thead>
			              <tbody>
								<tr><td>Test Statistic (TS): </td><td id="he_ts" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Significance: </td><td id="he_tssigma" align="right" style="padding-right:18px"></td></tr>
								<tr><td>RA: </td><td id="he_ra" align="right" style="padding-right:18px"></td></tr>	
								<tr><td>Dec: </td><td id="he_dec" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Error (95%): </td><td id="he_r95" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Flux: </td><td id="he_flux" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Index: </td><td id="he_index" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table>  			            

			            <table class="table table-striped">
			              <thead>
			                <tr>
			                  <th>Associations</th><th></th>
			                </tr>
			              </thead>
			              <tbody>						
   								<!-- <tr><td>FAVA Association: </td><td td id="favasrc" align="right" style="padding-right:18px"></td></tr>		 -->
								<tr><td>3FGL Association: </td><td id="fglassoc" align="right" style="padding-right:18px"></td></tr>
								<tr><td>Catalog Association: </td><td td id="assoc" align="right" style="padding-right:18px"></td></tr>
			              </tbody>
			            </table>  

 				    </div>
				</div>
			</div>
			<!-- Position information ends here -->		


			<!-- FAVA Resources start here -->		
			<div class="panel panel-default" style="height: 225px;">
				<div class="panel-heading">
			        <h3 class="panel-title">FAVA Resources</h3>
			     </div>

					<center>

			            <table class="table table-striped">
			            <!-- <table class="table"> -->
			              <tbody>					
  								<tr><td><a href="index.php">FAVA Weekly Flare List</a></td><td td id="table_flarelist"></td></tr>			
  								<tr><td><a href="LightCurve.php">FAVA Light Curve Generator</a></td><td td id="table_lightcurve"></td></tr>
								<tr><td><a href="http://adsabs.harvard.edu/abs/2013ApJ...771...57A">1st FAVA Catalog</a></td><td id="table1_1FAV"></td></tr>
								<!-- <tr><td><a href="CatalogView_2FAV.php">2nd FAVA Catalog</a></td><td td id="table1_2FAV"></td></tr> -->
								<tr><td><a href="http://fermi.gsfc.nasa.gov/ssc/data/access/lat/fava_catalog/">2nd FAVA Catalog</a></td><td td id="table1_2FAV"></td></tr>
								<tr><td><a href="About.html">About FAVA</a></td><td></td></tr>		
			              </tbody>
			            </table>  

				    </center>

		    </div>
			<!-- FAVA Resources ends here -->	


			<!-- Download panel start here -->		
			<div id="DownloadPanel" class="panel panel-default" style="display:none">
				<div class="panel-heading">
			        <h3 class="panel-title">Download</h3>
			     </div>
			     <div class="panel-body">

			     <center>
					<button id="Download" style="margin:5px 0px 0px 2px" id="Download" class="btn btn-default" title="Download data" rel="nofollow"><span class="glyphicon glyphicon-cloud-download"></span> Download Data</button>
              	</center>

		      	</div>
		    </div>
			<!-- Download panel ends here -->		


		    <!-- Caveat statement start here -->	
			<div>	
				<center>
					<div class="alert alert-info" role="alert">All analysis results presented here are preliminary unless otherwise stated.  Please consult the <a href="About.html">about page</a> for important details and caveats associated with this analysis.</div>
			    </center>
		    </div>
			<!-- Caveat statement ends here -->	

			<!-- Citation request start here -->	
			<div>	
				<center>
					<div class="alert alert-success" role="alert">Please reference <a href="http://adsabs.harvard.edu/abs/2017ApJ...846...34A">Abdollahi et al. 2017</a> for any use of the presented results</div>
			    </center>
		    </div>
			<!--  Citation request ends here -->	

					</div>
		<!-- sidebar ends here -->

		<!-- Content starts here -->
		<div id="content" style="padding-right:100px">

			<!-- Low energy light curves panel start here -->	
		    <div style="width:1300px; margin-left: 340px;">
		 		<div class="panel panel-default" style="height: 900px;">
					<div class="panel-heading"><h3 class="panel-title">
						Low Energy Light Curve (100 MeV - 800 MeV)</h3>
					</div>

				     <div class="panel-body">

						<center>

						<div id="RelativeFluxGT100MeV" style="height: 400px; margin: -5px; min-width: 1000px; max-width: 1200px;"></div>
						<div id="RelativeFluxGT100MeV_Significance" style="height: 400px; margin:0px -5px; padding:0px; min-width: 1200px; max-width: 1200px"></div>

						<div id="contentPlaceholderLow" style="margin-top:400px; top: 55%; left: 55%; font-weight: normal; color:#ddd; vertical-align: middle;"> 
							No Data Selected
						</div>



						</center>

					</div>	

	      		</div>
		    </div>
			<!-- Low energy light curves panel ends here -->	


			<!-- High energy light curves panel start here -->	
		    <div style="width:1300px; margin-left: 340px;">
			 	<div class="panel panel-default" style="height: 900px;">
					<div class="panel-heading"><h3 class="panel-title">
						High Energy Light Curve (800 MeV - 300 GeV)</h3>
					</div>

				    <div class="panel-body">

						<center>

						<div id="RelativeFluxGT800MeV" style="height: 400px; margin: -5px; min-width: 1200px; max-width: 1200px"></div>
						<div id="RelativeFluxGT800MeV_Significance" style="height: 400px; margin:0px -5px; padding:0px; min-width: 1200px; max-width: 1200px"></div>
		
						<div id="contentPlaceholderHigh" style="margin-top:400px; top: 55%; left: 55%; font-weight: normal; color:#ddd; vertical-align: middle;"> 
							No Data Selected
						</div>


						</center>

					</div>	

		      	</div>
		    </div>
			<!-- High energy light curves panel ends here -->


			<!-- TS map panels start here -->	
			<div style="width:1500px; margin-left: 340px;">

				<!-- Low energy TS map panels start here -->	
				<div class="panel panel-default" style="width:645px; height:600px; margin-left: 0px; float:left;">
					<div class="panel-heading"><h3 class="panel-title">Low Energy TS Map (100 - 800 MeV)</h3></div>
				    <div class="panel-body">
				    <img id="lowEnergyTSMap" src="#" style="width: 90%; height: 90%; display: block; margin-left: auto; margin-right: auto }"/>
					</div>	
				</div>
				<!-- Low energy TS map panels ends here -->	

				<!-- High energy TS map panels start here -->	
				<div class="panel panel-default" style="width:645px; height:600px; margin-left: 10px; float:left;">
					<div class="panel-heading"><h3 class="panel-title">High Energy TS Map (800 MeV - 300 GeV)</h3></div>
				    <div class="panel-body">
   				    <img id="highEnergyTSMap" src="#" style="width: 90%; height:90%; display: block; margin-left: auto; margin-right: auto }"/>
					</div>	
				</div>
				<!-- High energy TS map panels ends here -->	
			</div>
			<!-- TS map panels end here -->	


			<!-- Flare map panel start here -->	
			<div style="width:1500px; margin-left: 340px;">
				<div class="panel panel-default" style="width:1300px; height:800px; margin-left: 0px; float:left;">
					<div class="panel-heading"><h3 class="panel-title">Flare Map</h3></div>
				    <div id="FlareMap" class="panel-body">

						<div id="cursorcoords">RA: --, Dec: --</div>


				    <div class="buttons">
					    <button id="zoom_in" class="btn btn-default" title="Zoom In" rel="nofollow">+</button>
					    <button id="zoom_out" class="btn btn-default" title="Zoom In" rel="nofollow">-</button>
					    <BR>
					    <button style="margin:5px 0px 0px 2px" id="LabelToggle" class="btn btn-default" title="Toggle map labels" rel="nofollow">Labels</button>
					</div>


					</div>	
				</div>	
			</div>
			<!-- Flare overview panel ends here -->	

		</div>
		<!-- Content ends here -->

		<canvas width="1250" height="700" id="exportCanvas"></canvas>



	<!-- Main ends here -->
	</div>

	<!-- footer starts here -->	
	<div id="footer">
		<div id="footer-content">
		
			<p>
				<hr>
				FAVA Data Portal v2.0	- Support Contact:<a href="mailto:daniel.kocevski@nasa.gov"> Daniel Kocevski</a>
			</p>

		</div>
	</div>
	<!-- footer ends here -->


</body>


