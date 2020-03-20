<style>
    .table tr:first-child td {
        border-top: 0;
    }
</style>
<div class="table-responsive">
    <table class="table">
        @foreach($dependencies as $dependency => $version)
            <tr>
                <td width="240px" class="bold text-80">{{ $dependency }}</td>
                <td><span class="label bg-primary">{{ $version }}</span></td>
            </tr>
        @endforeach
    </table>
</div>