<?php
$data = file_get_contents('hashtagSearch4.csv'); // change the file name here
$api_key = '/* ADD YOUR OWN KEY HERE */';
$lines = explode("\n",$data);

foreach($lines as $key => $value) {
	if($key > 0 && strlen($value) > 20) {
		$line = explode(",", $value);
		$markers[$key] = trim($line[0]).','.trim($line[1]).','.trim($line[2]).','.trim($line[3]); // find relevant information from each row
	}
}
?>

<!DOCTYPE html>
<html>
	<head>
		<title> ENGR 180 Project </title>
		<style type="text/css">
			html, body, #map-canvas {
				height: 100%; margin: 0; padding: 0;
			}
			#legend {
				background: #FFF; padding: 15px; margin: 5px;
				font-size: 12px; font-family: Courier New, sans-serif;
			}
			.color {
				border: 1px solid; height: 12px; width: 12px;
				margin-right: 3px; float: left;
			}
			.red { background: #C00; }
			.yellow { background: #FF3; }
			.gray { background: #008080; }
			.blue { background: #06C; }
		</style>
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?=$api_key?>"></script>
		<script type="text/javascript">
			var map;
			var marker = {};
			var locationColumn = "geometry";
			var tableId = "1FbzvoRkdJzXvIxMJg2mGYVz5Q1BW2-4feMolgVc"; // California Census Tracts
			var condition = "'Median income (dollars)'>0";
			
			function initialize() {
				var geocoder = new google.maps.Geocoder();
				var POS = {lat: 37.000, lng: -120.000}; // center screen at California
				var mapOptions = { center: POS, zoom: 6 };
				map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				
				var layer = new google.maps.FusionTablesLayer({ // Google Fusion Table layer for the Median Household Income
					query: {
						select: locationColumn,
						from: tableId,
						where: condition
					},
					styles: [{
						where: "'Median income (dollars)' < 35000",
						polygonOptions: { fillColor: '#ff0000', fillOpacity: 0.4 }
					}, {
						where: "'Median income (dollars)' > 34999.99 AND 'Median income (dollars)' < 65000",
						polygonOptions: { fillColor: '#ffff00', fillOpacity: 0.4 }
					}, {
						where: "'Median income (dollars)' > 64999.99 AND 'Median income (dollars)' < 100000",
						polygonOptions: { fillColor: '#008080', fillOpacity: 0.4 }
					}, {
						where: "'Median income (dollars)' > 99999.99",
						polygonOptions: { fillColor: '#0000ff', fillOpacity: 0.4 }
					}]
				});
				geocoder.geocode({ 
					'address': 'California'
				 },
				 
				function(results, status) {
					var sw = results[0].geometry.viewport.getSouthWest();
					var ne = results[0].geometry.viewport.getNorthEast();
					var bounds = new google.maps.LatLngBounds(sw, ne);
					map.fitBounds(bounds)
				});
				
				var legend = document.createElement('div');
				legend.id = 'legend';
				var content = [];
				content.push('<h3>Median Household Income</h3>'); // legend with the break down of income disparity
				content.push('<p><div class="color red"></div>Less than 35k</p>');
				content.push('<p><div class="color yellow"></div>35k to 65k</p>');
				content.push('<p><div class="color gray"></div>65 to 100k</p>');
				content.push('<p><div class="color blue"></div>Greater than 100k</p>');
				legend.innerHTML = content.join('');
				legend.index = 1;
				map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend); // legend position
				layer.setMap(map);
				
				var markers = []; // container for the markers
				<?php 
					$counter = 0;
					foreach($markers as $index => $list) { // print marker information
						$marker_details = explode(',',$list);
							echo 'markers["m'.($index-1).'"] = {};'."\n";
							echo "markers['m".($index-1)."'].name = '".$marker_details[0]."';\n";
							echo "markers['m".($index-1)."'].content = '".$marker_details[1]."';\n";
							echo "markers['m".($index-1)."'].lat = '".$marker_details[2]."';\n";
							echo "markers['m".($index-1)."'].lon = '".$marker_details[3]."';\n";
							$counter++;
					}
				?>
				
				var totalMarkers = <?=$counter?>;
				var i = 0,  infowindow, contentString;
				for(var i = 0; i < totalMarkers; i++) { // formatting marker information and put marker on map
					contentString = '<div class="content">' + '<h1 class="firstHeading">' + markers['m'+i].name + '</h1>' + '<div class="bodyContent">' + '<p>' + markers['m'+i].content + '</p>' + '</div>' + '</div>';
					infowindow = new google.maps.InfoWindow({
						content: contentString
					});
					marker['c'+i] = new google.maps.Marker({
						position: new google.maps.LatLng(markers['m'+i].lat, markers['m'+i].lon),
						map: map,
						title: markers['m'+i].name,
						infowindow: infowindow,
						animation: google.maps.Animation.DROP
					});
					google.maps.event.addListener(marker['c'+i], 'click', function() { // display marker information when clicked
						for(var key in marker) {
							marker[key].infowindow.close();
						}
						this.infowindow.open(map, this);
					});
				}
			}
			
			function panMap(la, lo) {
				map.panTo(new google.maps.LatLng(la, lo));
			}
			
			function openMarker(mName) { // if any marker is opened and another one is clicked, close the other marker
				for(var key in marker) {
					marker[key].infowindow.close();
				}
				for(var key in marker) {
					if(marker[key].title.search(mName) != -1) {
						marker[key].infowindow.open(map,marker[key]);
					}
				}
			}
			
			google.maps.event.addDomListener(window, 'load', initialize);
</script>
	</head>
<body>
	<div id="map-canvas"></div>
</body>
</html>
