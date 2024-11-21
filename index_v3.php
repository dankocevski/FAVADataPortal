<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en-us">
<head>
    <title>Fermi GBM Online Targeted Search</title>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

	<link rel="icon" type="image/png" href="./img/favicon2.png">

    <!-- jQuery -->
    <script type="text/javascript" src="./js/jquery-1.12.0.min.js"></script>
    <!-- <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> -->
	<!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> -->

	<!-- Chart.js -->
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

    <!-- Reset css -->
    <link rel="stylesheet" hrmouseef="./css/reset.css" type="text/css" />

    <!-- NASA theme -->
    <link rel="stylesheet" href="./css/NASA.css">

	<!-- Bootstrap compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Bootstrap Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<!-- Bootstrap compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>



</head>

<!-- custom css -->
<style type="text/css">

	#footer { 
		float:left;
		padding:25px 0 10px 10px;
		width:99%}
	}

	canvas {
		-moz-user-select: none;
		-webkit-user-select: none;
		-ms-user-select: none;
	}

/*	.modal-open .container-fluid, .modal-open  .container {
	    -webkit-filter: blur(5px) grayscale(90%);
	}	
*/
	.modal-backdrop {
	   /*background-color: red;*/
	   -webkit-filter: blur(5px) grayscale(90%);
	}

	body.modal-open .background-container{
	    -webkit-filter: blur(4px);
	    -moz-filter: blur(4px);
	    -o-filter: blur(4px);
	    -ms-filter: blur(4px);
	    filter: blur(4px);
	    filter: url("https://gist.githubusercontent.com/amitabhaghosh197/b7865b409e835b5a43b5/raw/1a255b551091924971e7dee8935fd38a7fdf7311/blur".svg#blur);
	filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='4');
	}

	.panel-body.active{
	  background: url('./img/loading_apple.gif') no-repeat;
	  background-repeat: no-repeat; 
	  background-position:center; 
	  background-size:30px; 
	  min-height: 250px;
	}

	.panel-body {
	  background: none
	}


</style>


<body id="body-plain">

	<script type="text/javascript" src="./js/dat.gui.js"></script>
	<script type="text/javascript">

		console.log('This is index.php v2')
		// Make the data variable global
		var data_allTriggers;
		var sampleList;
		var passphrase;

		// Define trigger counters
		var L1_Triggers = 0
		var H1_Triggers = 0
		var V1_Triggers = 0
		var L1_H1_Triggers = 0
		var L1_H1_V2_Triggers = 0
		var L1_V1_Triggers = 0
		var H1_V1_Triggers = 0
		var Other_Triggers = 0

		// Define the coverage counters
		var coverage = 0
		var no_coverage = 0
		var unknown_coverage = 0

		// Random data generator
		var randomScalingFactor = function() {
			return Math.round(Math.random() * 100);
		};

		// Set cookie data
		function setCookieData(name, value, expiration_days) {
			var date = new Date();
			date.setTime(date.getTime() + (expiration_days*24*60*60*1000));
			var expires = "expires="+ date.toUTCString();
			document.cookie = name + "=" + value + ";" + expires + ";path=/";
		}

		// Get the cookie data. Return the value if found, return empty string if not
		function getCookieData(name) {
			var name_mod = name + "=";
			var decodedCookie = decodeURIComponent(document.cookie);
			var ca = decodedCookie.split(';');
			for(var i = 0; i <ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name_mod) == 0) {
					return c.substring(name_mod.length, c.length);
				}
			}
			return null;
		}

		// Check for an existing cookie
		function checkCookie() {
			
			// Check if the cookie contains the passphrase data
			passphrase = getCookieData("passphrase");

			console.log("Cookie stored passphrase = " + passphrase)

			if (passphrase == null) {

				// Show the passphrase modal if the cookie doesn't contain the passphrase data
				$('#magic_word_dialog').modal('show');

			} else {

				// Use the passphrase in the cookie to query the trigger list
				var magic_word = passphrase;
				queryDB('TriggerList', magic_word)

			}
		}

		function getURLVariables() {
		    var vars = {};
		    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		        vars[key] = value;
		    });
		    return vars;
		}

	    function GetUrlValue(VarSearch){
	        var SearchString = window.location.search.substring(1);
	        var VariableArray = SearchString.split('&');
	        for(var i = 0; i < VariableArray.length; i++){
	            var KeyValuePair = VariableArray[i].split('=');
	            if(KeyValuePair[0] == VarSearch){
	                return KeyValuePair[1];
	            }
	        }
	    }

		// Call the database
		function queryDB(typeOfRequest, magic_word) {

			console.log('Querying the database...')

			if (typeOfRequest === 'TriggerList') {

				// Setup the URL
				var typeOfRequest = 'TriggerList'
				var typeOfRequest_urlEncoded = encodeURIComponent(typeOfRequest);
				var magic_word_urlEncoded = encodeURIComponent(magic_word);
				var URL = "queryDB_mySQL_v2.php?typeOfRequest=" + typeOfRequest_urlEncoded + "&magicWord=" + magic_word_urlEncoded;

				console.log(URL);

				$.ajax({url: URL, success: function(responseText){

					// console.log(responseText)

					try {
						data_allTriggers = JSON.parse(responseText);
					} catch (error) {
						console.log('Error parsing query response.')
						return;
					}

					// Get the currently selected sample
					selectedSample = $("#sampleSelection")[0].value

					// Create a empty list to store the filtered data
					data = []

					// Make a list of unique available samples
					sampleList = ''

					// Loop through each record and only keep the triggers in the selected sample
					for (var i=0, size=data_allTriggers.length; i<size; i++) {

						// Get the current source
					    sourceRecord = data_allTriggers[i];

					    // Add the sample to the sample list
						if (sampleList.indexOf(sourceRecord['sample']) === -1) {
							sampleList = sampleList + '_' + sourceRecord['sample'];

							// Add this sample to the sample selection form
							$('#sampleSelection').append($('<option>', {
							    text: sourceRecord['sample']
							}));							
						}

						// Set 'All' as the default sample
					    if (sourceRecord['sample'] === selectedSample || selectedSample === 'All') {

					    	// console.log(sourceRecord['trigger_detector'])

					    	// Add the matching record to the data subset
					    	data.push(sourceRecord)

					    }
					}

					console.log('trigger data recieved')
					console.log('found ' + data.length + ' triggers')

					// Check if data was successfully retrieved. If the passphrase wasn't set before, set it to the supplied magic_word
					if (data.length != 0) {
						if (passphrase == null) {
							setCookieData('passphrase', magic_word, 1)	// Cookie expires in 1 day
						}
					}

					// Fill the primary table
					fillTable(data);

					// Fill the plots
					fillDoughnutPlots(data)

					// Unhide stuff
					$("#resources").show();
					$("#sampleSeletionContainer").show();
					$("#caveats").show();



				}});
			}
		}

		// Fill the primary table
		function fillTable(data) {

			// Setup the row array
			var row = new Array(), j = -1;

			// Create the header string
			var header = '<tr> \
			<th style="text-align: center;">Trigger ID</th> \
			<th style="text-align: center;">External ID</th> \
			<th style="text-align: center;">Source</th> \
			<th style="text-align: center;">Trigger MET</th> \
			<th style="text-align: center;">Trigger Date <BR>(UTC)</th> \
			<th style="text-align: center;">Trigger Time <BR>(UTC)</th> \
			<th style="text-align: center;">Distance <BR>(Mpc)</th> \
			<th style="text-align: center;">Area <BR>(deg<sup>2</sup>)</th> \
			<th style="text-align: center;">Coverage</th> \
			<th style="text-align: center;">Comment</th> \
			</tr>'


			// Loop through each data entry and add columns to the corresponding row entry
			for (var i=0, size=data.length; i<size; i++) {

			    sourceRecord = data[i];
			    row[++j] = '<tr>';

			    for (var key in sourceRecord) {

					if (key === 'trigger_id') {

						row[++j] ='<td class="' + key + '" style="text-align: center;">';

						//if (sourceRecord['hash'] == null) {
						//	row[++j] = '<a href="./trigger_report_v1.php?trigger_id=' + sourceRecord[key] + '&sample=' + sourceRecord['sample'] + '">' + sourceRecord[key] + '</a>';
						//} else if (sourceRecord['targeted_search_version'] === 'v1') {
						if (sourceRecord['targeted_search_version'] === 'v1') {
							row[++j] = '<a href="./trigger_report_v1.php?trigger_id=' + sourceRecord[key] + '&sample=' + sourceRecord['sample'] + '&hash=' + sourceRecord['hash'] + '">' + sourceRecord[key] + '</a>';
						// } else if (sourceRecord['targeted_search_version'] === 'v2' && sourceRecord[key].includes("Job")) {
						// 	row[++j] = sourceRecord[key];
						} else if (sourceRecord['targeted_search_version'] === 'v2') {
							row[++j] = '<a href="./trigger_report.php?trigger_id=' + sourceRecord[key] + '&sample=' + sourceRecord['sample'] + '&hash=' + sourceRecord['hash'] + '">' + sourceRecord[key] + '</a>';
						}

					} else if (key === 'sample' || key === 'hash' || key === 'targeted_search_version' || key === 'inSAA') {
						
						// skip 

					} else if (key === 'coverage') {

						row[++j] ='<td class="' + key + '" style="text-align: center;">';
						if (sourceRecord[key] != null) {
				        	row[++j] = sourceRecord[key].replace('%','')
				        }
					} else {

						row[++j] ='<td class="' + key + '" style="text-align: center;">';
			        	row[++j] = sourceRecord[key];
					}

			        row[++j] = '</td>';
			    }

			}


			// Add the header to the start of the array
			row.unshift(header);

			// Join the row array into one long string and place it inside the table element
			$('#dataTable').html(row.join('')); 


			// Jobs in progress
			$(".Processing").html('<div class="progress center-block" style="height:14px; width:80%; margin:0px; margin-left: auto; margin-right:auto; "><div class="progress-bar progress-bar-primary progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="vertical-align:middle; width: 100%; height:14px"></div></div>');

            // Make sure the progress bars are centered
            $(".Processing").css({ verticalAlign: "middle" });

			// Turn off the loading spinners in the background
			$(".panel-body").removeClass("active")
		}

		function fillDoughnutPlots(data) {

			// Define trigger counters
			L1_Triggers = 0
			H1_Triggers = 0
			V1_Triggers = 0
			L1_H1_Triggers = 0
			L1_H1_V2_Triggers = 0
			L1_V1_Triggers = 0
			H1_V1_Triggers = 0
			Other_Triggers = 0

			// Define the coverage counters
			some_coverage = 0
			no_coverage = 0
			unknown_coverage = 0

			// Loop through each record and only keep the triggers in the selected sample
			for (var i=0, size=data.length; i<size; i++) {

				// Get the current source
			    sourceRecord = data[i];

		    	// Enumerate the triggering detector. Skip if triggering detector is unspecified
		    	if (sourceRecord['trigger_detector'] != null) {

			    	L1 = 0
			    	H1 = 0
			    	V1 = 0

				    // Determine the number of trigger types for triggers in the selected sample
				    if (sourceRecord['trigger_detector'].indexOf('L1') != -1) {
				    	L1 = 1
				    }
				    if (sourceRecord['trigger_detector'].indexOf('H1') != -1) {
				    	H1 = 1
				    }
				    if (sourceRecord['trigger_detector'].indexOf('V1') != -1) {
				    	V1 = 1
				    }

				    if (L1 == 1 && H1 == 1 && V1 == 1) {
				    	L1_H1_V2_Triggers = L1_H1_V2_Triggers + 1
				    } else if (L1 == 1 && H1 == 1) {
				    	L1_H1_Triggers = L1_H1_Triggers + 1
				    } else if (L1 == 1 && V1 == 1) {
				    	L1_V1_Triggers = L1_V1_Triggers + 1
				    } else if (H1 == 1 && V1 == 1) {
				    	H1_V1_Triggers = H1_V1_Triggers + 1
				    } else if (L1 == 1) {
				    	L1_Triggers = L1_Triggers + 1
				    } else if (H1 == 1) {
				    	H1_Triggers = H1_Triggers + 1
				    } else if (V1 == 1) {
				    	V1_Triggers = V1_Triggers + 1
				    } else {
						Other_Triggers = Other_Triggers + 1
				    }

				} else {
					Other_Triggers = Other_Triggers + 1
				}

				coverage = sourceRecord['coverage']

				if (sourceRecord['inSAA'] == 'True') {
					no_coverage = no_coverage + 1

				} else if (coverage == null) {
					unknown_coverage = unknown_coverage + 1

				} else if (coverage.length == 0) {
					unknown_coverage = unknown_coverage + 1

				} else {

					coverage = coverage.replace('%','')
					coverage = parseFloat(coverage)

					if (coverage == 0) {
						no_coverage = no_coverage + 1

					} else {
						some_coverage = some_coverage + 1

					}
				}
		    }

			// Bundle up the data points
		    data_triggers = [L1_Triggers,
						H1_Triggers,
						V1_Triggers,
						L1_H1_Triggers,
						L1_V1_Triggers,
						H1_V1_Triggers,
						L1_H1_V2_Triggers,
						Other_Triggers]

			// Bundle up the data points
			data_coverage = [some_coverage,
							no_coverage,
							unknown_coverage]

			// Configure the detector distribution plot
			var config1 = {
				type: 'doughnut',
				data: {
					datasets: [{
						data: data_triggers,
						backgroundColor: [

							// gspec
							// '#394264', 
							// '#6E8846', 
							// '#7B3F5B', 
							// '#aa611d', 
							// '#463965', 

							// dashboard
							// '#488adb',
							// '#29b2b0',
							// '#7c6bd3',
							// '#db5c48', 
							// '#dba548' 

							// Bootstrap
							'#377bb5',
							'#7d69d7',
							'#00695c',
							'#33b5e5', //#eeac57
							'#bf8cd9',
							'#5fb760', //#FF8800
							'#d75452',
							'#4B515D'  //#4B515D

							// Chartjs
							// window.chartColors.orange,
							// window.chartColors.yellow,
							// window.chartColors.green,
							// window.chartColors.blue,

						],
						label: 'Dataset 1'
					}],
					labels: [
						'L1',
						'H1',
						'V1',
						'L1+H1',
						'H1+V1',
						'L1+V1',
						'L1+H1+V1',
						'Other'
					]
				},
				options: {
					responsive: true,
					legend: {
						display: true,
						position: 'bottom',									
						labels: { 
							fontSize: 12,
							boxWidth: 10,
							usePointStyle: false,
							fullWidth: false,
							fontFamily: 'Helvetica Neue',
							fontColor: '#333333'
						}
					},
					title: {
						display: true,
						text: 'Triggering Detector',
						fontFamily: 'Helvetica Neue',
						fontColor: '#333333',
						fontSize: 14

					},
					animation: {
						animateScale: true,
						animateRotate: true
					},
					layout: {
						padding: {
							top: 0
						}
					}
				}
			};

			// Configure the GBM coverage plot
			var config2 = {
				type: 'doughnut',
				data: {
					datasets: [{
						data: data_coverage,
						backgroundColor: [

							// gspec
							// '#394264', 
							// '#6E8846',

							// Dashboard 
							// '#488adb',
							// '#29b2b0',

							// Bootstrap
							'#377bb5',
							'#5fb760',
							'#4B515D'

						],
						label: 'Dataset 2'
					}],
					labels: [
						'Coverge',
						'No Coverage',
						'Unknown'

					]
				},

				// For use with gradient colors
				// data: {
				//     labels: ["A", "B"],
				//     datasets: [{
				//         label: "Status",
				//         backgroundColor: gradients,
				//         borderColor: 'rgba(73, 79, 92, 0)',
				//         data: [randomScalingFactor(), randomScalingFactor()]
				//     }]
				// },

				options: {
					responsive: true,
					legend: {
						display: true,
						position: 'bottom',									
						labels: { 
							fontSize: 12,
							boxWidth: 10,
							fontFamily: 'Helvetica Neue',
							fontColor: '#333333'
						}
					},
					title: {
						display: true,
						text: 'GBM Coverage',
						fontFamily: 'Helvetica Neue',
						fontColor: '#333333',
						fontSize: 14


					},
					animation: {
						animateScale: true,
						animateRotate: true
					}
				}
			};

			// Create the detector distribution plot if it doesn't already exist, otherwise update it
			if (typeof window.myDoughnut1 == "undefined") {
				console.log('Creating detector distribution plot')
			   	var ctx1 = document.getElementById('chart-area_1').getContext('2d');
				window.myDoughnut1 = new Chart(ctx1, config1);
			} else {
				console.log('Updating detector distribution plot')
				window.myDoughnut1.data.datasets[0].data = data_triggers
				window.myDoughnut1.update();
			}

			// Create the GBM coverage plot if it doesn't already exist, otherwise update it
			if (typeof window.myDoughnut2 == "undefined") {
				console.log('Creating GBM coverage plot')
				var ctx2 = document.getElementById('chart-area_2').getContext('2d');
				window.myDoughnut2 = new Chart(ctx2, config2);
				window.myDoughnut2.update();
			} else {
				console.log('Updating GBM coverage plot')
				window.myDoughnut2.data.datasets[0].data = data_coverage
				window.myDoughnut2.update();
			}
		}

		function updateSampleSelection() {

			// Get the currently selected sample
			selectedSample = $("#sampleSelection")[0].value

			console.log("Updating data...")

			// Create a empty list to store the filtered data
			data = []

			// Loop through each record and only keep the triggers in the selected sample
			for (var i=0, size=data_allTriggers.length; i<size; i++) {

				// Get the current source
			    sourceRecord = data_allTriggers[i];

			    if (sourceRecord['sample'] === selectedSample || selectedSample === 'All') {

			    	// Add the matching record to the data subset
			    	data.push(sourceRecord)

			    }

			}

			// Fill the primary table
			fillTable(data);

			// Fill the plots
			fillDoughnutPlots(data)
		}

		// Query the database when the page is finished loading
		$(function() {

			// Turn on the loading spinners in the background
			$(".panel-body").addClass("active")

			// queryDB('TriggerList');

		    // Bind an event to the selection action
		    $('select').on('change', function (e) {
			    var valueSelected = this.value;
			    // console.log("valueSelected: " + valueSelected)

			    // Update the table and plots
			    updateSampleSelection()

			});

			// // Show the modal when the page is done loading
			// $(window).on('load',function(){
			// 	$('#magic_word_dialog').modal('show');
			// });	

			// Check to see if the passphrase data is stored in a cookie
			checkCookie();
				
			// Bind a click event to the form submission	
		    $("#submitForm").on('click', function() {
		        $("#magic_word_form").submit();
		    });

		    // Query the database only after the magic word has been submitted
		    $("#magic_word_form").on("submit", function(e) {

		        // Get the form data
		        var magic_word_submitted=document.forms["magic_word_form"]["magic_word"].value;

		        e.preventDefault();

		        $('#magic_word_dialog').modal('hide');

		        queryDB('TriggerList', magic_word_submitted)

		    });

		});


	</script>

	<!-- main starts here -->		
	<div id="main" style="width:1650px">	
		<div class="background-container">


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
				<H2>Fermi GBM Online Targeted Search</H2>
			</div>
		</div>
		<!-- Header ends here -->

		<!-- sidebar start here -->
	    <div style="width:300px; margin-left:25px; float:left;" id="coordinateInput">

			<!-- Analysis Overview start here -->		
			<div class="panel panel-default"  style="height: 760px;">
				<div class="panel-heading">
			        <h3 class="panel-title">GBM Analysis Overview</h3>
			     </div>
					<center>
					    <!-- Detector distribution plot beings here -->
						<div id="canvas-holder1" style="width:95%; padding:15px 0 0 0px">
							<canvas id="chart-area_1"></canvas>
						</div>
					    <!-- Detector distribution plot ends here -->

					    </BR>

					    <!-- GBM coverage plot beings here -->
						<div id="canvas-holder2" style="width:95%; padding:0px 0 0 0px">
							<canvas id="chart-area_2"></canvas>
						</div>
					    <!-- GBM coverage plot ends here -->

						<div id="sampleSeletionContainer" style="width: 50%; padding-top:25px; display:none;">
							<p style="font-size: 15px; font-weight: bold;">Sample Selection</p>
							<select id="sampleSelection" class="form-control">
							  <option>All</option>
							</select>
						</div>

					</center>
		    </div>
			<!-- Analysis Overview ends here -->	


			<!-- GBM+LIGO Resources start here -->		
			<div id="resources" class="panel panel-default" style="height: 263px; display:none">
				<div class="panel-heading">
			        <h3 class="panel-title">GBM-LIGO Resources</h3>
			     </div>
					<center>
			            <table class="table table-striped">
			              <tbody>					
                            <tr><td><a href="https://gracedb.ligo.org">LIGO GraceDB</a></td><td td id="graceDB"></td></tr>
                            <tr><td><a href="https://www.gw-openscience.org/detector_status/">LVC Detector Status</a></td><td td id="status"></td></tr>
                            <tr><td><a href="https://ldas-jobs.ligo.caltech.edu/~gwistat/gwistat/gwistat.html">LVC Detector Snapshot</a></td><td td id="status"></td></tr>
                            <tr><td><a href="https://gamma-wiki.mpe.mpg.de/GBM/BAInformation?action=AttachFile&do=view&target=GBMpipeline-ShiftManual.pdf">GBM GW Shift Manual</a></td><td td id="gbm_wiki"></td></tr>
                            <tr><td><a href="https://fermi.gsfc.nasa.gov/ssc/data/access/gbm/">GBM Data Access</a></td><td id="gbm_data_access"></td></tr>
                            <tr><td><a href="https://heasarc.gsfc.nasa.gov/W3Browse/fermi/fermigbrst.html">GBM Burst Catalog</a></td><td id="gbm_catalog"></td></tr>
			              </tbody>
			            </table>  
				    </center>
		    </div>
			<!-- GBM+LIGO Resources ends here -->		


		    <!-- Caveat statement start here -->	
			<div>	
				<center>
					<div id="caveats" class="alert alert-info" role="alert" style="display:none;">All analysis results presented here are preliminary unless otherwise stated.</div>
			    </center>
		    </div>
			<!-- Caveat statement ends here -->	

			<!-- Citation request start here -->	
		</div>
		<!-- sidebar ends here -->

		<!-- Content starts here -->
		<div id="content">

			<!-- Targeted search runs panel start here -->	
		    <div style="width:1250px; margin-left: 340px;">
			 	<div class="panel panel-default">
					<div class="panel-heading"><h3 class="panel-title">Targeted Search Runs</h3></div>
				    <div class="panel-body">
				    	<center>
			            <table class="table table-striped table-condensed table-bordered" id="dataTable" style="width:1350px;"></table>  
                        <!-- Pagination buttons -->
                        <nav aria-label="Page navigation">
                            <ul class="pagination" id="pagination"></ul>
                        </nav>
			            </center>
					</div>	
		      	</div>
		    </div>
			<!-- Targeted search runs panel ends here -->

		</div>
		<!-- Content ends here -->

		</div>
	<!-- Main ends here -->
	</div>


	<!-- footer starts here -->	
	<div id="footer">
		<div id="footer-content">
		
			<p>
				<hr>
				Fermi GBM Online Targeted Search - Support Contact:<a href="mailto:daniel.kocevski@nasa.gov"> Daniel Kocevski</a>
			</p>

		</div>
	</div>
	<!-- footer ends here -->


	<!-- Quick GCN modal view starts here -->
	<div id="magic_word_dialog" class="modal fade">
		<div class="modal-dialog" style="width:800px; margin: auto; margin-top:10%">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" style="text-align: center;">Welcome to the Fermi GBM Online Targeted Search Database</h4>
				</div>

				<div class="modal-body center-block" style="text-align: center; height:200px; margin-top:10%">

					
					Please specify the dataset that you would like to examine:
					
					<BR>
					<BR>
				
					<form id="magic_word_form" name='MagicWordForm'>
						<input id="magic_word" type="text" class="input-small" placeholder="">
						</form>

				</div> <!-- /.modal-body -->

				<div class="modal-footer">             
	                <div id="SaveButtonDiv" style="float: right;">
	                    <button type="button" id="submitForm" class="btn btn-primary" style="color:white;font-size: 12px;margin:10px">Submit</button>
	                </div>  
	                <div style="float: right;"> 
	                    <button type="button" class="btn btn-default" data-dismiss="modal" style="font-size: 12px;margin-top:10px">Close</button>
	                </div> 
				</div> <!-- /.modal-footer -->

			</div> <!-- /.modal-content -->
	    </div> <!-- /.modal-dialog -->
	</div> <!-- /.modal -->  



</body>


