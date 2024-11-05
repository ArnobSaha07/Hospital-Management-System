document.addEventListener('turbo:load', loadDoctorHolidayDetails)

function loadDoctorHolidayDetails () {

    let lang = $('.currentLanguage').val()

    $('#doctorHolidayDate').flatpickr({
        'locale': lang,
        minDate: new Date().fp_incr(1),
        disableMobile: true,
    })
}


