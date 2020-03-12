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
<ul class="products-list product-list-in-box" id="extension-box" style="margin-top:10px;min-height: 100px">
    @foreach($extensions as $extension)
        <li class="item hidden">
            <div class="product-img">
                <i class="{{$extension['icon']}} fa-2x ext-icon"></i>
            </div>
            <div class="product-info" data-key="{{$extension['key']}}">
                <a href="{{ $extension['link'] }}" target="_blank" class="">
                    {{ $extension['name'] }}
                </a>
                @if($extension['installed'])
                    <span class="pull-right installed"><i class="ti-check"></i></span>
                @endif
            </div>
        </li>
@endforeach

</ul>

<div class="box-footer text-center">
    <a href="https://github.com/jqhph/dcat-admin#%E6%89%A9%E5%B1%95" target="_blank" class="uppercase">View All Extensions</a>
</div>

<script>Dcat.ready(function () {
    // var $box = $('#extension-box');
    // $box.loading();
    //
    // $.ajax({
    //     url: 'https://jqhph.github.io/dcat-admin/extra/extensions.html',
    //     success: function (response) {
    //         $box.loading(false);
    //
    //         $box.html(response);
    //     },
    //     error: function () {
    //         $box.loading(false);
    //
    //         $box.find('.item').removeClass('hidden');
    //     }
    // });
})</script>