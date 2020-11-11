<div class="{{$viewClass['form-group']}}">

    <label class="{{$viewClass['label']}} control-label">{!! $label !!}</label>

    <div class="{{$viewClass['field']}}">

        @include('admin::form.error')

        <textarea class="form-control {{$class}} form-field-editor" name="{{$name}}" placeholder="{{ $placeholder }}" {!! $attributes !!} >{{ $value }}</textarea>

        @include('admin::form.help-block')

    </div>
</div>

<script require="@tinymce" init=".form-field-editor" id>
    var opts = {!! admin_javascript_json($options) !!};

    opts.selector = '#'+id;

    if (! opts.init_instance_callback) {
        opts.init_instance_callback = function (editor) {
            editor.on('Change', function(e) {
                var content = e.target.getContent();
                if (! content) {
                    content = e.level.fragments;
                    content = content.length && content.join('');
                }

                $this.val(String(content).replace('<p><br data-mce-bogus="1"></p>', '').replace('<p><br></p>', ''));
            });
        }
    }

    tinymce.init(opts)
</script>
