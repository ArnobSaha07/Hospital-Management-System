<div>
    <?php
    $currencySymbol = getCurrencySymbol();
    ?>
    <div class="d-flex flex-column">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="row">
                    {{-- Appointments Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a class="text-decoration-none" href="{{ route('appointments.index') }}">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-file-invoice fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">
                                        {{ $TotalAppointments }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.patient.total_appointments') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    {{-- Today's Appointments Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('appointments.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fas fa-calendar-check fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">
                                        {{ $TodayAppointments }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.lunch_break.todays_appointments') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    {{-- Meetings --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('live.consultation.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa fa-video fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">
                                        {{ $TotalMeetings }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.lunch_break.total_meetings') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    @if ($modules['Bills'] == true)
                        {{-- IPD Amount Widget --}}
                        <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                            <a href="{{ route('bill.index') }}" class="text-decoration-none">
                                <div
                                    class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                    <div
                                        class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                        <i class="fa-solid fa-coins fs-1 text-white"></i>
                                    </div>
                                    <div class="text-end">
                                        <h2 class="fs-1-xxl fw-bolder text-primary">{{ $currencySymbol }}
                                            {{ formatCurrency($patientBill) }}</h2>
                                        <h3 class="mb-0 fs-5 fw-bold text-dark">
                                            {{ __('messages.dashboard.total_bills') }}</h3>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-xxl-12 col-12 mb-7 mb-xxl-0">
                <div class="card">
                    <div class="card-body">
                        <h3 class="mb-0">
                            {{ __('messages.lunch_break.recent_appointments') }}
                        </h3>
                        <livewire:upcoming-appointment-table />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
