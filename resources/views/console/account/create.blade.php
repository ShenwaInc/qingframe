@include('common.header')

@if(!$_W['isajax'])
<div class="main-content">
    <h2 class="weui-desktop-page__title">{{ __('createNewData', array('data'=>__('platform'))) }}</h2>
    <div class="fui-card layui-card">
        <div class="layui-card-body">
            <div class="un-padding">
@endif
                <form action="{{ wurl('account/create') }}" method="post" class="layui-form">
                    @csrf
                    <div class="layui-form-item must">
                        <label class="layui-form-label">{{ __('nameOfData', array('data'=>__('platform'))) }}</label>
                        <div class="layui-input-block">
                            <input type="text" required lay-verify="required" name="data[name]" value="" placeholder="{{ __('typeSomething', array('data'=>__('platformName'))) }}" autocomplete="off" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">@lang('platformIntroduction')</label>
                        <div class="layui-input-block">
                            <input type="text" name="data[description]" value="" placeholder="{{ __('typeSomething', array('data'=>__('platformIntroduction'))) }}" class="layui-input" />
                        </div>
                    </div>
                    <div class="layui-form-item must">
                        <label class="layui-form-label">@lang('platformLOGO')</label>
                        {!! serv('storage', 0)->tpl_form_image('data[logo]', "",array('required'=>true,'placeholder'=>__('chooseImageSquare', array('size'=>'200x200')))); !!}
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block">
                            <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('create')</button>
                            <button type="reset" class="layui-btn layui-btn-primary">@lang('reset')</button>
                        </div>
                    </div>
                </form>
@if(!$_W['isajax'])
            </div>
        </div>
    </div>
</div>
@endif

@include('common.footer')
