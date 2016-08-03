(function(){
	var app = angular.module('home', []);
	app.controller('HomeController',function($scope, $http, $sce){
	});

	app.directive('homePage', function(){
		return{
			restrict: 'E',
			templateUrl: 'teachers.html',
			controller: ['$http','$scope','$sce','filterFilter', '$filter', function($http, $scope, $sce, filterFilter, $filter){
				var teachersCtrl = this;
				this.teachers = teachers;

				$scope.renderHtml = function (htmlCode) {
					return $sce.trustAsHtml(htmlCode);
				};

				$scope.search = {};

				$scope.resetFilters = function () {
					$scope.search = {};
				};
				$scope.setPage = function(page){
					$scope.currentPage = page;
				}
				$scope.currentPage = 0;
				$scope.pageSize = 10;
				$scope.totalItems = this.teachers.length;
				$scope.noOfPages = Math.ceil(this.teachers.length/$scope.pageSize);

				$scope.$watch('search', function (newVal, oldVal) {
					$scope.filtered = filterFilter(teachersCtrl.teachers, newVal.query);
					$scope.totalItems = $scope.filtered.length;
					$scope.noOfPages = Math.ceil($scope.totalItems / $scope.pageSize);
					$scope.currentPage = 0;
				}, true);
				var orderBy = $filter('orderBy');
				$scope.random = function() { return 0.5 - Math.random(); }
				$scope.predicate = 'random';
				$scope.reverse = true;
				$scope.order = function(predicate) {
					$scope.setPage(0);
					$scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
					$scope.predicate = predicate;
					if(predicate != 'random') teachersCtrl.teachers = orderBy(teachersCtrl.teachers, predicate, $scope.reverse);
					else teachersCtrl.teachers = orderBy(teachersCtrl.teachers, $scope.random, $scope.reverse);
				};

				$scope.order('random', true);
			}],
			controllerAs: 'teachersCtrl',

		}
	});

	app.filter('startFrom', function() {
		return function(input, start) {
			start = +start;
			return input.slice(start);
		}
	});


	app.filter('range', function() {
		return function (input, total) {
			total = parseInt(total);

			for (var i = 0; i < total; i++) {
				input.push(i);
			}

			return input;
		};
	});


})();