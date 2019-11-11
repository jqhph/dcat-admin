@if($showHeader)
    <div class="box-header with-border">
        <h3 class="box-title" style="line-height:30px">{!! $form->title() !!}</h3>
        <div class="pull-right">{!! $form->renderTools() !!}</div>
    </div>
@endif

<div class="box-body" style="padding:18px 18px 30px">
    @if($steps->count())
        <div class="fields-group la-step-box" style="padding:18px;max-width: {{ $steps->getOption('width') }}">

            <ul class="la-step-horizontal la-step-label-horizontal la-step ">
                @foreach($steps->all() as $step)
                <li class="la-step-item">
                    <a href="#{{ $step->getFormId() }}" class="la-step-item-container">
                        <div class="la-step-line"></div>
                        <div class="la-step-icons">
                            <span class="la-step-icon" data-index="{{ $step->getIndex() }}">{{ $step->getIndex() + 1 }}</span>
                        </div>
                        <div class="la-step-content">
                            <div class="la-step-title">{!! $step->getTitle() !!}</div>
                            <div class="la-step-desc"> {{ $step->getDescription() }} </div>
                        </div>
                    </a>
                </li>
                @endforeach

                <li class="la-step-item">
                    <a href="#{{ $steps->getDoneStep()->getElementId() }}" class="la-step-item-container">
                        <div class="la-step-line"></div>
                        <div class="la-step-icons">
                            <span class="la-step-icon" data-index="{{ $steps->count() }}"> {{ $steps->count() + 1 }} </span>
                        </div>
                        <div class="la-step-content">
                            <div class="la-step-title">{{ $steps->getDoneStep()->title() }}</div>
                            <div class="la-step-desc"></div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="la-step-form">
                @foreach($steps->all() as $step)
                    {!! $step->render() !!}
                @endforeach

                <div id="{{ $steps->getDoneStep()->getElementId() }}" class="la-done-step" style="display: none;">
                </div>
            </div>
        </div>
    @endif
</div>

@foreach($form->getHiddenFields() as $field)
    {!! $field->render() !!}
@endforeach

<input type="hidden" class="current-step-input" name="{{ Dcat\Admin\Form\StepBuilder::CURRENT_VALIDATION_STEP }}" />
<input type="hidden" class="all-steps-input" name="{{ Dcat\Admin\Form\StepBuilder::ALL_STEPS }}" />
<input type="hidden" name="_token" value="{{ csrf_token() }}">

@php
$lastStep = $step;
@endphp

<script>
LA.ready(function () {
    var form = $('#{{ $form->getFormId() }}'),
        box = form.find('.la-step-box'),
        stepInput = form.find('.current-step-input'),
        allStepInput = form.find('.all-steps-input'),
        smartWizard,
        isSubmitting;

    var submitBtn = $('<button style="margin-left: 10px"></button>')
        .text('{{ trans('admin.submit') }}')
        .addClass('btn btn-primary step-submit-btn disabled hide')
        .on('click', function(){
            var $t = $(this);

            if ($t.hasClass('disabled') || isSubmitting) {
               return false;
            }

            form.validator('validate');
            if (form.find('.has-error').length > 0) {
                return false;
            }

            allStepInput.val("1");
            stepInput.val("");
            $t.button('loading').removeClass('waves-effect');
            isSubmitting = 1;

            // 提交完整表单
            submit(function (state, data) {
                $t.button('reset');
                isSubmitting = 0;

                if (state) {
                    if (data) {
                        form.find('.la-done-step').html(data);
                    }

                    smartWizard.next();

                    toggle_btn();
                }
            });

            return false;

        });

    smartWizard = box.smartWizard({
        transitionEffect: 'fade',
        useURLhash: false,
        lang: {
            next: '{{ trans('admin.next_step') }}',
            previous: '{{ trans('admin.prev_step') }}'
        },
        toolbarSettings: {
            toolbarPosition: 'bottom',
            toolbarExtraButtons: [submitBtn,],
            toolbarButtonPosition: 'left'
        },
        anchorSettings: {
            enableAnchorOnDoneStep: false,
        },
    });

    smartWizard = smartWizard.data('smartWizard');

    var prev = box.find('.sw-btn-prev').click(function (e) {
        e.preventDefault();
        if (smartWizard.steps.index(this) !== smartWizard.current_index) {
            smartWizard.prev();
        }

        toggle_btn();
    });

    var next = box.find('.sw-btn-next');
    next.click(function (e) {
        e.preventDefault();

        if ($(this).hasClass('disabled') || isSubmitting) {
            return false;
        }

        var stepForm = form.find('.sw-container [data-toggle="validator"]').eq(smartWizard.current_index);

        stepForm.validator('validate');
        if (stepForm.find('.has-error').length > 0) {
            return false;
        }

        var self = this;
        $(self).button('loading').removeClass('waves-effect');
        isSubmitting = 1;

        // 发送表单到服务器进行验证
        stepInput.val(smartWizard.current_index);
        submit(function (state) {
            $(self).button('reset');
            isSubmitting = 0;

            if (state) {
                // 表单验证成功
                if (smartWizard.steps.index(self) !== smartWizard.current_index) {
                    smartWizard.next();
                }

                toggle_btn();
            }

        });
    });

    function submit(after) {
        LA.Form({
            $form: form,
            after: function (state, b, c, d) {
                after(state, b, c, d);

                if (state) {
                    return false;
                }

            }
        });
    }

    function toggle_btn() {
        var last = {{ $lastStep->getIndex() }},
            sbm = box.find('.step-submit-btn');

        if (smartWizard.current_index == last) {
            sbm.removeClass('disabled hide');
            next.hide();
            prev.show();
        } else {
            sbm.addClass('disabled hide');
            if (smartWizard.current_index !== 0) {
                prev.show();
            } else {
                prev.hide();
            }

            if (smartWizard.current_index != (last + 1)) {
                next.show()
            }
        }

        if (smartWizard.current_index == (last + 1)) {
            box.find('.sw-btn-group').remove()
        }

    }

    toggle_btn();
});
</script>

