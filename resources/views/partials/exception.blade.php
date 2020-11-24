@if(isset($errors) && $errors->hasBag('exception'))
    <?php $error = $errors->getBag('exception'); ?>
    <div class="alert alert-warning alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h4>
            <i class="icon fa fa-warning"></i>
            <i style="border-bottom: 1px dotted #fff;cursor: pointer;" title="{{ $error->get('type')[0] }}" ondblclick="var f=this.innerHTML;this.innerHTML=this.title;this.title=f;">{{ class_basename($error->get('type')[0]) }}</i>
            In <i title="{{ $error->get('file')[0] }} line {{ $error->get('line')[0] }}" style="border-bottom: 1px dotted #fff;cursor: pointer;" ondblclick="var f=this.innerHTML;this.innerHTML=this.title;this.title=f;">{{ basename($error->get('file')[0]) }} line {{ $error->get('line')[0] }}</i> :
        </h4>
        <p><a style="cursor: pointer;" onclick="$('#dcat-admin-exception-trace').toggleClass('hidden');$('i', this).toggleClass('fa-angle-double-down fa-angle-double-up');"><i class="fa fa-angle-double-down"></i>&nbsp;&nbsp;{!! $error->first('message') !!}</a></p>

        <p class="hidden" id="dcat-admin-exception-trace"><br>{!! nl2br($error->first('trace')) !!}</p>
    </div>
@endif
