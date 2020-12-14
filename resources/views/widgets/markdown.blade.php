<div {!! $attributes !!}><textarea style="display:none;">{!! $content !!}</textarea></div>

<script require="@editor-md">
    editormd.markdownToHTML('{{ $id }}', {!! admin_javascript_json($options) !!});
</script>