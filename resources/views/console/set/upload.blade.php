@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">站点上传设置</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                        @endif

                        <div class="fui-form">
                            <form action="{{ url('console/setting') }}" method="post" class="layui-form">
                                @csrf
                                <input type="hidden" name="op" value="attachset">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">图片格式</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="imgextentions" value="{{ implode(',',$_W['setting']['upload']['image']['extentions']) }}" placeholder="请输入允许上传的图片格式" autocomplete="off" class="layui-input">
                                        <div class="layui-word-aux">多个格式用英文逗号隔开，留空代表不允许上传图片</div>
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">单个图片限制</label>
                                    <div class="layui-input-block">
                                        <div class="input-group">
                                            <input type="text" name="imglimit" value="{{ $_W['setting']['upload']['image']['limit'] }}" placeholder="单位为KB，留空为不限" autocomplete="off" class="layui-input">
                                            <span class="input-group-addon">KB</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">图片压缩率</label>
                                    <div class="layui-input-block">
                                        <div class="input-group">
                                            <input type="number" min="1" max="100" name="imgzip" value="{{ $_W['setting']['upload']['image']['zip_percentage'] }}" placeholder="留空或100为不压缩" autocomplete="off" class="layui-input">
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="layui-form-item">
                                    <label class="layui-form-label">多媒体格式</label>
                                    <div class="layui-input-block">
                                        <input type="text" name="mediaext" value="{{ implode(',',$_W['setting']['upload']['media']['extentions']) }}" placeholder="请输入允许上传的多媒体格式" autocomplete="off" class="layui-input">
                                        <div class="layui-word-aux">多个格式用英文逗号隔开，留空代表不允许上传多媒体文件</div>
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">单个文件限制</label>
                                    <div class="layui-input-block">
                                        <div class="input-group">
                                            <input type="text" name="medialimit" value="{{ $_W['setting']['upload']['media']['limit'] }}" placeholder="单位为KB，留空为不限" autocomplete="off" class="layui-input">
                                            <span class="input-group-addon">KB</span>
                                        </div>
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
            </div>        </div>
            @endif

    @include('common.footer')
