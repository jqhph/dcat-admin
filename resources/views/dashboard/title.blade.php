<style>
    .dashboard-title .links {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .dashboard-title .links > a {
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.7);
    }
    .dashboard-title .links > a:hover {
        color: #fff;
    }
    .dashboard-title h1 {
        font-weight: 200;
        font-size: 2.5rem;
    }
    .dashboard-title.card {
        background: {{ Admin::color()->alpha('primary', 0.95) }}
    }
    .dashboard-title .avatar {
        background: transparent;
    }
</style>

<div class="dashboard-title card">
    <div class="card-body">
        <div class="text-center ">

            <div class="avatar avatar-xl shadow mt-1">
                <img class="avatar-content" src="{{ admin_asset('@admin/images/logo.png') }}">
            </div>

            <div class="text-center mb-1">
                <h1 class="mb-3 mt-2 white">Dcat Admin</h1>
                <div class="links">
                    <a href="https://github.com/jqhph/dcat-admin" target="_blank">Github</a>
                    <a href="https://jqhph.github.io/dcat-admin/docs.html" id="doc-link" target="_blank">{{ __('admin.documentation') }}</a>
                    <a href="https://jqhph.github.io/dcat-admin/docs/master/extensions.html" id="demo-link" target="_blank">{{ __('admin.extensions') }}</a>
                    <a href="https://jqhph.github.io/dcat-admin/demo.html" id="demo-link" target="_blank">{{ __('admin.demo') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>