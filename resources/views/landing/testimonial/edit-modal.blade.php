<div id="editTestimonialModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">{{ __('messages.testimonial.edit_testimonial') }}</h3>
            </div>
            {{ Form::open(['id'=>'editAdminTestimonialForm','files' => true]) }}
            <div class="modal-body">
                <div class="alert alert-danger d-none hide" id="editValidationErrorsBox"></div>
                <div class="row">
                    {{ Form::hidden('id',null,['id'=>'testimonialId']) }}
                    <div class="form-group col-sm-12 mb-5">
                        {{ Form::label('name', __('messages.testimonial.name').(':'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('name', null, ['class' => 'form-control','id' => 'editName','required','placeholder'=>__('messages.testimonial.name')]) }}
                    </div>
                    <div class="form-group col-sm-12 mb-5">
                        {{ Form::label('position', __('messages.testimonial.position').(':'), ['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::text('position', null, ['class' => 'form-control','id' => 'editPosition','required','placeholder'=>__('messages.testimonial.position')]) }}
                    </div>
                    <div class="form-group col-sm-12 mb-5">
                        {{ Form::label('description', __('messages.testimonial.description').(':'),['class' => 'form-label']) }}
                        <span class="required"></span>
                        {{ Form::textarea('description', null, ['class' => 'form-control description','id' => 'editDescription','rows' => 6,'placeholder'=> __('messages.testimonial.description')]) }}
                    </div>
                    <div class="form-group col-sm-12">
                        <?php
                        $style = 'style=';
                        $background = 'background-image:';
                        ?>
                        <label class="form-label mb-3" for="file">
                            <span>{{__('messages.common.profile')}}: </span>
                            <span class="required"></span>
                            <span data-bs-toggle="tooltip"
                                  id="planTooltip"
                                  data-placement="top"
                                  data-bs-original-title="{{ __('messages.new_change.best_resolution_profile') }} 800x800">
                                    <i class="fas fa-question-circle ml-1 mt-1 general-question-mark"></i>
                                </span>
                        </label>
                            <br>
                            <div class="d-block">
                                <?php
                            $style = 'style=';
                            $background = 'background-image:';
                            ?>
                            <div class="image-picker">
                                <div class="image previewImage" id="editPreviewImage"
                                     style="background-image: url({{ asset('assets/img/default_image.jpg') }})"></div>
                                <span class="picker-edit rounded-circle" data-bs-toggle="tooltip"
                                      data-placement="top"
                                      data-bs-original-title={{ __('messages.profile.change_Profile') }}>
                                    <label>
                                    <i class="fa-solid fa-pen" id="profileImageIcon"></i>
                                        {{ Form::file('profile',['class' => 'image-upload d-none','accept' => 'image/*' , 'id'=>'editProfiles']) }}
                                        <input type="hidden" name="avatar_remove">
                                    </label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-text">{{ __('messages.common.allow_img_text') }}</div>
                </div>
            </div>
            <div class="modal-footer pt-0">
                {{ Form::button(__('messages.common.save'), ['type' => 'submit','class' => 'btn btn-primary me-2','id' => 'editTestimonialBtnSave','data-loading-text' => "<span class='spinner-border spinner-border-sm'></span> Processing..."]) }}
                <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">{{ __('messages.common.cancel') }}</button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
{{--</div>--}}
