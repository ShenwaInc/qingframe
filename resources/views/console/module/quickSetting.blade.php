@include('common.header')
<div class="layui-fluid">
    <div class="main-content">
        <h2>应用配置</h2>
        <div class="layui-card fui-card">
            <div class="layui-card-body">
                <form class="layui-form" action="{{ $_W['siteurl'] }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="layui-form-item must">
                        <label class="layui-form-label">应用名称</label>
                        <div class="layui-input-block">
                            <input type="text" required lay-verify="required" name="module[name]" value="{{ $moduleInfo['name'] }}" placeholder="请输入平台名称" autocomplete="off" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <label class="layui-form-label">应用图标</label>
                        {!! serv('storage')->tpl_form_image('module[logo]', $moduleInfo['logo'],array('required'=>true,'placeholder'=>'请选择图片上传（正方形128x128）')); !!}
                    </div>
                    @if($application_type==2)
                        <div class="layui-form-item must">
                            <label class="layui-form-label">应用入口</label>
                            <div class="layui-input-block">
                                <input type="text" required lay-verify="required" name="data[WebIndex]" value="{{ $configs['WebIndex'] }}" placeholder="请输入第三方应用入口链接" autocomplete="off" class="layui-input" />
                                <div class="layui-word-aux">
                                    第三方应用入口链接需要符合单点登录的规范，<a href="https://www.yuque.com/shenwa/tt5ahr/co6qmcz2kicd9wms" class="text-blue" target="_blank">查看详情</a>
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">打开方式</label>
                            <div class="layui-input-block">
                                <input type="radio" name="data[openType]" value="" title="默认" {{ empty($configs['openType']) ? 'checked' : '' }} />
                                <input type="radio" disabled name="data[openType]" value="iframe" title="iFrame" {{ $configs['openType']=='iframe' ? 'checked' : '' }} />
                            </div>
                        </div>
                    @endif
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">保存</button>
                            <button type="reset" class="layui-btn layui-btn-primary">重填</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('common.footer')
