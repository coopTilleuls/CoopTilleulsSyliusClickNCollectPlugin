'use strict';

$(function () {
    var config = JSON.parse($('#calendar_config').text());

    var $shippingMethod = $('input[name="sylius_checkout_select_shipping[shipments][0][method]"]');
    var $place = $('#sylius_checkout_select_shipping_shipments_0_place');
    var $collectionTime = $('#sylius_checkout_select_shipping_shipments_0_collectionTime');

    var $calendar = $('<div id="calendar"></div>');
    var $previousEl = null;
    var calendar = new FullCalendar.Calendar($calendar[0], {
        plugins: [ 'dayGrid' ],
        defaultView: 'dayGridWeek',
        eventColor: config.unselectedBackgroundColor,
        eventRender: function (info) {
            console.log(info.event.id, $collectionTime.val());
            if (info.event.id !== $collectionTime.val()) return;
            console.log(info);
            selectEvent(info.el);
        },
        eventClick: function (info) {
            $collectionTime.val(info.event.start.toISOString());
            selectEvent(info.el);
        }
    });
    var calendarRendered = false;

    var placesCache = [];
    var $places = $('<select name="sylius_checkout_select_shipping[shipments][0][place]"></select>').change(function () {
        var code = $(this).val();
        populatePlaceAddress(findPlaceByCode(code));
        populateCalendar(code);
    });
    var $placeAddress = $('<div id="place_address"></div>');

    $places.hide().insertBefore($place);
    $place.remove();

    $placeAddress.hide().insertAfter($places);
    $calendar.hide().insertAfter($placeAddress);

    populatePlaces($shippingMethod.filter(':checked').val(), $place.val());
    $shippingMethod.change(function () {
        populatePlaces($(this).val());
    });

    function selectEvent(eventEl) {
        var $el = $(eventEl);

        if ($previousEl) $previousEl.css('background-color', config.unselectedBackgroundColor).css('border-color', config.unselectedBackgroundColor);
        $el.css('background-color', config.selectedBackgroundColor).css('border-color', config.selectedBackgroundColor);
        $previousEl = $el;
    }

    function findPlaceByCode(code) {
        return placesCache.find(function (place) {
            return place.code === code;
        });
    }

    function populatePlaces(shippingMethod, currentValue = undefined) {
        $places.empty();
        $.getJSON('../click-n-collect/places/'+shippingMethod, function (places) {
            placesCache = places;
            if (places.length === 0) {
                $places.hide();
                $placeAddress.hide();
                $calendar.hide();

                return;
            }

            $places.append(places.map(function (place) {
                return $('<option>').val(place.code).text(place.name);
            }));

            if (currentValue) $places.val(currentValue);
            else currentValue = $places.val();

            $places.show();
            populateCalendar(currentValue);
            populatePlaceAddress(findPlaceByCode(currentValue));
        });
    }

    function populatePlaceAddress(place) {
        $placeAddress.text((place.street || '') + ' ' + (place.postcode || '') + ' ' + (place.city || '') + ' ' + (place.provinceCode || '') + ' ' + (place.provinceName || '') + ' ' + (place.countryCode || ''));
        $placeAddress.show();
    }

    function populateCalendar(placeCode) {
        calendar.removeAllEventSources();
        calendar.addEventSource('../click-n-collect/collection-times/'+config.shipmentID+'/'+placeCode);
        $calendar.show();
        if (!calendarRendered) {
            calendar.render();
            calendarRendered = true;
        }
    }
});
