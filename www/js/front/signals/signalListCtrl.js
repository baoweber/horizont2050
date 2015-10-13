app.controller("signalListCtrl", function($scope, $http) {

    var _self = this;

    $scope.search = {
        'name' : '',
        categories_id: {},
        challenges: ''
    };

    $http.get('/api/1.0/signals').success(function (responseData) {
        $scope.signals = responseData.results;
        console.log($scope.signals);
    });

    $scope.advSearch = function (item) {

        if (!($scope.search.name === undefined || $scope.search.name == '' || item.name.toLowerCase().indexOf($scope.search.name.toLowerCase()) > -1 )) {
            return false;
        }

        if ( !_self.filterOneInArray($scope.search.categories_id, item.categories_id) ) {
            return false;
        }

        if ( !_self.filterMoreInArray($scope.search.challenges, item.challenges) ) {
            return false;
        }

        if ( !_self.filterOneInArray($scope.search.relevance, item.relevance) ) {
            return false;
        }

        if ( !_self.filterMoreInArray($scope.search.keywords, item.keywords) ) {
            return false;
        }

        return true;
    };

    this.filterMoreInArray = function(array, needleArray) {

        var needleCheck = false;
        if(array === undefined) {
            return true;
        }
        if (!_self.isCheckboxArray(array)) {
            return true;
        }
        angular.forEach(needleArray, function(needle, key){
            if (_self.checkCheckboxArray(array, needle.id)) {
                needleCheck = true;
            }
        });
        if(needleCheck) {
            return true;
        }
        return false;
    };

    this.filterOneInArray = function(array, needle) {
        if(array === undefined) {
            return true;
        }
        if (!_self.isCheckboxArray(array)) {
            return true;
        }
        if (_self.checkCheckboxArray(array, needle)) {
            return true;
        }
        return false;
    };

    this.isCheckboxArray = function (array) {
        var checked = false;
        angular.forEach(array, function(value, key){
            if (value) {
                checked = true;
            }
        });
        return checked;
    };

    this.checkCheckboxArray = function(array, needle) {
        var show = false;
        angular.forEach(array, function(value, key){
            if (needle == key && value == true) {
                show = true;
            }
        });
        return show;
    }

});

app.directive('signalListItem', function() {
    return {
        scope: {
            signal : '='
        },
        templateUrl: '/js/front/signals/signal-list-item.html'
    };
});
