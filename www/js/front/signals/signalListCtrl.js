app.controller("signalListCtrl", function($scope, $http) {

    $http.get('/api/1.0/signals').success(function (responseData) {
        $scope.signals = responseData;
        console.log($scope.signals);
    });
});
