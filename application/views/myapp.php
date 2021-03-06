<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">


<style type="text/css">
	.dataJSON{
		width: 69%;
		margin-top: 5%;
	}

	.mymap{
		margin-top: 100px;
		margin-left: 100px;
	}

	#infowindow-content {
	  display: none;
	}

	#map #infowindow-content {
	  display: inline;
	}
</style>
</head>
<body ng-app = "formforinputs">
<div class="row">
  <div class="col-md-8">
  	<div id="map-canvas" class= "mymap"style="width:660px; height:560px;"></div>
  	<div class="hr vpad"></div>	
  </div>
  <div class="col-md-4">
  	

  	<h1>Max 4 Input points:</h1>
	<div ng-controller="Mycontroller">
	<form novalidate class="simple-form form-group">
	 <label>Input point 1: 
	 <input id="autocomplete1" ng-model="user.point1" class="form-control"></input>
	 </label><br />
	 <label>Input point 2: 
	 	<input id="autocomplete2" ng-model="user.point2" class="form-control" type="text"></input>
	 </label><br />
	 <label>Input point 3: 
	 	<input id="autocomplete3" ng-model="user.point3" class="form-control" type="text"></input>
	 </label><br />
	 <label>Input point 4: 
	 	<input id="autocomplete4" ng-model="user.point4" class="form-control" type="text"></input>
	 </label><br />
	 <!-- <input type="submit" ng-click="update(user)" value="Save" class = "submit-button"  /> -->
	 <!-- <input type="button" id= "reset-autofill" ng-click="reset()" value="Reset autofill data" /> -->
	</form>

	<div>
	  <table style="margin-top: 10%">
	      <tr class="ga-info" style="display:none">
	          <td >Generations: </td><td id="generations-passed">0</td>
	      </tr>
	      <tr class="ga-info" style="display:none">
	          <td id="time">Best Time: </td><td id="best-time">?</td>
	      </tr>
	      <tr id="ga-buttons">
	          <td colspan="2"><button id="find-route">Start</button> <button id="clear-map">Clear</button></td>
	      </tr>
	  </table>
	</div>
	</div>
  </div>
</div>
</body>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyA0_YHN3bbKJOfNTH-UsuEQh2i970MD39A&libraries=places"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" type="text/javascript"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!--Flot end-->
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>

<script type="text/javascript">
var map;
var directionsDisplay = null;
var directionsService;
var polylinePath;
var nodes = [];
var prevNodes = [];
var markers = [];
var durations = [];

var placeSearch, autocomplete;


function initAutocomplete() {
  
  autocomplete = new google.maps.places.Autocomplete(
      (document.getElementById('autocomplete1')),
      {types: ['geocode']});

  autocomplete.addListener('place_changed', createMarkers);

  autocomplete2 = new google.maps.places.Autocomplete(
      (document.getElementById('autocomplete2')),
      {types: ['geocode']});

  autocomplete2.addListener('place_changed', createMarkers);
  
  autocomplete3 = new google.maps.places.Autocomplete(
      (document.getElementById('autocomplete3')),
      {types: ['geocode']});

  autocomplete3.addListener('place_changed', createMarkers);
  
  autocomplete4 = new google.maps.places.Autocomplete(
      (document.getElementById('autocomplete4')),
      {types: ['geocode']});

  autocomplete4.addListener('place_changed', createMarkers);
}

function createMarkers() {
  
  var place = autocomplete.getPlace();
  marker = new google.maps.Marker({position: place.geometry.location, map: map});
  markers.push(marker);
  nodes.push(place.geometry.location);
  
  var place2 = autocomplete2.getPlace();
  marker = new google.maps.Marker({position: place2.geometry.location, map: map});
  markers.push(marker);
  nodes.push(place2.geometry.location);
  
  
  var place3 = autocomplete3.getPlace();
  marker = new google.maps.Marker({position: place3.geometry.location, map: map});
  markers.push(marker);
  nodes.push(place3.geometry.location);
  
  
  var place4 = autocomplete4.getPlace();
  marker = new google.maps.Marker({position: place4.geometry.location, map: map});
  markers.push(marker);
  nodes.push(place4.geometry.location);
  
}


function initializeMap() {
    // Map options
    var opts = {
        center: new google.maps.LatLng(28.613939, 77.209021),
        zoom: 10,
        streetViewControl: false,
        mapTypeControl: false,
    };
    map = new google.maps.Map(document.getElementById('map-canvas'), opts);
}

function getDurations(callback) {
    var service = new google.maps.DistanceMatrixService();
    service.getDistanceMatrix({
        origins: nodes,
        destinations: nodes,
        travelMode: 'DRIVING',
        avoidHighways: false,
        avoidTolls: false,
    }, function(distanceData) {
        // Create duration data array
        var nodeDistanceData;
        for (originNodeIndex in distanceData.rows) {
            nodeDistanceData = distanceData.rows[originNodeIndex].elements;
            durations[originNodeIndex] = [];
            for (destinationNodeIndex in nodeDistanceData) {
                if (durations[originNodeIndex][destinationNodeIndex] = nodeDistanceData[destinationNodeIndex].duration == undefined) {
                    alert('Error: couldn\'t get a trip duration from API');
                    return;
                }
                durations[originNodeIndex][destinationNodeIndex] = nodeDistanceData[destinationNodeIndex].duration.value;
            }
        }
        if (callback != undefined) {
            callback();
        }
    });
}

function clearMapMarkers() {
    for (index in markers) {
        markers[index].setMap(null);
    }
    prevNodes = nodes;
    nodes = [];
    if (polylinePath != undefined) {
        polylinePath.setMap(null);
    }
    
    markers = [];
    
    $('#ga-buttons').show();
}

function clearDirections() {
    // If there are directions being shown, clear them
    if (directionsDisplay != null) {
        directionsDisplay.setMap(null);
        directionsDisplay = null;
    }
}

function clearMap() {
    clearMapMarkers();
    clearDirections();
    markers = [];
    nodes = [];
    
}

google.maps.event.addDomListener(window, 'load', initializeMap);
google.maps.event.addDomListener(window, 'load', initAutocomplete);


$(document).ready(function() {
    $('#clear-map').click(function(){
    	clearMap();
    	location.reload();

    });
    // Start GA
    $('#find-route').click(function() {    
        if (nodes.length < 2) {
            if (prevNodes.length >= 2) {
                nodes = prevNodes;
            } else {
                alert('Refill auto complete after clicking on Reset Autofill Button');
                return;
            }
        }
        if (directionsDisplay != null) {
            directionsDisplay.setMap(null);
            directionsDisplay = null;
        }
        
        $('#ga-buttons').hide();
        // Get route durations
        getDurations(function(){
            
            ga.getConfig();
            var pop = new ga.population();
            pop.initialize(nodes.length);
            var route = pop.getFittest().chromosome;
            ga.evolvePopulation(pop, function(update) {
                $('#generations-passed').html(update.generation);
                $('#best-time').html((update.population.getFittest().getDistance() / 60).toFixed(2) + ' Mins');
            
                // Get route coordinates
                var route = update.population.getFittest().chromosome;
                var routeCoordinates = [];
                for (index in route) {
                    routeCoordinates[index] = nodes[route[index]];
                }
                routeCoordinates[route.length] = nodes[route[0]];
                // Display temp. route
                if (polylinePath != undefined) {
                    polylinePath.setMap(null);
                }
                polylinePath = new google.maps.Polyline({
                    path: routeCoordinates,
                    strokeColor: "#0066ff",
                    strokeOpacity: 0.75,
                    strokeWeight: 2,
                });
                polylinePath.setMap(map);
            }, function(result) {
                // Get route
                route = result.population.getFittest().chromosome;
                // Add route to map
                directionsService = new google.maps.DirectionsService();
                directionsDisplay = new google.maps.DirectionsRenderer();
                directionsDisplay.setMap(map);
                var waypts = [];
                for (var i = 1; i < route.length; i++) {
                    waypts.push({
                        location: nodes[route[i]],
                        stopover: true
                    });
                }
                
                // Add final route to map
                var request = {
                    origin: nodes[route[0]],
                    destination: nodes[route[0]],
                    waypoints: waypts,
                    travelMode: 'DRIVING',
                    avoidHighways: false,
                    avoidTolls: false
                };
                directionsService.route(request, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        directionsDisplay.setDirections(response);
                    }
                    clearMapMarkers();
                });
            });
        });
    });
});
// Gwnwtic Algo
var ga = {
    "crossoverRate": 0.5,
    "mutationRate": 0.1,
    "populationSize": 50,
    "tournamentSize": 5,
    "elitism": true,
    "maxGenerations": 50,
    
    "tickerSpeed": 60,
    // Loads config from HTML inputs
    "getConfig": function() {
        ga.crossoverRate = 0.5;
        ga.mutationRate = 50;
        ga.populationSize = 50;
        ga.elitism = false;
        ga.maxGenerations = 50;
    },
    
    // Evolves given population
    "evolvePopulation": function(population, generationCallBack, completeCallBack) {        
        // Start evolution
        var generation = 1;
        var evolveInterval = setInterval(function() {
            if (generationCallBack != undefined) {
                generationCallBack({
                    population: population,
                    generation: generation,
                });
            }
            // Evolve population
            population = population.crossover();
            population.mutate();
            generation++;
            
            // If max generations passed
            if (generation > ga.maxGenerations) {
                // Stop looping
                clearInterval(evolveInterval);
                
                if (completeCallBack != undefined) {
                    completeCallBack({
                        population: population,
                        generation: generation,
                    });
                }
            }
        }, ga.tickerSpeed);
    },
    // Population class
    "population": function() {
        // Holds individuals of population
        this.individuals = [];
    
        // Initial population of random individuals with given chromosome length
        this.initialize = function(chromosomeLength) {
            this.individuals = [];
    
            for (var i = 0; i < ga.populationSize; i++) {
                var newIndividual = new ga.individual(chromosomeLength);
                newIndividual.initialize();
                this.individuals.push(newIndividual);
            }
        };
        
        // Mutates current population
        this.mutate = function() {
            var fittestIndex = this.getFittestIndex();
            for (index in this.individuals) {
                // Don't mutate if this is the elite individual and elitism is enabled 
                if (ga.elitism != true || index != fittestIndex) {
                    this.individuals[index].mutate();
                }
            }
        };
        // Applies crossover to current population and returns population of offspring
        this.crossover = function() {
            // Create offspring population
            var newPopulation = new ga.population();
            
            // Find fittest individual
            var fittestIndex = this.getFittestIndex();
            for (index in this.individuals) {
                // Add unchanged into next generation if this is the elite individual and elitism is enabled
                if (ga.elitism == true && index == fittestIndex) {
                    // Replicate individual
                    var eliteIndividual = new ga.individual(this.individuals[index].chromosomeLength);
                    eliteIndividual.setChromosome(this.individuals[index].chromosome.slice());
                    newPopulation.addIndividual(eliteIndividual);
                } else {
                    // Select mate
                    var parent = this.tournamentSelection();
                    // Apply crossover
                    this.individuals[index].crossover(parent, newPopulation);
                }
            }
            
            return newPopulation;
        };
        // Adds an individual to current population
        this.addIndividual = function(individual) {
            this.individuals.push(individual);
        };
        // Selects an individual with tournament selection
        this.tournamentSelection = function() {
            // Randomly order population
            for (var i = 0; i < this.individuals.length; i++) {
                var randomIndex = Math.floor(Math.random() * this.individuals.length);
                var tempIndividual = this.individuals[randomIndex];
                this.individuals[randomIndex] = this.individuals[i];
                this.individuals[i] = tempIndividual;
            }
            // Create tournament population and add individuals
            var tournamentPopulation = new ga.population();
            for (var i = 0; i < ga.tournamentSize; i++) {
                tournamentPopulation.addIndividual(this.individuals[i]);
            }
            return tournamentPopulation.getFittest();
        };
        
        // Return the fittest individual's population index
        this.getFittestIndex = function() {
            var fittestIndex = 0;
            // Loop over population looking for fittest
            for (var i = 1; i < this.individuals.length; i++) {
                if (this.individuals[i].calcFitness() > this.individuals[fittestIndex].calcFitness()) {
                    fittestIndex = i;
                }
            }
            return fittestIndex;
        };
        // Return fittest individual
        this.getFittest = function() {
            return this.individuals[this.getFittestIndex()];
        };
    },
    // Individual class
    "individual": function(chromosomeLength) {
        this.chromosomeLength = chromosomeLength;
        this.fitness = null;
        this.chromosome = [];
        // Initialize random individual
        this.initialize = function() {
            this.chromosome = [];
            // Generate random chromosome
            for (var i = 0; i < this.chromosomeLength; i++) {
                this.chromosome.push(i);
            }
            for (var i = 0; i < this.chromosomeLength; i++) {
                var randomIndex = Math.floor(Math.random() * this.chromosomeLength);
                var tempNode = this.chromosome[randomIndex];
                this.chromosome[randomIndex] = this.chromosome[i];
                this.chromosome[i] = tempNode;
            }
        };
        
        // Set individual's chromosome
        this.setChromosome = function(chromosome) {
            this.chromosome = chromosome;
        };
        
        // Mutate individual
        this.mutate = function() {
            this.fitness = null;
            
            // Loop over chromosome making random changes
            for (index in this.chromosome) {
                if (ga.mutationRate > Math.random()) {
                    var randomIndex = Math.floor(Math.random() * this.chromosomeLength);
                    var tempNode = this.chromosome[randomIndex];
                    this.chromosome[randomIndex] = this.chromosome[index];
                    this.chromosome[index] = tempNode;
                }
            }
        };
        
        // Returns individuals route distance
        this.getDistance = function() {
            var totalDistance = 0;
            for (index in this.chromosome) {
                var startNode = this.chromosome[index];
                var endNode = this.chromosome[0];
                if ((parseInt(index) + 1) < this.chromosome.length) {
                    endNode = this.chromosome[(parseInt(index) + 1)];
                }
                totalDistance += durations[startNode][endNode];
            }
            
            totalDistance += durations[startNode][endNode];
            
            return totalDistance;
        };
        // Calculates individuals fitness value
        this.calcFitness = function() {
            if (this.fitness != null) {
                return this.fitness;
            }
        
            var totalDistance = this.getDistance();
            this.fitness = 1 / totalDistance;
            return this.fitness;
        };
        // Applies crossover to current individual and mate, then adds it's offspring to given population
        this.crossover = function(individual, offspringPopulation) {
            var offspringChromosome = [];
            // Add a random amount of this individual's genetic information to offspring
            var startPos = Math.floor(this.chromosome.length * Math.random());
            var endPos = Math.floor(this.chromosome.length * Math.random());
            var i = startPos;
            while (i != endPos) {
                offspringChromosome[i] = individual.chromosome[i];
                i++
                if (i >= this.chromosome.length) {
                    i = 0;
                }
            }
            // Add any remaining genetic information from individual's mate
            for (parentIndex in individual.chromosome) {
                var node = individual.chromosome[parentIndex];
                var nodeFound = false;
                for (offspringIndex in offspringChromosome) {
                    if (offspringChromosome[offspringIndex] == node) {
                        nodeFound = true;
                        break;
                    }
                }
                if (nodeFound == false) {
                    for (var offspringIndex = 0; offspringIndex < individual.chromosome.length; offspringIndex++) {
                        if (offspringChromosome[offspringIndex] == undefined) {
                            offspringChromosome[offspringIndex] = node;
                            break;
                        }
                    }
                }
            }
            // Add chromosome to offspring and add offspring to population
            var offspring = new ga.individual(this.chromosomeLength);
            offspring.setChromosome(offspringChromosome);
            offspringPopulation.addIndividual(offspring);
        };
    },
};


angular.module('formforinputs', [])
.controller('Mycontroller', ['$scope', function($scope) {
 $scope.master = {};

 $scope.update = function(user) {
   $scope.master = angular.copy(user);
 };

 $scope.reset = function() {
   $scope.master = {};
   $scope.user = angular.copy($scope.master);
 };
 $scope.reset();
}]);

</script>

</html>