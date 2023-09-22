@include('console.server.header')
<div class="layui-fluid">
    <div class="layui-row layui-col-space15 main-content">
        <div class="layui-col-md12 layui-col-xs12">
            <div class="layui-card fui-card">
                <div class="layui-card-header nobd">
                    <span class="title">{{ $title }}</span>
                    <p class="layui-word-aux">该服务部分功能需要依赖Composer组件包，请按如下步骤安装依赖包</p>
                </div>
                <div class="layui-card-body">
                    <blockquote class="layui-elem-quote">使用putty等ssh命令行工具，或者登陆宝塔终端，逐一输入如下指令并回车</blockquote>
                    <blockquote class="layui-elem-quote"><strong>如安装过程遇到问题，请<a href="https://www.yuque.com/shenwa/qingru/ze9hby#qUvo3" target="_blank" class="text-blue">参考Composer完整说明文档</a>排查</strong></blockquote>
                    <pre class="layui-code" lay-title="使用Composer安装依赖包">
cd {{ $WorkingDirectory }}
composer {{ DEVELOPMENT ? 'update':'require '.$requireName.($composerVer?' '.$composerVer:'') }} @if($composerErr)
rm -f {{ $composerErr }}@endif</pre>
                    <div class="margin-bottom">
                        @if($composerNext) {!! $composerNext !!}@else<strong>安装好后，请<a href="{{ $_W['siteurl'] }}" class="text-blue">刷新</a>此页面，或返回上一步操作</strong>@endif
                    </div>
                    <blockquote class="layui-elem-quote">如果您的服务器没有安装Composer，请参考如下指令，或者参考<a href="https://pkg.xyz/#how-to-install-composer" target="_blank" class="color-default">Composer官网的安装说明</a></blockquote>
                    <pre class="layui-code" lay-title="安装Composer">
yum install -y composer

#将镜像源更改为国内镜像源（否则部分资源下载会很卡顿）
composer config -g repo.packagist composer https://packagist.phpcomposer.com

#或者采用阿里云镜像源
#composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/</pre>
                    <blockquote class="layui-elem-quote">安装依赖包的过程如果出现404等其它错误，请检查镜像源地址是否被防火墙封禁，也可以尝试升级Composer版本</blockquote>
                    <pre class="layui-code" lay-title="升级Composer">
composer self-update</pre>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .layui-elem-quote{font-size: inherit;}
</style>
@include('common.footer')
