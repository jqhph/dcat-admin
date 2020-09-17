<style>
    @php($primary = admin_color()->primary())

    .dcat-step .dcat-step-item:not(.active) > .dcat-step-item-container[role=button]:hover .dcat-step-icons,
    .done > .dcat-step-item-container > .dcat-step-line:after,
    .done .dcat-step-icons,
    .active .dcat-step-icons {
        border-color: {!! $primary !!}
    }

    .dcat-step .dcat-step-item:not(.active) > .dcat-step-item-container[role=button]:hover .dcat-step-icons .dcat-step-icon,
    .dcat-step .dcat-step-item:not(.active) > .dcat-step-item-container[role=button]:hover .dcat-step-desc .dcat-step .dcat-step-item:not(.active) > .dcat-step-item-container[role=button]:hover .dcat-step-title,
    .done .dcat-step-icons > .dcat-step-icon,
    .active .dcat-step-icons > .dcat-step-icon {
        color: {!! $primary !!}
    }

    .done > .dcat-step-item-container > .dcat-step-content > .dcat-step-title:after,
    .done .dcat-step-icons > .dcat-step-icon .dcat-step-icon-dot,
    .active .dcat-step-icons,
    .active .dcat-step-icons > .dcat-step-icon .dcat-step-icon-dot {
        background-color: {!! $primary !!}
    }
</style>

@if($showHeader)
    <div class="box-header with-border">
        <h3 class="box-title" style="line-height:30px">{!! $form->title() !!}</h3>
        <div class="pull-right">{!! $form->renderTools() !!}</div>
    </div>
@endif

<div class="box-body">
    @if($steps->count())
        <div class="fields-group dcat-step-box" style="padding: {{ $steps->getOption('padding') }};max-width: {{ $steps->getOption('width') }}">

            <ul class="dcat-step-horizontal dcat-step-label-horizontal dcat-step ">
                @foreach($steps->all() as $step)
                <li class="dcat-step-item">
                    <a href="#{{ $step->getElementId() }}" class="dcat-step-item-container">
                        <div class="dcat-step-line"></div>
                        <div class="dcat-step-icons">
                            <span class="dcat-step-icon" data-index="{{ $step->index() }}">{{ $step->index() + 1 }}</span>
                        </div>
                        <div class="dcat-step-content">
                            <div class="dcat-step-title">{!! $step->title() !!}</div>
                            <div class="dcat-step-desc"> {{ $step->description() }} </div>
                        </div>
                    </a>
                </li>
                @endforeach

                <li class="dcat-step-item">
                    <a href="#{{ $steps->done()->getElementId() }}" class="dcat-step-item-container">
                        <div class="dcat-step-line"></div>
                        <div class="dcat-step-icons">
                            <span class="dcat-step-icon" data-index="{{ $steps->count() }}"> {{ $steps->count() + 1 }} </span>
                        </div>
                        <div class="dcat-step-content">
                            <div class="dcat-step-title">{{ $steps->done()->title() }}</div>
                            <div class="dcat-step-desc"></div>
                        </div>
                    </a>
                </li>
            </ul>
            <div class="dcat-step-form">
                {!! $steps->build() !!}

                <div id="{{ $steps->done()->getElementId() }}" class="dcat-done-step" style="display: none;">
                </div>
            </div>
        </div>
    @endif
</div>

@foreach($form->hiddenFields() as $field)
    {!! $field->render() !!}
@endforeach

<input type="hidden" class="current-step-input" name="{{ Dcat\Admin\Form\Step\Builder::CURRENT_VALIDATION_STEP }}" />
<input type="hidden" class="all-steps-input" name="{{ Dcat\Admin\Form\Step\Builder::ALL_STEPS }}" />
<input type="hidden" name="_token" value="{{ csrf_token() }}">

@php
$lastStep = $step;
@endphp

<script>
Dcat.ready(function () {
    var form = $('#{{ $form->getElementId() }}'),
        box = form.find('.dcat-step-box'),
        stepInput = form.find('.current-step-input'),
        allStepInput = form.find('.all-steps-input'),
        smartWizard,
        isSubmitting;

    var submitBtn = $('<button style="margin-left: 10px"></button>')
        .text('{{ trans('admin.submit') }}')
        .addClass('btn btn-primary step-submit-btn disabled d-none')
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
            $t.buttonLoading().removeClass('waves-effect');
            isSubmitting = 1;

            // 提交完整表单
            submit(function (state, data) {
                $t.buttonLoading(false);
                isSubmitting = 0;

                if (state) {
                    if (data) {
                        form.find('.dcat-done-step').html(data);
                    }

                    smartWizard.next();

                    toggleBtn();
                }
            });

            return false;

        });

    smartWizard = box.smartWizard({
        selected: {{ $steps->getOption('selected') }},
        transitionEffect: 'fade',
        useURLhash: false,
        keyNavigation: false,
        showStepURLhash: false,
        autoAdjustHeight: false,
        lang: {
            next: '{!! trans('admin.next_step') !!}',
            previous: '{!! trans('admin.prev_step') !!}'
        },
        toolbarSettings: {
            toolbarPosition: 'bottom',
            toolbarExtraButtons: [submitBtn,],
            toolbarButtonPosition: 'left'
        },
        anchorSettings: {
            removeDoneStepOnNavigateBack: true,
            enableAnchorOnDoneStep: false,
        },
    }).on('leaveStep', function (e, tab, idx, direction) {
        @if ($leaving = $steps->getOption('leaving'))

        var callbacks = [];

        @foreach($leaving as $fun)
            callbacks.push({!! $fun !!});
        @endforeach

        return callListeners(callbacks, buildArgs(e, tab, idx, direction));
        @endif

    }).on('showStep', function (e, tab, idx, direction) {
        @if ($shown = $steps->getOption('shown'))

        var callbacks = [];

        @foreach($shown as $fun)
        callbacks.push({!! $fun !!});
        @endforeach

        return callListeners(callbacks, buildArgs(e, tab, idx, direction));
        @endif
    });

    @if ($steps->getOption('leaving') || $steps->getOption('shown'))

    // 执行回调函数
    function callListeners(func, args) {
        for (var i in func) {
            if (func[i](args) === false) {
                return false;
            }
        }
    }

    // 获取步骤表单
    function getForm(idx) {
        return box.find('.dcat-step-form [data-toggle="validator"]').eq(idx);
    }

    // 构建参数
    function buildArgs(e, tab, idx, direction) {
        return {
            event: e,
            tab: tab,
            index: idx,
            direction: direction,
            form: getForm(idx),
            getFrom: function (idx) {
                return getForm(idx)
            },
            formArray: getForm(idx).formToArray(),
            getFormArray: function (idx) {
                return getForm(idx).formToArray();
            }
        };
    }
    @endif

    smartWizard = smartWizard.data('smartWizard');

    // 上一步
    var prev = box.find('.sw-btn-prev').click(function (e) {
        e.preventDefault();
        if (smartWizard.steps.index(this) !== smartWizard.current_index) {
            smartWizard.prev();
        }

        toggleBtn();
    });

    // 下一步
    var next = box.find('.sw-btn-next').click(function (e) {
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
        $(self).buttonLoading().removeClass('waves-effect');
        isSubmitting = 1;

        // 发送表单到服务器进行验证
        stepInput.val(smartWizard.current_index);
        submit(function (state) {
            $(self).buttonLoading(false);
            isSubmitting = 0;

            if (state) {
                // 表单验证成功
                if (smartWizard.steps.index(self) !== smartWizard.current_index) {
                    smartWizard.next();
                }

                toggleBtn();
            }

        });
    });

    // 提交表单
    function submit(after) {
        Dcat.Form({
            form: form,
            after: function (state, b, c, d) {
                after(state, b, c, d);

                if (state) {
                    return false;
                }
            }
        });
    }

    // 按钮显示隐藏切换
    function toggleBtn() {
        var last = {{ $lastStep->index() }},
            sbm = box.find('.step-submit-btn');

        if (smartWizard.current_index == last) {
            sbm.removeClass('disabled d-none');
            next.hide();
            prev.show();
        } else {
            sbm.addClass('disabled d-none');
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

    toggleBtn();
});
</script>

