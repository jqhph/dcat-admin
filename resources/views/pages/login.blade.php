<style>
    .login-page {background: #f7f7f9;}

    .login-box {
        margin-top: -5rem;
    }

    .login-btn {
        padding-left: 2rem!important;;
        padding-right: 1.5rem!important;
    }
</style>

<div class="login-page">
    <div class="login-box">
        <div class="login-logo mb-2">
            {{ config('admin.name') }}
        </div>
        <div class="card">
            <div class="card-body login-card-body p-2 shadow-100">
                <p class="login-box-msg mb-1">{{ __('admin.welcome_back') }}</p>

                <form id="login-form" method="POST" action="{{ admin_url('auth/login') }}">

                    @csrf

                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input
                                type="text"
                                class="form-control {{ $errors->has('username') ? 'is-invalid' : '' }}"
                                name="username"
                                placeholder="{{ trans('admin.username') }}"
                                value="{{ old('username') }}"
                                required
                                autofocus
                        >

                        <div class="form-control-position">
                            <i class="feather icon-user"></i>
                        </div>

                        <label for="email">{{ trans('admin.username') }}</label>

                        <div class="help-block with-errors"></div>
                        @if($errors->has('username'))
                            <span class="invalid-feedback text-danger" role="alert">
                                            @foreach($errors->get('username') as $message)
                                    <span class="control-label" for="inputError"><i class="feather icon-x-circle"></i> {{$message}}</span><br>
                                @endforeach
                                        </span>
                        @endif
                    </fieldset>

                    <fieldset class="form-label-group form-group position-relative has-icon-left">
                        <input
                                minlength="5"
                                maxlength="20"
                                id="password"
                                type="password"
                                class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                                name="password"
                                placeholder="{{ trans('admin.password') }}"
                                required
                                autocomplete="current-password"
                        >

                        <div class="form-control-position">
                            <i class="feather icon-lock"></i>
                        </div>
                        <label for="password">{{ trans('admin.password') }}</label>

                        <div class="help-block with-errors"></div>
                        @if($errors->has('password'))
                            <span class="invalid-feedback text-danger" role="alert">
                                            @foreach($errors->get('password') as $message)
                                    <span class="control-label" for="inputError"><i class="feather icon-x-circle"></i> {{$message}}</span><br>
                                @endforeach
                                            </span>
                        @endif

                    </fieldset>
                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="text-left">
                            <fieldset class="checkbox">
                                <div class="vs-checkbox-con vs-checkbox-primary">
                                    <input id="remember" name="remember"  value="1" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                    <span class="vs-checkbox">
                                                        <span class="vs-checkbox--check">
                                                          <i class="vs-icon feather icon-check"></i>
                                                        </span>
                                                    </span>
                                    <span> {{ trans('admin.remember_me') }}</span>
                                </div>
                            </fieldset>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary float-right login-btn">

                        {{ __('admin.login') }}
                        &nbsp;
                        <i class="feather icon-arrow-right"></i>
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
Dcat.ready(function () {
    // ajax表单提交
    $('#login-form').form({
        validate: true,
        success: function (data) {
            if (! data.status) {
                Dcat.error(data.message);

                return false;
            }

            Dcat.success(data.message);

            location.href = data.redirect;

            return false;
        }
    });
});
</script>
