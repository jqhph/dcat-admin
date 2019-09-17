<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ admin_asset(\Dcat\Admin\Admin::$baseCss['bootstrap']) }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ admin_asset(\Dcat\Admin\Admin::$baseCss['font-awesome']) }}">

  <link rel="stylesheet" href="{{ admin_asset(\Dcat\Admin\Admin::$baseCss['icons']) }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ admin_asset(\Dcat\Admin\Admin::$baseCss['adminLTE']) }}">

  <link rel="stylesheet" href="{{ admin_asset("vendor/dcat-admin/dcat-admin/main.min.css") }}">

  <link rel="stylesheet" href="{{ admin_asset(\Dcat\Admin\Admin::$fonts) }}">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <style>
    /*.login-logo {*/
      /*font-family: 'Rancho', cursive, 'Raleway', sans-serif;*/
    /*}*/
    .login-box {
      width:330px;
    }
    .login-box-body {
      box-shadow:0 1px 5px rgba(0, 0, 0, .09), 0 2px 2px rgba(0, 0, 0, .09), 0 3px 1px -2px rgba(0, 0, 0, .09);
      padding: 30px 25px;
    }
    .login-label {
      font-weight: 500;
      margin-bottom: 8px;
    }
    .login-page {
      background: #f1f1f1;
    }
  </style>
</head>
<body class="hold-transition login-page" @if(config('admin.login_background_image'))style="background:url({{config('admin.login_background_image')}});background-size:cover"@endif>
<div class="login-box">
  <div class="login-logo">
    <a href="{{ admin_url('/') }}">{{config('admin.name')}}</a>
  </div>
  <!-- /.login-logo -->
  <div class="login-box-body">
    {{--<p class="login-box-msg">{{ trans('admin.login') }}</p>--}}

    <form action="{{ admin_url('auth/login') }}" method="post">
      <div class="form-group has-feedback {!! !$errors->has('username') ?: 'has-error' !!}">

        @if($errors->has('username'))
          @foreach($errors->get('username') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif
        <label class="login-label">{{ trans('admin.username') }}</label>
        <input type="text" class="form-control" placeholder="{{ trans('admin.username') }}" name="username" value="{{ old('username') }}">
        <span class="fa fa-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback {!! !$errors->has('password') ?: 'has-error' !!}">

        @if($errors->has('password'))
          @foreach($errors->get('password') as $message)
            <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>{{$message}}</label><br>
          @endforeach
        @endif
        <label class="login-label">{{ trans('admin.password') }}</label>
        <input type="password" class="form-control" placeholder="{{ trans('admin.password') }}" name="password">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>

      <div class="row" style="margin-top:28px">
        <div class="col-xs-8">
          @if(config('admin.auth.remember'))
            <div class="checkbox checkbox-primary">
              <input id="remember" name="remember" type="checkbox" value="1" {{ (old('remember')) ? 'checked' : '' }}>
              <label for="remember">
                {{ trans('admin.remember_me') }}
              </label>
            </div>
          @endif
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <button type="submit" class="btn btn-primary btn-block">{{ trans('admin.login') }}</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 2.1.4 -->
<script src="{{ admin_asset(\Dcat\Admin\Admin::$jQuery)}} "></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{ admin_asset(\Dcat\Admin\Admin::$baseJs['bootstrap'])}}"></script>

</body>
</html>
