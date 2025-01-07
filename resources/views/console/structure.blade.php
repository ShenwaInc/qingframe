@include('common.header')
<div class="layui-tab layui-tab-brief margin-0">
    <ul class="layui-tab-title">
        <li @if(empty($curShow) || $curShow=='compare') class="layui-this" @endif>@lang('文件差异')</li>
        <li @if($curShow=='logs') class="layui-this" @endif >@lang('更新日志')</li>
    </ul>
    <div class="layui-tab-content">
        <div class="layui-tab-item @if(empty($curShow) || $curShow=='compare') layui-show @endif">
            @if(empty($structures))
                <div class="text-empty">@lang('所有文件与云端对比没有差异')</div>
            @else
            <div class="layui-code margin-0 fui-structure" lay-options="{theme: 'dark', encode: false, ln: true, codeStyle:'height: 430px'}">@foreach($structures as $key=>$value)
{{ $value }}
@endforeach</div>
            @endif
        </div>
        <div class="layui-tab-item bg-gray-light padding @if($curShow=='logs') layui-show @endif ">
            @if(empty($updateLogs))
                <div class="text-empty">@lang('暂无记录')</div>
            @else
                @foreach($updateLogs as $key=>$logs)
                    <div class="layui-card fui-card">
                        <div class="layui-card-header">
                            <span class="title">{{ $logs['datetime'] }}&nbsp;&nbsp;v{{ $logs['version'] }}</span>
                        </div>
                        <div class="layui-card-body">
                            <div class="content rich_media_content">
                                {!! htmlspecialchars_decode($logs['logs']) !!}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<style>
    .fui-structure ol li:last-child{display: none;}
</style>
@include('common.footer')
