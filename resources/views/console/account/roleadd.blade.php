@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">{{ __('newData', array('data'=>__('platformOperator'))) }}</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                <div class="un-padding">
                    @endif
                    <form class="layui-form" style="min-height: 360px" method="POST" action="{{ wurl('account/role',array('uniacid'=>$uniacid,'op'=>'add')) }}">
                        @csrf
                        <div class="layui-form-item">
                            <label class="layui-form-label">{{ __('chooseData', array('data'=>__('user'))) }}</label>
                            <div class="layui-input-block">
                                <div class="layui-input-inline" style="width: 70%">
                                    <select name="uid" lay-search required lay-verify="required">
                                        <option value="">{{ __('type&search', array('input'=>__('username'))) }}</option>
                                        @foreach($subusers as $sub)

                                            <option value="{{ $sub['uid'] }}"{{ $owner==$sub['uid']?' disabled':'' }}>{{ $sub['username'] }}{{ $owner==$sub['uid']?'('.__('owner').')':'' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <a href="{{ wurl('user/create',array('uid'=>0),true) }}" class="layui-btn ajaxshow">{{ __('newData', array('data'=>__('user'))) }}</a>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">@lang('role')</label>
                            <div class="layui-input-block">
                                <input type="radio" name="role" value="manager" checked title="@lang('manager')" />
                                <input type="radio" name="role" value="operator" title="@lang('operator')" />
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('save')</button>
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
