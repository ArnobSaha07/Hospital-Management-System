<div>
    <div class="our-hospitals">
        <div class="row">
            {{-- @if ($data['hospitals']->count() == 0) --}}
            <div class="col-xl-3 col-md-3 col-12 bg-light me-3 mt-3">
                <div class="p-3 mt-3">
                    <div class="d-flex justify-content-between">
                        <h5 class="text-secondary fs-c-5">{{ __('messages.lunch_break.filters') . ':' }}</h5>
                        <div class="pb-3">
                            <a class="btn btn-outline-primary reset-filter" data-turbo="false"  wire:click="clearFilter">
                                {{ __('messages.lunch_break.reset_filter') }}
                            </a>
                        </div>
                    </div>
                    <div class="input-group">
                        <input class="form-control border radius-10" type="text"
                            placeholder="{{ __('auth.hospital_name') }}" wire:model.live="search" id="example-search-input">
                    </div>
                    <h5 class="text-secondary fs-c-5 mt-4">{{ __('messages.hospitals_type') . ':' }}</h5>
                    <div class="" wire:ignore>
                        <select class='text-gray bg-white selectized' id="hospitalType" wire:model="type">
                            <option value="0" class="text-gray">{{ __('messages.common.select_type') }}</option>
                            @foreach ($data['hospital_types'] as $key => $hospitalType)
                                <option value="{{ $hospitalType->id }}">{{ $hospitalType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            @if ($data['hospitals']->count() == 0)
                <div class="col-8 p-5 rounded-20">
                    <div class="row d-flex align-items-center justify-content-center">
                        <div class="col-12 text-center shadow rounded-20">
                            <h4 class="p-5">
                                {{ __('messages.common.no_matching_records_found') }}
                            </h4>
                        </div>
                    </div>
                </div>
            @else
                @foreach ($data['hospitals'] as $key => $hospital)
                    @if ($key == 0)
                        {{-- <div class="col-xl-3 col-md-3 col-12 bg-light me-3 mt-3"> --}}
                            {{-- <div class="p-3 mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="text-secondary fs-c-5">{{ __('messages.lunch_break.filters') . ':' }}</h5>
                                    <div class="pb-3">
                                        <a class="btn btn-outline-primary reset-filter" data-turbo="false" wire:click="clearFilter">
                                            {{ __('messages.lunch_break.reset_filter') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input class="form-control border radius-10" type="search"
                                        placeholder="{{ __('auth.hospital_name') }}" wire:model="search" id="example-search-input">
                                </div>
                                <h5 class="text-secondary fs-c-5 mt-4">{{ __('messages.hospitals_type') . ':' }}</h5>
                                <div class="" wire:ignore>
                                    <select class='text-gray bg-white' id="hospitalType" wire:model="type">
                                        <option value="" class="text-gray">{{ __('messages.common.select_type') }}</option>
                                        @foreach ($data['hospital_types'] as $key => $hospitalType)
                                            <option value="{{ $hospitalType->id }}">{{ $hospitalType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}
                        {{-- </div> --}}
                    @else
                        <div class="col-xl-3 col-md-3 col-12 bg-white me-3">
                        </div>
                    @endif
                    <div class="col-xl-8 col-md-8 col-12 shadow rounded-20 {{ $key == 0 ? '' : 'mt-3' }}">
                        <div class="row  p-4">
                            <div class="col-xl-3 col-lg-3 col-md-12 col-12 pe-4 image image-medium pe-4">
                                <img class="rounded" src="{{ getAvatarUrl()."?name=$hospital->full_name&size=100&color=fff&background=".getRandomColor($hospital->id) }}" alt="image" loading="lazy"/>
                            </div>
                            <div class="col-xl-9 col-lg-9 col-md-10 col-12">
                                <div class="row d-flex justify-content-between">
                                    <div class="col-xl-8 col-12">
                                        <p>
                                            <h4><a href="#"
                                                class="text-secondary text-decoration-none fs-5">{{ $hospital->hospital_name }}</a>
                                            </h4>
                                        </p>
                                        <p>
                                            <span class="card-text fs-6 text-secondary">
                                                <i class="fa-solid fa-envelope me-1"></i>
                                                <a href="mailto:{{ $hospital->email }}"
                                                    class="text-secondary text-decoration-none fs-5">
                                                    {{ $hospital->email }}
                                                </a>
                                            </span>
                                        </p>
                                        <p>
                                            <span class="card-text fs-6 text-secondary">
                                                <i class="fa-solid fa-phone text-secondary me-3"></i>
                                                <a href="tel:{{ $hospital->phone }}"
                                                    class="text-decoration-none text-secondary">
                                                    {{ $hospital->phone }}
                                                </a>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="col-xl-4 col-12 image image-hospital">
                                        <div class="row d-flex justify-content-center mt-2">
                                            <div class="col-12 mb-6 pb-3">
                                                <a class="btn btn-outline-primary w-100" data-turbo="false"
                                                    target="_blank"
                                                    href="{{ route('front', $hospital->username) }}"><i class="fa-solid fa-location-dot me-2"></i>{{ __('messages.lunch_break.get_direction') }}</a>
                                            </div>
                                            <div class="col-12 mb-6 mb-md-0 pb-3">
                                                <a class="btn btn-outline-primary w-100" data-turbo="false"
                                                    target="_blank"
                                                    href="{{ route('appointment', $hospital->username) }}"><i class="fas fa-calendar-check me-2"></i>{{ __('messages.delete.appointment') }}</a>
                                            </div>
                                            <div class="col-12">
                                                <a class="btn btn-outline-primary w-100" data-turbo="false"
                                                    target="_blank"
                                                    href="{{ route('contact', $hospital->username) }}"><i class="fas fa-hand-pointer me-2"></i>{{ __('messages.landing.get_in_touch') }}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="col-lg-4 col-md-6 mb-lg-5 mb-md-4 mb-3 d-flex align-items-stretch ps-4 ps-md-3">
                    <div class="card flex-fill ms-lg-4 me-xl-5 ms-md-4 me-md-2 ms-4 ps-1 ps-md-0">
                        <a href="{{ route('front',$hospital->username) }}" data-turbo="false">
                            <div class="row justify-content-between align-items-center">
                                <div class="col-md-2 col-1 ps-xl-2 ps-2">
                                    <img class="card-img rounded-circle"
                                         src="{{ isset($hospital) ? asset($hospital['image_url']) : ''}}"
                                         alt="New-Horizon">
                                </div>
                                <div class="col-md-10 col-11">
                                    <div class="card-body d-flex flex-column py-4">
                                        <h3>{{ $hospital->full_name }}</h3>
                                        <p class="card-text">{{ $hospital->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div> --}}
                @endforeach
            @endif
        </div>
    </div>
    <div class="pagination-section pt-5 d-flex justify-content-center">
        {{ $data['hospitals']->links() }}
    </div>
</div>
