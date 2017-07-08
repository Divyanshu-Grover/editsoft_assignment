<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!doctype html>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

	<link href='https://fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>
	<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
<style type="text/css">
	.dataJSON{
		width: 69%;
		margin-top: 5%;
	}

	.map{
		margin-top: 100px;
		margin-left: 100px;
		
	}
</style>

</head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>

<body ng-app = "formExample">
 
<div class="row">
  <div class="col-md-8">
     
    <!--  <iframe class="map" 
     	width="600"
     	height="450"
       frameborder="0" style="border:0"
       src="https://www.google.com/maps/embed/v1/place?key=AIzaSyA0_YHN3bbKJOfNTH-UsuEQh2i970MD39A
         &q=Editsoft Solutions Pvt Ltd, New Delhi" allowfullscreen>
     </iframe> -->
     <div id="map"></div>
     <div id="output"></div>
    <!--  <div id="map-canvas" style="width:660px; height:560px;"></div>
     <div class="hr vpad"></div> -->

  </div>
  
  <div class="col-md-4">
     <h1>4 Input points:</h1>
     <div ng-controller="ExampleController">
       <form novalidate class="simple-form form-group">
         <label>Input point 1 latitude: <input type="number" ng-model="user.point1.lat" class="form-control" /></label><br />
         <label>Input point 1 longitude: <input type="number" ng-model="user.point1.long" class="form-control" /></label><br />
         <label>Input point 2 latitude: <input type="number" ng-model="user.point2.lat" class="form-control"  /></label><br />
         <label>Input point 2 longitude: <input type="number" ng-model="user.point2.long" class="form-control"  /></label><br />
         <!-- <label>Input point 3 latitude: <input type="number" ng-model="user.point3.lat" class="form-control"  /></label><br />
         <label>Input point 3 longitude: <input type="number" ng-model="user.point3.long" class="form-control"  /></label><br />
         <label>Input point 4 latitude: <input type="number" ng-model="user.point4.lat" class="form-control"  /></label><br />
         <label>Input point 4 longitude: <input type="number" ng-model="user.point4.long" class="form-control"  /></label><br /> -->
         <input type="submit" ng-click="update(user)" value="Save" />
         <input type="button" ng-click="reset()" value="Reset" />
       </form>
       <pre class = "dataJSON">data = {{master | json}}</pre>
     </div>
  </div>
</div>  

</body>
   <script>
   	var pointsarray = [];
	var order[4];
	var visit[4];
 	var dist[4][4];



	function setpoints(points){
		
		for(point in points) {
		    pointsarray.push(points[point]);
		}
		console.log(pointsarray);
		
		

	}

	angular.module('formExample', [])
	.controller('ExampleController', ['$scope', function($scope) {
	 $scope.master = {};

	 $scope.update = function(user) {
	   $scope.master = angular.copy(user);
	   pointsarray = [];
	   setpoints($scope.master);
	   calculate_dist();
	   
	   
	 };

	 $scope.reset = function() {
	   $scope.user = angular.copy($scope.master);
	 };
	 $scope.reset();
	}]);

   </script>
   
   <script type="text/javascript">
   
   function initMap(){

   	var bounds = new google.maps.LatLngBounds;
   	var markersArray = [];
   	var destinationIcon = 'https://chart.googleapis.com/chart?' +
   	    'chst=d_map_pin_letter&chld=D|FF0000|000000';
   	var originIcon = 'https://chart.googleapis.com/chart?' +
   	    'chst=d_map_pin_letter&chld=O|FFFF00|000000';
   	var map = new google.maps.Map(document.getElementById('map'), {
   	  center: {lat: 55.53, lng: 9.4},
   	  zoom: 10
   	});

   	var geocoder = new google.maps.Geocoder;


   }

   function calculate_dist(){

   	 var origin1 = {lat: pointsarray[0].lat, lng: pointsarray[0].long};
   	 var dest1 = {lat: pointsarray[1].lat, lng: pointsarray[1].long};
     var bounds = new google.maps.LatLngBounds;
     var markersArray = [];
     var destinationIcon = 'https://chart.googleapis.com/chart?' +
         'chst=d_map_pin_letter&chld=D|FF0000|000000';
     var originIcon = 'https://chart.googleapis.com/chart?' +
         'chst=d_map_pin_letter&chld=O|FFFF00|000000';
     var map = new google.maps.Map(document.getElementById('map'), {
       center: {lat: 55.53, lng: 9.4},
       zoom: 10
     });

     var geocoder = new google.maps.Geocoder;

     var service = new google.maps.DistanceMatrixService;
     service.getDistanceMatrix({
       origins: [origin1],
       destinations: [dest1],
       travelMode: 'DRIVING',
       unitSystem: google.maps.UnitSystem.METRIC,
       avoidHighways: false,
       avoidTolls: false
     }, function(response, status) {
       if (status !== 'OK') {
         alert('Error was: ' + status);
       } else {
         var originList = response.originAddresses;
         var destinationList = response.destinationAddresses;
         var outputDiv = document.getElementById('output');
         outputDiv.innerHTML = '';
         

         var showGeocodedAddressOnMap = function(asDestination) {
           var icon = asDestination ? destinationIcon : originIcon;
           return function(results, status) {
             if (status === 'OK') {
               map.fitBounds(bounds.extend(results[0].geometry.location));
               markersArray.push(new google.maps.Marker({
                 map: map,
                 position: results[0].geometry.location,
                 icon: icon
               }));
             } else {
               alert('Geocode was not successful due to: ' + status);
             }
           };
         };

         for (var i = 0; i < originList.length; i++) {
           var results = response.rows[i].elements;
           geocoder.geocode({'address': originList[i]},
               showGeocodedAddressOnMap(false));
           for (var j = 0; j < results.length; j++) {
             geocoder.geocode({'address': destinationList[j]},
                 showGeocodedAddressOnMap(true));
             outputDiv.innerHTML += results[j].distance.text + ' in ' +
                 results[j].duration.text + '<br>';
           }
         }
       }
     });
   
   }


   </script>
   	<script async defer
	src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA0_YHN3bbKJOfNTH-UsuEQh2i970MD39A&callback=initMap"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    
      <!--Flot end-->

</html>

<!-- 
55.93
-3.118
50.087
14.421 
-->