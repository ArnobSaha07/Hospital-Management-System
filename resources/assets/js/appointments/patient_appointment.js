// document.addEventListener('turbo:load', loadPatientAppointmentData)
Livewire.hook("element.init", () => {
    loadPatientAppointmentData();
});
function loadPatientAppointmentData() {
    $('#status').select2();
}

listenClick('.appointment-delete-btn', function (event) {
    let appointmentId = $(event.currentTarget).attr('data-id');
    deleteItem($('#appointmentIndexURL').val() + '/' + appointmentId,
        '#appointmentsTbl',
        $('#appointmentLang').val())
})

listenClick('#resetAppointmentFilter', function () {
    timeRange.data('daterangepicker').
        setStartDate(moment().startOf('week').format('MM/DD/YYYY'));
    timeRange.data('daterangepicker').
        setEndDate(moment().endOf('week').format('MM/DD/YYYY'));
    startTime = timeRange.data('daterangepicker').
        startDate.
        format('YYYY-MM-D  H:mm:ss');
    endTime = timeRange.data('daterangepicker').
        endDate.
        format('YYYY-MM-D  H:mm:ss');
    $('#status').val(2).trigger('change');
    hideDropdownManually('.dropdown-menu,#dropdownMenuButton1')
})

let timeRange = $('#time_range');
var start = moment().subtract(29, 'days');
var end = moment();
let startTime = '';
let endTime = '';

function cb (start, end) {
    $('#time_range').
        html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
}

timeRange.daterangepicker({
    startDate: start,
    endDate: end,
    locale: {
        customRangeLabel: Lang.get('js.custom'),
        applyLabel: Lang.get('js.apply'),
        cancelLabel: Lang.get('js.cancel'),
        fromLabel: Lang.get('js.from'),
        toLabel: Lang.get('js.to'),
        monthNames: [
            Lang.get('js.jan'),
            Lang.get('js.feb'),
            Lang.get('js.mar'),
            Lang.get('js.apr'),
            Lang.get('js.may'),
            Lang.get('js.jun'),
            Lang.get('js.jul'),
            Lang.get('js.aug'),
            Lang.get('js.sep'),
            Lang.get('js.oct'),
            Lang.get('js.nov'),
            Lang.get('js.dec'),
        ],
        daysOfWeek: [
            Lang.get('js.sun'),
            Lang.get('js.mon'),
            Lang.get('js.tue'),
            Lang.get('js.wed'),
            Lang.get('js.thu'),
            Lang.get('js.fri'),
            Lang.get('js.sat'),
        ],
    },
    ranges: {
        [Lang.get('js.today')]: [moment(), moment()],
        [Lang.get('js.yesterday')]: [
            moment().subtract(1, 'days'),
            moment().subtract(1, 'days')],
        [Lang.get('js.last_7_days')]: [
            moment().
                subtract(6, 'days'), moment()],
        [Lang.get('js.last_30_days')]: [
            moment().
                subtract(29, 'days'), moment()],
        [Lang.get('js.this_month')]: [
            moment().startOf('month'),
            moment().endOf('month')],
        [Lang.get('js.last_month')]: [
            moment().subtract(1, 'month').startOf('month'),
            moment().subtract(1, 'month').endOf('month')],
    },
}, cb);

cb(start, end);
timeRange.on('apply.daterangepicker', function (ev, picker) {
    startTime = picker.startDate.format('YYYY-MM-D  H:mm:ss');
    endTime = picker.endDate.format('YYYY-MM-D  H:mm:ss');
    Livewire.dispatch('refresh')
    // $('#appointmentsTbl').DataTable().ajax.reload(null, true);
});
