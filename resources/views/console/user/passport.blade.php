@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="padding-bottom-xl">{{ __('modifyData', array('data'=>__('password'))) }}</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
@endif

                <div class="fui-form">
                    <form action="{{ url('console/user/passport') }}" method="post" class="layui-form ajaxpost">
                        @csrf
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('oldPassword')</label>
                            <div class="layui-input-block">
                                <input type="password" required lay-verify="required" name="oldpassword" value="" placeholder="@lang('typeOldPassword')" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('newPassword')</label>
                            <div class="layui-input-block">
                                <input type="password" required lay-verify="required" name="newpassword" value="" placeholder="@lang('typeNewPassword')" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('rePassword')</label>
                            <div class="layui-input-block">
                                <input type="password" required lay-verify="required" name="repassword" value="" placeholder="@lang('reTypePassword')" autocomplete="off" class="layui-input">
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
    </div>
@endif

@include('common.footer')
