@include('console.server.header')
<div class="layui-fluid">
    <div class="layui-row layui-col-space15 main-content">
        <div class="layui-col-md12 layui-col-xs12">
            <div class="layui-card fui-card">
                <div class="layui-card-header nobd">
                    <span class="title">{{ $title }}</span>
                    <p class="layui-word-aux">{!! __('welcomeEdit',['path'=>$path]) !!}</p>
                </div>
                <div class="layui-card-body">
                    <form class="layui-form" method="post" action="{{ wurl('setting/welcome') }}">
                        @csrf
                        <input type="hidden" name="save" value="true" />
                        <input type="hidden" name="redirect" value="{{ $redirect }}">
                        <div class="layui-form-item">
                            <label class="layui-form-label">HTML</label>
                            <div class="layui-input-block">
                                <textarea class="layui-textarea" rows="30" name="welcomeHTML">{{ $html }}</textarea>
                                <p class="layui-word-aux">
                                    <strong class="text-red">{!! __('welcomeTips') !!}</strong>
                                </p>
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
</div>
@include('common.footer')
