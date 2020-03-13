@if(Session::has('dcat-admin-toastr'))
    @php
        $toastr  = Session::get('dcat-admin-toastr');
        $type    = \Illuminate\Support\Arr::get($toastr->get('type'), 0, 'success');
        $message = \Illuminate\Support\Arr::get($toastr->get('message'), 0, '');
        $options = json_encode($toastr->get('options', []));
    @endphp
    <script>$(function () { toastr.{{$type}}('{!!  $message  !!}', null, {!! $options !!}); })</script>
@endif