'use strict';

$(function () {
    var config = JSON.parse($('#calendar_config').text());
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
                if (info.event.id !== $collectionTime.val()) return;
                selectEvent(info.el);
            },
            eventClick: function (info) {
                $collectionTime.val(info.event.start.toISOString());
                selectEvent(info.el);
            }
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

                $locations.show();
                populateCalendar(currentValue);
                populateLocationAddress(findLocationByCode(currentValue));
            });
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
});
