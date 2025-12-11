@include('common.header')
<div class="layui-fluid unpadding">
    <div class="main-content">
        <h2 class="{{ $_W['isajax'] ? 'layui-hide' : '' }}">{{ $title }}</h2>
        <div class="layui-card fui-card">
            <div class="layui-card-body">
                <form class="layui-form" action="{{ wurl('module/quickCreate') }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <input type="hidden" name="uniacid" value="{{ $uniacid }}" />
                    <div class="layui-form-item must">
                        <label class="layui-form-label">@lang('应用标识')</label>
                        <div class="layui-input-block">
                            <input type="text" required lay-verify="required" name="module[identifier]" value="" placeholder="@lang('请输入应用标识')" autocomplete="off" class="layui-input" />
                            <div class="layui-word-aux">
                                @lang('英文小写字母、数字、下划线，长度不超过32个字符, 必须以小写字母开头')
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <label class="layui-form-label">{{ __('nameOfData', array('data'=>__('app'))) }}</label>
                        <div class="layui-input-block">
                            <input type="text" required lay-verify="required" name="module[name]" value="" placeholder="{{ __('typeName', array('data'=>__('app'))) }}" autocomplete="off" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">@lang('icon')</label>
                        {!! serv('storage')->tpl_form_image('module[logo]', '',array('required'=>true,'placeholder'=>__('chooseImageSquare', array('size'=>'128x128')))) !!}
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">@lang('description')</label>
                        <div class="layui-input-block">
                            <input type="text" name="module[description]" value="" placeholder="@lang('请输入应用描述')" autocomplete="off" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <label class="layui-form-label">@lang('applicationEntry')</label>
                        <div class="layui-input-block">
                            <input type="text" required lay-verify="required" name="data[WebIndex]" value="" placeholder="{{ __('applicationEntryType') }}" autocomplete="off" class="layui-input" />
                            <div class="layui-word-aux">
                                {!! __('applicationEntryRemind') !!}
                            </div>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">@lang('displayMode')</label>
                        <div class="layui-input-block">
                            <input type="radio" name="data[openType]" value="" title="@lang('default')" {{ empty($configs['openType']) ? 'checked' : '' }} />
                            <input type="radio" disabled name="data[openType]" value="iframe" title="iFrame(内测中)" {{ $configs['openType']=='iframe' ? 'checked' : '' }} />
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('save')</button>
                            <button type="reset" class="layui-btn layui-btn-primary">@lang('reset')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('common.footer')
