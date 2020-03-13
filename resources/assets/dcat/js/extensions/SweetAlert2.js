
import Swal from '../sweetalert/sweetalert2'

export default class SweetAlert2 {
    constructor(Dcat) {
        let _this = this;

        Swal.success = _this.success.bind(_this);
        Swal.error = _this.error.bind(_this);
        Swal.info = _this.info.bind(_this);
        Swal.warning = _this.warning.bind(_this);
        Swal.confirm = _this.confirm.bind(_this);

        _this.swal = Dcat.swal = Swal;
        
        Dcat.confirm = Swal.confirm;
    }

    success(title, message, options) {
        return this.fire(title, message, 'success', options)
    }

    error(title, message, options) {
        return this.fire(title, message, 'error', options)
    }

    info(title, message, options) {
        return this.fire(title, message, 'info', options)
    }

    warning(title, message, options) {
        return this.fire(title, message, 'warning', options)
    }

    confirm(title, message, success, fail, options) {
        let btnClass = 'btn btn-outline-dark',
            lang = Dcat.lang;

        options = $.extend({
            showCancelButton: true,
            showLoaderOnConfirm: true,
            confirmButtonText: lang['confirm'],
            cancelButtonText: lang['cancel'],
            confirmButtonClass: btnClass,
            cancelButtonClass: btnClass + 'ml-1',
        }, options);

        this.fire(title, message, 'question', options).then(function (result) {
            if (result.value) {
                return success && success()
            }

            fail && fail()
        })
    }

    fire(title, message, type, options) {
        options = $.extend({
            title: title,
            text: message,
            type: type,
            // buttonsStyling: false,
        }, options);

        return this.swal.fire(options);
    }
}
