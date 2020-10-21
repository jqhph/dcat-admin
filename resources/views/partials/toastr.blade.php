@if(Session::has('dcat-admin-toastr'))
    @php
        $toastr  = Session::get('dcat-admin-toastr');
        $type    = $toastr->get('type')[0] ?? 'success';
        $message = $toastr->get('message')[0] ?? '';
        $options = admin_javascript_json($toastr->get('options', []));
    @endphp
    <script>$(function () { toastr.{{$type}}('{!!  $message  !!}', null, {!! $options !!}); })</script>
@endif