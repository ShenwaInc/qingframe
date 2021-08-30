@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="padding-bottom-xl">{{ $uid>0 ? '编辑' : '新增子' }}账户</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                @endif

                <div class="fui-form">
                    <form action="{{ url('console/user/create') }}" method="post" class="layui-form ajaxpost">
                        @csrf
                        @if($uid>0)
                            <input type="hidden" name="uid" value="{{ $uid }}" />
                        @endif
                        <div class="layui-form-item must">
                            <label class="layui-form-label">用户名</label>
                            <div class="layui-input-block">
                                <input type="text" required lay-verify="required"{{ $uid>0 ? ' readonly' : '' }} name="username" value="{{ $user['username'] }}" placeholder="请输入用户名" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item{{ $uid==0 ? ' must' : '' }}">
                            <label class="layui-form-label">登录密码</label>
                            <div class="layui-input-block">
                                <input type="password"{{ $uid==0 ? ' required lay-verify="required"' : '' }} name="password" value="" placeholder="请设置一个登录登录密码{{ $uid>0 ? '，如不修改请留空' : '' }}" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        @if($uid==0)
                        <div class="layui-form-item must">
                            <label class="layui-form-label">确认密码</label>
                            <div class="layui-input-block">
                                <input type="password" required lay-verify="required" name="repassword" value="" placeholder="请重复您的登录密码" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        @endif
                        <div class="layui-form-item">
                            <label class="layui-form-label">到期时间</label>
                            <div class="layui-input-block">
                                <input type="text" readonly name="endtime" value="{{ $user['endtime']==0 ? '' : date('Y-m-d',$user['endtime']) }}" placeholder="请选择到期时间，留空表示不限期" autocomplete="off" class="layui-input layui-input-laydate">
                            </div>
                        </div>
                        @if($_W['isfounder'])
                            <div class="layui-form-item">
                                <label class="layui-form-label">平台权限</label>
                                <div class="layui-input-block">
                                    <input type="number" min="0" name="maxaccount" value="{{ $user['maxaccount'] }}" placeholder="请输入允许用户创建的平台数量，填0或留空表示不允许创建" autocomplete="off" class="layui-input">
                                    <div class="layui-word-aux">用户可创建平台总数量，填0或留空表示不允许创</div>
                                </div>
                            </div>
                        @endif
                        <div class="layui-form-item">
                            <label class="layui-form-label">备注</label>
                            <div class="layui-input-block">
                                <textarea rows="2" class="layui-textarea" placeholder="请输入用户备注，50字内" name="remark">{{ $user['remark'] }}</textarea>
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
    </div>
@endif

@include('common.footer')
