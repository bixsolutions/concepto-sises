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

    angular.module(G.APP)

        .directive('sisesCalendar', function() {
            return {
                restrict: 'A',
                replace: true,
                templateUrl: G.template('directive/widget_calendar'),
                scope: {
                    events: '=sisesCalendar',
                    asignacion: '=',
                    selectedDate: '='

                },
                link: function(scope) {

                    /**
                     * @type {DateRange|null}
                     */
                    var range = null;

                    /** @namespace scope.asignacion.fechaInicio */
                    /** @namespace scope.asignacion.fechaCierre */

                    scope.buildCalendar = {};
                    scope.dates = {
                        min: null,
                        max: null
                    };

                    scope.showingDate = moment().startOf('day');
                    scope.$watch('showingDate', function(m) {
                        if (!moment.isMoment(m)) {
                            return;
                        }

                        buildCalendar(m);
                    }, true);


                    scope.$watch('_selectedDate', function(val) {
                        scope.selectedDate = val ? val.toDate() : null;
                    }, true);

                    scope.$watch('asignacion', function() {
                        if (scope.asignacion) {
                            scope.showingDate = moment(scope.asignacion.fechaInicio).startOf('day');
                            range = moment.range(
                                moment(scope.asignacion.fechaInicio).startOf('day').toDate(),
                                moment(scope.asignacion.fechaCierre).endOf('day').toDate()
                            );
                        }
                    }, true);

                    /**
                     * Se asegura que el calendario muestre solo fechas dentro de la asignación
                     */
                    function alignDate() {
                        if (scope.asignacion) {
                            var _fechaInicio = moment(scope.asignacion.fechaInicio).startOf('day'),
                                _fechaCierre = moment(scope.asignacion.fechaCierre).endOf('day');
                            if (scope.showingDate.isBefore(_fechaInicio)) {
                                scope.showingDate = _fechaInicio;
                            } else if (scope.showingDate.isAfter(_fechaCierre)) {
                                scope.showingDate = _fechaCierre;
                            }
                        }
                    }

                    /**
                     * Construye el calendario a mostrar
                     * @param m
                     */
                    function buildCalendar(m) {

                        var month = m.format('M'),
                            week, day, dayTitle,
                            startMonth = moment(m).startOf('month'),
                            endMonth = moment(m).endOf('month'),
                            looper = moment(startMonth);

                        // Define el mes actual
                        scope.month = m.format('MMMM');
                        // Define el año actual
                        scope.year = m.format('YYYY');

                        scope.days = [];
                        scope.dayTitle = [];
                        scope.weeks = [];
                        scope.buildCalendar = [];
                        scope._selectedDate = null;

                        // Se asegura que se inicie siempre al principio de semana y al principio del dia
                        looper.startOf('week');
                        looper.startOf('day');

                        // Construye calendario
                        while (looper.isBefore(endMonth)) {
                            week = looper.format('ww');
                            day = looper.format('E');
                            dayTitle = looper.format('dddd');

                            if (scope.weeks.indexOf(week) === -1) {
                                scope.weeks.push(week);
                            }

                            if (scope.days.indexOf(day) === -1) {
                                scope.dayTitle.push(dayTitle);
                                scope.days.push(day);
                            }

                            scope.buildCalendar[week + '-' + day] = {
                                currentMonth: looper.format('M') === month,
                                inRange: range !== null ? range.contains(looper.toDate()): false,
                                date: moment(looper),
                                display: looper.format('D')
                            };

                            looper.add(1, 'days');
                        }
                    }

                    /**
                     * Selecciona la fecha como activa
                     * @param m
                     */
                    scope.setCurDate = function setCurDate(m) {
                        if (m.inRange) {
                            scope._selectedDate = m.date;
                        }
                    };

                    /**
                     * Devuelve las clasess (CSS) de la celda
                     * @param m
                     * @returns {string}
                     */
                    scope.getClass = function getClass(m) {
                        var classes = '';

                        if (m.inRange) {
                            classes = 'in-range';
                        }

                        if (scope._selectedDate && m.date.isSame(scope._selectedDate)) {
                            classes += ' active-date';
                        }

                        return classes;
                    };

                    /**
                     * Devuelve la fecha apropiada
                     * @param week
                     * @param day
                     * @returns {*}
                     */
                    scope.getCal = function getCal(week, day) {
                        return scope.buildCalendar[week + '-' + day];
                    };

                    /**
                     * Mueve el calendario un mes hacia atras
                     */
                    scope.prev = function prev() {
                        scope.showingDate.subtract(1, 'month');
                        alignDate();
                    };

                    /**
                     * Mueve el calendario un mes hacia delante
                     */
                    scope.next = function next() {
                        scope.showingDate.add(1, 'month');
                        alignDate();
                    };

                    /**
                     * Devuelve un rango [] de valores
                     *
                     * @param min
                     * @param max
                     * @param step
                     * @returns {Array}
                     */
                    scope.range = function range(min, max, step) {
                        var input = [], i;

                        step = step || 1;

                        for (i = min; i <= max; i += step) {
                            input.push(i);
                        }

                        return input;
                    }
                }
            }
        })
    ;
})();