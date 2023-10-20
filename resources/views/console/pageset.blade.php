@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2>{{ __('modifyData', array('data'=>__('siteInformation'))) }}</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
@else
                <div class="padding">
@endif

                    <div class="fui-form">
                        <form action="{{ wurl('setting') }}" method="post" class="layui-form">
                            @csrf
                            <input type="hidden" name="op" value="pageset">
                            <div class="layui-form-item must">
                                <label class="layui-form-label">@lang('siteName')</label>
                                <div class="layui-input-block">
                                    <input type="text" name="data[title]" required lay-verify="required" value="{{ $_W['setting']['page']['title'] }}" placeholder="{{ __('typeSomething', array('data'=>__('siteName'))) }}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item must">
                                <label class="layui-form-label">LOGO</label>
                                {!! serv('storage', 0)->tpl_form_image('data[logo]', $_W['setting']['page']['logo'],array('required'=>true,'placeholder'=>__('chooseImageSquare', array('size'=>'128x128')))); !!}
                            </div>
                            <div class="layui-form-item must">
                                <label class="layui-form-label">@lang('icon')</label>
                                {!! serv('storage', 0)->tpl_form_image('data[icon]', $_W['setting']['page']['icon'],array('required'=>true,'placeholder'=>__('chooseImageSquare', array('size'=>'48x48')))); !!}
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">@lang('SEOKeywords')</label>
                                <div class="layui-input-block">
                                    <input type="text" name="data[keywords]" value="{{ $_W['setting']['page']['keywords'] }}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">@lang('SEODescription')</label>
                                <div class="layui-input-block">
                                    <input type="text" name="data[description]" value="{{ $_W['setting']['page']['description'] }}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">@lang('copyright')</label>
                                <div class="layui-input-block">
                                    <input type="text" name="data[copyright]" value="{{ $_W['setting']['page']['copyright'] }}" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">@lang('bottomNavigation')</label>
                                <div class="layui-input-block">
                                    <textarea class="layui-textarea" name="data[links]">{{ $_W['setting']['page']['links'] }}</textarea>
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

    @if(!$_W['isajax'])
        </div>
    </div>
    @endif

</div>
@include('common.footer')
