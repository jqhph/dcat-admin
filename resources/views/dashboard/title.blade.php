<style>
    .links {
        text-align: center;
    }

    .links > a {
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
    }

    .dashborard h1 {
        font-weight: 200;
        font-size: 2.5rem;
    }
</style>

<div class="card" style="background: {{ Admin::color()->alpha('primary', 0.95) }}">
    <div class="card-body">
        <div class="text-center dashborard">

            <div class="avatar avatar-xl bg-white shadow mt-1">
                <div class="avatar-content">
                    <i class="feather icon-award text-primary font-large-1"></i>
                </div>
            </div>

            <div class="text-center mb-1">
                <h1 class="mb-3 mt-2 white">Dcat Admin</h1>
                <div class="links mb-2">
                    <a href="https://github.com/jqhph/dcat-admin" target="_blank">Github</a>
                    <a href="https://jqhph.github.io/dcat-admin/docs.html" id="doc-link" target="_blank">{{ __('admin.documentation') }}</a>
                    <a href="https://jqhph.github.io/dcat-admin/docs/master/extensions.html" id="demo-link" target="_blank">{{ __('admin.extensions') }}</a>
                    <a href="https://jqhph.github.io/dcat-admin/demo.html" id="demo-link" target="_blank">{{ __('admin.demo') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>