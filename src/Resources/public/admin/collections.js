$(function () {
    var calendar = new FullCalendar.Calendar($('#calendar')[0], {
        plugins: [ 'timeGrid' ],
        defaultView: 'timeGrid',
        nowIndicator: true,
    });
    var calendarRendered = false;

    var $places = $('#places');
    $places.change(function () {
        populateCalendar($(this).val());
    });
    populateCalendar($places.val());

    function populateCalendar(placeCode) {
        calendar.removeAllEventSources();
        calendar.addEventSource('/admin/click-n-collect/collections/'+placeCode);
        if (!calendarRendered) {
            calendar.render();
            calendarRendered = true;
        }
    }
});
