app.controller("signalListCtrl", function($scope, $http) {

    $scope.search = {
        'name' : '',
        categories_id: '',
        challenges: ''
    };

    $http.get('/api/1.0/signals').success(function (responseData) {
        $scope.signals = responseData.results;
        console.log($scope.signals);
    });

    $scope.advSearch = function (item) {

        var returnValue =  true;

        if (!($scope.search.name === undefined || $scope.search.name == '' || item.name.toLowerCase().indexOf($scope.search.name.toLowerCase()) > -1 )) {
            returnValue = false;
        }

        if (!($scope.search.name === undefined || $scope.search.name == '' || item.title.toLowerCase().indexOf($scope.search.name.toLowerCase()) > -1 )) {
            returnValue = false;
        }

        if (!($scope.search.categories_id === undefined || $scope.search.categories_id === '' || $scope.search.categories_id == item.categories_id)) {
            returnValue = false;
        }

        var challengesFilter = false;
        angular.forEach(item.challenges, function(pdata, index) {
            if (pdata.id == $scope.search.challenges) {
                challengesFilter = true;
            }
        });

        if(!( $scope.search.challenges === undefined || $scope.search.challenges === '' || challengesFilter == true)) {
            returnValue = false;
        }

        return returnValue;
    };

});

app.directive('signalListItem', function() {
    return {
        scope: {
            signal : '='
        },
        templateUrl: '/js/front/signals/signal-list-item.html'
    };
});
