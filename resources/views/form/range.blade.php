<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="row" style="max-width: 603px">
            <div class="col-lg-6">
                <div class="input-group">
                     <span class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="feather icon-edit-2"></i></span>
                    </span>
                    <input autocomplete="off" type="text" name="{{$name['start']}}" value="{{ $value['start'] ?? null }}" class="form-control {{$class['start']}}" style="width: 150px" {!! $attributes !!} />
                </div>
            </div>

            <div class="col-lg-6">
                <div class="input-group">
                     <span class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="feather icon-edit-2"></i></span>
                    </span>
                    <input autocomplete="off" type="text" name="{{$name['end']}}" value="{{ $value['end'] ?? null }}" class="form-control {{$class['end']}}" style="width: 150px" {!! $attributes !!} />
                </div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>
