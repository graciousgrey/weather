define(['services/module'], function (services) {
    'use strict';
    services.service('Api', ['$resource', function ($resource) {
        function userFactory () {
            return $resource(
                "/api/user/:user_id",
                {
                    user_id: "@user_id"
                },
                {
                    update: { method: 'PUT' }
                }
            );
        }

        function locationFactory () {
            return $resource(
                "/api/country/:country/location/:location", // :bla meint dann values
                {
                    country: "@country",
                    location: "@location"
                }
            );
        }

        return {
            getUserResource : userFactory,
            getLocationResource : locationFactory
        }
    }]);
});
