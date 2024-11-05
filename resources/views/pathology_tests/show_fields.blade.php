<div class="row">
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.bed_status.patient_name')  }}</label>
        <span class="fs-5 text-gray-800">{{$pathologyTest->patient->patientUser->full_name ?? __('messages.common.n/a')}}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.test_name')  }}</label>
        <span class="fs-5 text-gray-800">{{$pathologyTest->test_name}}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.short_name')  }}</label>
        <span class="fs-5 text-gray-800">{{$pathologyTest->short_name}}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.test_type')  }}</label>
        <span class="fs-5 text-gray-800">{{$pathologyTest->test_type}}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.category_name')  }}</label>
        <span class="fs-5 text-gray-800">{{$pathologyTest->pathologycategory->name}}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.unit')  }}</label>
        <span class="fs-5 text-gray-800">{{ (!empty($pathologyTest->unit)) ? $pathologyTest->unit : __('messages.common.n/a') }}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.subcategory')  }}</label>
        <span class="fs-5 text-gray-800">{{ (!empty($pathologyTest->subcategory)) ? $pathologyTest->subcategory : __('messages.common.n/a') }}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.method')  }}</label>
        <span class="fs-5 text-gray-800">{{ (!empty($pathologyTest->method)) ? $pathologyTest->method : __('messages.common.n/a') }}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.report_days')  }}</label>
        <span class="fs-5 text-gray-800">{{ (!empty($pathologyTest->report_days)) ? nl2br(e($pathologyTest->report_days)) : __('messages.common.n/a') }}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.charge_category')  }}</label>
        <span class="fs-5 text-gray-800">{{$pathologyTest->chargecategory->name}}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.pathology_test.standard_charge')  }}</label>
        <span class="fs-5 text-gray-800">{{ getCurrencyFormat($pathologyTest->standard_charge) }}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.common.created_on')  }}</label>
        <span class="fs-5 text-gray-800" data-toggle="tooltip" data-placement="right"
              title="{{ \Carbon\Carbon::parse($pathologyTest->created_at)->translatedFormat('jS M, Y') }}">{{ \Carbon\Carbon::parse($pathologyTest->created_at)->diffForHumans() }}</span>
    </div>
    <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
        <label class="pb-2 fs-5 text-gray-600">{{ __('messages.common.last_updated')  }}</label>
        <span class="fs-5 text-gray-800" data-toggle="tooltip" data-placement="right"
              title="{{ \Carbon\Carbon::parse($pathologyTest->updated_at)->translatedFormat('jS M, Y') }}">{{ \Carbon\Carbon::parse($pathologyTest->updated_at)->diffForHumans() }}</span>
    </div>
    <div class="px-30 table-responsive">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th scope="col">{{ __('messages.new_change.parameter_name') }}</th>
                    <th scope="col">{{ __('messages.new_change.patient_result') }}</th>
                    <th scope="col">{{ __('messages.new_change.reference_range') }}</th>
                    <th scope="col">{{ __('messages.item.unit') }}</th>
                </tr>
            </thead>
            <tbody>
                @if (empty($pathologyParameterItems))
                    {{ __('messages.common.n/a') }}
                @else
                    @foreach ($pathologyParameterItems as $pathologyParameterItem)
                        <tr>
                            <td class="py-4 border-bottom-0">{{  $pathologyParameterItem->pathologyParameter->parameter_name }}</td>
                            <td class="py-4 border-bottom-0">
                                {{ $pathologyParameterItem->patient_result }}
                            </td>
                            <td class="py-4 border-bottom-0">{{ $pathologyParameterItem->pathologyParameter->reference_range }}
                            </td>
                            <td class="py-4 border-bottom-0">
                                {{ $pathologyParameterItem->pathologyParameter->pathologyUnit->name }}</td>
                        </tr>
                @endforeach
            @endif
            </tbody>
        </table>
    </div>
</div>
