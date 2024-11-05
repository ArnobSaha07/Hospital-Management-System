{{ (!empty($row->patient->user->phone)) ? $row->patient->user->region_code.$row->patient->user->phone : __('messages.common.n/a') }}
