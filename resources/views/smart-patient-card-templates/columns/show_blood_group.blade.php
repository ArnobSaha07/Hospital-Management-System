<div class="d-flex align-items-center">
    <label class="form-check form-switch form-switch-sm">
        <input name="show_blood_group" id="show_blood_group" class="form-check-input" type="checkbox"
               value="1" {{$row->show_blood_group == 0 ? '' : 'checked'}} data-id="{{ $row->id }}">
        <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
    </label>
</div>
