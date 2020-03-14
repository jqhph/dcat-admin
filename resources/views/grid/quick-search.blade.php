<style>::-ms-clear,::-ms-reveal{display: none;}</style>

<form action="{!! $action !!}" class="input-no-border" pjax-container style="display:inline-block;">
    <div class="dataTables_filter">
        <label style="width: {{ $width }}rem">
            <input
                    type="search"
                    class="form-control form-control-sm quick-search-input"
                    placeholder="{{ $placeholder }}"
                    name="{{ $key }}"
                    value="{{ $value }}"
                    aria-controls="DataTables_Table_0"
            >

        </label>
        <span class="quick-search-clear" style="{{$value ? 'color:#333;cursor:pointer;' : ''}}">×</span>
    </div>
</form>
