'use strict';

$(function () {
    var config = JSON.parse($('#calendar_config').text());
    var locale = config.locale.includes('_') ? config.locale.split('_')[0] : config.locale;
    // eslint-disable-next-line prefer-destructuring
    var excludeDays = [];
    $('input.click_n_collect_location').each(function () {
        var n = $(this).attr('id').match(/sylius_checkout_select_shipping_shipments_([0-9]+)_location/)[1];

        var $shippingMethod = $('input[name="sylius_checkout_select_shipping[shipments]['+n+'][method]"]');
        var $location = $('#sylius_checkout_select_shipping_shipments_'+n+'_location');
        var $collectionTime = $('#sylius_checkout_select_shipping_shipments_'+n+'_collectionTime');

        var $calendar = $('<div id="calendar_'+n+'"></div>');
        var $previousEl = null;
        var calendar = new FullCalendar.Calendar($calendar[0], {
            nowIndicator: true,
            plugins: [ 'timeGrid' ],
            defaultView: 'timeGridFourDay',
            views: {
                timeGridFourDay: {
                    type: 'timeGrid',
                    duration: { days: 4 },
                    buttonText: '4 day'
                }
            },
            eventColor: config.unselectedBackgroundColor,
            eventRender: function (info) {
                var eventStartString = info.event.start.toISOString().substring(0, 10);
                if (excludeDays && $.inArray(eventStartString, excludeDays) > -1) {
                    $(info.el).hide()
                }
                if (info.event.id.slice(0, 19) !== $collectionTime.val().slice(0, 19)) return;
                selectEvent(info.el);
            },
            eventClick: function (info) {
                $collectionTime.val(info.event.start.toISOString());
                selectEvent(info.el);
            },
            locale: locale,
        });
        var calendarRendered = false;

        var locationsCache = [];
        var $locations = $('<select name="sylius_checkout_select_shipping[shipments]['+n+'][location]"></select>').change(function () {
            var code = $(this).val();
            populateLocationAddress(findLocationByCode(code));
            populateCalendar(code);
        });
        var $locationAddress = $('<div id="location_address_'+n+'"></div>');

        $locations.hide().insertBefore($location);
        $location.remove();

        $locationAddress.hide().insertAfter($locations);
        $calendar.hide().insertAfter($locationAddress);

        populateLocations($shippingMethod.filter(':checked').val(), $location.val());
        $shippingMethod.change(function () {
            populateLocations($(this).val());
        });

        function selectEvent(eventEl) {
            var $el = $(eventEl);

            if ($previousEl) $previousEl.css('background-color', config.unselectedBackgroundColor).css('border-color', config.unselectedBackgroundColor);
            $el.css('background-color', config.selectedBackgroundColor).css('border-color', config.selectedBackgroundColor);
            $previousEl = $el;
        }

        function findLocationByCode(code) {
            return locationsCache.find(function (location) {
                return location.code === code;
            });
        }

        function populateLocations(shippingMethod, currentValue = undefined) {
            $locations.empty();
            $.getJSON('/'+config.locale+'/click-n-collect/locations/'+shippingMethod, function (locations) {
                locationsCache = locations;
                if (locations.length === 0) {
                    $locations.hide();
                    $locationAddress.hide();
                    $calendar.hide();

                    return;
                }

                $locations.append(locations.map(function (location) {
                    return $('<option>').val(location.code).text(location.name);
                }));

                if (currentValue) $locations.val(currentValue);
                else currentValue = $locations.val();

                //get good location
                var currentLocation = findLocationByCode(currentValue);
                //check if closed periods exist and populate excludeDays
                if (currentLocation.closedPeriods && currentLocation.closedPeriods.length > 0) {
                    currentLocation.closedPeriods.forEach(function (period) {
                        var start = new Date(period.startAt.substring(0, 10));
                        if (period.endAt) {
                            var end = new Date(period.endAt.substring(0, 10));
                            var rangeDates = getDatesInRange(start, end);
                            for (var i = 0; i < rangeDates.length; i++) {
                                excludeDays.push(rangeDates[i].toISOString().substring(0, 10));
                            }
                        } else {
                            var startString = start.toISOString().substring(0, 10);
                            excludeDays.push(startString);
                        }
                    });
                }

                $locations.show();
                populateCalendar(currentValue);
                populateLocationAddress(currentLocation);
            });
        }

        function getDatesInRange(startDate, endDate) {
            const date = new Date(startDate.getTime());
            const dates = [];
            while (date <= endDate) {
                dates.push(new Date(date));
                date.setDate(date.getDate() + 1);
            }

            return dates;
        }

        function populateLocationAddress(location) {
            $locationAddress.text((location.street || '') + ' ' + (location.postcode || '') + ' ' + (location.city || '') + ' ' + (location.provinceCode || '') + ' ' + (location.provinceName || '') + ' ' + (location.countryCode || ''));
            $locationAddress.show();
        }

        function populateCalendar(locationCode) {
            calendar.removeAllEventSources();
            calendar.addEventSource('/'+config.locale+'/click-n-collect/collection-times/'+config.shipmentID+'/'+locationCode);
            $calendar.show();
            if (!calendarRendered) {
                calendar.render();
                calendarRendered = true;
            }
        }
    });

    var $form = $('form[name="sylius_checkout_select_shipping"]')[0];
    $form.onsubmit = function (e) {
        var valid = $($form).serializeArray()
            .filter((input, foo, inputs) => {
                var matches = input.name.match(/sylius_checkout_select_shipping\[shipments\]\[(\d+)\]\[collectionTime\]/);
                if (!matches) {
                    return false;
                }

                var n = matches[1];
                var method = inputs.find(i => i.name === 'sylius_checkout_select_shipping[shipments]['+n+'][method]');

                return method && method.value === 'click_n_collect';
            })
            .every(input => !!input.value);

        if (!valid) {
            e.preventDefault();
            $($form).removeClass('loading');
            alert(config.messages.noSlotSelected);
        }
    };
});
