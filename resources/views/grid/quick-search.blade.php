<style>::-ms-clear,::-ms-reveal{display: none;}</style>

<form action="{!! $action !!}" class="input-no-border" pjax-container style="display:inline-block;margin:0 5px 13px 0;">
    <div class="input-group quick-search" style="width:{{$width}}rem;">

        <input type="text"
               placeholder="{{ $placeholder }}"
               name="{{ $key }}"
               class="form-control quick-search-input"
               style="margin-left:-1px;padding:0 1.6rem 0 3.8rem;height:36px;line-height:36px;"
               value="{{ $value }}"
        >
        <a onclick="$(this).submit()" style="overflow:hidden;position:absolute;top:8px;margin-left:-{{$width - 0.9}}rem;cursor:pointer;z-index:100">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="20"
                 height="20"
                 viewBox="0 0 20 20"
                 aria-labelledby="search"
                 role="presentation"
                 class="text-70"
                 style="fill: currentColor;"
            >
                <path fill-rule="nonzero" d="M14.32 12.906l5.387 5.387a1 1 0 0 1-1.414 1.414l-5.387-5.387a8 8 0 1 1 1.414-1.414zM8 14A6 6 0 1 0 8 2a6 6 0 0 0 0 12z"></path>
            </svg>
        </a>
        <span class="quick-search-clear" style="margin-left:-1.45rem;{{$value ? 'color:#333' : ''}}">×</span>
    </div>
</form>
