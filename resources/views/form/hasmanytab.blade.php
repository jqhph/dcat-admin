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
<div class="nav-tabs-custom has-many-{{$columnClass}}">
    <div class="row header">
        <div class="{{$viewClass['label']}}"><h4 class="pull-right">{!! $label !!}</h4></div>
        <div class="{{$viewClass['field']}}" style="margin-bottom: 5px">
            <div class="add btn btn-outline-primary btn-sm"><i class="feather icon-plus"></i>&nbsp;{{ trans('admin.new') }}</div>
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
    
    <div class="tab-content has-many-{{$columnClass}}-forms">

        @foreach($forms as $pk => $form)
            <div class="tab-pane fields-group has-many-{{$columnClass}}-form @if ($form == reset($forms)) active @endif" id="{{ $relationName . '_' . $pk }}">
                {!! $form->render() !!}
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
        <div class="tab-pane fields-group new" id="{{ $relationName . '_new_' . Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}">
            {!! $template !!}
        </div>
    </template>

</div>

<script>
    var container = '.has-many-{{ $columnClass }}';
    
    $(container+' > .nav').off('click', 'i.close-tab').on('click', 'i.close-tab', function(){
        var $navTab = $(this).siblings('a');
        var $pane = $($navTab.attr('href'));
        if( $pane.hasClass('new') ){
            $pane.remove();
        }else{
            $pane.removeClass('active').find('.{{ Dcat\Admin\Form\NestedForm::REMOVE_FLAG_CLASS }}').val(1);
        }
        if($navTab.closest('li').hasClass('active')){
            $navTab.closest('li').remove();
            $(container+' > .nav > li:nth-child(1) > a').click();
        }else{
            $navTab.closest('li').remove();
        }
    });

    var nestedIndex = {!! $count !!};

    function replaceNestedFormIndex(value) {
        return String(value).replace(/{{ Dcat\Admin\Form\NestedForm::DEFAULT_KEY_NAME }}/g, nestedIndex);
    }

    $(container+' > .header').off('click', '.add').on('click', '.add', function(){
        nestedIndex++;
        var navTabHtml = replaceNestedFormIndex($(container+' > template.nav-tab-tpl').html());
        var paneHtml = replaceNestedFormIndex($(container+' > template.pane-tpl').html());
        $(container+' > .nav').append(navTabHtml);
        $(container+' > .tab-content').append(paneHtml);
        $(container+' > .nav > li:last-child a').click();
    });

    if ($('.has-error').length) {
        $('.has-error').parent('.tab-pane').each(function () {
            var tabId = '#'+$(this).attr('id');
            $('li a[href="'+tabId+'"] i').removeClass('d-none');
        });

        var first = $('.has-error:first').parent().attr('id');
        $('li a[href="#'+first+'"]').tab('show');
    }
</script>