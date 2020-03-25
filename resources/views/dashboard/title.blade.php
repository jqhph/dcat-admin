<style>
    .links {
        text-align: center;
        /*margin-bottom: 20px;*/
    }

    .links > a {
        padding: 0 25px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: .1rem;
        text-decoration: none;
        text-transform: uppercase;
        color: {{ Admin::color()->white50() }};
    }

    .dashborard h1 {
        font-weight: 200;
        font-size: 2.5rem;
    }
</style>

<div class="card" style="height: 176px;background: {{ Admin::color()->alpha('primary', 0.9) }}">
    <div class="card-body">
        <div class="text-center dashborard">
            <div class="text-center mb-1">
                <h1 class="mb-3 mt-2 white">Dcat Admin</h1>
                <div class="links mb-2">
                    <a href="https://github.com/jqhph/dcat-admin" target="_blank">Github</a>
                    <a href="https://jqhph.github.io/dcat-admin/docs.html" id="doc-link" target="_blank">Documentation</a>
                    <a href="https://jqhph.github.io/dcat-admin/demo.html" id="demo-link" target="_blank">Demo</a>
                </div>
            </div>
        </div>
    </div>
</div>