@include('common.header')
<iframe class="fui-iframe" src="{{ $src }}" width="100%"></iframe>
<style>
    .layui-body{padding-top: 0 !important;}
    .fui-iframe{height: calc(100vh - 60px); border: none;}
</style>
@include('common.footer')
