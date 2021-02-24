<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <div class="row" style="max-width: 603px">
            <div class="col-md-6" style="margin-right: 0">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="feather icon-calendar"></i></span>
                    </span>
                    <input autocomplete="off" type="text" name="{{$name['start']}}" value="{{ $value['start'] ?? null }}" class="form-control {{$class['start']}}" style="width:180px" {!! $attributes !!} />
                </div>
            </div>

            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-prepend">
                        <span class="input-group-text bg-white"><i class="feather icon-calendar"></i></span>
                    </span>
                    <input autocomplete="off" type="text" name="{{$name['end']}}" value="{{ $value['end'] ?? null }}" class="form-control {{$class['end']}}" style="width: 180px" {!! $attributes !!} />
                </div>
            </div>
        </div>

        @include('admin::form.help-block')

    </div>
</div>

<script require="@moment,@bootstrap-datetimepicker" init="{!! $selector['start'] !!}">
    var options = {!! admin_javascript_json($options) !!};
    var $end = $('{!! $selector['end'] !!}');

    $this.datetimepicker(options);
    $end.datetimepicker($.extend(options, {useCurrent: false}));
    $this.on("dp.change", function (e) {
        $end.data("DateTimePicker").minDate(e.date);
    });
    $end.on("dp.change", function (e) {
        $this.data("DateTimePicker").maxDate(e.date);
    });
</script>

