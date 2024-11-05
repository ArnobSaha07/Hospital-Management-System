<div>
    <?php
    $currencySymbol = getCurrencySymbol();
    ?>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="row">
                @if ($modules['Invoices'] == true)
                    {{-- Invoices Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a class="text-decoration-none" href="{{ route('invoices.index') }}">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-file-invoice fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $currencySymbol }}
                                        {{ formatCurrency($invoiceAmount) }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.dashboard.total_invoices') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Bills'])
                    {{-- Bills Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('bills.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-money-bill fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $currencySymbol }}
                                        {{ formatCurrency($billAmount) }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.dashboard.total_bills') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Payments'] == true)
                    {{-- Payments Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('payments.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-coins fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $currencySymbol }}
                                        {{ formatCurrency($paymentAmount) }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.dashboard.total_payments') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Advance Payments'] == true)
                    {{-- Advance Payments Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('advanced-payments.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-money-check fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $currencySymbol }}
                                        {{ formatCurrency($advancePaymentAmount) }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.dashboard.total_advance_payments') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Doctors'] == true)
                    {{-- Doctors Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('doctors.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-doctor fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $doctors }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">{{ __('messages.dashboard.doctors') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Patients'] == true)
                    {{-- Patients Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('patients.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-injured fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $patients }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">{{ __('messages.dashboard.patients') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Nurses'] == true)
                    {{-- Nurses Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('nurses.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-user-nurse fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $nurses }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">{{ __('messages.nurses') }}</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
                @if ($modules['Beds'] == true)
                    {{-- Avaiable Beds Widget --}}
                    <div class="col-xxl-3 col-xl-4 col-sm-6 widget">
                        <a href="{{ route('beds.index') }}" class="text-decoration-none">
                            <div
                                class="bg-white shadow-md rounded-10 p-xxl-10 px-7 py-10 d-flex align-items-center justify-content-between my-3">
                                <div
                                    class="bg-primary widget-icon rounded-10 d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-bed fs-1 text-white"></i>
                                </div>
                                <div class="text-end">
                                    <h2 class="fs-1-xxl fw-bolder text-primary">{{ $availableBeds }}</h2>
                                    <h3 class="mb-0 fs-5 fw-bold text-dark">
                                        {{ __('messages.dashboard.available_beds') }}
                                    </h3>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-xxl-7 col-12 mb-7 mb-xxl-0">
            <div class="card mb-7">
                <div class="card-header pb-0 px-10">
                    <h3 class="mb-0">
                        {{ __('messages.enquiries') }}
                    </h3>
                </div>
                <div class="card-body pt-7">
                    @if (count($enquiries) > 0)
                        <div class="table table-responsive">
                            <table class="table table-responsive ">
                                <thead>
                                    <tr>
                                        <th scope="col">{{ __('messages.enquiry.name') }}</th>
                                        <th scope="col">{{ __('messages.enquiry.email') }}</th>
                                        <th scope="col">{{ __('messages.common.created_on') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="text-gray-600 fw-bold">
                                    @foreach ($enquiries as $enquiry)
                                        <tr>
                                            <td>
                                                <a href="{{ route('enquiry.show', $enquiry->id) }}"
                                                    class="text-primary-800 text-decoration-none mb-1 fs-6">{{ $enquiry->full_name }}</a>
                                            </td>
                                            <td class="text-start">
                                                <span class="text-muted fw-bold d-block">{{ $enquiry->email }}</span>
                                            </td>
                                            <td class="text-center text-muted fw-bold d-flex">
                                                <span class="badge bg-light-info">
                                                    {{ \Carbon\Carbon::parse($enquiry->created_at)->translatedFormat('jS M, Y') }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <h4 class="mb-0 text-center fs-2">{{ __('messages.dashboard.no_enquiries_yet') }}</h4>
                    @endif
                </div>
            </div>
            <div class="card mb-7">
                <div class="card-body pt-7">
                    <h3 class="mb-0">
                        {{ __('messages.new_change.upcoming_appointments') }}
                    </h3>
                    <livewire:upcoming-appointment-table lazy />
                </div>
            </div>
        </div>
        <div class="col-xxl-5 col-12 mb-7 mb-xxl-0">
            <div class="card">
                <div class="card-header pb-0 px-10">
                    <h3 class="mb-0">
                        {{ __('messages.dashboard.notice_boards') }}
                    </h3>
                </div>
                <div class="card-body pt-7">
                    @if (count($noticeBoards) > 0)
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">{{ __('messages.dashboard.title') }}</th>
                                    <th scope="col">{{ __('messages.common.created_on') }}</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 fw-bold">
                                @foreach ($noticeBoards as $noticeBoard)
                                    <tr>
                                        <td>
                                            <a href="{{ route('notice-boards.show', $noticeBoard->id) }}"
                                                class="text-decoration-none notice-board-view-btn">
                                                {{ Str::limit($noticeBoard->title, 32, '...') }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-info">
                                                {{ \Carbon\Carbon::parse($noticeBoard->created_at)->translatedFormat('jS M, Y') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <h4 class="mb-0 text-center fs-2">{{ __('messages.dashboard.no_notice_yet') }}</h4>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
