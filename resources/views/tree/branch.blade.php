<li class="dd-item" data-id="{{ $branch[$keyName] }}">
    <div class="dd-handle">
        {!! $branchCallback($branch) !!}
        <span class="pull-right dd-nodrag">
            @if($useEdit)
            <a href="{{ $path }}/{{ $branch[$keyName] }}/edit"><i class="ti-pencil-alt "></i>&nbsp;</a>
            @endif

            @if($useQuickEdit)
                <a href="javascript:void(0);" data-url="{{ $path }}/{{ $branch[$keyName] }}/edit" class=" tree-quick-edit"><i class=" fa fa-clone"></i></a>
            @endif

            @if($useDelete)
            <a href="javascript:void(0);" data-id="{{ $branch[$keyName] }}" class="tree_branch_delete"><i class="ti-trash "></i></a>
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