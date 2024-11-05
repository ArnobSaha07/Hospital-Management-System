<div class="d-flex justify-content-end">
@if(!empty($row->standard_charge))
        <p class="cur-margin">
            {{ getCurrencyFormat($row->standard_charge) }}
        </p>
@else
        {{ __('messages.common.n/a')}}
@endif
</div>
