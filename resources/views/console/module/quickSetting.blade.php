@include('common.header')
<div class="layui-fluid">
    <div class="main-content">
        <h2>@lang('applicationConfiguration')</h2>
        <div class="layui-card fui-card">
            <div class="layui-card-body">
                <form class="layui-form" action="{{ $_W['siteurl'] }}" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <div class="layui-form-item must">
                        <label class="layui-form-label">{{ __('nameOfData', array('data'=>__('app'))) }}</label>
                        <div class="layui-input-block">
                            <input type="text" required lay-verify="required" name="module[name]" value="{{ $moduleInfo['name'] }}" placeholder="{{ __('typeName', array('data'=>__('app'))) }}" autocomplete="off" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <label class="layui-form-label">@lang('icon')</label>
                        {!! serv('storage')->tpl_form_image('module[logo]', $moduleInfo['logo'],array('required'=>true,'placeholder'=>__('chooseImageSquare', array('size'=>'128x128')))) !!}
                    </div>
                    @if($application_type==2)
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('applicationEntry')</label>
                            <div class="layui-input-block">
                                <input type="text" required lay-verify="required" name="data[WebIndex]" value="{{ $configs['WebIndex'] }}" placeholder="@lang('applicationEntryType')" autocomplete="off" class="layui-input" />
                                <div class="layui-word-aux">
                                    {!! __('applicationEntryRemind') !!}
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">@lang('displayMode')</label>
                            <div class="layui-input-block">
                                <input type="radio" name="data[openType]" value="" title="@lang('default')" {{ empty($configs['openType']) ? 'checked' : '' }} />
                                <input type="radio" disabled name="data[openType]" value="iframe" title="iFrame" {{ $configs['openType']=='iframe' ? 'checked' : '' }} />
                            </div>
                        </div>
                    @endif
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
