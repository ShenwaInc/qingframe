@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">新增平台权限</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                <div class="un-padding">
                    @endif
                    <form class="layui-form" method="POST" action="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'add')) }}">
                        @csrf
                        <div class="layui-form-item">
                            <label class="layui-form-label">选择用户</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline" style="width: 70%">
                                    <select name="uid" lay-search required lay-verify="required">
                                        <option value="">输入用户名搜索</option>
                                        @foreach($subusers as $sub)

                                            <option value="{{ $sub['uid'] }}"{{ $owner==$sub['uid']?' disabled':'' }}>{{ $sub['username'] }}{{ $owner==$sub['uid']?'(所有者)':'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <a href="{{ wurl('user/create',array('uid'=>0),true) }}" class="layui-btn ajaxshow">新增用户</a>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">权限角色</label>
                            <div class="layui-input-block">
                                <input type="radio" name="role" value="manager" checked title="管理员" />
                                <input type="radio" name="role" value="operator" title="操作员" />
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">保存</button>
                                <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                            </div>
                        </div>
                    </form>
                    @if(!$_W['isajax'])
                </div>
            </div>
        </div>
    </div>
@endif

@include('common.footer')
