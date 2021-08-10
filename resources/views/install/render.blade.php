@include('install.header')
<div class="installer">
    <div class="layui-card shadows">
        <div class="layui-card-header fui-step">
            <div class="layui-container">
                <div class="layui-row text-center">
                    <div class="layui-col-md4 layui-col-lg4 fui-item done">
                        数据库
                        <span class="num"></span>
                    </div>
                    <div class="layui-col-md4 layui-col-lg4 fui-item done">
                        SOCKET
                        <span class="num"></span>
                    </div>
                    <div class="layui-col-md4 layui-col-lg4 fui-item cur">
                        安装完成
                        <span class="num">3</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="layui-card-body">
        </div>
    </div>
</div>
<script type="text/javascript">
    layui.use(['form'],function(){
        var form = layui.form;
        form.on('submit(formDemo)',function (data){
            return false;
        });
    });
</script>
@include('install.footer')
