<div class="d-flex align-items-center">
    <label class="form-check form-switch form-switch-sm">
        <input name="show_email" id="show_email" class="form-check-input" type="checkbox"
               value="1" {{$row->show_email == 0 ? '' : 'checked'}} data-id="{{ $row->id }}">
        <span class="switch-slider" data-checked="&#x2713;" data-unchecked="&#x2715;"></span>
    </label>
</div>
