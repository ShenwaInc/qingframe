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
                            <label class="layui-form-label">@lang('content')</label>
                            <div class="layui-input-block">
                                <textarea name="data[content]" required lay-verify="required" placeholder="@lang('workOrderDescription')" class="layui-textarea"></textarea>
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('category')</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline">
                                    <select name="cateId" lay-filter="orderCategory" class="layui-select" required lay-verify="required" lay-search>
                                        <option value="">@lang('workOrderCate')</option>
                                        @foreach($cates as $key=>$value)
                                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @foreach($subcates as $cateId=>$subCate)
                                    <div class="layui-input-inline subcates layui-hide" id="subCate{{ $cateId }}">
                                        <select name="subCate[{{ $cateId }}]" lay-search>
                                            <option value="">@lang('workOrderCateSub')</option>
                                            @foreach($subCate as $cate)
                                                <option value="{{ $cate['id'] }}">{{ $cate['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">@lang('confidential')</label>
                            <div class="layui-input-block">
                                <textarea name="data[secret]" placeholder="@lang('workOrderConfidential')" class="layui-textarea"></textarea>
                                <p class="layui-word-aux layui-hide">{!! __('workOrderAgreement') !!}</p>
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('contact')</label>
                            <div class="layui-input-block upload-btn">
                                <input type="text" name="data[mobile]" value="{{ $cloudState['mobile'] }}" required lay-verify="required" class="layui-input" />
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">@lang('attachment')</label>
                            <div class="layui-input-block upload-btn">
                                <button type="button" class="layui-btn layui-btn-sm layui-btn-normal js-uploader" id="attach{{ TIMESTAMP }}">{{ __('newData', array('data'=>__('attachment'))) }}</button>
                                <div class="layui-word-aux">@lang('attachExtLimit')</div>
                            </div>
                            <div class="layui-input-block" style="margin-top: 10px;">
                                <div class="row layui-col-space10" id="attachList"></div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit lay-filter="formSubmitBtn" type="submit" value="true" name="savedata">@lang('save')</button>
                                <button class="layui-btn layui-btn-primary" type="reset">@lang('reset')</button>
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
                layer.msg('@lang("workOrderSubmitted")', {icon:1});
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
