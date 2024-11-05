<div class="d-flex align-items-center mt-2">
    <?php $checked = $row->report_generated == 0 ? '' : 'checked'; ?>
        <label class="form-check form-switch d-flex justify-content-start cursor-pointer">
            <input data-id="{{$row->id}}" class="form-check-input report-generate cursor-pointer"
                   type="checkbox"
                   value="1" {{$checked}}>
            <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
        </label>
</div>
