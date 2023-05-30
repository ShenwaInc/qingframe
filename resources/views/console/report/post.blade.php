@include('common.header')
<div class="layui-fluid">
    <div class="layui-row layui-col-space15 @if(empty($_GPC['inajax'])) main-content @endif">
        <div class="layui-col-md12">
            <div class="@if(empty($_GPC['inajax']))layui-card fui-card @endif">
                @if(empty($_GPC['inajax']))
                <div class="layui-card-header">
                    <span class="title">{{ $title }}</span>
                </div>
                @endif
                <div class="layui-card-body">
                    <form class="layui-form" method="post" lay-filter="orderReportForm" action="{{ wurl('report/post') }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="layui-form-item must">
                            <label class="layui-form-label">问题描述</label>
                            <div class="layui-input-block">
                                <textarea name="data[content]" required lay-verify="required" placeholder="请在此处描述您遇到的问题" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">问题分类</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <select name="cateId" lay-filter="orderCategory" class="layui-select" required lay-verify="required" lay-search>
                                        <option value="">请选择工单问题的类型</option>
                                        @foreach($cates as $key=>$value)
                                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach($subcates as $cateId=>$subCate)
                                    <div class="layui-input-inline subcates layui-hide" id="subCate{{ $cateId }}">
                                        <select name="subCate[{{ $cateId }}]" lay-search>
                                            <option value="">请选择工单详细分类</option>
                                            @foreach($subCate as $cate)
                                                <option value="{{ $cate['id'] }}">{{ $cate['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">机密信息</label>
                            <div class="layui-input-block">
                                <textarea name="data[secret]" placeholder="请在此处填写您的机密信息，如账号密码、IP地址等" class="layui-textarea"></textarea>
                                <p class="layui-word-aux layui-hide">填写此项内容表示您已阅读并同意<a href="" target="_blank" class="text-blue">《轻如云服务协议》</a>中关于机密信息的管理条例</p>
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">联系方式</label>
                            <div class="layui-input-block upload-btn">
                                <input type="text" name="data[mobile]" value="{{ $cloudState['mobile'] }}" required lay-verify="required" class="layui-input" />
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">附件</label>
                            <div class="layui-input-block upload-btn">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal js-uploader" id="attach{{ TIMESTAMP }}">添加附件</button>
                                <div class="layui-word-aux">支持 .png .jpg .pdf .txt .rar .doc .xls .zip .mp4等格式，最多上传5个附件</div>
                            </div>
                            <div class="layui-input-block" style="margin-top: 10px;">
                                <div class="row layui-col-space10" id="attachList"></div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formSubmitBtn" type="submit" value="true" name="savedata">提交</button>
                                <button class="layui-btn layui-btn-primary" type="reset">重填</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #attachList *{box-sizing: border-box;}
</style>
@include('common.footer')
<script type="text/javascript">
    @if(!$_W['isajax'])
    function delAttach(Elem){
        let file = $(Elem).prev().prev().val();
        $('.upload-btn').removeClass('layui-hide');
        $(Elem).parent().parent().remove();
        Core.get('{{ wurl("report/rmAttach") }}', function (res) {
            console.log(res);
        }, {file:file});
    }
    @endif
    $(function (){
        layform.on("select(orderCategory)", function (data) {
            $('.subcates').addClass('layui-hide');
            let cateId = data.value;
            let subElem = $('#subCate' + cateId);
            subElem.removeClass('layui-hide').find('select').prop('required', true).attr('lay-verify', 'required');
            layform.render('select', 'orderReportForm');
        });
        layform.on('submit(formSubmitBtn)', function (data) {
            let postData = data.field;
            delete postData.file;
            Core.post('{{ wurl("report/post") }}', function (res) {
                console.log(res);
                if(res.type!=='success') return Core.report(res);
                layer.msg('工单提交成功！请耐心等待工作人员处理', {icon:1});
                setTimeout(function () {
                    window.location.reload();
                }, 1800);
            },postData)
            return false;
        })
        layform.render(null, 'orderReportForm');
        layupload.render({
            elem: '#attach{{ TIMESTAMP }}',
            url:'{{ wurl("report/attach") }}',
            accept:'file',
            data:{
              _token:"{{ $_W['token'] }}"
            },
            acceptMime:'*',
            done:function (res) {
                if(res.type!=='success') return Core.report(res);
                let attach = res.data, attachList = $('#attachList');
                let Html = '<div class="layui-col-md6 layui-col-sm12 layui-col-xs12"><div class="layui-bg-gray" style="padding: 5px 10px 0">' +
                    '<input type="hidden" name="attach[]" value="'+attach.path+'" />' +
                    '<input type="hidden" name="attachName[]" value="'+attach.name+'" />' +
                    '<span class="pull-right" onclick="delAttach(this)">('+attach.size+')&nbsp;<i style="cursor: pointer;" class="layui-icon layui-icon-close"></i></span>' +
                    '<a href="'+attach.url+'" target="_blank" class="text-blue text-cut" style="display: inline-block; max-width: 72%;">'+attach.name+'</a>' +
                    '</div></div>';
                attachList.append(Html);
                if(attachList.find('.layui-bg-gray').length>=5){
                    $('.upload-btn').addClass('layui-hide');
                }
            }
        });
    });
</script>
