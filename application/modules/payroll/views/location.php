<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
	<meta name="description" content="">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

	
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<script src="https://code.jquery.com/jquery-3.1.1.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
	
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	
        
  </head>
  <body>  	
	<div class="container">
	<br><br>
	<div class="row text-center">		
		<div class="form-group">
		  <div class="col-md-4 col-md-offset-4">
		  <input id="direccion" name="direccion" type="text" placeholder="Buscar" class="form-control input-md validate[required, minSize[3]]">					
		  <input id="latitud" name="latitud" type="text">					
		  <input id="longitud" name="longitud" type="text">					
		  </div>
		</div>
	</div>
	<br><br>
	<div class="row text-center">	
		<div id="map" style="width: 100%; height: 350px">
		
		</div>			
	</div>
	
  <script>
    // The following example creates complex markers to indicate beaches near
	// Sydney, NSW, Australia. Note that the anchor is set to (0,32) to correspond
	// to the base of the flagpole.

	var options = {
	  enableHighAccuracy: true,
	  timeout: 5000,
	  maximumAge: 0
	};

	function success(pos) {
	  var crd = pos.coords;

	  console.log('Your current position is:');
	  console.log('Latitude : ' + crd.latitude);
	  console.log('Longitude: ' + crd.longitude);
	  console.log('More or less ' + crd.accuracy + ' meters.');
	  $("#latitud").val(crd.latitude);
	  $("#longitud").val(crd.longitude);
	  var pos = {
				  lat: crd.latitude,
				  lng: crd.longitude
				};
	  map.setCenter(pos);
	  map.setZoom(14);
	  
	  
	  ultimaPosicionUsuario = new google.maps.LatLng(crd.latitude, crd.longitude);
      marcadorUsuario = new google.maps.Marker({
        position: ultimaPosicionUsuario,
        map: map
      });
	};

	function error(err) {
	  console.warn('ERROR(' + err.code + '): ' + err.message);
	};

	function handleLocationError(browserHasGeolocation, infoWindow, pos) {
		infoWindow.setPosition(pos);
		infoWindow.setContent(browserHasGeolocation ?
							  'Error: Error en el servicio de localizacion.' :
							  'Error: Navegador no soporta geolocalizacion.');
	  }
	
	
	function initMap() {
		var pais = new google.maps.LatLng(51.0209884,-114.1591999);
		var mapOptions = {
			center: pais,
			zoom: 11,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
		
		map = new google.maps.Map(document.getElementById('map'), mapOptions);
		
		
		
		//Inicializa el objeto geocoder
		geocoder = new google.maps.Geocoder();
		
		//Inicializa el objeto para autocompletado en el campo de texto para la direcci?n
		autocompletar = new google.maps.places.Autocomplete((
				document.getElementById("direccion")), {});
		lugares = new google.maps.places.PlacesService(map);

		//Configura el listener para cuando se elige la direcci?n
		autocompletar.addListener("place_changed", function () {
			var lugar = autocompletar.getPlace();
			if (lugar.geometry) {
				
				map.panTo(lugar.geometry.location);
				map.setZoom(14);				
			} else {
				document.getElementById("direccion").placeholder = "Busca la direcci&oacute;n y/o lugar de estudio/artista";
			}
		});
		
		
		
		navigator.geolocation.getCurrentPosition(success, error, options);
		
		/*var infoWindow = new google.maps.InfoWindow({map: map});
		// Try HTML5 geolocation.
        if (navigator.geolocation) {
			  navigator.geolocation.getCurrentPosition(function(position) {
				var pos = {
				  lat: position.coords.latitude,
				  lng: position.coords.longitude
				};

				infoWindow.setPosition(pos);
				infoWindow.setContent('Su ubicacion.');
				map.setCenter(pos);
			  }, function() {
				handleLocationError(true, infoWindow, map.getCenter());
			  });
			} else {
			  // Browser doesn't support Geolocation
			  handleLocationError(false, infoWindow, map.getCenter());
			}
*/
	}	

  </script>

	
		   
	</div> 		
	<script async defer		
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDi5PwRbCh0Jsgqgtnrc6dsb7za4wyq3c8&libraries=places&callback=initMap">
	</script>
  </body>
</html>