app.controller("signalListCtrl", function($scope, $http) {

    $http.get('/api/1.0/signals').success(function (responseData) {
        $scope.signals = responseData.results;
        console.log($scope.signals);
    });

});

app.directive('signalListItem', function() {
    return {
        scope: {
            signal : '='
        },
        templateUrl: '/js/front/signals/signal-list-item.html'
    };
});
