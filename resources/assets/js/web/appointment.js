"use strict";

document.addEventListener("turbo:load", loadHospitalAppointmentData);

function loadHospitalAppointmentData() {
    if (!$("#appointmentForm").length) {
        return;
    }

    var customDate = $("#customFieldDate").val();
    var customDateTime = $("#customFieldDateTime").val();

    $("#customFieldDate").datepicker({
        format: "Y-m-d",
        useCurrent: false,
        sideBySide: true,
        minDate: customDateTime ? customDateTime : new Date(),
    });

    // $('#customFieldDateTime').datepicker({
    //     format: "Y-m-d H:i",
    //     useCurrent: false,
    //     sideBySide: true,
    //     minDate: customDateTime? customDateTime :new Date(),
    // });

    $("#customFieldDateTime").flatpickr({
        format: "Y-m-d H:i",
        enableTime: true,
        defaultDate: customDateTime ? customDateTime : new Date(),
        locale: $(".userCurrentLanguage").val(),
        onOpen: function (selectedDates, dateStr, instance) {
            var calendar = instance.calendarContainer;
            calendar.classList.add("custom-calendar");
            var todayElement = calendar.querySelector(".flatpickr-day.today");
            if (todayElement) {
                todayElement.classList.add("selected");
            }
        },
    });

    $(".custom-field-select").selectize();
    $(".custom-field-multi-select").selectize();

    $("#patientId").selectize();
    $("#frontAppointmentDepartmentId").selectize({
        create: false,
        sortField: "text",
    });
    $("#frontAppointmentDoctorId").selectize();
    $("#webAppointmentPayment").selectize();
    // $('#frontAppointmentOPDDate').selectize();

    var selectedDate;
    var intervals;
    var alreadyCreateTimeSlot;
    var appointmentBreakIntervals;
    // $.datepicker.setDefaults( $.datepicker.regional[ $('.userCurrentLanguage').val() ] )
    let opdDate = $("#frontAppointmentOPDDate").datepicker({
        useCurrent: false,
        sideBySide: true,
        minDate: new Date(),
        dateFormat: "yy-mm-dd",
        onSelect: function (selectedDate, dateStr) {
            let selectDate = selectedDate;
            $(".doctor-schedule").css("display", "none");
            $(".error-message").css("display", "none");
            $(".available-slot-heading").css("display", "none");
            $(".color-information").css("display", "none");
            $(".time-slot").remove();
            if ($("#frontAppointmentDepartmentId").val() == "") {
                $("#validationErrorsBox")
                    .show()
                    .html(
                        Lang.get("js.please_select_doctor") +
                        " " +
                        Lang.get("js.department")
                    );
                $("#validationErrorsBox").delay(5000).fadeOut();
                $("#opdDate").val("");
                // opdDate.clear();
                return false;
            } else if ($("#frontAppointmentOPDDate").val() == "") {
                $("#validationErrorsBox")
                    .show()
                    .html(Lang.get("js.please_select_doctor"));
                $("#validationErrorsBox").delay(5000).fadeOut();
                $("#frontAppointmentOPDDate").val("");
                // opdDate.clear();
                return false;
            }
            var weekday = [
                Lang.get("js.sunday"),
                Lang.get("js.monday"),
                Lang.get("js.tuesday"),
                Lang.get("js.wednesday"),
                Lang.get("js.thursday"),
                Lang.get("js.friday"),
                Lang.get("js.saturday"),
            ];
            var selected = new Date(selectedDate);
            let dayName = weekday[selected.getDay()];
            selectedDate = dateStr;

            //if dayName is blank, then ajax call not run.
            if (dayName == null || dayName == "") {
                return false;
            }

            //get doctor schedule list with time slot.
            if (
                typeof doctorId == "undefined" ||
                doctorId == "" ||
                doctorId == null
            ) {
                $("#frontAppointmentOPDDate").val("");
                return displayErrorMessage("Please select doctor");
            }
            $.ajax({
                type: "GET",
                url: $(".doctorScheduleList").val(),
                data: {
                    day_name: dayName,
                    doctor_id: doctorId,
                    date: moment(new Date(selectDate)).format("YYYY-MM-DD"),
                },
                success: function (result) {
                    if (result.success) {
                        if (result.data != "") {
                            if (
                                result.data.scheduleDay.length != 0 &&
                                result.data.doctorHoliday.length == 0
                            ) {
                                let availableFrom = "";
                                if (
                                    moment(new Date()).format("MM/DD/YYYY") ===
                                    selectDate
                                ) {
                                    availableFrom = moment().ceil(
                                        moment
                                            .duration(
                                                result.data.perPatientTime[0]
                                                    .per_patient_time
                                            )
                                            .asMinutes(),
                                        "minute"
                                    );
                                    availableFrom = moment(
                                        availableFrom.toString()
                                    ).format("H:mm:ss");
                                    // console.log(availabl eFrom);
                                } else {
                                    availableFrom =
                                        result.data.scheduleDay[0]
                                            .available_from;
                                }
                                var doctorStartTime =
                                    selectedDate + " " + availableFrom;
                                var doctorEndTime =
                                    selectedDate +
                                    " " +
                                    result.data.scheduleDay[0].available_to;
                                var checkTime = moment(new Date(selectDate)).format("YYYY-MM-DD") +
                                    " " +
                                    result.data.scheduleDay[0].available_to;

                                if (moment(checkTime).isBefore(moment())) {

                                    $(".doctor-schedule").css("display", "none");
                                    $(".color-information").css("display", "none");
                                    $(".available-slot").css("display", "none");
                                    $(".error-message").css("display", "block");
                                    $(".error-message").html(Lang.get("js.doctor_schedule_not_available_on_this_date"));
                                    return; // Exit the function if the end time is less than the current time
                                }

                                var doctorPatientTime =
                                    result.data.perPatientTime[0]
                                        .per_patient_time;


                                //perPatientTime convert to Minute
                                var a = doctorPatientTime.split(":"); // split it at the colons

                                var minutes = +a[0] * 60 + +a[1]; // convert to minute

                                //parse In
                                var startTime = parseIn(doctorStartTime);
                                // let now =  new Date();
                                // if(selectedDate.selectedDay == now.getDate()){
                                //     startTime.setTime(startTime.getTime() + 1000 * 60 * minutes);
                                // }

                                var endTime = parseIn(doctorEndTime);

                                //call to getTimeIntervals function
                                intervals = getTimeIntervals(
                                    startTime,
                                    endTime,
                                    minutes
                                );
                                if (result.data.doctorBreak != null) {
                                    for (
                                        var breakIndex = 0;
                                        breakIndex <
                                        result.data.doctorBreak.length;
                                        ++breakIndex
                                    ) {
                                        var startBreakTime = parseIn(
                                            selectedDate +
                                            " " +
                                            result.data.doctorBreak[
                                                breakIndex
                                            ].break_from
                                        );

                                        var endBreakTime = parseIn(
                                            selectedDate +
                                            " " +
                                            result.data.doctorBreak[
                                                breakIndex
                                            ].break_to
                                        );

                                        appointmentBreakIntervals =
                                            getTimeIntervals(
                                                startBreakTime,
                                                endBreakTime,
                                                1
                                            );
                                        intervals = intervals.filter(
                                            (slot) =>
                                                !appointmentBreakIntervals.includes(
                                                    slot
                                                )
                                        );
                                    }
                                }

                                //if intervals array length is grater then 0 then process
                                if (intervals.length > 0) {
                                    $(".available-slot-heading").css(
                                        "display",
                                        "block"
                                    );
                                    $(".color-information").css(
                                        "display",
                                        "block"
                                    );
                                    $(".available-slot").css(
                                        "display",
                                        "block"
                                    );
                                    var index;
                                    let timeStlots = "";
                                    for (
                                        index = 0;
                                        index < intervals.length;
                                        ++index
                                    ) {
                                        let data = [
                                            {
                                                index: index,
                                                timeSlot: intervals[index],
                                            },
                                        ];
                                        var timeSlot = prepareTemplateRender(
                                            "#appointmentSlotTemplate",
                                            data
                                        );
                                        timeStlots = timeStlots + timeSlot;
                                    }
                                    $(".available-slot").append(timeStlots);
                                }

                                // display Day Name and time
                                if (
                                    availableFrom != "00:00:00" &&
                                    result.data.scheduleDay[0].available_to !=
                                    "00:00:00" &&
                                    doctorStartTime != doctorEndTime
                                ) {
                                    $(".doctor-schedule").css(
                                        "display",
                                        "block"
                                    );
                                    $(".color-information").css(
                                        "display",
                                        "block"
                                    );
                                    $(".available-slot").css(
                                        "display",
                                        "block"
                                    );
                                    $(".day-name").html(
                                        result.data.scheduleDay[0].available_on
                                    );
                                    $(".schedule-time").html(
                                        "[" +
                                        availableFrom +
                                        " - " +
                                        result.data.scheduleDay[0]
                                            .available_to +
                                        "]"
                                    );
                                } else {
                                    $(".doctor-schedule").css(
                                        "display",
                                        "none"
                                    );
                                    $(".color-information").css(
                                        "display",
                                        "none"
                                    );
                                    $(".available-slot").css("display", "none");
                                    $(".error-message").css("display", "block");
                                    $(".error-message").html(
                                        Lang.get(
                                            "js.doctor_schedule_not_available_on_this_date"
                                        )
                                    );
                                }
                            } else {
                                $(".doctor-schedule").css("display", "none");
                                $(".color-information").css("display", "none");
                                $(".error-message").css("display", "block");
                                $(".error-message").html(
                                    Lang.get(
                                        "js.doctor_schedule_not_available_on_this_date"
                                    )
                                );
                            }
                        }
                    }
                },
            });

            if ($(".isCreate").val() || $(".isEdit").val()) {
                var delayCall = 200;
                setTimeout(getCreateTimeSlot, delayCall);
                let slotsData = null;

                function getCreateTimeSlot() {
                    if ($(".isCreate").val()) {
                        slotsData = {
                            editSelectedDate: moment(
                                $("#frontAppointmentOPDDate").datepicker(
                                    "getDate"
                                )
                            ).format("YYYY-MM-DD"),
                            doctor_id: doctorId,
                        };
                    } else {
                        slotsData = {
                            editSelectedDate: moment(
                                $("#frontAppointmentOPDDate").datepicker(
                                    "getDate"
                                )
                            ).format("YYYY-MM-DD"),
                            editId: appointmentEditId,
                            doctor_id: doctorId,
                        };
                    }

                    $.ajax({
                        url: $(".getBookingSlot").val(),
                        type: "GET",
                        data: slotsData,
                        success: function (result) {
                            alreadyCreateTimeSlot = result.data.bookingSlotArr;
                            if (result.data.hasOwnProperty("onlyTime")) {
                                if (result.data.bookingSlotArr.length > 0) {
                                    editTimeSlot =
                                        result.data.onlyTime.toString();
                                    $.each(
                                        result.data.bookingSlotArr,
                                        function (index, value) {
                                            $.each(intervals, function (i, v) {
                                                if (value == v) {
                                                    $(".time-interval").each(
                                                        function () {
                                                            if (
                                                                $(this).data(
                                                                    "id"
                                                                ) == i
                                                            ) {
                                                                if (
                                                                    $(
                                                                        this
                                                                    ).html() !=
                                                                    editTimeSlot
                                                                ) {
                                                                    $(this)
                                                                        .parent()
                                                                        .css({
                                                                            "background-color":
                                                                                "#ffa721",
                                                                            border: "1px solid #ffa721",
                                                                            color: "#ffffff",
                                                                        });
                                                                    $(this)
                                                                        .parent()
                                                                        .addClass(
                                                                            "booked"
                                                                        );
                                                                    $(this)
                                                                        .parent()
                                                                        .children()
                                                                        .prop(
                                                                            "disabled",
                                                                            true
                                                                        );
                                                                }
                                                            }
                                                        }
                                                    );
                                                }
                                            });
                                        }
                                    );
                                }
                                $(".time-interval").each(function () {
                                    if (
                                        $(this).html() == editTimeSlot &&
                                        result.data.bookingSlotArr.length > 0
                                    ) {
                                        $(this)
                                            .parent()
                                            .addClass("time-slot-book");
                                        $(this).parent().removeClass("booked");
                                        $(this)
                                            .parent()
                                            .children()
                                            .prop("disabled", false);
                                        $(this).click();
                                    }
                                });
                            } else if (alreadyCreateTimeSlot.length > 0) {
                                $.each(
                                    alreadyCreateTimeSlot,
                                    function (index, value) {
                                        $.each(intervals, function (i, v) {
                                            if (value === v) {
                                                $(".time-interval").each(
                                                    function () {
                                                        if (
                                                            $(this).data(
                                                                "id"
                                                            ) === i
                                                        ) {
                                                            $(this)
                                                                .parent()
                                                                .addClass(
                                                                    "time-slot-book"
                                                                );
                                                            $(
                                                                ".time-slot-book"
                                                            ).css({
                                                                "background-color":
                                                                    "#FF8E4B",
                                                                border: "1px solid #FF8E4B",
                                                                color: "#ffffff",
                                                            });
                                                            $(this)
                                                                .parent()
                                                                .addClass(
                                                                    "booked"
                                                                );
                                                            $(this)
                                                                .parent()
                                                                .children()
                                                                .prop(
                                                                    "disabled",
                                                                    true
                                                                );
                                                        }
                                                    }
                                                );
                                            }
                                        });
                                    }
                                );
                            }
                        },
                    });
                }
            }

            /*
            if (isCreate || isEdit) {
                var delayCall = 200;
                setTimeout(getCreateTimeSlot, delayCall);

                function getCreateTimeSlot() {
                    if (isCreate) {
                        var data = {
                            editSelectedDate: selectedDate,
                            doctor_id: doctorId,
                        };
                    } else {
                        var data = {
                            editSelectedDate: selectedDate,
                            editId: appointmentEditId,
                            doctor_id: doctorId,
                        };
                    }

                    $.ajax({
                        url: getBookingSlot,
                        type: 'GET',
                        data: data,
                        success: function (result) {
                            alreadyCreateTimeSlot = result.data.bookingSlotArr;
                            if (result.data.hasOwnProperty('onlyTime')) {
                                if (result.data.bookingSlotArr.length > 0) {
                                    editTimeSlot = result.data.onlyTime.toString();
                                    $.each(result.data.bookingSlotArr,
                                        function (index, value) {
                                            $.each(intervals, function (i, v) {
                                                if (value == v) {
                                                    $('.time-interval').each(function () {
                                                        if ($(this).data('id') == i) {
                                                            if ($(this).html() !=
                                                                editTimeSlot) {
                                                                $(this).parent().css({
                                                                    'background-color': '#ffa721',
                                                                    'border': '1px solid #ffa721',
                                                                    'color': '#ffffff',
                                                                });
                                                                $(this).parent().addClass(
                                                                    'booked');
                                                                $(this).parent().children().prop(
                                                                    'disabled',
                                                                    true);
                                                            }
                                                        }
                                                    });
                                                }
                                            });
                                        });
                                }
                                $('.time-interval').each(function () {
                                    if ($(this).html() == editTimeSlot &&
                                        result.data.bookingSlotArr.length > 0) {
                                        $(this).parent().addClass('time-slot-book');
                                        $(this).parent().removeClass('booked');
                                        $(this).parent().children().prop('disabled', false);
                                        $(this).click();
                                    }
                                });
                            } else if (alreadyCreateTimeSlot.length > 0) {
                                $.each(alreadyCreateTimeSlot,
                                    function (index, value) {
                                        $.each(intervals, function (i, v) {
                                            if (value == v) {
                                                $('.time-interval').each(function () {
                                                    if ($(this).data('id') ==
                                                        i) {
                                                        $(this).parent().addClass(
                                                            'time-slot-book');
                                                        $('.time-slot-book').css({
                                                            'background-color': '#ffa721',
                                                            'border': '1px solid #ffa721',
                                                            'color': '#ffffff',
                                                        });
                                                        $(this).parent().addClass('booked');
                                                        $(this).parent().children().prop('disabled',
                                                            true);
                                                    }
                                                });
                                            }
                                        });
                                    });
                            }
                        },
                    });
                }
            }

             */
        },
    });

    let doctor = $("#doctor").val();
    let appointmentDate = $("#appointmentDate").val();

    if (appointmentDate !== null) {
        loadAppointmentDate();
    }

    function loadAppointmentDate() {
        opdDate.datepicker("setDate", appointmentDate);
        if (opdDate !== null) {
            opdDate instanceof Date;
            let dateStr = opdDate;
            let selectedDate = appointmentDate;
            $(".doctor-schedule").css("display", "none");
            $(".error-message").css("display", "none");
            $(".available-slot-heading").css("display", "none");
            $(".color-information").css("display", "none");
            $(".time-slot").remove();
            // if ($('#frontAppointmentDepartmentId').val() == '') {
            //     $('#validationErrorsBox').
            //         show().
            //         html('Please select Doctor Department');
            //     $('#validationErrorsBox').delay(5000).fadeOut();
            //     $('#opdDate').val('');
            //     // opdDate.clear();
            //     return false;
            // } else if ($('#doctorId').val() == '') {
            //     $('#validationErrorsBox').show().html('Please select Doctor');
            //     $('#validationErrorsBox').delay(5000).fadeOut();
            //     $('#opdDate').val('');
            //     // opdDate.clear();
            //     return false;
            // }
            var weekday = [
                "Sunday",
                "Monday",
                "Tuesday",
                "Wednesday",
                "Thursday",
                "Friday",
                "Saturday",
            ];
            var selected = new Date(selectedDate);
            let dayName = weekday[selected.getDay()];
            selectedDate = dateStr;

            //if dayName is blank, then ajax call not run.
            if (dayName == null || dayName == "") {
                return false;
            }

            //get doctor schedule list with time slot.
            $.ajax({
                type: "GET",
                url: $(".doctorScheduleList").val(),
                data: {
                    day_name: dayName,
                    doctor_id: doctor,
                    date: moment(new Date(appointmentDate)).format("YYYY-MM-DD"),
                },
                success: function (result) {
                    if (result.success) {
                        if (result.data != "") {
                            if (result.data.scheduleDay.length != 0 &&
                                result.data.doctorHoliday.length == 0) {
                                let availableFrom = "";
                                if (
                                    moment(new Date()).format("MM/DD/YYYY") ===
                                    appointmentDate
                                ) {
                                    availableFrom = moment().ceil(
                                        moment
                                            .duration(
                                                result.data.perPatientTime[0]
                                                    .per_patient_time
                                            )
                                            .asMinutes(),
                                        "minute"
                                    );
                                    availableFrom = moment(
                                        availableFrom.toString()
                                    ).format("H:mm:ss");
                                    // availableFrom = moment(new Date()).
                                    //     add(result.data.perPatientTime[0].per_patient_time,
                                    //         'minutes').format('H:mm:ss');
                                } else {
                                    availableFrom =
                                        result.data.scheduleDay[0]
                                            .available_from;
                                }
                                var doctorStartTime =
                                    selectedDate + " " + availableFrom;
                                var doctorEndTime =
                                    selectedDate +
                                    " " +
                                    result.data.scheduleDay[0].available_to;

                                var checkTime = appointmentDate +
                                    " " +
                                    result.data.scheduleDay[0].available_to;

                                if (moment(checkTime).isBefore(moment())) {
                                    $(".doctor-schedule").css("display", "none");
                                    $(".color-information").css("display", "none");
                                    $(".available-slot").css("display", "none");
                                    $(".error-message").css("display", "block");
                                    $(".error-message").html(Lang.get("js.doctor_schedule_not_available_on_this_date"));
                                    return; // Exit the function if the end time is less than the current time
                                }
                                var doctorPatientTime =
                                    result.data.perPatientTime[0]
                                        .per_patient_time;

                                //perPatientTime convert to Minute
                                var a = doctorPatientTime.split(":"); // split it at the colons
                                var minutes = +a[0] * 60 + +a[1]; // convert to minute

                                //parse In
                                var startTime = parseIn(doctorStartTime);
                                var endTime = parseIn(doctorEndTime);

                                //call to getTimeIntervals function
                                intervals = getTimeIntervals(
                                    startTime,
                                    endTime,
                                    minutes
                                );

                                //if intervals array length is grater then 0 then process
                                if (intervals.length > 0) {
                                    $(".available-slot-heading").css(
                                        "display",
                                        "block"
                                    );
                                    $(".color-information").css(
                                        "display",
                                        "block"
                                    );
                                    $(".available-slot").css(
                                        "display",
                                        "block"
                                    );
                                    var index;
                                    let timeStlots = "";
                                    for (
                                        index = 0;
                                        index < intervals.length;
                                        ++index
                                    ) {
                                        let data = [
                                            {
                                                index: index,
                                                timeSlot: intervals[index],
                                            },
                                        ];
                                        var timeSlot = prepareTemplateRender(
                                            "#appointmentSlotTemplate",
                                            data
                                        );
                                        timeStlots += timeSlot;
                                    }
                                    $(".available-slot").append(timeStlots);
                                }

                                // display Day Name and time
                                if (
                                    availableFrom != "00:00:00" &&
                                    result.data.scheduleDay[0].available_to !=
                                    "00:00:00" &&
                                    doctorStartTime != doctorEndTime
                                ) {
                                    $(".doctor-schedule").css(
                                        "display",
                                        "block"
                                    );
                                    $(".color-information").css(
                                        "display",
                                        "block"
                                    );
                                    $(".day-name").html(
                                        result.data.scheduleDay[0].available_on
                                    );
                                    $(".schedule-time").html(
                                        "[" +
                                        availableFrom +
                                        " - " +
                                        result.data.scheduleDay[0]
                                            .available_to +
                                        "]"
                                    );
                                } else {
                                    $(".doctor-schedule").css(
                                        "display",
                                        "none"
                                    );
                                    $(".color-information").css(
                                        "display",
                                        "none"
                                    );
                                    $(".error-message").css("display", "block");
                                    $(".error-message").html(
                                        Lang.get(
                                            "js.doctor_schedule_not_available_on_this_date"
                                        )
                                    );
                                }
                            } else {
                                $(".doctor-schedule").css("display", "none");
                                $(".color-information").css("display", "none");
                                $(".error-message").css("display", "block");
                                $(".error-message").html(
                                    Lang.get(
                                        "js.doctor_schedule_not_available_on_this_date"
                                    )
                                );
                            }
                        }
                    }
                },
            });
            /*
            if (isCreate || isEdit) {
                var delayCall = 200;
                setTimeout(getCreateTimeSlot, delayCall);

                function getCreateTimeSlot () {
                    if (isCreate) {
                        var data = {
                            editSelectedDate: selectedDate,
                            doctor_id: doctorId,
                        };
                    } else {
                        var data = {
                            editSelectedDate: selectedDate,
                            editId: appointmentEditId,
                            doctor_id: doctorId,
                        };
                    }

                    $.ajax({
                        url: getBookingSlot,
                        type: 'GET',
                        data: data,
                        success: function (result) {
                            alreadyCreateTimeSlot = result.data.bookingSlotArr;
                            if (result.data.hasOwnProperty('onlyTime')) {
                                if (result.data.bookingSlotArr.length > 0) {
                                    editTimeSlot = result.data.onlyTime.toString();
                                    $.each(result.data.bookingSlotArr,
                                        function (index, value) {
                                            $.each(intervals, function (i, v) {
                                                if (value == v) {
                                                    $('.time-interval').
                                                        each(function () {
                                                            if ($(this).
                                                                data('id') == i) {
                                                                if ($(this).
                                                                        html() !=
                                                                    editTimeSlot) {
                                                                    $(this).
                                                                        parent().
                                                                        css({
                                                                            'background-color': '#ffa721',
                                                                            'border': '1px solid #ffa721',
                                                                            'color': '#ffffff',
                                                                        });
                                                                    $(this).
                                                                        parent().
                                                                        addClass(
                                                                            'booked');
                                                                    $(this).
                                                                        parent().
                                                                        children().
                                                                        prop(
                                                                            'disabled',
                                                                            true);
                                                                }
                                                            }
                                                        });
                                                }
                                            });
                                        });
                                }
                                $('.time-interval').each(function () {
                                    if ($(this).html() == editTimeSlot &&
                                        result.data.bookingSlotArr.length > 0) {
                                        $(this).parent().addClass('time-slot-book');
                                        $(this).parent().removeClass('booked');
                                        $(this).
                                            parent().
                                            children().
                                            prop('disabled', false);
                                        $(this).click();
                                    }
                                });
                            } else if (alreadyCreateTimeSlot.length > 0) {
                                $.each(alreadyCreateTimeSlot,
                                    function (index, value) {
                                        $.each(intervals, function (i, v) {
                                            if (value == v) {
                                                $('.time-interval').
                                                    each(function () {
                                                        if ($(this).data('id') ==
                                                            i) {
                                                            $(this).
                                                                parent().
                                                                addClass(
                                                                    'time-slot-book');
                                                            $('.time-slot-book').
                                                                css({
                                                                    'background-color': '#ffa721',
                                                                    'border': '1px solid #ffa721',
                                                                    'color': '#ffffff',
                                                                });
                                                            $(this).
                                                                parent().
                                                                addClass('booked');
                                                            $(this).
                                                                parent().
                                                                children().
                                                                prop('disabled',
                                                                    true);
                                                        }
                                                    });
                                            }
                                        });
                                    });
                            }
                        },
                    });
                }
            }

             */
            if ($(".isCreate").val() || $(".isEdit").val()) {
                var delayCall = 200;
                setTimeout(getCreateTimeSlot, delayCall);
                let slotsData = null;

                function getCreateTimeSlot() {
                    if ($(".isCreate").val()) {
                        slotsData = {
                            editSelectedDate: moment(
                                $("#frontAppointmentOPDDate").datepicker(
                                    "getDate"
                                )
                            ).format("YYYY-MM-DD"),
                            doctor_id: doctor,
                        };
                    } else {
                        slotsData = {
                            editSelectedDate: moment(
                                $("#frontAppointmentOPDDate").datepicker(
                                    "getDate"
                                )
                            ).format("YYYY-MM-DD"),
                            editId: appointmentEditId,
                            doctor_id: doctor,
                        };
                    }

                    $.ajax({
                        url: $(".getBookingSlot").val(),
                        type: "GET",
                        data: slotsData,
                        success: function (result) {
                            alreadyCreateTimeSlot = result.data.bookingSlotArr;
                            if (result.data.hasOwnProperty("onlyTime")) {
                                if (result.data.bookingSlotArr.length > 0) {
                                    editTimeSlot =
                                        result.data.onlyTime.toString();
                                    $.each(
                                        result.data.bookingSlotArr,
                                        function (index, value) {
                                            $.each(intervals, function (i, v) {
                                                if (value == v) {
                                                    $(".time-interval").each(
                                                        function () {
                                                            if (
                                                                $(this).data(
                                                                    "id"
                                                                ) == i
                                                            ) {
                                                                if (
                                                                    $(
                                                                        this
                                                                    ).html() !=
                                                                    editTimeSlot
                                                                ) {
                                                                    $(this)
                                                                        .parent()
                                                                        .css({
                                                                            "background-color":
                                                                                "#ffa721",
                                                                            border: "1px solid #ffa721",
                                                                            color: "#ffffff",
                                                                        });
                                                                    $(this)
                                                                        .parent()
                                                                        .addClass(
                                                                            "booked"
                                                                        );
                                                                    $(this)
                                                                        .parent()
                                                                        .children()
                                                                        .prop(
                                                                            "disabled",
                                                                            true
                                                                        );
                                                                }
                                                            }
                                                        }
                                                    );
                                                }
                                            });
                                        }
                                    );
                                }
                                $(".time-interval").each(function () {
                                    if (
                                        $(this).html() == editTimeSlot &&
                                        result.data.bookingSlotArr.length > 0
                                    ) {
                                        $(this)
                                            .parent()
                                            .addClass("time-slot-book");
                                        $(this).parent().removeClass("booked");
                                        $(this)
                                            .parent()
                                            .children()
                                            .prop("disabled", false);
                                        $(this).click();
                                    }
                                });
                            } else if (alreadyCreateTimeSlot.length > 0) {
                                $.each(
                                    alreadyCreateTimeSlot,
                                    function (index, value) {
                                        $.each(intervals, function (i, v) {
                                            if (value === v) {
                                                $(".time-interval").each(
                                                    function () {
                                                        if (
                                                            $(this).data(
                                                                "id"
                                                            ) === i
                                                        ) {
                                                            $(this)
                                                                .parent()
                                                                .addClass(
                                                                    "time-slot-book"
                                                                );
                                                            $(
                                                                ".time-slot-book"
                                                            ).css({
                                                                "background-color":
                                                                    "#FF8E4B",
                                                                border: "1px solid #FF8E4B",
                                                                color: "#ffffff",
                                                            });
                                                            $(this)
                                                                .parent()
                                                                .addClass(
                                                                    "booked"
                                                                );
                                                            $(this)
                                                                .parent()
                                                                .children()
                                                                .prop(
                                                                    "disabled",
                                                                    true
                                                                );
                                                        }
                                                    }
                                                );
                                            }
                                        });
                                    }
                                );
                            }
                        },
                    });
                }
            }
        }
    }

    $("#patientId").first().focus();

    listenChange("#frontAppointmentDepartmentId", function () {
        $(".error-message").css("display", "none");
        var selectize = $("#frontAppointmentDoctorId")[0].selectize;
        selectize.clearOptions();
        $("#frontAppointmentOPDDate").val("");
        // opdDate.clear();
        $(".doctor-schedule").css("display", "none");
        $(".available-slot-heading").css("display", "none");
        $(".available-slot").css("display", "none");
        $.ajax({
            url: $(".doctorDepartmentUrl").val(),
            type: "get",
            dataType: "json",
            data: { id: $(this).val() },
            success: function (data) {
                $("#frontAppointmentDoctorId").empty();
                $("#frontAppointmentDoctorId").append(
                    $(
                        '<option value="">' +
                        Lang.get("js.select_doctors") +
                        "</option>"
                    )
                );
                $.each(data.data, function (i, v) {
                    $("#frontAppointmentDoctorId").append(
                        $("<option></option>").attr("value", i).text(v)
                    );
                });
                let $select = $(
                    document.getElementById("frontAppointmentDoctorId")
                ).selectize();
                let selectize = $select[0].selectize;
                $.each(data.data, function (i, v) {
                    selectize.addOption({ value: i, text: v });
                });
                selectize.refreshOptions();
            },
        });
    });

    var doctorId;
    let doctorChange = false;

    listenChange("#frontAppointmentDoctorId", function () {
        if (doctorChange) {
            $(".error-message").css("display", "none");
            // opdDate.clear();
            $(".doctor-schedule").css("display", "none");
            $(".error-message").css("display", "none");
            $(".available-slot-heading").css("display", "none");
            $(".color-information").css("display", "none");
            $(".time-slot").remove();
            $(".available-slot").css("display", "none");
            $("#webAppointmentCharge").val("");
            doctorChange = true;
        }
        $(".error-message").css("display", "none");
        doctorId = $(this).val();
        doctorChange = true;

        $.ajax({
            url: route("get-appointment-charge"),
            type: "get",
            dataType: "json",
            data: {
                doctor_id: doctorId,
            },
            success: function (result) {
                if (result.success) {
                    if (result.data != null) {
                        let charge = result.data.appointment_charge;
                        $("#webAppointmentCharge").val(charge);
                        if (charge != 0) {
                            $(".appointmentCharge").removeClass("d-none");
                            $("#webAppointmentCharge").val(charge);
                            $(".web-appointment-payment").removeClass("d-none");
                        }
                        if (charge == 0 || charge == undefined) {
                            $(".web-appointment-payment").addClass("d-none");
                            $(".appointmentCharge").addClass("d-none");
                        }
                    }
                }
            },
            error: function (result) {
                printErrorMessage("#editAppointmentErrorsBox", result);
            },
        });
    });

    // if edit record then trigger change
    var editTimeSlot;
    if ($(".isEdit").val()) {
        $("#frontAppointmentDoctorId").trigger("change", function (event) {
            doctorId = $(this).val();
        });

        $("#frontAppointmentOPDDate").trigger("dp.change", function () {
            var selected = new Date($(this).val());
        });
    }

    //parseIn date_time
    window.parseIn = function (date_time) {
        var d = new Date();
        d.setHours(date_time.substring(16, 18));
        d.setMinutes(date_time.substring(19, 21));

        return d;
    };

    //make time slot list
    window.getTimeIntervals = function (time1, time2, duration) {
        var arr = [];
        while (time1 < time2) {
            arr.push(time1.toTimeString().substring(0, 5));
            time1.setMinutes(time1.getMinutes() + duration);
        }
        return arr;
    };

    //slot click change color
    var selectedTime;
    listenClick(".time-interval", function (event) {
        let appointmentId = $(event.currentTarget).data("id");
        if ($(this).data("id") == appointmentId) {
            if ($(this).parent().hasClass("booked")) {
                $(".time-slot-book").css("background-color", "#ffa0a0");
            }
        }
        selectedTime = $(this).text();
        $(".time-slot").removeClass("time-slot-book");
        $(this).parent().addClass("time-slot-book");
    });

    var editTimeSlot;
    listenClick(".time-interval", function () {
        editTimeSlot = $(this).text();
    });

    let oldPatient = false;
    listenChange(".new-patient-radio", function () {
        if ($(this).is(":checked")) {
            $(".old-patient").addClass("d-none");
            $(".first-name-div").removeClass("d-none");
            $(".last-name-div").removeClass("d-none");
            $(".gender-div").removeClass("d-none");
            $(".password-div").removeClass("d-none");
            $(".confirm-password-div").removeClass("d-none");
            $("#frontAppointmentFirstName").prop("required", true);
            $("#frontAppointmentLastName").prop("required", true);
            $("#frontAppointmentPassword").prop("required", true);
            $("#frontAppointmentConfirmPassword").prop("required", true);
            oldPatient = false;
        }
    });

    listenChange(".old-patient-radio", function () {
        if ($(this).is(":checked")) {
            $(".old-patient").removeClass("d-none");
            $(".first-name-div").addClass("d-none");
            $(".last-name-div").addClass("d-none");
            $(".gender-div").addClass("d-none");
            $(".password-div").addClass("d-none");
            $(".confirm-password-div").addClass("d-none");
            $("#frontAppointmentFirstName").prop("required", false);
            $("#frontAppointmentLastName").prop("required", false);
            $("#frontAppointmentPassword").prop("required", false);
            $("#frontAppointmentConfirmPassword").prop("required", false);
            oldPatient = true;
        }
    });

    listen("focusout", ".old-patient-email", function () {
        let email = $(".old-patient-email").val();
        if (oldPatient && email != "") {
            $.ajax({
                url: route("appointment.patient.details", email),
                type: "get",
                success: function (result) {
                    if (result.data != null) {
                        $("#patient").empty();
                        $.each(result.data, function (index, value) {
                            $("#patientName").val(value);
                            $("#patient").val(index);
                        });
                    } else {
                        displayErrorMessage(
                            Lang.get(
                                "js.patient_not_exists_or_status_is_not_active"
                            )
                        );
                    }
                },
            });
        }
    });

    function formReset() {
        $(".old-patient").addClass("d-none");
        $(".first-name-div").removeClass("d-none");
        $(".last-name-div").removeClass("d-none");
        $(".gender-div").removeClass("d-none");
        $(".password-div").removeClass("d-none");
        $(".confirm-password-div").removeClass("d-none");
        $(".appointment-slot").removeClass("d-none");
        $("#frontAppointmentFirstName").prop("required", true);
        $("#frontAppointmentLastName").prop("required", true);
        $("#frontAppointmentPassword").prop("required", true);
        $("#frontAppointmentConfirmPassword").prop("required", true);
    }

    // if ($('#appointment-g-recaptcha').length) {
    //     grecaptcha.render('appointment-g-recaptcha', {
    //         'sitekey': $('#appointmentGRecaptcha').val(),
    //     })
    // }

    //create appointment
    listenSubmit("#appointmentForm", function (event) {
        event.preventDefault();

        showScreenLoader();
        if (!oldPatient) {
            let isValidate = validatePassword();
            if (!isValidate) {
                hideScreenLoader();
                return false;
            }
        }

        var isValid = true;
        $(".dynamic-field").each(function () {
            var fieldValue = $(this).val();
            var fieldLabel = $(this)
                .closest(".form-group")
                .find("label")
                .text()
                .replace(":", "")
                .trim();

            if (
                $(this).is(
                    ':input[type="text"], :input[type="number"], textarea'
                )
            ) {
                if (!fieldValue || fieldValue.trim() === "") {
                    displayErrorMessage(
                        fieldLabel + " " + Lang.get("js.field_required")
                    );
                    isValid = false;
                    return false;
                }
            } else if ($(this).is(':input[type="toggle"]')) {
                if (!$(this).is(":checked")) {
                    displayErrorMessage(
                        fieldLabel + " " + Lang.get("js.field_required")
                    );
                    isValid = false;
                    return false;
                }
            } else if ($(this).is("select")) {
                if (
                    !fieldValue &&
                    $(this).val().length === 0 &&
                    fieldValue.trim() === ""
                ) {
                    displayErrorMessage(
                        fieldLabel + " " + Lang.get("js.field_required")
                    );
                    isValid = false;
                    return false;
                }
            }
        });

        if (
            $("#frontAppointmentDepartmentId").val() == null ||
            $("#frontAppointmentDepartmentId").val() == ""
        ) {
            displayErrorMessage(
                Lang.get("js.please_select_doctor") +
                " " +
                Lang.get("js.department")
            );
            hideScreenLoader();

            return false;
        }

        if (
            $("#frontAppointmentDoctorId").val() == null ||
            $("#frontAppointmentDoctorId").val() == ""
        ) {
            displayErrorMessage(
                Lang.get("messages.appointment.please_select_doctor")
            );
            hideScreenLoader();
            return false;
        }

        if ($("#webAppointmentCharge").val() != null) {
            if ($("#webAppointmentPayment").val() == null) {
                displayErrorMessage(Lang.get("Select Payment Mode"));
                hideScreenLoader();
                return false;
            }
        }
        if (selectedTime == null || selectedTime == "") {
            displayErrorMessage(Lang.get("js.select_time_slot"));
            hideScreenLoader();
            return false;
        }
        // screenLock();
        let formData = $(this).serialize() + "&time=" + selectedTime;

        $.ajax({
            url: $(".appointmentSaveUrl").val(),
            type: "POST",
            dataType: "json",
            data: formData,
            success: function (result) {
                if (result.data == null) {
                    displaySuccessMessage(result.message);
                    setTimeout(function () {
                        location.reload();
                    }, 5000);
                } else {
                    if (result.data && result.data.payment_type) {
                        if (result.data.payment_type == "1") {
                            let stripeKey = $("#webStripeConfigKey").val();
                            let stripe = Stripe(stripeKey);
                            let payloadData = {
                                appointment_id: result.data.appointment_id,
                                payment_type: result.data.payment_type,
                                amount: result.data.amount,
                            };
                            $(".custom-btn-lg").addClass("disabled");
                            $.post(
                                route("web.appointment.stripe.session"),
                                payloadData
                            )
                                .done((res) => {
                                    let sessionId = res.data.sessionId;
                                    stripe
                                        .redirectToCheckout({
                                            sessionId: sessionId,
                                        })
                                        .then(function (res) {
                                            manageAjaxErrors(res);
                                        });
                                })
                                .catch((error) => {
                                    manageAjaxErrors(error);
                                });
                        } else if (result.data.payment_type == "2") {
                            let appId = result.data.appointment_id;
                            let formData =
                                $("#appointmentForm").serialize() +
                                "&appointment_id=" +
                                appId;

                            $.ajax({
                                url: route("web.appointment.razorpay.init"),
                                type: "POST",
                                data: formData,
                                processData: false,
                                success: function (data) {
                                    if (data.success) {
                                        options.order_id = data.data.id;
                                        options.appointment_id =
                                            data.data.appointment_id;
                                        options.amount = data.data.amount;
                                        options.payment_mode =
                                            data.data.payment_mode;
                                        let rzp = new Razorpay(options);
                                        rzp.open();
                                    }
                                },
                                error: function (data) {
                                    $("#appointmentForm")[0].reset();
                                    displayErrorMessage(data.responseJSON.message);
                                },
                                complete: function () { },
                            });
                        } else if (result.data.payment_type == "3") {
                            $.ajax({
                                type: "GET",
                                url: route("web.appointment.paypal.init"),
                                data: {
                                    appointment_id: result.data.appointment_id,
                                    payment_type: result.data.payment_type,
                                    amount: result.data.amount,
                                },
                                success: function (data) {
                                    if (data.url) {
                                        window.location.href = data.url;
                                    }
                                },
                                error: function (data) {
                                    $("#appointmentForm")[0].reset();
                                    displayErrorMessage(data.responseJSON.message);
                                },
                                complete: function () { },
                            });
                        } else if (result.data.payment_type == "5") {
                            $.ajax({
                                type: "GET",
                                url: route("web.appointment.flutterwave"),
                                data: {
                                    appointment_id: result.data.appointment_id,
                                    payment_type: result.data.payment_type,
                                    amount: result.data.amount,
                                },
                                success: function (data) {
                                    if (data.data.url) {
                                        window.location.href = data.data.url;
                                    }
                                },
                                error: function (data) {
                                    $("#appointmentForm")[0].reset();
                                    displayErrorMessage(data.responseJSON.message);
                                },
                            });
                        } else if (result.data.payment_type == "7") {
                            $.ajax({
                                type: "GET",
                                url: route("web.appointment.phone.pay.init"),
                                data: {
                                    data: result.data.input,
                                    payment_type: result.data.payment_type,
                                    amount: result.data.amount,
                                },
                                success: function (data) {
                                    if (data.data.url) {
                                        window.location.href = data.data.url;
                                    }
                                },
                                error: function (data) {
                                    $("#appointmentForm")[0].reset();
                                    displayErrorMessage(data.responseJSON.message);
                                    setTimeout(function () {
                                        window.location.href = $(".backUrl").val();
                                    }, 5000);
                                },
                            });
                        }
                    } else if (result.data.payStackData.payment_type != null) {
                        if (result.data.payStackData.payment_type == 8) {
                            window.location.replace(
                                route("web.appointment.paystack.init", {
                                    data: result.data.payStackData,
                                })
                            );
                        }
                    }
                }
            },
            error: function (result) {
                printErrorMessage("#validationErrorsBox", result);
                $(".alert").delay(5000).slideUp(300);
                hideScreenLoader();
                if ($(".isGoogleCaptchaEnabled").val()) {
                    grecaptcha.reset();
                }
            },
        });
    });

    function showScreenLoader() {
        $("#overlay-screen-lock").removeClass("d-none");
    }

    function hideScreenLoader() {
        $("#overlay-screen-lock").addClass("d-none");
    }

    function validatePassword() {
        let password = $("#frontAppointmentPassword").val();
        let confirmPassword = $("#frontAppointmentConfirmPassword").val();

        if (password == "" || confirmPassword == "") {
            displayErrorMessage(Lang.get("js.all_required_fields"));
            return false;
        }

        if (password !== confirmPassword) {
            displayErrorMessage(Lang.get("js.password_not_match"));
            return false;
        }

        return true;
    }

    listenClick("#reset", function () {
        $(this)
            .closest("#appointmentForm")
            .find(
                "input[type=text], input[type=password], input[type=email], textarea"
            )
            .val("");
        $(
            "#patientId, #frontAppointmentDoctorId, #frontAppointmentDepartmentId"
        )
            .val("")
            .trigger("change.select2");
    });

    $.ajax({
        url: $(".doctorUrl").val(),
        type: "get",
        dataType: "json",
        data: { id: doctor },
        success: function (data) {
            $("#frontAppointmentDoctorId").empty();
            let $select = $(
                document.getElementById("frontAppointmentDoctorId")
            ).selectize();
            let selectize = $select[0].selectize;
            $.each(data.data, function (i, v) {
                selectize.addOption({ value: i, text: v });
                selectize.setValue(i);
            });
        },
    });

    // listenKeyup("#webAppointmentCharge", function () {
    //     let charge = $(this).val();
    //     if (charge != 0) {
    //         $(".web-appointment-payment").removeClass("d-none");
    //     }
    //     if (charge == 0 || charge == "") {
    //         $(".web-appointment-payment").addClass("d-none");
    //     }
    // });
    // })
}
