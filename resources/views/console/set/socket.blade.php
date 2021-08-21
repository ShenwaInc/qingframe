@include('common.header')

@if(!$_W['isajax'])
    <div class="main-content">
        <h2 class="weui-desktop-page__title">SOCKET配置</h2>
        <div class="fui-card layui-card">
            <div class="layui-card-body">
                @else
                    <div class="padding">
                        @endif

                        <div class="fui-form">
                            <form action="{{ url('console/setting') }}" method="post" class="layui-form">
                                @csrf
                                <input type="hidden" name="op" value="socketset">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">连接方式</label>
                                    <div class="layui-input-block">
                                        <input type="radio" value="remote" name="data[type]" title="远程SOCKET"{{ $_W['setting']['swasocket']['type']=='local' ? '' : ' checked="checked"' }} />
                                        <input type="radio" value="local" name="data[type]" title="本地SOCKET"{{ $_W['setting']['swasocket']['type']=='local' ? ' checked="checked"' : '' }} />
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">SOCKET服务器</label>
                                    <div class="layui-input-block">
                                        <input type="text" required lay-verify="required" name="data[server]" value="{{ $_W['setting']['swasocket']['server'] }}" placeholder="请输入SOCKET服务器地址" autocomplete="off" class="layui-input">
                                        @if($_W['setting']['swasocket']['type']=='local')
                                        <div class="layui-word-aux">请修改为您本地的自定义SOCKET域名，<strong class="text-black">必须以ws{{$_W['ishttps']?'s':''}}://开头</strong></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="layui-form-item must">
                                    <label class="layui-form-label">WEB推送接口</label>
                                    <div class="layui-input-block">
                                        <input type="text" required lay-verify="required" name="data[api]" value="{{ $_W['setting']['swasocket']['api'] }}" placeholder="请输入WEB消息推送接口" autocomplete="off" class="layui-input">
                                        @if($_W['setting']['swasocket']['type']=='local')
                                        <div class="layui-word-aux">请修改为您本地的自定义接口地址(只需要修改域名部分)</div>
                                        @endif
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
            </div>
            @endif
        </div>

    @include('common.footer')
