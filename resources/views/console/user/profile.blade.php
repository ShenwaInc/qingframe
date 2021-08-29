@include('common.header')

<div class="main-content">

    <h2 class="weui-desktop-page__title">账户管理</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li class="layui-this">
                <a href="javascript:;">个人资料</a>
            </li>
            <li>
                <a href="{{ wurl('user/subuser') }}">子账户</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">账户管理</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="100" />
                    </colgroup>
                    <tbody>
                        <tr>
                            <td><span class="fui-table-lable">用户名</span></td>
                            <td class="soild-after">{{ $_W['user']['username'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">头像</span></td>
                            <td class="soild-after">
                                <img class="radius" id="user-avatar" src="{{ tomedia($profile['avatar']) }}" width="72" />
                            </td>
                            <td class="text-right soild-after">
                                <a href="javascript:" class="text-blue js-avatar" data-prev="#user-avatar" title="修改头像">修改</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">密码</span></td>
                            <td class="soild-after"> ******** </td>
                            <td class="text-right soild-after">
                                <a href="{{ url('console/user/passport') }}" class="text-blue ajaxshow" title="修改登录密码">修改</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@include('common.footer')

<script type="text/javascript">
    layer.ready(function (){
        $('.js-avatar').each(function (index,element){
            let preview = $(this).data('prev');
            layui.upload.render({
                elem: element
                ,url: '{{ url("console/user/avatar") }}' //必填项
                ,accept:'images'
                ,acceptMime:'images'
                ,exts:"{{ implode('|',$_W['setting']['upload']['image']['extentions']) }}"
                ,data:{_token:"{{ csrf_token() }}"}
                ,done:function (res, index, upload){
                    if(res.type!=='success') return Core.report(res);
                    layer.msg('修改成功！',{icon:1});
                    if (typeof(preview)!='undefined'){
                        $(preview).attr('src',res.message.url);
                    }
                }
            });
        });
    });
</script>
