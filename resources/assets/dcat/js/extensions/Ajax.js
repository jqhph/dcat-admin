
export default class Ajax {
    constructor(Dcat) {
        this.dcat = Dcat;

        Dcat.handleAjaxError = this.handleAjaxError.bind(this)
    }

    handleAjaxError(xhr, text, msg) {
        let Dcat = this.dcat;

        Dcat.NP.done();
        Dcat.loading(false);// 关闭所有loading效果

        var json = xhr.responseJSON || {}, _msg = json.message;
        switch (xhr.status) {
            case 500:
                return Dcat.error(_msg || (Dcat.lang['500'] || 'Server internal error.'));
            case 403:
                return Dcat.error(_msg || (Dcat.lang['403'] || 'Permission deny!'));
            case 401:
                if (json.login) {
                    return location.href = json.login;
                }
                return Dcat.error(Dcat.lang['401'] || 'Unauthorized.');
            case 419:
                return Dcat.error(Dcat.lang['419'] || 'Sorry, your page has expired.');

            case 422:
                if (json.errors) {
                    try {
                        var err = [], i;
                        for (i in json.errors) {
                            err.push(json.errors[i].join('<br/>'));
                        }
                        Dcat.error(err.join('<br/>'));
                    } catch (e) {}
                    return;
                }
        }

        Dcat.error(_msg || (xhr.status + ' ' + msg));
    }
}
