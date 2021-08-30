@include('common.header')

<div class="main-content">

    <h2 class="weui-desktop-page__title">账户管理</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('user/profile') }}">个人资料</a>
            </li>
            <li class="layui-this">
                <a href="javascript:;">子账户</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <a href="{{ wurl('user/create',array('uid'=>0),true) }}" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">新增子账户</a>
            <span class="title">子账户列表</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="120" />
                        <col width="120" />
                        <col width="200" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>UID</th>
                        <th>用户名</th>
                        <th>添加时间</th>
                        <th style="text-align: center">到期时间</th>
                        <th><div class="text-right">操作</div></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(empty($users))
                        <tr>
                            <td colspan="5" class="text-center"><span class="text-gray">暂无数据</span></td>
                        </tr>
                    @endif
                    @foreach($users as $key=>$value)
                    <tr>
                        <td>{{ $value['uid'] }}</td>
                        <td>
                            {{ $value['username'] }}
                            @if(!empty($value['remark']))
                                <span class="text-gray">({{$value['remark']}})</span>
                            @endif
                        <td>{{ $value['createdate'] }}</td>
                        <td class="text-center{{ $value['expire'] ? ' text-red' : '' }}">{{ $value['expiredate'] }}</td>
                        <td class="text-right">
                            <a href="{{ wurl('user/checkout',array('uid'=>$value['uid']),true) }}" class="text-blue margin-right-sm confirm" data-text="切换后将退出当前账号并自动登录该账户，是否确定要切换？">切换</a>
                            <a href="{{ wurl('user/create',array('uid'=>$value['uid']),true) }}" class="text-blue ajaxshow margin-right-sm">修改</a>
                            <a href="{{ wurl('user/remove',array('uid'=>$value['uid'])) }}" class="text-red confirm" data-text="删除后该用户的操作记录及权限都将被清空且不可恢复，是否确定要删除？">删除</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@include('common.footer')
