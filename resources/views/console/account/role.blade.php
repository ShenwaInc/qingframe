@include('common.header')

<div class="main-content">

    <h2>平台权限</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">平台信息</a>
            </li>
            <li>
                <a href="{{ wurl('account/setting',array('uniacid'=>$uniacid)) }}">参数设置</a>
            </li>
            <li class="layui-this">
                <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">管理权限</a>
            </li>
            @if($_W['isfounder'])
                <li>
                    <a href="{{ wurl('account/component',array('uniacid'=>$uniacid)) }}">应用管理</a>
                </li>
            @endif
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <a href="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'add'),true) }}" title="新增平台权限" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">新增权限</a>
            <span class="title">平台权限</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="250" />
                        <col />
                        <col width="200" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>用户</th>
                        <th>权限</th>
                        <th style="text-align: right">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(empty($users))
                    <tr>
                        <td colspan="3" class="text-center">暂无数据</td>
                    </tr>
                    @endif
                    @foreach($users as $key=>$value)
                        <tr>
                            <td><span{{ $value['expired'] ? ' class=text-gray' : '' }}>{{ $value['username'] }}</span>&nbsp;<span class="layui-badge layui-bg-{{ $colors[$value['role']] }}">{{ $value['roler'] }}</span></td>
                            <td>{{ empty($value['permission']) ? '所有权限' : '部分权限' }}</td>
                            <td class="text-right">
                                @if($value['role']=='owner')
                                    @if($_W['isfounder'])
                                        <a href="javascript:;" onclick="showWindow(this)" data-id="#role-setowner" title="更换所有者" class="text-blue">修改</a>
                                    @endif
                                @else
                                    <a href="javascript:layer.msg('敬请期待',{icon:7});" class="text-blue margin-right-sm layui-hide">设置权限</a>
                                    <a href="{{ wurl('account/role',array('uniacid'=>$uniacid,'uid'=>$value['uid'],'op'=>'remove'),true) }}" class="text-red confirm" data-text="确定要删除该用户的操作权限吗？">删除</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="layer-content">
    <div class="layui-hide" id="role-setowner">
        <form class="layui-form" style="min-height: 360px" method="POST" action="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'setowner')) }}">
            @csrf
            <div class="layui-form-item">
                <label class="layui-form-label">选择用户</label>
                <div class="layui-input-block">
                    <div class="layui-input-inline" style="width: 70%">
                        <select name="uid" lay-search required lay-verify="required">
                            <option value="">输入用户名搜索</option>
                            <option value="{{ $_W['uid'] }}">{{ $_W['user']['username'] }}</option>
                            @foreach($subusers as $sub)
                                <option value="{{ $sub['uid'] }}">{{ $sub['username'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ wurl('user/create',array('uid'=>0),true) }}" class="layui-btn ajaxshow">新增用户</a>
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
</div>

@include('common.footer')
<script type="text/javascript">
    function showWindow(Elem){
        let title = Elem.title;
        let id = $(Elem).attr('data-id');
        layer.open({
            title:title,
            shade:0.3,
            shadeClose:true,
            type:1,
            content:$(id).html(),
            area: '720px',
            skin:'fui-layer',
            success: function(layero, index){
                let filter = 'layform-' + Wrandom(8);
                let Obj = $(layero);
                Obj.find('form.layui-form').attr('lay-filter',filter);
                FormInit(filter);
                EventInit(Obj);
            }
        });
    }
</script>

