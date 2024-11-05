<div id="showSmartCardModal" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content bg-transparent">
            <div class="modal-body">
                <div class="d-flex justify-content-between">
                    <span></span>
                    <button type="button" aria-label="Close" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="row mt-4 d-flex justify-content-center">
                    <div class="patient-card-modal">
                        <div class="patient-card-header d-flex align-items-center custom-card-header">
                            <div class="flex-1 d-flex align-items-center me-3">
                                <div class="patient-card-logo me-4">
                                    <img src="{{ asset(getSettingValue()['app_logo']['value']) }}" alt="logo" class="h-100 img-fluid" />
                                </div>
                                <h4 class="text-white mb-0 fw-bold">{{ getAppName() }}</h4>
                            </div>
                            <div class="flex-1 text-end">
                                <address class="text-white fs-12 mb-0">
                                    <p class="mb-0">
                                        {{ getSettingValueByKey('hospital_address') }}
                                    </p>
                                </address>
                            </div>
                        </div>
                        <div class="patient-card-body bg-white card-body-radius">
                            <div class="d-flex justify-content-between">
                                <div class="">
                                    <div class="d-flex mb-3">
                                        <div class="image image-circle image-small me-3">
                                            <a>
                                                <div>
                                                    <img alt=""
                                                        class="user-img rounded-circle image profile-img">
                                                </div>
                                            </a>
                                        </div>
                                        <div class="">
                                            <table class="table table-borderless patient-desc mb-0">
                                                <tr class="cardName">
                                                    <td class="pe-3">{{ __('messages.item.name') }}:</td>
                                                    <td id="patientCardName"></td>
                                                </tr>
                                                <tr class="patientEmail d-none">
                                                    <td class="pe-3">{{ __('auth.email') }}:</td>
                                                    <td id="patientCardEmail"></td>
                                                </tr>
                                                <tr class="patientNumber d-none">
                                                    <td class="pe-3">{{ __('messages.web_home.contact') }}:</td>
                                                    <td id="patientCardMob"></td>
                                                </tr>
                                                <tr class="patientDob d-none">
                                                    <td class="pe-3">{{ __('messages.lunch_break.dob') }}:</td>
                                                    <td id="patientCardDob"></td>
                                                </tr>
                                                <tr class="patientBloodGroup d-none">
                                                    <td class="pe-3">{{ __('messages.user.blood_group') }}:</td>
                                                    <td id="patientCardBloodGroup"></td>
                                                </tr>
                                                <tr class="patientAddress d-none">
                                                    <td class="pe-3">
                                                        {{ __('messages.common.address') }}:
                                                    </td>
                                                    <td>
                                                        <address class="mb-0" id="patientCardAddress"></address>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    {{-- <div class="d-flex address-text me-5 patientAddress d-none">
                                        <div class="mb-0 me-3">{{ __('messages.common.address') }}:</div>
                                        <div>
                                            <address class="mb-0" id="patientCardAddress">
                                            </address>
                                        </div>
                                    </div> --}}
                                </div>

                                <div class="w-25">
                                    <div class="text-end">
                                        <div class="text-end mb-5">
                                            <div class="qr-code ms-auto mt-4">
                                            </div>
                                        </div>
                                        <h6 class="text-primary text-end patient-code mb-3 d-none" id="uniqueCardID"></h6>
                                        @role('Patient')
                                            <div class="text-end">
                                                <a title="{{ __('messages.expense.download')}}" class="btn px-5 fs-1 ps-0 download-icon" href="{{url('smart-patient-card-download').'/'.Auth::user()->patient->id}}" target="_blank" data-bs-toggle="tooltip" data-bs-original-title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        @endrole
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
