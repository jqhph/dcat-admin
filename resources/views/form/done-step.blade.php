<style>
    .la-done-step {
        max-width: 560px;
        margin: 0 auto;
        padding: 24px 0 8px;
    }
    .la-done-step .st-icon {
        color: {{ \Dcat\Admin\Widgets\Color::success() }};
        font-size: 72px;
        text-align:center;
    }
    .la-done-step .st-content {
        text-align:center;
    }
    .la-done-step .st-title {
        font-size: 24px;
    }
    .la-done-step .st-desc {
        color: rgba(0,0,0,.5);
        font-size: 14px;
        line-height: 1.6;
    }
    .la-done-step .st-btn {
        margin: 30px 0 10px;
    }
</style>
<div style="margin: 0 auto">
    <div class="st-icon">
        <svg viewBox="64 64 896 896" focusable="false" class="" data-icon="check-circle" width="1em" height="1em" fill="currentColor" aria-hidden="true"><path d="M512 64C264.6 64 64 264.6 64 512s200.6 448 448 448 448-200.6 448-448S759.4 64 512 64zm193.5 301.7l-210.6 292a31.8 31.8 0 0 1-51.7 0L318.5 484.9c-3.8-5.3 0-12.7 6.5-12.7h46.9c10.2 0 19.9 4.9 25.9 13.3l71.2 98.8 157.2-218c6-8.3 15.6-13.3 25.9-13.3H699c6.5 0 10.3 7.4 6.5 12.7z"></path></svg>
    </div>

    <div class="st-content">
        <div class="st-title">
            {{ $title }}
        </div>
        <div class="st-desc">
            {{ $description }}
        </div>

        <div class="st-btn">
            <a class="btn btn-success" href="{{ $createUrl }}" >{{ trans('admin.continue_creating') }}</a>
            &nbsp;
            <a class="btn btn-default" href="{{ $backUrl }}"><i class="fa fa-long-arrow-left"></i> {{ trans('admin.back') }}</a>
        </div>
    </div>
</div>