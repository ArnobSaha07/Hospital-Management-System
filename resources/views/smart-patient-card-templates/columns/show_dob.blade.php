<div class="d-flex align-items-center">
    <label class="form-check form-switch form-switch-sm">
        <input name="show_dob" id="show_dob" class="form-check-input" type="checkbox"
               value="1" {{$row->show_dob == 0 ? '' : 'checked'}} data-id="{{ $row->id }}">
        <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
    </label>
</div>
