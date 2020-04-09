$(function () {
    var config = JSON.parse($('#calendar_config').text());
    var locale = config.locale.includes('_') ? config.locale.split('_')[0] : config.locale;

    var calendar = new FullCalendar.Calendar($('#calendar')[0], {
        plugins: [ 'timeGrid' ],
        defaultView: 'timeGrid',
        nowIndicator: true,
        locale: locale,
    });
    var calendarRendered = false;

    var $locations = $('#locations');
    $locations.change(function () {
        populateCalendar($(this).val());
    });
    populateCalendar($locations.val());

    function populateCalendar(locationCode) {
        calendar.removeAllEventSources();
        calendar.addEventSource('/admin/click-n-collect/collections/'+locationCode);
        if (!calendarRendered) {
            calendar.render();
            calendarRendered = true;
        }
    }
});
