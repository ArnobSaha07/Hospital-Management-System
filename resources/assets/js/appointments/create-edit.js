"use strict";

document.addEventListener("turbo:load", loadAppointmentCreateEdit);

function loadAppointmentCreateEdit() {
    if ($("#appointmentForm").length || $("#editAppointmentForm").length) {
        const appointmentPatientIdElement = $("#appointmentsPatientId");
        const appointmentDoctorIdElement = $("#appointmentDoctorId");
        const appointmentDepartmentIdElement = $("#appointmentDepartmentId");
        var customDate = $("#customFieldDate").val();
        var customDateTime = $("#customFieldDateTime").val();

        $("#customFieldDate").flatpickr({
            defaultDate: customDate ? customDate : new Date(),
            dateFormat: "Y-m-d",
            locale: $(".userCurrentLanguage").val(),
        });

        $("#customFieldDateTime").flatpickr({
            enableTime: true,
            defaultDate: customDateTime ? customDateTime : new Date(),
            dateFormat: "Y-m-d H:i",
            locale: $(".userCurrentLanguage").val(),
        });

        if (appointmentPatientIdElement.length) {
            $("#appointmentsPatientId").select2({
                width: "100%",
            });
            $("#appointmentsPatientId").first().focus();
        }

        if (appointmentDoctorIdElement.length) {
            $("#appointmentDoctorId").select2({
                width: "100%",
            });
        }

        if (appointmentDepartmentIdElement.length) {
            $("#appointmentDepartmentId").select2({
                width: "100%",
            });
        }

        if ($("#appointmentPayment").length) {
            $("#appointmentPayment").select2({
                width: "100%",
            });

            if ($(".isEdit").val()) {
                $(".appointmentCharge").removeClass("d-none");
                $(".editSlotTime").removeClass("d-none");
            }

            var appointmentSelectedDate;
            var appointmentIntervals;
            var appointmentAlreadyCreateTimeSlot;
            var appointmentBreakIntervals;

            let opdDate = $(".opdDate").flatpickr({
                enableTime: false,
                // minDate: moment().subtract(1, 'days').format(),
                minDate: moment(new Date()).format("YYYY-MM-DD"),
                dateFormat: "Y-m-d",
                locale: $(".userCurrentLanguage").val(),
                onChange: function (selectedDates, dateStr, instance) {
                    if (!isEmpty(dateStr)) {
                        $(".doctor-schedule").css("display", "none");
                        $(".error-message").css("display", "none");
                        $(".available-slot-heading").css("display", "none");
                        $(".color-information").css("display", "none");
                        $(".available-slot").css("display", "none");
                        $(".time-slot").remove();
                        if ($("#appointmentDepartmentId").val() == "") {
                            $("#createAppointmentErrorsBox")
                                .show()
                                .html(
                                    Lang.get("js.please_select_doctor") +
                                    " " +
                                    Lang.get("js.department")
                                );
                            $("#createAppointmentErrorsBox")
                                .delay(5000)
                                .fadeOut();
                            $(".opdDate").val("");
                            opdDate.clear();
                            return false;
                        } else if ($("#appointmentDoctorId").val() == "") {
                            $("#createAppointmentErrorsBox")
                                .show()
                                .html(Lang.get("js.please_select_doctor"));
                            $("#createAppointmentErrorsBox")
                                .delay(5000)
                                .fadeOut();
                            $(".opdDate").val("");
                            opdDate.clear();
                            return false;
                        }
                        var weekday = [
                            "Sunday",
                            "Monday",
                            "Tuesday",
                            "Wednesday",
                            "Thursday",
                            "Friday",
                            "Saturday",
                        ];
                        var selected = new Date(dateStr);
                        let dayName = weekday[selected.getDay()];
                        appointmentSelectedDate = dateStr;

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
                                doctor_id: appointmentDoctorId,
                                date: appointmentSelectedDate,
                            },
                            success: function (result) {
                                if (result.success) {
                                    if (result.data != "") {
                                        if (
                                            result.data.scheduleDay.length !=
                                            0 &&
                                            result.data.doctorHoliday.length ==
                                            0
                                        ) {
                                            let availableFrom = "";
                                            if (
                                                moment(new Date()).format(
                                                    "YYYY-MM-DD"
                                                ) === dateStr
                                            ) {
                                                availableFrom = moment().ceil(
                                                    moment
                                                        .duration(
                                                            result.data
                                                                .perPatientTime[0]
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
                                                //         'minutes').
                                                //     format('H:mm:ss');
                                            } else {
                                                availableFrom =
                                                    result.data.scheduleDay[0]
                                                        .available_from;
                                            }

                                            var doctorStartTime =
                                                appointmentSelectedDate +
                                                " " +
                                                availableFrom;
                                            var doctorEndTime =
                                                appointmentSelectedDate +
                                                " " +
                                                result.data.scheduleDay[0]
                                                    .available_to;
                                            if (moment(doctorEndTime).isBefore(moment())) {
                                                $(".doctor-schedule").css("display", "none");
                                                $(".color-information").css("display", "none");
                                                $(".available-slot").css("display", "none");
                                                $(".error-message").css("display", "block");
                                                $(".error-message").html(Lang.get("js.doctor_schedule_not_available_on_this_date"));
                                                return;
                                            }

                                            var doctorPatientTime =
                                                result.data.perPatientTime[0]
                                                    .per_patient_time;
                                            //perPatientTime convert to Minute
                                            var a =
                                                doctorPatientTime.split(":"); // split it at the colons
                                            var minutes = +a[0] * 60 + +a[1]; // convert to minute
                                            //parse In

                                            var startTime =
                                                appointmentParseIn(
                                                    doctorStartTime
                                                );
                                            var endTime =
                                                appointmentParseIn(
                                                    doctorEndTime
                                                );
                                            //call to getTimeIntervals function
                                            appointmentIntervals =
                                                appointmentGetTimeIntervals(
                                                    startTime,
                                                    endTime,
                                                    minutes
                                                );
                                            if (
                                                result.data.doctorBreak != null
                                            ) {
                                                for (
                                                    var breakIndex = 0;
                                                    breakIndex <
                                                    result.data.doctorBreak
                                                        .length;
                                                    ++breakIndex
                                                ) {
                                                    var startBreakTime =
                                                        appointmentParseIn(
                                                            appointmentSelectedDate +
                                                            " " +
                                                            result.data
                                                                .doctorBreak[
                                                                breakIndex
                                                            ].break_from
                                                        );

                                                    var endBreakTime =
                                                        appointmentParseIn(
                                                            appointmentSelectedDate +
                                                            " " +
                                                            result.data
                                                                .doctorBreak[
                                                                breakIndex
                                                            ].break_to
                                                        );

                                                    appointmentBreakIntervals =
                                                        appointmentGetTimeIntervals(
                                                            startBreakTime,
                                                            endBreakTime,
                                                            1
                                                        );
                                                    appointmentIntervals =
                                                        appointmentIntervals.filter(
                                                            (slot) =>
                                                                !appointmentBreakIntervals.includes(
                                                                    slot
                                                                )
                                                        );
                                                }
                                            }

                                            //if intervals array length is grater then 0 then process
                                            if (
                                                appointmentIntervals.length > 0
                                            ) {
                                                $(
                                                    ".available-slot-heading"
                                                ).css("display", "block");
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
                                                    index <
                                                    appointmentIntervals.length;
                                                    ++index
                                                ) {
                                                    let data = [
                                                        {
                                                            index: index,
                                                            timeSlot:
                                                                appointmentIntervals[
                                                                index
                                                                ],
                                                        },
                                                    ];
                                                    var timeSlot =
                                                        prepareTemplateRender(
                                                            "#appointmentSlotTemplate",
                                                            data
                                                        );
                                                    timeStlots += timeSlot;
                                                }
                                                $(".available-slot").append(
                                                    timeStlots
                                                );
                                            }

                                            // display Day Name and time
                                            if (
                                                availableFrom != "00:00:00" &&
                                                result.data.scheduleDay[0]
                                                    .available_to !=
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
                                                    result.data.scheduleDay[0]
                                                        .available_on
                                                );
                                                $(".schedule-time").html(
                                                    "[" +
                                                    availableFrom +
                                                    " - " +
                                                    result.data
                                                        .scheduleDay[0]
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
                                                $(".available-slot").css(
                                                    "display",
                                                    "none"
                                                );
                                                $(".error-message").css(
                                                    "display",
                                                    "block"
                                                );
                                                $(".error-message").html(
                                                    Lang.get(
                                                        "js.doctor_schedule_not_available_on_this_date"
                                                    )
                                                );
                                            }
                                        } else {
                                            $(".doctor-schedule").css(
                                                "display",
                                                "none"
                                            );
                                            $(".color-information").css(
                                                "display",
                                                "none"
                                            );
                                            $(".available-slot").css(
                                                "display",
                                                "none"
                                            );
                                            $(".error-message").css(
                                                "display",
                                                "block"
                                            );
                                            $(".error-message").html(
                                                Lang.get(
                                                    "js.doctor_schedule_not_available_on_this_date"
                                                )
                                            );
                                        }
                                    }
                                }
                            },
                            error: function (error) {
                                displayErrorMessage(error.responseJSON.message);
                            },
                        });

                        if ($(".isCreate").val() || $(".isEdit").val()) {
                            var delayCall = 200;
                            setTimeout(getCreateTimeSlot, delayCall);

                            function getCreateTimeSlot() {
                                if ($(".isCreate").val()) {
                                    var data = {
                                        editSelectedDate:
                                            appointmentSelectedDate,
                                        doctor_id: appointmentDoctorId,
                                    };
                                } else {
                                    var data = {
                                        editSelectedDate:
                                            appointmentSelectedDate,
                                        editId: $("#appointmentEditsID").val(),
                                        doctor_id: appointmentDoctorId,
                                    };
                                }

                                $.ajax({
                                    url: $(".getBookingSlot").val(),
                                    type: "GET",
                                    data: data,
                                    success: function (result) {
                                        appointmentAlreadyCreateTimeSlot =
                                            result.data.bookingSlotArr;
                                        if (
                                            result.data.hasOwnProperty(
                                                "onlyTime"
                                            )
                                        ) {
                                            if (
                                                result.data.bookingSlotArr
                                                    .length > 0
                                            ) {
                                                appointmentEditTimeSlot =
                                                    result.data.onlyTime.toString();
                                                $.each(
                                                    result.data.bookingSlotArr,
                                                    function (index, value) {
                                                        $.each(
                                                            appointmentIntervals,
                                                            function (i, v) {
                                                                if (
                                                                    value == v
                                                                ) {
                                                                    $(
                                                                        ".time-interval"
                                                                    ).each(
                                                                        function () {
                                                                            if (
                                                                                $(
                                                                                    this
                                                                                ).data(
                                                                                    "id"
                                                                                ) ==
                                                                                i
                                                                            ) {
                                                                                if (
                                                                                    $(
                                                                                        this
                                                                                    ).html() !=
                                                                                    appointmentEditTimeSlot
                                                                                ) {
                                                                                    $(
                                                                                        this
                                                                                    )
                                                                                        .parent()
                                                                                        .css(
                                                                                            {
                                                                                                "background-color":
                                                                                                    "#ffa721",
                                                                                                border: "1px solid #ffa721",
                                                                                                color: "#ffffff",
                                                                                            }
                                                                                        );
                                                                                    $(
                                                                                        this
                                                                                    )
                                                                                        .parent()
                                                                                        .addClass(
                                                                                            "booked"
                                                                                        );
                                                                                    $(
                                                                                        this
                                                                                    )
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
                                                            }
                                                        );
                                                    }
                                                );
                                            }
                                            $(".time-interval").each(
                                                function () {
                                                    if (
                                                        $(this).html() ==
                                                        appointmentEditTimeSlot &&
                                                        result.data
                                                            .bookingSlotArr
                                                            .length > 0
                                                    ) {
                                                        $(this)
                                                            .parent()
                                                            .addClass(
                                                                "time-slot-book"
                                                            );
                                                        $(this)
                                                            .parent()
                                                            .removeClass(
                                                                "booked"
                                                            );
                                                        $(this)
                                                            .parent()
                                                            .children()
                                                            .prop(
                                                                "disabled",
                                                                false
                                                            );
                                                        $(this).click();
                                                    }
                                                }
                                            );
                                        } else if (
                                            appointmentAlreadyCreateTimeSlot.length >
                                            0
                                        ) {
                                            $.each(
                                                appointmentAlreadyCreateTimeSlot,
                                                function (index, value) {
                                                    $.each(
                                                        appointmentIntervals,
                                                        function (i, v) {
                                                            if (value == v) {
                                                                $(
                                                                    ".time-interval"
                                                                ).each(
                                                                    function () {
                                                                        if (
                                                                            $(
                                                                                this
                                                                            ).data(
                                                                                "id"
                                                                            ) ==
                                                                            i
                                                                        ) {
                                                                            $(
                                                                                this
                                                                            )
                                                                                .parent()
                                                                                .addClass(
                                                                                    "time-slot-book"
                                                                                );
                                                                            $(
                                                                                ".time-slot-book"
                                                                            ).css(
                                                                                {
                                                                                    "background-color":
                                                                                        "#ffa721",
                                                                                    border: "1px solid #ffa721",
                                                                                    color: "#ffffff",
                                                                                }
                                                                            );
                                                                            $(
                                                                                this
                                                                            )
                                                                                .parent()
                                                                                .addClass(
                                                                                    "booked"
                                                                                );
                                                                            $(
                                                                                this
                                                                            )
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
                                                        }
                                                    );
                                                }
                                            );
                                        }
                                    },
                                });
                            }
                        }
                    }
                },
            });

            listenChange("#appointmentDepartmentId", function () {
                $(".error-message").css("display", "none");
                // $('#opdDate').data('DateTimePicker').clear();
                opdDate.clear();
                $(".doctor-schedule").css("display", "none");
                $(".available-slot-heading").css("display", "none");
                $(".available-slot").css("display", "none");
                $.ajax({
                    url: $(".doctorDepartmentUrl").val(),
                    type: "get",
                    dataType: "json",
                    data: { id: $(this).val() },
                    success: function (data) {

                        $("#appointmentDoctorId").empty();
                        $("#appointmentDoctorId").append(
                            $(
                                '<option value="">' +
                                Lang.get("js.select_doctor") +
                                "</option>"
                            )
                        );
                        $.each(data.data, function (i, v) {
                            $("#appointmentDoctorId").append(
                                $("<option></option>").attr("value", i).text(v)
                            );
                        });
                    },
                });
            });

            var appointmentDoctorId;
            let appointmentDoctorChange = false;

            listenChange("#appointmentDoctorId", function () {
                if (appointmentDoctorChange) {
                    $(".doctor-schedule").css("display", "none");
                    $(".available-slot-heading").css("display", "none");
                    $(".available-slot").css("display", "none");
                    $(".error-message").css("display", "none");
                    $("#appointmentCharge").val("");
                    $("#appointmentPayment").prop("required", false);
                    opdDate.clear();
                    appointmentDoctorChange = true;
                }
                $(".error-message").css("display", "none");
                appointmentDoctorId = $(this).val();
                appointmentDoctorChange = true;
                $.ajax({
                    url: route("get-appointment-charge"),
                    type: "get",
                    dataType: "json",
                    data: {
                        doctor_id: appointmentDoctorId,
                    },
                    success: function (result) {
                        if (result.success) {
                            if (result.data != null) {
                                let charge = result.data.appointment_charge;

                                if (charge >= 0 && charge != 0) {
                                    $(".appointmentCharge").removeClass(
                                        "d-none"
                                    );
                                    $("#appointmentCharge").val(charge);
                                    $(".appointment-payment").removeClass(
                                        "d-none"
                                    );
                                    $("#appointmentPayment").prop(
                                        "required",
                                        true
                                    );
                                }
                                if (charge <= 0 || charge == undefined) {
                                    $(".appointment-payment").addClass(
                                        "d-none"
                                    );
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
            var appointmentEditTimeSlot;
            if ($(".isEdit").val()) {
                $("#appointmentDoctorId").trigger("change", function (event) {
                    appointmentDoctorId = $(this).val();
                });

                $(".opdDate").trigger("dp.change", function () {
                    var selected = new Date($(this).val());
                });
            }

            //parseIn date_time
            function appointmentParseIn(date_time) {
                var d = new Date();
                d.setHours(date_time.substring(11, 13));
                d.setMinutes(date_time.substring(14, 16));

                return d;
            }

            //make time slot list
            function appointmentGetTimeIntervals(time1, time2, duration) {
                var arr = [];

                while (time1 < time2) {
                    arr.push(time1.toTimeString().substring(0, 5));
                    time1.setMinutes(time1.getMinutes() + duration);
                }
                return arr;
            }

            var appointmentEditTimeSlot;
            listenClick(".time-interval", function () {
                appointmentEditTimeSlot = $(this).text();
            });

            //Edit appointment
            listenSubmit("#editAppointmentForm", function (event) {
                let editAppointmentOpdDate = $("#appointmentOpdDate").val();
                var editSlotTime = $("#editTimeSlot").val();
                var isValid = true;

                if (isEmpty(editAppointmentOpdDate)) {
                    $("#editAppointmentErrorsBox")
                        .show()
                        .removeClass("d-none")
                        .html(Lang.get("js.select_appointment_date"))
                        .delay(5000)
                        .slideUp(300);
                    return false;
                }
                if (
                    (appointmentEditTimeSlot == null ||
                        appointmentEditTimeSlot == "") &&
                    (editSlotTime == null || editSlotTime == "")
                ) {
                    $("#editAppointmentErrorsBox")
                        .show()
                        .html(Lang.get("js.select_time_slot"))
                        .delay(5000)
                        .slideUp(300);
                    return false;
                }

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
                            $("#editAppointmentErrorsBox")
                                .show()
                                .removeClass("d-none")
                                .html(fieldLabel + " field is required.")
                                .delay(5000)
                                .slideUp(300);
                            isValid = false;
                            return false;
                        }
                    } else if ($(this).is(':input[type="checkbox"]')) {
                        if (!$(this).is(":checked")) {
                            $("#editAppointmentErrorsBox")
                                .show()
                                .removeClass("d-none")
                                .html(fieldLabel + " field is required.")
                                .delay(5000)
                                .slideUp(300);
                            isValid = false;
                            return false;
                        }
                    } else if ($(this).is("select")) {
                        if (
                            !fieldValue &&
                            $(this).val().length === 0 &&
                            fieldValue.trim() === ""
                        ) {
                            $("#editAppointmentErrorsBox")
                                .show()
                                .removeClass("d-none")
                                .html("Please select " + fieldLabel)
                                .delay(5000)
                                .slideUp(300);
                            isValid = false;
                            return false;
                        }
                    }
                });

                event.preventDefault();

                if (isValid) {
                    screenLock();
                    var appointmentEditTime = appointmentEditTimeSlot
                        ? appointmentEditTimeSlot
                        : editSlotTime;
                    let formData =
                        $(this).serialize() + "&time=" + appointmentEditTime;
                    $.ajax({
                        url: $("#appointmentUpdateUrl").val(),
                        type: "POST",
                        dataType: "json",
                        data: formData,
                        success: function (result) {
                            displaySuccessMessage(result.message);
                            screenUnLock();
                            setTimeout(function () {
                                window.location.href = $(
                                    ".appointmentIndexPage"
                                ).val();
                            }, 2000);
                        },
                        error: function (result) {
                            printErrorMessage(
                                "#editAppointmentErrorsBox",
                                result
                            );
                            screenUnLock();
                        },
                    });
                }
            });
        } else {
            return false;
        }
    }
}

// listenKeyup('#appointmentCharge', function () {
//     let charge = $(this).val();
//     if(charge != 0){
//         $('.appointment-payment').removeClass('d-none');
//     }if(charge == 0 || charge == ''){
//         $('.appointment-payment').addClass('d-none');
//     }
// });

//slot click change color
var appointmentSelectedTime;
listenClick(".time-interval", function (event) {
    let appointmentId = $(event.currentTarget).attr("data-id");
    if ($(this).data("id") == appointmentId) {
        if ($(this).parent().hasClass("booked")) {
            $(".time-slot-book").css("background-color", "#ffa0a0");
        }
    }
    appointmentSelectedTime = $(this).text();
    $(".time-slot").removeClass("time-slot-book");
    $(this).parent().addClass("time-slot-book");
    if ($(".isEdit").val()) {
        $("#editTimeSlot").val(appointmentSelectedTime);
    }
});

//create appointment
listenSubmit("#appointmentForm", function (event) {
    let appointmentOpdDate = $("#appointmentOpdDate").val();

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
            $(this).is(':input[type="text"], :input[type="number"], textarea')
        ) {
            if (!fieldValue || fieldValue.trim() === "") {
                $("#createAppointmentErrorsBox")
                    .show()
                    .removeClass("d-none")
                    .html(fieldLabel + " field is required.")
                    .delay(5000)
                    .slideUp(300);
                isValid = false;
                return false;
            }
        } else if ($(this).is(':input[type="checkbox"]')) {
            if (!$(this).is(":checked")) {
                $("#createAppointmentErrorsBox")
                    .show()
                    .removeClass("d-none")
                    .html(fieldLabel + " field is required.")
                    .delay(5000)
                    .slideUp(300);
                isValid = false;
                return false;
            }
        } else if ($(this).is("select")) {
            if (
                !fieldValue &&
                $(this).val().length === 0 &&
                fieldValue.trim() === ""
            ) {
                $("#createAppointmentErrorsBox")
                    .show()
                    .removeClass("d-none")
                    .html("Please select " + fieldLabel)
                    .delay(5000)
                    .slideUp(300);
                isValid = false;
                return false;
            }
        }
    });

    if (isEmpty(appointmentOpdDate)) {
        $("#createAppointmentErrorsBox")
            .show()
            .removeClass("d-none")
            .html(Lang.get("js.select_appointment_date"))
            .delay(5000)
            .slideUp(300);
        return false;
    }
    if ($("#appointmentCharge").val() != null) {
        if ($("#appointmentPayment").val() == null) {
            displayErrorMessage(Lang.get("Select Payment Mode"));
            hideScreenLoader();
            return false;
        }
    }
    if (appointmentSelectedTime == null || appointmentSelectedTime == "") {
        $("#createAppointmentErrorsBox")
            .show()
            .removeClass("d-none")
            .html(Lang.get("js.select_time_slot"))
            .delay(5000)
            .slideUp(300);
        return false;
    }

    event.preventDefault();
    screenLock();
    let formData = $(this).serialize() + "&time=" + appointmentSelectedTime;
    $.ajax({
        url: $("#saveAppointmentURLID").val(),
        type: "POST",
        dataType: "json",
        data: formData,
        success: function (result) {
            screenUnLock();
            if (
                (result.data && result.data.payment_type)
            ) {
                if (result.data.payment_type == "1") {
                    let stripeKey = $("#stripeConfigKey").val();
                    let stripe = Stripe(stripeKey);
                    let payloadData = {
                        appointment_id: result.data.appointment_id,
                        payment_type: result.data.payment_type,
                        amount: result.data.amount,
                    };
                    $(this)
                        .html(
                            '<div class="spinner-border spinner-border-sm " role="status">\n' +
                            '                                            <span class="sr-only">Loading...</span>\n' +
                            "                                        </div>"
                        )
                        .addClass("disabled");
                    $.post(route("appointment.stripe.session"), payloadData)
                        .done((result) => {
                            let sessionId = result.data.sessionId;
                            stripe
                                .redirectToCheckout({
                                    sessionId: sessionId,
                                })
                                .then(function (result) {
                                    manageAjaxErrors(result);
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
                        url: route("appointmentRazorpay.init"),
                        type: "POST",
                        data: formData,
                        processData: false,
                        success: function (data) {
                            // if (data.url) {
                            //     window.location.href = data.url
                            // }
                            if (data.success) {
                                options.order_id = data.data.id;
                                options.appointment_id =
                                    data.data.appointment_id;
                                options.amount = data.data.amount;
                                options.payment_mode = data.data.payment_mode;
                                let rzp = new Razorpay(options);
                                screenUnLock();
                                rzp.open();
                                // razorPay.on('payment.failed', storePatientFailedPayment)
                            }
                        },
                        error: function (data) {
                            screenUnLock();
                            displayErrorMessage(data.responseJSON.message);
                            setTimeout(function () {
                                window.location.href = $(
                                    ".appointmentIndexPage"
                                ).val();
                            }, 2000);
                        },
                        complete: function () { },
                    });
                } else if (result.data.payment_type == "3") {
                    $.ajax({
                        type: "GET",
                        url: route("appointment.paypal.init"),
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
                            setTimeout(function () {
                                window.location.href = $(
                                    ".appointmentIndexPage"
                                ).val();
                            }, 2000);
                            displayErrorMessage(data.responseJSON.message);
                        },
                        complete: function () { },
                    });
                } else if (result.data.payment_type == 5) {
                    $.ajax({
                        type: "GET",
                        url: route("appointment.flutterwave.payment"),
                        data: {
                            appointment_id: result.data.appointment_id,
                            payment_type: result.data.payment_type,
                            amount: result.data.amount,
                        },
                        success: function (result) {
                            if (result.data.url) {
                                window.location.href = result.data.url;
                            }
                        },
                        error: function (data) {
                            setTimeout(function () {
                                window.location.href = $(
                                    ".appointmentIndexPage"
                                ).val();
                            }, 2000);
                            displayErrorMessage(data.responseJSON.message);
                        },
                    });
                } else if (result.data.payment_type == "7") {
                    $.ajax({
                        type: "GET",
                        url: route("appointment.phone.pay.init"),
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
                        },
                    });
                } else if (result.data.payStackData != null) {
                    if (result.data.payStackData.payment_type == 8) {
                        window.location.replace(
                            route("appointment.paystack.init", {
                                data: result.data.payStackData,
                            })
                        );
                    }
                }
            } else {
                displaySuccessMessage(result.message);
                setTimeout(function () {
                    window.location.href = $(".appointmentIndexPage").val();
                }, 2000);
            }
        },
        error: function (result) {
            printErrorMessage("#createAppointmentErrorsBox", result);
            screenUnLock();
        },
        complete: function () {
            processingBtn("#appointmentForm", "#saveAppointment");
        },
    });
});
