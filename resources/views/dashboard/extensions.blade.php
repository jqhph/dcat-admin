<style>
    .ext-icon {
        color: rgba(0,0,0,0.5);
        margin-left: 10px;
    }
    .installed {
        color: #00a65a;
        margin-right: 15px;
        font-size:20px;
    }
</style>
<ul class="products-list product-list-in-box" id="extension-box" style="margin-top:10px">

    @foreach($extensions as $extension)
        <li class="item">
            <div class="product-img">
                <i class="fa {{$extension['icon']}} fa-2x ext-icon"></i>
            </div>
            <div class="product-info">
                <a href="{{ $extension['link'] }}" target="_blank" class="">
                    {{ $extension['name'] }}
                </a>
                @if($extension['installed'])
                    <span class="pull-right installed"><i class="ti-check"></i></span>
                @endif
            </div>
        </li>
@endforeach

<!-- /.item -->
</ul>
<!-- /.box-body -->
<div class="box-footer text-center">
    <a href="https://github.com/dcat-admin-extensions" target="_blank" class="uppercase">View All Extensions</a>
</div>

<script>LA.ready(function () {
//    $('#extension-box').loading({style: 'left:0;right:0'});


})</script>
