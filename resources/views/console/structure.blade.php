@include('common.header')
<div class="layui-code layui-code-notepad fui-structure" style="margin: 0;" lay-height="360px" lay-about="false" lay-title="{{ __('structureDifference', array('files'=>$total)) }}">@foreach($structures as $key=>$value)
{{ $value }}
@endforeach</div>
<style>
    .fui-structure ol li:last-child{display: none;}
</style>
@include('common.footer')
