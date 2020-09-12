<div class="{{$viewClass['form-group']}} {!! !$errors->has($errorKey) ? '' : 'has-error' !!}">

    <label for="{{$id['lat']}}" class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        @if(config('admin.map.provider') == 'baidu')
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="search-{{$id['lat'].$id['lng']}}">
                        <span class="input-group-btn">
                        <button type="button" class="btn btn-info btn-flat"><i class="fa fa-search"></i></button>
                    </span>
                    </div>
                </div>
            </div>
        @endif

        <div id="map_{{$id['lat'].$id['lng']}}" style="width: 100%;height: 300px"></div>
        <input type="hidden" id="{{$id['lat']}}" name="{{$name['lat']}}" value="{{ old($column['lat'], $value['lat'] ?? null) }}" {!! $attributes !!} />
        <input type="hidden" id="{{$id['lng']}}" name="{{$name['lng']}}" value="{{ old($column['lng'], $value['lng'] ?? null) }}" {!! $attributes !!} />

        @include('admin::form.help-block')

    </div>
</div>
