/**
 * Created by julian on 5/09/14.
 */
;
(function () {
    "use strict";

    var inputLinkFunc = function(scope, el, attrs, form) {

        scope.tt = {
            id: G.guid(),
            type: 'text'
        };

        scope.form = form.scope;

        var attribs = [
            'placeholder',
            'label',
            'required'
        ];

        // Proccess extra attributes
        angular.forEach(attribs, function(attr) {
            scope.tt[attr] = attrs[attr] ? scope.$eval(attrs[attr]) : '';
        });

        scope.isRequired = function() {
            if (scope.tt.required) {
                return scope.tt.required;
            }

            return false;
        };

        scope.hasErrors = function() {
            return scope.getErrors().length > 0;
        };

        scope.getErrors = function() {
            if (scope.form.errors
                && scope.form.errors[scope.property]
                && scope.form.errors[scope.property].errors) {
                return scope.form.errors[scope.property].errors;
            }

            return [];
        };
    };

    angular.module(G.APP)
        .directive('sisesForm', function() {
            return {
                restrict: 'A',
                transclude: true,
                replace: true,
                template: '<form class="form-horizontal sises-form" data-ng-transclude></form>',
                scope: {
                    model: '=sisesForm',
                    errors: '='
                },
                link: function(scope){},
                controller: function($scope) {
                    this.scope = $scope;
                }
            };
        })
        .directive('sisesFormInput', function() {
            return {
                restrict: 'A',
                replace: true,
                require: '^sisesForm',
                templateUrl: G.template('directive/form_input'),
                scope: {
                    property: '@sisesFormInput'
                },
                link: inputLinkFunc
            };
        })
        .directive('sisesFormEmail', function() {
            return {
                restrict: 'A',
                replace: true,
                require: '^sisesForm',
                templateUrl: G.template('directive/form_input'),
                scope: {
                    property: '@sisesFormEmail'
                },
                link: function(scope, el, attrs, form) {
                    inputLinkFunc.call(this, scope, el, attrs, form);
                    scope.tt.placeholder = 'nombre@ejemplo.com';
                    scope.tt.type = 'email';
                }
            }
        })

        .directive('sisesFormSelect', function() {
            return {
                restrict: 'A',
                replace: true,
                require: '^sisesForm',
                templateUrl: G.template('directive/form_select'),
                scope: {
                    property: '@sisesFormSelect',
                    options: '=',
                    optionsKey: '@',
                    optionsLabel: '@'
                },
                link: inputLinkFunc
            }
        })
})();