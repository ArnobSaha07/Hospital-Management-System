<div class="d-flex flex-xxl-row flex-column  mt-md-0 mt-sm-3 ">
    <div class="d-flex justify-content-end flex-wrap">
        <div class="d-flex align-items-center py-2">
            <a href="{{ route('appointment-calendars.index') }}" class="btn btn-icon btn-primary me-2">
                <i class="fas fa-calendar-alt fs-3"></i>
            </a>
            <div class="dropdown d-flex align-items-center me-sm-3" wire:ignore>
                <button class="btn btn btn-icon btn-primary text-white dropdown-toggle hide-arrow ps-2 pe-0"
                    type="button" id="dropdownMenuButton1" data-bs-auto-close="outside" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class='fas fa-filter'></i>
                </button>
                <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuButton1">
                    <div class="text-start border-bottom py-4 px-7">
                        <h3 class="text-gray-900 mb-0">{{ __('messages.common.filter_options') }}</h3>
                    </div>
                    <div class="p-5">
                        <div class="mb-5">
                            <label for="appointmentStatus"
                                class="form-label">{{ __('messages.common.status') . ':' }}</label>
                            {{--                        {{ Form::select('status', $filterHeads[0],null, ['id' => 'appointmentStatus', 'data-control' =>'select2', 'class' => 'form-select status-selector select2-hidden-accessible data-allow-clear="true"']) }} --}}
                            <select class="form-select status-selector" id="appointmentStatus" data-control="select2"
                                name="status">
                                <option value="2">{{ __('messages.filter.all') }}</option>
                                <option value="0">{{ __('messages.appointment.pending') }}</option>
                                <option value="1">{{ __('messages.appointment.completed') }}</option>
                                <option value="3">{{ __('messages.common.canceled') }}</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="reset" id="appointmentResetFilter"
                                class="btn btn-secondary">{{ __('messages.common.reset') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center flex-wrap justify-content-end" wire:ignore>
            <div class="">
                <input class="form-control custom-width px-3" placeholder="Pick date range" id="time_range" />
                <b class="caret"></b>
            </div>
            <div class="ms-3 py-3">
            @if (Auth::user()->hasRole('Admin'))
                <div class="d-flex align-items-center">
                    <a href="{{ route('appointments.create') }}"
                        class="btn btn-primary" data-turbo="false">{{ __('messages.appointment.new_appointment') }}</a>
                </div>
            @endif
            @if (Auth::user()->hasRole('Doctor'))
                <div class="d-flex align-items-center">
                    <a data-turbo="false" href="{{ route('appointments.excel') }}"
                        class="btn btn-primary">{{ __('messages.common.export_to_excel') }}</a>
                </div>
            @endif
            @if (Auth::user()->hasRole('Patient|Receptionist'))
                <div class="dropdown pt-1">
                    <a href="#" class="btn btn-primary" id="dropdownMenuButton" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">{{ __('messages.common.actions') }}
                        <i class="fa-solid fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a href="{{ route('appointments.create') }}" class="dropdown-item  px-5" data-turbo="false">
                                {{ __('messages.appointment.new_appointment') }}
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('appointments.excel') }}" class="dropdown-item  px-5" data-turbo="false">
                                {{ __('messages.common.export_to_excel') }}
                            </a>
                        </li>
                    </ul>
                </div>
            @endif
            </div>
        </div>
    </div>
</div>
