<div class="modal fade" id="{{ $id }}" role="dialog">
    <div class="modal-dialog modal-{{ $size }}">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{!! $title !!}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">{!! $content !!}</div>
        </div>
    </div>
</div>