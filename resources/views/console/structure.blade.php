@include('common.header')
<div class="layui-tab layui-tab-brief margin-0">
    <ul class="layui-tab-title">
        <li class="layui-this">@lang('文件差异')</li>
        <li>@lang('更新日志')</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item layui-show">
            <div class="layui-code margin-0 fui-structure" lay-options="{theme: 'dark', encode: false, ln: true, codeStyle:'height: 430px'}">@foreach($structures as $key=>$value)
{{ $value }}
@endforeach</div>
        </div>
        <div class="layui-tab-item">
            @if(empty($updateLogs))
                <div class="text-empty">@lang('暂无记录')</div>
            @else
                <div class="layui-collapse" lay-filter="update-logs">
                    @foreach($updateLogs as $key=>$logs)
                    <div class="layui-colla-item">
                        <div class="layui-colla-title text-bold">{{ $logs['datetime'] }}&nbsp;&nbsp;V{{ $logs['version'] }}&nbsp;Rel{{$logs['versionCode']}}</div>
                        <div class="layui-colla-content @if($key===0) layui-show @endif">
                            <p>{!! htmlspecialchars_decode($logs['logs']) !!}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
<style>
    .fui-structure ol li:last-child{display: none;}
</style>
@include('common.footer')
