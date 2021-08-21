@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">SOCKET配置</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                @else
                    <div class="padding">
                        @endif

                        <div class="fui-form">
                            <form action="{{ url('console/user/passport') }}" method="post" class="layui-form ajaxpost">
                                @csrf
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">旧密码</label>
                                    <div class="layui-input-block">
                                        <input type="password" required lay-verify="required" name="oldpassword" value="" placeholder="请输入您的旧登录密码" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">新密码</label>
                                    <div class="layui-input-block">
                                        <input type="password" required lay-verify="required" name="newpassword" value="" placeholder="请设置一个登录登录密码" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">确认密码</label>
                                    <div class="layui-input-block">
                                        <input type="password" required lay-verify="required" name="repassword" value="" placeholder="请重复您的登录密码" autocomplete="off" class="layui-input">
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <div class="layui-input-block">
                                        <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">保存</button>
                                        <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                                    </div>
                                </div>
                            </form>
                        </div>


                        @if(!$_W['isajax'])
                    </div>
            </div>
            @endif
        </div>

    @include('common.footer')
