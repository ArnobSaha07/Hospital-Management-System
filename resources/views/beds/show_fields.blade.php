<div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="poverview" role="tabpanel">
            <div class="d-md-flex align-items-center justify-content-between mb-7">
                <h1 class="mb-0">{{ __('messages.bed.bed_details') }}</h1>
                <div class="text-end mt-4 mt-md-0">
                    @if (!Auth::user()->hasRole('Doctor|Receptionist'))
                        <a class="btn btn-primary bed-edit-btn"
                            data-id="{{ $bed->id }}">{{ __('messages.common.edit') }}</a>
                    @endif
                    <a href="{{ url()->previous() }}"
                        class="btn btn-outline-primary ms-2">{{ __('messages.common.back') }}</a>
                </div>
            </div>
            <div class="card mt-5 mb-5">
                <div>
                    <div class="card-body  border-top p-9">
                        <div class="row mb-7">
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-5 text-gray-600">{{ __('messages.bed_assign.bed') . ':' }}</label>
                                <span class="fs-5 text-gray-800">{{ $bed->name }}</span>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-5 text-gray-600">{{ __('messages.bed.bed_type') . ':' }}</label>
                                <span class="fs-5 text-gray-800">{{ $bed->bedType->title }}</span>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-5 text-gray-600">{{ __('messages.bed.bed_id') . ':' }}</label>
                                <span class="fs-5 text-gray-800">{{ $bed->bed_id }}</span>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-5 text-gray-600">{{ __('messages.bed.charge') . ':' }}</label>
                                <span class="fs-5 text-gray-800">{{ getCurrencyFormat($bed->charge) }}</span>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label class="pb-2 fs-5 text-gray-600">{{ __('messages.bed.available') . ':' }}</label>
                                <p class="m-0">
                                    <span
                                        class="badge fs-6 bg-light-{{ !empty($bed->is_available) ? 'success' : 'danger' }} mt-2">{{ $bed->is_available ? 'Yes' : 'No' }}</span>
                                </p>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label
                                    class="pb-2 fs-5 text-gray-600">{{ __('messages.common.created_at') . ':' }}</label>
                                <span class="fs-5 text-gray-800" data-toggle="tooltip" data-placement="right"
                                    title="{{ date('jS M, Y', strtotime($bed->created_at)) }}">{{ $bed->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="col-md-6 d-flex flex-column mb-md-10 mb-5">
                                <label
                                    class="pb-2 fs-5 text-gray-600">{{ __('messages.common.updated_at') . ':' }}</label>
                                <span class="fs-5 text-gray-800" data-toggle="tooltip" data-placement="right"
                                    title="{{ date('jS M, Y', strtotime($bed->updated_at)) }}">{{ $bed->updated_at->diffForHumans() }}</span>
                            </div>
                            <div class="col-md-12 d-flex flex-column mb-md-10 mb-5">
                                <label
                                    class="pb-2 fs-5 text-gray-600">{{ __('messages.bed.description') . ':' }}</label>
                                <span class="fs-5 text-gray-800">{!! !empty($bed->description) ? nl2br(e($bed->description)) : 'N/A' !!}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="d-md-flex align-items-center justify-content-between">
            <h1 class="m-0">{{ __('messages.bed_assign.bed_assigns') }}</h1>
        </div>
        <livewire:assign-bed-table bedId="{{ $bed->id }}" lazy/>
    </div>
</div>
