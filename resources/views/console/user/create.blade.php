@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="padding-bottom-xl">{{ __($uid==0?'newData':'modifyData', array('data'=>__('subAccount'))) }}</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                @endif

                <div class="fui-form">
                    <form action="{{ url('console/user/create') }}" method="post" class="layui-form ajaxpost">
                        @csrf
                        @if($uid>0)
                            <input type="hidden" name="uid" value="{{ $uid }}" />
                        @endif
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('username')</label>
                            <div class="layui-input-block">
                                <input type="text" required lay-verify="required"{{ $uid>0 ? ' readonly' : '' }} name="username" value="{{ $user['username'] }}" placeholder="{{ __('typeSomething', array('data'=>__('username'))) }}" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item{{ $uid==0 ? ' must' : '' }}">
                            <label class="layui-form-label">@lang('loginPassword')</label>
                            <div class="layui-input-block">
                                <input type="password"{{ $uid==0 ? ' required lay-verify="required"' : '' }} name="password" value="" placeholder="{{ __('loginPasswordSet', array('extra'=>$uid>0?__('loginPasswordNotModify'):'')) }}" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        @if($uid==0)
                        <div class="layui-form-item must">
                            <label class="layui-form-label">@lang('rePassword')</label>
                            <div class="layui-input-block">
                                <input type="password" required lay-verify="required" name="repassword" value="" placeholder="@lang('reTypePassword')" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        @endif
                        <div class="layui-form-item">
                            <label class="layui-form-label">@lang('expireDate')</label>
                            <div class="layui-input-block">
                                <input type="text" readonly name="endtime" value="{{ $user['endtime']==0 ? '' : date('Y-m-d',$user['endtime']) }}" placeholder="@lang('chooseDate')" autocomplete="off" class="layui-input layui-input-laydate">
                                <div class="layui-word-aux">@lang('emptyForLongtime')</div>
                            </div>
                        </div>
                        @if($_W['isfounder'])
                            <div class="layui-form-item">
                                <label class="layui-form-label">{{ __('permissions', array('operation'=>__('platform'))) }}</label>
                                <div class="layui-input-block">
                                    <input type="number" min="0" name="maxaccount" value="{{ $user['maxaccount'] }}" placeholder="@lang('platformLimit')" autocomplete="off" class="layui-input">
                                    <div class="layui-word-aux">@lang('platformLimit')</div>
                                </div>
                            </div>
                        @endif
                        <div class="layui-form-item">
                            <label class="layui-form-label">@lang('remark')</label>
                            <div class="layui-input-block">
                                <textarea rows="2" class="layui-textarea" placeholder="{{ __('typeRemark', array('size'=>'50')) }}" name="remark">{{ $user['remark'] }}</textarea>
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
