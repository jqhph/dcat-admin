<style>::-ms-clear,::-ms-reveal{display: none;}</style>

<form pjax-container action="{!! $action !!}" class="input-no-border quick-search-form d-md-inline-block" style="display:none;margin-right: 16px">
    <div class="table-filter">
        <label style="width: {{ $width }}rem">
            <input
                    type="search"
                    class="form-control form-control-sm quick-search-input"
                    placeholder="{{ $placeholder }}"
                    name="{{ $key }}"
                    value="{{ $value }}"
                    auto="{{ $auto ? '1' : '0' }}"
            >
        </label>
    </div>
</form>
