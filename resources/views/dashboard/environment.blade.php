<div class="table-responsive">
    <table class="table">

        @foreach($envs as $env)
            <tr>
                <td width="120px" class="bold text-80">{{ $env['name'] }}</td>
                <td>{{ $env['value'] }}</td>
            </tr>
        @endforeach
    </table>
</div>