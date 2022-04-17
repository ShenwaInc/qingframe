@include('common.header')
<div class="layui-fluid">
    <div class="layui-row layui-col-space15 main-content">
        <h2>站点设置</h2>
        <div class="layui-tab fui-tab">
            <ul class="layui-tab-title title_tab">
                <li>
                    <a href="{{ url('console/setting') }}">站点信息</a>
                </li>
                <li>
                    <a href="{{ url('console/setting/attach') }}">附件设置</a>
                </li>
                <li class="layui-this">
                    <a href="{{ url('console/server') }}">服务管理</a>
                </li>
                <li>
                    <a href="{{ url('console/setting/plugin') }}">应用管理</a>
                </li>
            </ul>
        </div>
        <div class="layui-col-md12 layui-col-xs12">
            <div class="layui-card fui-card">
                <div class="layui-card-header nobd">
                    @if($wiki) <li><a href="{$wiki}" class="layui-btn layui-btn-danger fr"  target="_blank">详细调用说明文档</a></li>@endif
                    <span class="title">{{ $title }}</span>
                    <p class="layui-word-aux">查看内部调用方法</p>
                </div>
                <div class="layui-card-body">
                    <div class="layui-tab layui-tab-brief">
                        @php $curview=0; @endphp
                        <ul class="layui-tab-title">
                            @foreach($methods as $key=>$value)
                            <li @if($curview==0) class="layui-this" @php $curview+=1; @endphp @endif>{{ $value['name'] }}</li>
                            @endforeach
                        </ul>
                        @php $curview=0; @endphp
                        <div class="layui-tab-content">
                            @foreach($methods as $key=>$value)
                            <div class="layui-tab-item @if($curview==0) layui-show @php $curview+=1; @endphp @endif">
                                <blockquote class="layui-elem-quote">{{ $value['summary'] }} @if(!empty($value['wiki'])) <a href="{$value['wiki']}" class="layui-btn layui-btn-normal" target="_blank" style="margin-left: 15px">更多说明</a>@endif</blockquote>
                                <pre class="layui-code" lay-title="内部调用示例">
serv('{{ $service['identity'] }}')->{{ $key }}({{ \App\Services\MSService::showparams($value['params']) }});</pre>
                                <pre class="layui-code" lay-title="方法参数详解">
class {{ $classname }}Service {

 /**
  * {{ $value['name'] }}

 @foreach($value['params'] as $ke=>$pa)
 * @param {{ $pa[1] }} ${{ $ke }} {{ $pa[0] }}

 @endforeach
 * @return {{ $value['return'][1] }} {{ $value['return'][0] }}

 */
 public function {{ $key }}({{ \App\Services\MSService::showparams($value['params'], true) }}){
    //Todo something
    @if(!empty($value['listener']))$this->Event("{{ $value['listener'] }}", $data); //事件广播    @endif

 }



}</pre>
                                @if(!empty($value['listener']))
                                <blockquote class="layui-elem-quote">广播事件名：<strong>{{ $value['listener'] }}</strong>&nbsp;&nbsp;<span title="点击复制" class="fa fa-copy color-default js-clip" data-url="{{ $value['listener'] }}"></span></blockquote>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var Controller = "{$_W['controller']}",Action = "{$_W['action']}";
    var layform;
    layui.use(['element','layer','code'], function(){
        var element=layui.element,layer = layui.layer;
        layui.code();
        layer.ready(function(){
        });
    });
</script>
<style>
    .layui-elem-quote{font-size: inherit;}
</style>
@include('common.footer')
