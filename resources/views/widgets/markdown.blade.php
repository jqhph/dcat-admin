<div {!! $attributes !!}><textarea style="display:none;">{!! $content !!}</textarea></div>

<script require="@editor-md">
    editormd.markdownToHTML('{{ $id }}', {!! json_encode($options) !!});
</script>