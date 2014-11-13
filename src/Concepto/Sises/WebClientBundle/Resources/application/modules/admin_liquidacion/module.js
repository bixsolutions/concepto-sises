/**
 * Copyright © 2014 Julian Reyes Escrigas <julian.reyes.escrigas@gmail.com>
 *
 * This file is part of concepto-sises.
 *
 * concepto-sises
 * can not be copied and/or distributed without the express
 * permission of Julian Reyes Escrigas <julian.reyes.escrigas@gmail.com>
 */
;
(function () {
    "use strict";

    G.modules.ADMIN_LIQUIDACION = 'ADMIN_LIQUIDACION';

    G.BuildModule(G.modules.ADMIN_LIQUIDACION, {
        register: true,
        label: 'Gestión de liquidaciones',
        category: 'liquidacion_category',
        controllers: {
            edit: {
                deps: ['$http'],
                func: function(RR, scope, $http) {
                    scope.detalles = [];
                    scope.detalles_cierre = {};
                    scope.calcular = function calcular() {
                        RR.admin_entrega_calcular.get({id: scope.element.id}, function(data) {
                            scope.detalles = data.results;
                            scope.detalles_cierre = {};
                            angular.forEach(data.results, function(item) {
                                scope.detalles_cierre[item.id] = {
                                    id: item.id,
                                    cantidad: item.total
                                };
                            });
                        });
                    };

                    scope.hasCierre = function hasCierre() {

                        if (!scope.detalles) {
                            return false;
                        }

                        return scope.detalles.length > 0 && scope.element.estado === 'pendiente';
                    };

                    scope.cancelarCierre = function cancelarCierre() {
                        scope.detalles = [];
                    };

                    scope.estaPendiente = function estaPendiente() {
                        return (scope.element.estado && scope.element.estado === 'pendiente');
                    };

                    scope.$watch('element.estado', function(val) {
                        if (val && val === 'finalizada') {
                            $http.get(G.route('get_entrega_detalles', {
                                id: scope.element.id
                            })).success(function(data) {
                                scope.detalles = data;
                            });
                        }
                    });

                    scope.okCierre = function okCierre() {
                        var servicios = [];

                        angular.forEach(scope.detalles_cierre, function(servicio) {
                            servicios.push(servicio);
                        });

                        $http.put(G.route('put_entrega_cierre'), {
                            id: scope.element.id,
                            servicios: servicios
                        }).success(function() {
                            scope.details(scope.element.id);
                        })
                    };
                }
            }
        }
    });
})();