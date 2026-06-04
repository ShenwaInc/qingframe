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
                    <form class="layui-form" method="post" action="{{ wurl('report/feedback', array('id'=>$id)) }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('content')</label>
                            <div class="layui-input-block">
                                <textarea name="data[content]" required lay-verify="required" placeholder="@lang('workOrderDescription')" class="layui-textarea"></textarea>
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
                                <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('save')</button>
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
