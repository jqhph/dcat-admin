<div {!! $attributes !!}><textarea style="display:none;">{!! $content !!}</textarea></div>

<script first>
    var ele = window.Element;
    Dcat.eMatches = ele.prototype.matches ||
        ele.prototype.msMatchesSelector ||
        ele.prototype.webkitMatchesSelector;
</script>

<script require="@editor-md">
    editormd.markdownToHTML('{{ $id }}', {!! admin_javascript_json($options) !!});

    Element.prototype.matches = Dcat.eMatches;
</script>