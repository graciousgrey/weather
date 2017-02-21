
define(['app', 'angular', 'angularRoute'], function (app) {

    app.config(['$locationProvider', function($locationProvider) {
        $locationProvider.hashPrefix('');
    }]);

    app.config(['$routeProvider', function ($routeProvider) {
        $routeProvider.when('/home', {
            templateUrl: '/partials/home.html',
            controller: 'HomeController'
        }).when('/users', {
            templateUrl: '/partials/users.html',
            controller: 'UsersController'
        }).when('/weather', {
            templateUrl: '/partials/weather.html',
            controller: 'WeatherController'
        }).when('/weather/:country/:location', {
            templateUrl: '/partials/weather.html',
            controller: 'WeatherController'
        }).when('/profile/:userId', {
            templateUrl: '/partials/profile.html',
            controller: 'ProfileController'
        }).otherwise({redirectTo: '/home'});
    }]);

});