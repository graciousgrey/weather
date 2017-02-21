define(['controllers/module'], function (controllers) {
    'use strict';
    return controllers.controller('WeatherController', ['$scope', '$routeParams', '$route', 'Api', 'Alert', function ($scope, $routeParams, $route, Api, Alert) {
/// fügt zu angular neuen Controller mit Namen WeatherController hinzu mit dependencies [scope, route params...werden in function
        //übergeben, diese function ist der Controller = dependency injection
        var locationResource = Api.getLocationResource();

        $scope.country = $routeParams.country ? $routeParams.country : 'Germany';//sorgt dafür dass nach suche nicht mehr die defaults in den feldern stehen
        $scope.location = $routeParams.location ? $routeParams.location: 'Berlin';
//$scope entspricht $this, muss bei angular allerdings extra übergeben werden
        //funktion sind wie Klassen in JS
        $scope.weather = locationResource.get({ //ajaxrequest, in weather dann daten gespeichert
            country: $scope.country,
            location: $scope.location
        });

        $scope.search = function () {
            $route.updateParams({location : $scope.location, country : $scope.country});
        } //sobald url sich geändert hat sind alle Variablen wieder leer

    }]);
});
//  /module ist angula di container hierrein definiere ich jetzt den WeatherController, die dependencies (scope, routeParams etc
// gebe ich als Abhängigkeiten dan, sie stehen schon irgendwo anders in dem module container drin