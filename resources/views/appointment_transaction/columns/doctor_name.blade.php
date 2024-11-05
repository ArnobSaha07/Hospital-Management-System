<div class="d-flex align-items-center">
    @if (Auth::user()->hasRole('Patient|Case Manager'))
        <div class="image image-mini me-3">
            <div>
                <img src="{{ $row->appointment->doctor->doctorUser->image_url }}" alt=""
                    class="user-img image image-circle object-contain" width="35px" height="35px">
            </div>
        </div>
        <div class="d-flex flex-column">
            <span
                class="mb-1 text-dark text-decoration-none object-contain">{{ $row->appointment->doctor->doctorUser->full_name }}</span>
            <span>{{ $row->appointment->doctor->doctorUser->email }}</span>
        </div>
    @else
        <div class="d-flex align-items-center">
            <div class="image image-mini me-3">
                <a href="{{ url('doctors', $row->appointment->doctor->id) }}">
                    <div>
                        <img src="{{ $row->appointment->doctor->doctorUser->image_url }}" alt=""
                            class="user-img image image-circle object-contain">
                    </div>
                </a>
            </div>
            <div class="d-flex flex-column">
                <a href="{{ url('doctors', $row->appointment->doctor->id) }}"
                    class="mb-1 text-decoration-none">{{ $row->appointment->doctor->doctorUser->fullname }}</a>
                <span>{{ $row->appointment->doctor->doctorUser->email }}</span>
            </div>
        </div>
    @endif
</div>
