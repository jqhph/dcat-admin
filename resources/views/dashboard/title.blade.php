<style>
    .links {
        text-align: center;
        /*margin-bottom: 20px;*/
    }

    .links > a {
        color: {{ \Dcat\Admin\Admin::color()->white50()  }};
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
    }
</style>

<div class="text-center">
    <div class="avatar avatar-xl bg-primary shadow mt-0 mb-2">
        <div class="avatar-content">
            <i class="feather icon-award white font-large-1"></i>
        </div>
    </div>
    <div class="text-center mb-1">
        <h1 class="mb-2 text-white">Dcat Admin</h1>
        <div class="links">
            <a href="https://github.com/jqhph/dcat-admin" target="_blank">Github</a>
            <a href="https://jqhph.github.io/dcat-admin/docs.html" id="doc-link" target="_blank">Documentation</a>
            <a href="https://jqhph.github.io/dcat-admin/demo.html" id="demo-link" target="_blank">Demo</a>
        </div>
    </div>
</div>