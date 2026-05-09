@include('console.server.header')
<div class="layui-fluid unpadding">
    <div class="main-content">
        <div class="layui-card fui-card">
            <div class="layui-card-header nobd">
                <span class="title layui-hide-layer">{{ $title }}</span>
                <p class="layui-word-aux">{!! __('welcomeEdit',['path'=>$file]) !!}</p>
            </div>
            <div class="layui-card-body">
                <form class="layui-form" method="post" action="{{ wurl('setting/blacklist') }}">
                    @csrf
                    <input type="hidden" name="save" value="true" />
                    <input type="hidden" name="redirect" value="{{ referer() }}" />
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin-left: 0">
                            <textarea class="layui-textarea" rows="10" name="blacklist">{!! $Blacklist !!}</textarea>
                            <p class="layui-word-aux">@lang('每行一个IP')</p>
                            <p class="layui-word-aux">@lang('支持配置通配符*，例如：192.168.* (每行仅限一个通配符，且*必须放在最后)')</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <div class="layui-input-block" style="margin-left: 0">
                            <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true">@lang('save')</button>
                            <button type="reset" class="layui-btn layui-btn-primary">@lang('reset')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('common.footer')
