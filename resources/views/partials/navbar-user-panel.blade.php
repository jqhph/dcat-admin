@if($user)
<li class="dropdown user user-menu">
    <!-- Menu Toggle Button -->
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <!-- The user image in the navbar-->
        <img src="{{ $user->getAvatar() }}" class="user-image" alt="User Image">
        <!-- hidden-xs hides the username on small devices so only the image appears. -->
        <span class="hidden-xs">{{ $user->name }}</span>
    </a>
    <ul class="dropdown-menu">
        <!-- The user image in the menu -->
        <li class="user-header">
            <img src="{{ $user->getAvatar() }}" class="img-circle" alt="User Image">

            <p>
                {{ $user->name }}
                <small>Member since admin {{ $user->created_at }}</small>
            </p>
        </li>
        <li class="user-footer">
            <div class="pull-left">
                <a href="{{ admin_url('auth/setting') }}" class="btn btn-default ">{{ trans('admin.setting') }}</a>
            </div>
            <div class="pull-right">
                <a href="{{ admin_url('auth/logout') }}" class="btn btn-default ">{{ trans('admin.logout') }}</a>
            </div>
        </li>
    </ul>
</li>
@endif