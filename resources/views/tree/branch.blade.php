<li class="dd-item" data-id="{{ $branch[$keyName] }}">
    <div class="dd-handle">
        {!! $branchCallback($branch) !!}
        <span class="pull-right dd-nodrag">
            @if($useEdit)
            <a href="{{ $path }}/{{ $branch[$keyName] }}/edit"><i class="feather icon-edit-1"></i>&nbsp;</a>
            @endif

            @if($useQuickEdit)
                <a href="javascript:void(0);" data-url="{{ $path }}/{{ $branch[$keyName] }}/edit" class="tree-quick-edit"><i class="feather icon-edit"></i></a>
            @endif

            @if($useDelete)
            <a href="javascript:void(0);" data-message="ID - {{ $branch[$keyName] }}" data-url="{{ $path }}/{{ $branch[$keyName] }}" data-action="delete"><i class="feather icon-trash"></i></a>
            @endif
        </span>
    </div>
    @if(isset($branch['children']))
    <ol class="dd-list">
        @foreach($branch['children'] as $branch)
            @include($branchView, $branch)
        @endforeach
    </ol>
    @endif
</li>