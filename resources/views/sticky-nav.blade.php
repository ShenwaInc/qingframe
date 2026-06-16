<style>
    .sticky-nav {
        position: fixed;
        top: 0;
        left: 0;
        z-index: 999;
        width: 100%;
    }
    .sticky-nav , .sticky-nav .logo{
        transition: all .3s ease-out 0s;
    }
    .sticky-nav nav a{
        font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Ubuntu, Helvetica Neue, Helvetica, Arial, PingFang SC, Hiragino Sans GB, Microsoft YaHei UI, Microsoft YaHei, Source Han Sans CN, sans-serif;
        color: #262626;
        position: relative;
        transition: all .3s ease-out 0s;
    }
    .sticky-nav nav a:after{
        content: '';
        height: 0;
        transition: all .3s ease-out 0s;
        opacity: 0;
    }
    .active-menu, .sticky-nav nav a:hover {
        color: #3690C4 !important;
        font-weight: 600;
    }
    .active-menu::after{
        content: '';
        position: absolute;
        bottom: -16px;
        left: 0;
        width: 100%;
        height: 3px !important;
        background-color: #3690C4;
        opacity: 1 !important;
    }
    .sticky-nav.fixed nav a:after{
        bottom: -12px !important;
    }
    /* 下拉菜单样式 */
    .group:hover .dropdown-menu {
        opacity: 1;
        visibility: visible;
    }

    .dropdown-menu {
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    @media (min-width: 768px) {
        .hide-sm {
            display: none;
        }
    }
</style>
<header class="sticky-nav bg-white shadow-sm py-3 px-4 md:px-8">
    <div class="container mx-auto flex justify-between items-center">
        <!-- 顶部左侧Logo -->
        <div class="flex items-center">
            <a href="/#" title="轻如云开放平台 - 首页">
                <img src="{{ asset('static/tailwind/images/logo.png') }}" alt="@lang('轻如云系统')" class="h-12 w-auto logo" />
            </a>
        </div>
        <!-- 顶部右侧导航菜单 -->
        <nav class="hidden md:flex items-center space-x-1 md:space-x-6">
            <a href="/#" data-uuid="index" class="px-3 py-2 text-sm md:text-base {{ empty($curPage) || $curPage=='home' ? 'active-menu' : '' }}">
                首页
            </a>
            <a href="/market/#" data-uuid="app-store" class="px-3 py-2 text-sm md:text-base {{ $curPage=='market' ? 'active-menu' : '' }}">
                应用市场
            </a>
            <div class="relative group">
                <a href="javascript:void(0);" data-uuid="service-store" class="px-3 py-2 text-sm md:text-base flex items-center">
                    解决方案
                    <i class="fas fa-angle-down ml-1"></i>
                </a>
                <!-- 下拉菜单 -->
                <div class="absolute left-0 mt-6 w-60 bg-white shadow-lg rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                    <a href="https://v3.whotalk.com.cn/" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">社交IM即时通讯系统</a>
                    <a href="https://demo.gxswa.com/login/6" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">智慧健康管理系统</a>
                    <a href="https://demo.gxswa.com/login/2" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">智能化集成系统</a>
                    <a href="https://demo.gxswa.com/login/11" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">供应链电商系统</a>
                    <a href="https://demo.gxswa.com/login/18" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">AI智能体集成系统</a>
                    <a href="https://demo.gxswa.com/login/4" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">知识付费云点播系统</a>
                    <a href="/website/app/swa_account.html" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">轻记账-多人协同记账系统</a>
                </div>
            </div>
            <div class="relative group hide-sm">
                <a href="javascript:void(0)" data-uuid="website" class="px-3 py-2 text-sm md:text-base flex items-center">
                    集团官网
                    <i class="fas fa-angle-down ml-1"></i>
                </a>
                <!-- 下拉菜单 -->
                <div class="absolute left-0 mt-6 w-60 bg-white shadow-lg rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                    <a href="https://www.gxit.org/" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">广西神蛙网络科技有限公司</a>
                    <a href="https://binglan.gghs.cc/" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">广西冰蓝科技有限公司</a>
                    <a href="https://www.yingxiaogx.com/" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">广西慧云物联网科技有限公司</a>
                </div>
            </div>
            <a href="/#download" data-uuid="free-download" class="px-3 py-2 text-sm md:text-base">
                免费下载
            </a>
            <a href="https://www.yuque.com/shenwa/qingru" data-uuid="featured" target="_blank" class="px-3 py-2 text-sm md:text-base">
                文档
            </a>
            <a href="https://bbs.qingruyun.com/forum.php" data-uuid="featured" class="px-3 py-2 text-sm md:text-base hide-sm">
                社区
            </a>
            <div class="ml-2 relative group">
                @if($_W['uid']>0)
                    <a href="/console/user/profile">
                        <img src="{{ globalMedia($_W['user']['avatar']) }}" alt="{{ $_W['user']['username'] }}" class="w-8 h-8 rounded-full object-cover" />
                    </a>
                    <!-- 下拉菜单 -->
                    <div class="absolute left-[-50px] mt-6 w-60 bg-white shadow-lg rounded-md opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                        <a href="/console" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">@lang('控制台')</a>
                        <a href="/console/user/profile" target="_blank" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">@lang('账户管理')</a>
                        <hr />
                        <a href="javascript:Core.logout()" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">@lang('logout')</a>
                    </div>
                @else
                    <a href="/console/user/profile">
                        <img src="{{ asset('static/tailwind/images/avatar.png') }}" alt="@lang('登录')" class="w-8 h-8 rounded-full object-cover" />
                    </a>
                @endif
            </div>
        </nav>
        <!-- 移动端菜单按钮 -->
        <button class="md:hidden text-gray-600 focus:outline-none">
            <i class="fas fa-bars text-xl"> </i>
        </button>
    </div>
</header>