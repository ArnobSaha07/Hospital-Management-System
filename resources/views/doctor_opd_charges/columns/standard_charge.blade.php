<div class="d-flex align-items-center justify-content-end mt-2">
    @if(!empty($row->standard_charge))
        <p class="cur-margin">
              {{ getCurrencyFormat($row->standard_charge) }}
        </p>
    @else
        {{ __('messages.common.n/a')}}
    @endif
</div>

