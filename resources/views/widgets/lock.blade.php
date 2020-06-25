<div class="mock" style="display: none">
    <form class="form-mock" method="POST" action="/admin/auth/verifypass">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
        <input class="form-item input" name="lockpass" type="password" placeholder="请输入密码解锁...">
        <button type="submit" class="btn btn-primary form-item button">解锁</button>
    </form>
</div>
