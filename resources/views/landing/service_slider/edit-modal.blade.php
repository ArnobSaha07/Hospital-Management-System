<div class="modal fade" tabindex="-1" id="editServiceSliderModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{__('messages.service_slider.edit_service_slider')}}</h3>
            </div>

            <div class="modal-body">
                {{ Form::open(['id' => 'serviceSliderEditForm', 'files' => true]) }}
                <div class="row">
                    <div class="alert alert-danger d-none hide" id="editServiceSliderValidationErrorsBox"></div>
                    <input type="hidden" id="serviceId" name="serviceId">
                    <div class="form-group col-sm-8">
                        <?php
                        $style = 'style=';
                        $background = 'background-image:';
                        ?>
                        <div class="row2">
                            <label class="form-label"
                                   for="about_us_image">
                                <span class="w-100">{{__('messages.service_slider.service_slider_image')}}: </span>
                                <span class="required"></span>
                            </label>
                            <span data-bs-toggle="tooltip"
                                  id="planTooltip"
                                  data-placement="top"
                                  data-bs-original-title="{{__('messages.service_slider.img_tooltip_text')}}">
                        <i class="fas fa-question-circle ml-1 mt-1 general-question-mark"></i>
                    </span>
                            <div class="d-block">
                                <div class="image-picker">
                                    <div class="image previewImage" id="previewEditImage"
                                         style="background-image: url({{ asset('web_front/images/doctors/doctor.png') }})"></div>
                                    <span class="picker-edit rounded-circle text-gray-500 fs-small"
                                          data-bs-toggle="tooltip"
                                          data-placement="top"
                                          data-bs-original-title={{ __('messages.new_change.change_image') }}>
                                    <label>
                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                        {{ Form::file('img_url',['id'=>'editServiceImages','class' => 'image-upload d-none','accept' => 'image/*']) }}
                                        <input type="hidden" name="avatar_remove">
                                    </label>
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-text">{{__('messages.common.allow_img_text')}}</div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                <button type="button" class="btn btn-primary m-0"
                        id="serviceSliderEditBtnSave">{{__('messages.common.save')}}</button>
                <button type="button" class="btn btn-secondary my-0 ms-5 me-0"
                        data-bs-dismiss="modal">{{__('messages.common.cancel')}}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
