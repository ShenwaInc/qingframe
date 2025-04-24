@include('common.header')
<div class="layui-fluid unpadding">
    <div{{ $_W['isajax'] ? '' : ' class=main-content' }}>
        <div class="{{ $_W['isajax'] ? '' : 'fui-card layui-card' }}">
            @if(!$_W['isajax'])<div class="layui-card-header"><span class="title">{{ $title }}</span></div>@endif
            <div class="layui-card-body">
                <div class="un-padding" style="min-height: 300px;">
                    <div class="layui-form-item">
                        <div class="layui-input-inline" style="width: 360px;">
                            <input type="text" id="js-passcode" class="layui-input radius-lg" value="" name="passcode" placeholder="@lang('请输入您的卡密或兑换码')" />
                        </div>
                        <button type="button" onclick="doQuery()" class="layui-btn">@lang('查询应用信息')</button>
                    </div>
                    <div class="layui-hide js-application">
                        <table class="layui-table fui-table lines" lay-even="" lay-skin="nob">
                            <colgroup>
                                <col />
                                <col width="360" />
                                <col width="60"/>
                            </colgroup>
                            <thead>
                            <tr>
                                <th>@lang('name')</th>
                                <th class="layui-hide-xs">@lang('description')</th>
                                <th><div class="text-right">@lang('售价')</div></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <img src="/static/images/microserver.png" class="fl bg-gray radius margin-right-sm logo" height="48" />
                                    <div class="fui-table-name text-cut">
                                        <a href="#" class="text-blue website" target="_blank">应用名称</a><br/>
                                        <span class="version">V1.0.1</span>
                                    </div>
                                </td>
                                <td class="layui-hide-xs">
                                    <p class="text-ellip summary"></p>
                                </td>
                                <td class="text-right">
                                    <span class="text-red price">￥0.0</span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function doQuery() {
        let input = $('#js-passcode');
        let passcode = input.val();
        if(!passcode || passcode===''){
            return layer.msg('{{ __("无效的卡密或兑换码") }}', {icon: 2}), input.focus();
        }
        Core.post('console.module.passcode', function (res) {
            console.log(res);
            if(res.type!=='success'){
                return layer.msg(res.message, {icon: 2});
            }
            let Elem = $('.js-application'), app = res.data.application, enable = true;
            Elem.find('.logo').attr('src', app.icon).attr('alt', app.name);
            Elem.find('.website').attr('href', app.website).text(app.name);
            Elem.find('.summary').text(app.summary);
            if(res.data.product && res.data.product.id){
                Elem.find('.price').text('￥' + res.data.product.price);
            }else{
                Elem.find('.price').text('-');
            }
            if(res.data.release){
                Elem.find('.version').text('V' + res.data.release.version).attr('title', 'Release ' + res.data.release.versionCode);
            }else{
                Elem.find('.version').text('{{ __("暂未发布") }}').removeAttr('title');
                enable = false;
            }
            Elem.removeClass('layui-hide');
        }, {
            op:"query",
            code: passcode
        });
    }
</script>
@include('common.footer')
