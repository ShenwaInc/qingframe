<!DOCTYPE html>
<html lang="{$locale}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ assets('/static/css/nunito.css') }}?v={{ QingRelease }}" />

        <!-- Styles -->
        <link rel="stylesheet" href="{{ assets('/static/css/welcome.css') }}?v={{ TIMESTAMP }}" />
    </head>
    <body>
        <div class="flex-bottom position-ref full-height">
            <div class="page-top">
                <span class="center text-black">@lang('轻如云')</span>
            </div>

            <nav class="top-nav">
                @if (Route::has('login'))
                    <div class="links">
                        @auth
                            <a href="/console">@lang('控制台')</a>
                            <a href="{{ url('auth/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">@lang('logout')</a>
                            <form id="logout-form" action="{{ url('auth/logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @else
                            <a href="{{ route('login') }}">@lang('控制台')</a>
                        @endauth
                        @if($Multilingual)
                            @if($locale=='en')
                                <a href="/?lang=zh">简体中文</a>
                            @else
                                <a href="/?lang=en">English</a>
                            @endif
                        @endif
                    </div>
                @endif
            </nav>

            <div class="content">
                <div class="title m-b-md text-black">
                    @lang("开放连接万事万物")
                </div>

                <div class="links">
                    <a href="https://www.yuque.com/shenwa/qingru" target="_blank">@lang('开发文档')</a>
                    <a href="https://www.yuque.com/shenwa/qingru/bggtv6hgvtf4ieun" target="_blank">@lang('更新日志')</a>
                    <a href="https://www.yuque.com/shenwa/qingru/ze9hby" target="_blank">@lang('使用指南')</a>
                    <a href="https://www.gxit.org/" target="_blank">@lang('技术支持')</a>
                    <a href="https://www.gxit.org/forum.php?mod=forumdisplay&fid=44" target="_blank">@lang('产品论坛')</a>
                    <a href="/console/module" target="_blank">@lang('应用市场')</a>
                    <a href="/console/report" target="_blank">@lang('workOrders')</a>
                </div>
            </div>
        </div>
    </body>
</html>
