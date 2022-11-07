@include('common.header')
<div class="layui-code layui-code-notepad" style="margin: 0;" lay-height="360px" lay-about="false" lay-title="以下文件经对比与最新版有差异">@foreach($structures as $key=>$value)
{{ $value }}
@endforeach共计{{ $total }}个文件</div>
@include('common.footer')
