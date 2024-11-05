<div class="row">
    <div class="form-group col-sm-6 mb-5">
        <div class="row2">
            <label class="form-label"
                   for="about_us_image"> <span>{{__('messages.landing_cms.image')}}: </span>
                <span class="required"></span>
                <span data-bs-toggle="tooltip"
                      id="planTooltip"
                      data-placement="top"
                      data-bs-original-title="{{ __('messages.new_change.best_resolution_image') }} 1200x800">
                        <i class="fas fa-question-circle ml-1 mt-1 general-question-mark"></i>
                    </span>
            </label>
            <div class="d-block">
                <?php
                $style = 'style=';
                $background = 'background-image:';
                ?>
                <div class="image-picker">
                    <div class="image previewImage" id="exampleInputImage"
                         style="background-image: url({{ isset($sectionOne['img_url']) ? asset($sectionOne['img_url']) : asset('web_front/images/doctors/doctor.png') }})"></div>
                    <span class="picker-edit rounded-circle text-gray-500 fs-small" data-bs-toggle="tooltip"
                          data-placement="top"
                          data-bs-original-title={{ __('messages.new_change.change_image') }}>
                                    <label>
                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                        {{ Form::file('img_url',['class' => 'image-upload d-none','accept' => 'image/*']) }}
                    <input type="hidden" name="avatar_remove">
                                    </label>
                                </span>
                </div>
            </div>


            <div class="form-text">{{ __('messages.common.allow_img_text') }}</div>
        </div>

        <!-- Text Main Field -->
        <div class="form-group col-sm-12 mb-5 mt-3">
            {{ Form::label('text_main', __('messages.landing_cms.text_main').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('text_main', $sectionOne['text_main'], ['class' => 'form-control', 'id' => 'textMain','maxLength' => '45','placeholder'=>__('messages.landing_cms.text_main')]) }}
        </div>

        <!-- Text Secondary Field -->
        <div class="form-group col-sm-12 mb-5">
            {{ Form::label('text_secondary', __('messages.landing_cms.text_secondary').':', ['class' => 'form-label']) }}
            <span class="required"></span>
            {{ Form::text('text_secondary', $sectionOne['text_secondary'], ['class' => 'form-control', 'id' => 'textSecondary', 'required','maxLength' => '135','placeholder'=> __('messages.landing_cms.text_secondary')]) }}
        </div>

    </div>
</div>
<div class="d-flex float-end">
    {{ Form::submit(__('messages.common.save'), ['class' => 'btn btn-primary me-2']) }}
    {{ Form::reset(__('messages.common.cancel'), ['class' => 'btn btn-secondary']) }}
</div>
