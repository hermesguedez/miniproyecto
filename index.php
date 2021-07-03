<?php
	if (isset($_FILES['video'])) {
		$video = $_FILES['video']['tmp_name'];
		$ffmpeg = "ffmpeg";
		$imgName = "thumbnail".rand(0,100).".jpg";
		$size = "300x300";
		$seconds = getDuration($video);
		$getFromSecond = rand(1,$seconds);
		$cmd = "$ffmpeg -i  $video -ss $getFromSecond -s $size $imgName";
		shell_exec($cmd);
		$location = shell_exec("exiftool -LocationInformation $video");
		$explode = explode("=",$location);
		$latitud = str_replace(" Lon","",$explode[2]);
		$longitud = str_replace(" Alt","",$explode[3]);
		shell_exec("exiftool -exif:GPSLatitude=$latitud -exif:GPSLongitude=$longitud $imgName");
	}
	function getDuration($video){
	   $dur = shell_exec("ffmpeg -i $video 2>&1");
	   if(preg_match("/: Invalid /", $dur)){
	      return false;
	   }
	   preg_match("/Duration: (.{2}):(.{2}):(.{2})/", $dur, $duration);
	   if(!isset($duration[1])){
	      return false;
	   }
	   return $duration[3];
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Mini proyecto</title>
		<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<style>
	  		#map {
		    	height: 500px;
		    	width: 100%;
		  	}
		  	html, body {
		    	height: ;
		    	margin: 0;
		    	padding: 0;
	  		}
		</style>
 	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  		<div class="container-fluid">
	    		<a class="navbar-brand" href="#">Automapp</a>
	    		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
	      			<span class="navbar-toggler-icon"></span>
	    		</button>
	  		</div>
		</nav>
		<div class="container-fluid">
			<div id="cargar" class="row mt-3">
				<div class="col-md-8 offset-md-2">
					<form method="POST" action="" enctype="multipart/form-data">
						<div class="row">
							<h4>Subir video</h4>
							<div class="form-group col-md-6">
								<input type="file" name="video" id="video" class="form-control">
							</div>
							<div class="form-group col-md-6">
								<button class="btn btn-dark form-control">Subir</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="row mt-3">
				<div class="col-md-8 offset-md-2">
					<div id="map"></div>
				</div>
			</div>
		</div>
		<script>
			let map;
			function initMap() {
				//const uluru = { lat: -23.676796877781765, lng: -70.40106282238955 };
				const location = { lat: <?=$latitud?>, lng: <?=$longitud?> };
	  			const map = new google.maps.Map(document.getElementById("map"), {
		    		zoom: 18,
		    		center: location,
		  		});
		  		const content = `
		  			<div class="card" style="width:300px; height:275px; border:none;">
	  					<img src="<?=$imgName?>" class="card-img-top" alt="..." style="width:300px; height:275px;">
					</div>
		  		`
		  		const infowindow = new google.maps.InfoWindow({
				    content: content,
				  });

		  		const marker = new google.maps.Marker({
		    		position: location,
		    		map: map,
		  		});
		  		marker.addListener("click", () => {
	    			infowindow.open({
		      			anchor: marker,
		      			map,
		  				shouldFocus: false,
		    		});
	  			});
			}
		</script>
		<script
	      src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap&libraries=&v=weekly"
	      async
	    ></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>