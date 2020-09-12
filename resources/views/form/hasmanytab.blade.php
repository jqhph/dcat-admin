<style>
    .nav-tabs > li:hover > i{
        display: inline;
    }
    .close-tab {
        position: absolute;
        font-size: 10px;
        top: 20px;
        right: 0;
        cursor: pointer;
        display: none;
    }
</style>
<div class="nav-tabs-custom has-many-{{$column}}">
    <div class="row header">
        <div class="{{$viewClass['label']}}"><h4 class="pull-right">{!! $label !!}</h4></div>
        <div class="{{$viewClass['field']}}">
            <div class="add btn btn-white btn-sm"><i class="feather icon-plus"></i>&nbsp;{{ trans('admin.new') }}</div>
        </div>
    </div>

    <hr class="mb-0 mt-0">

    <ul class="nav nav-tabs">
        @foreach($forms as $pk => $form)
            <li class="nav-item ">
                <a href="#{{ $relationName . '_' . $pk }}" class="nav-link @if ($form == reset($forms)) active @endif " data-toggle="tab">
                    {{ $pk }} <i class="feather icon-alert-circle text-red d-none"></i>
                </a>
                <i class="close-tab feather icon-trash text-red"></i>
            </li>
        @endforeach

    </ul>
    
    <div class="tab-content has-many-{{$column}}-forms">

        @foreach($forms as $pk => $form)
            <div class="tab-pane fields-group has-many-{{$column}}-form @if ($form == reset($forms)) active @endif" id="{{ $relationName . '_' . $pk }}">
                @foreach($form->fields() as $field)
                    {!! $field->render() !!}
                @endforeach
            </div>
        @endforeach
    </div>

    <template class="nav-tab-tpl">
        <li class="new nav-item">
            <a href="#{{ $relationName . '_new_' . \Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}" class="nav-link" data-toggle="tab">
                &nbsp;New {{ \Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }} <i class="feather icon-alert-circle text-red d-none"></i>
            </a>
            <i class="close-tab feather icon-trash text-red" ></i>
        </li>
    </template>
    <template class="pane-tpl">
        <div class="tab-pane fields-group new" id="{{ $relationName . '_new_' . \Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}">
            {!! $template !!}
        </div>
    </template>

</div>