$(function () {
    var calendar = new FullCalendar.Calendar($('#calendar')[0], {
        plugins: [ 'timeGrid' ],
        defaultView: 'timeGrid',
        nowIndicator: true,
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
