$(function () {
    // Autoclose date picker
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
        autoclose: true,
        // todayHighlight: true,
        // startDate: '2025-01-01',
        // endDate: '2025-12-31'
        orientation: "bottom", // Force it to open below
    });

    $(".datetimepicker").datetimepicker({
        format: "YYYY-MM-DD HH:mm", // Date + hour:minute
        icons: {
            time: "fa fa-clock",
            date: "fa fa-calendar",
            up: "fa fa-chevron-up",
            down: "fa fa-chevron-down",
            previous: "fa fa-chevron-left",
            next: "fa fa-chevron-right",
            today: "fa fa-screenshot",
            clear: "fa fa-trash",
            close: "fa fa-remove",
        },
    });

    $(".timepicker").datepicker({
        format: "HH:mm", // Only time picking (24-hour format)
    });
});
