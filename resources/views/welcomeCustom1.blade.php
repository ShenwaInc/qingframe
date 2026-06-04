<html lang="zh-CN">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>轻如云开放平台 - 首页</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="keywords" content="SaaS应用, 多租户, 模块化, 微信商城, 公众号, 小程序, App开发, PHP框架, PHP建站, 微信小程序, 抖音小程序, PHP源码">
    <meta name="description" content="轻如云应用服务开发框架（QingFrame，简称轻如云系统）是一款多租户（SaaS）、模块化的WEB系统集成开放平台，基于主流跨平台开发框架 Laravel 开发；集成各类通讯协议（如HTTP、TCP、MQTT、WebSocket等）、聚合各类第三方服务和生态（如微信生态、阿里云生态、腾讯云生态、各大主流人工智能大模型等）、将多种复杂应用和服务集成为开箱即用的服务能力，向系统内的所有应用模块和租户提供服务，并对外提供标准开放接口。">
    <script src="{{ asset('static/tailwind/tailwind3.4.17.js') }}"></script>
    <script type="text/javascript" src="{{ assets('/static/js/jquery-1.11.1.min.js') }}?v={{ QingRelease }}"></script>
    <script type="text/javascript" src="{{ assets('/static/js/core.jquery.js') }}?v={{ QingRelease }}"></script>
    <link href="{{ asset('static/tailwind/css/font-awesome.all.css') }}?v=1.1" rel="stylesheet" />
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              primary: '#3690C4',
              secondary: '#5260A1',
              dark: '#1A1A1A',
              light: '#F5F7FA'
            },
            fontFamily: {
              sans: ['-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'sans-serif']
            },
            spacing: {
              '128': '32rem',
              '144': '36rem'
            }
          }
        }
      };
    </script>
    <style type="text/tailwindcss">
      @layer utilities {
          .content-auto {
              content-visibility: auto;
          }
          .text-shadow {
              text-shadow: 0 2px 4px rgba(0,0,0,0.1);
          }
          .transition-transform-opacity {
              transition-property: transform, opacity;
          }
          .scrollbar-hide::-webkit-scrollbar {
              display: none;
          }
          .scrollbar-hide {
              -ms-overflow-style: none;
              scrollbar-width: none;
          }
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
      }
    </style>
    <style>
      @media (min-width: 1536px) {
        .container {
          max-width: 1400px !important;
        }
      }
    </style>
  </head>
  <body class="font-sans bg-white text-dark overflow-x-hidden">
    <header class="sticky-nav bg-white shadow-sm py-3 px-4 md:px-8">
      <div class="container mx-auto flex justify-between items-center">
          <!-- 顶部左侧Logo -->
          <div class="flex items-center">
              <a href="/" title="轻如云开放平台 - 首页">
                  <img src="{{ asset('static/tailwind/images/logo.png') }}" alt="@lang('轻如云系统')" class="h-12 w-auto logo" />
              </a>
          </div>
          <!-- 顶部右侧导航菜单 -->
          <nav class="hidden md:flex items-center space-x-1 md:space-x-6">
              <a href="/" data-uuid="index" class="active-menu px-3 py-2 text-sm md:text-base">
                  首页
              </a>
              <a href="/console/setting/market" data-uuid="app-store" target="_blank" class="px-3 py-2 text-sm md:text-base">
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
            <div class="relative group">
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
              <a href="https://bbs.qingruyun.com/forum.php" data-uuid="featured" class="px-3 py-2 text-sm md:text-base">
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
    <!-- 首屏轮播广告区 -->
    <section id="hero" class="relative w-full h-screen overflow-hidden">
      <div class="carousel-container relative h-full w-full">
        <!-- 轮播图1 -->
        <div class="carousel-slide absolute inset-0 opacity-100 transition-all duration-1000 ease-in-out">
          <div class="absolute inset-0 bg-gradient-to-r from-dark/60 to-dark/20 z-10"></div>
          <img src="/static/images/page-top.png" alt="轻如云开放平台" class="w-full h-full object-cover" />
          <div class="absolute inset-0 z-20 flex items-center">
            <div class="container mx-auto px-6 md:px-12">
              <div class="max-w-2xl">
                <h1 class="text-[clamp(2.5rem,5vw,4rem)] font-bold text-white leading-tight mb-4 text-shadow">
                  轻如云开放平台
                </h1>
                <p class="text-[clamp(1rem,2vw,1.25rem)] text-white/90 mb-8 max-w-xl">
                  连接服务与创新，打造无缝集成的应用生态系统
                </p>
                <div class="flex flex-wrap gap-4">
                  <a href="/console" class="bg-primary hover:bg-primary/90 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 transform hover:scale-105">
                    开始探索
                  </a>
                  <a href="https://www.yuque.com/shenwa/qingru" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 border border-white/30">
                    了解更多
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 轮播图2 -->
        <div class="carousel-slide absolute inset-0 opacity-0 transition-all duration-1000 ease-in-out">
          <div class="absolute inset-0 bg-gradient-to-r from-dark/60 to-dark/20 z-10"></div>
          <img src="https://www.qingruyun.com/storage/images/0/2026/02/TfXBLN70It9UpryZap8GtR7zAtuSKdV6.png" alt="多平台集成" class="w-full h-full object-cover" />
          <div class="absolute inset-0 z-20 flex items-center">
            <div class="container mx-auto px-6 md:px-12">
              <div class="max-w-2xl">
                <h1 class="text-[clamp(2.5rem,5vw,4rem)] font-bold text-white leading-tight mb-4 text-shadow">
                  一站式集成解决方案
                </h1>
                <p class="text-[clamp(1rem,2vw,1.25rem)] text-white/90 mb-8 max-w-xl">
                  轻松连接各类服务与协议，构建强大的应用生态系统
                </p>
                <div class="flex flex-wrap gap-4">
                  <a href="/console/setting/market" target="_blank" class="bg-primary hover:bg-primary/90 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 transform hover:scale-105">
                    应用市场
                  </a>
                  <a href="https://bbs.qingruyun.com/home.php" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 border border-white/30">
                    开发者中心
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 轮播图3 -->
        <div class="carousel-slide absolute inset-0 opacity-0 transition-all duration-1000 ease-in-out">
          <div class="absolute inset-0 bg-gradient-to-r from-dark/60 to-dark/20 z-10"></div>
          <img src="https://www.qingruyun.com/storage/images/0/2026/02/BNGjHWNasghjj6FiXdrPNjhBaV6n1CNR.png" alt="云服务平台" class="w-full h-full object-cover" />
          <div class="absolute inset-0 z-20 flex items-center">
            <div class="container mx-auto px-6 md:px-12">
              <div class="max-w-2xl">
                <h1 class="text-[clamp(2.5rem,5vw,4rem)] font-bold text-white leading-tight mb-4 text-shadow">
                  赋能开发者创新
                </h1>
                <p class="text-[clamp(1rem,2vw,1.25rem)] text-white/90 mb-8 max-w-xl">
                  开放的生态系统，让创意快速转化投入生产
                </p>
                <div class="flex flex-wrap gap-4">
                  <a href="https://bbs.qingruyun.com/member.php?mod=register" target="_blank" class="bg-primary hover:bg-primary/90 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 transform hover:scale-105">
                    开发者注册
                  </a>
                  <a href="https://www.yuque.com/shenwa/qingru/dae7wpppwv8lhzrz" target="_blank" class="bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 border border-white/30">
                    应用开发手册
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- 轮播控制按钮 -->
        <div
          class="absolute bottom-8 left-8 z-30 flex justify-center gap-3"
        >
          <button
            class="carousel-dot w-8 h-[5px] rounded-sm bg-white opacity-100 transition-all duration-300"
            data-index="0"
          ></button>
          <button
            class="carousel-dot w-8 h-[5px] rounded-sm bg-white opacity-50 transition-all duration-300"
            data-index="1"
          ></button>
          <button
            class="carousel-dot w-8 h-[5px] rounded-sm bg-white opacity-50 transition-all duration-300"
            data-index="2"
          ></button>
        </div>
      </div>
    </section>
    <!-- 第二屏：平台定位 -->
    <section id="platform定位" class="py-24 md:py-32 bg-light">
      <div class="container mx-auto px-6 md:px-12">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold text-dark mb-4">
            轻如云开放平台
          </h2>
          <div class="w-20 h-1 bg-primary mx-auto mb-6"></div>
          <p class="text-gray-600 text-lg">
            轻如云开放平台是一款多租户（SaaS）、模块化的WEB系统集成开放平台，连接各类服务与生态，打造集中管理、分散风险的业务中台生态体验。通过提供多协议集成、云服务聚合、微服务与模块化设计，轻如云开放平台帮助企业快速构建和部署业务中台，提升效率和竞争力。
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12">
          <!-- 定位特点1 -->
          <div
            class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1"
          >
            <div
              class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mb-6"
            >
              <i class="fas fa-plug text-primary text-2xl"> </i>
            </div>
            <h3 class="text-xl font-bold mb-3">多协议集成</h3>
            <p class="text-gray-600">
              集成 HTTP、TCP、MQTT、WebSocket、WebRTC、BG28181 等各类通讯协议，轻松连接不同系统与服务。
            </p>
          </div>
          <!-- 定位特点2 -->
          <div
            class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1"
          >
            <div
              class="w-16 h-16 bg-secondary/10 rounded-lg flex items-center justify-center mb-6"
            >
              <i class="fas fa-cloud text-secondary text-2xl"> </i>
            </div>
            <h3 class="text-xl font-bold mb-3">聚合云服务生态</h3>
            <p class="text-gray-600">
              聚合阿里云、腾讯云、Amazon AWS、华为云、微信生态、Dcloud生态等第三方云服务商，打造一站式云服务体验。
            </p>
          </div>
          <!-- 定位特点3 -->
          <div
            class="bg-white p-8 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1"
          >
            <div
              class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mb-6"
            >
              <i class="fas fa-cubes text-primary text-2xl"> </i>
            </div>
            <h3 class="text-xl font-bold mb-3">微服务 & 模块化</h3>
            <p class="text-gray-600">
              将各类服务集成为开箱即用的微服务，为所有应用模块和租户提供标准化服务接口。
            </p>
          </div>
        </div>
        <div class="mt-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h3 class="text-2xl md:text-3xl font-bold mb-6">
              为开发者打造的开放生态
            </h3>
            <p class="text-gray-600 mb-6">
              轻如云系统基于主流跨平台开发框架Laravel开发，提供开放的应用模块开发规范，供所有开发者在开放平台上开发、分发应用。
            </p>
            <ul class="space-y-4">
              <li class="flex items-start">
                <i class="fas fa-check-circle text-primary mt-1 mr-3"> </i>
                <span> 提供标准开放接口，供外部程序调用 </span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-primary mt-1 mr-3"> </i>
                <span> 支持多租户模式，满足不同用户需求 </span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-primary mt-1 mr-3"> </i>
                <span> 微服务+模块化设计，灵活扩展功能 </span>
              </li>
              <li class="flex items-start">
                <i class="fas fa-check-circle text-primary mt-1 mr-3"> </i>
                <span> 高度稳定与安全，保障服务可靠运行 </span>
              </li>
            </ul>
          </div>
          <div class="relative">
            <div
              class="absolute -top-6 -left-6 w-64 h-64 bg-primary/5 rounded-full blur-3xl"
            ></div>
            <div
              class="absolute -bottom-6 -right-6 w-64 h-64 bg-secondary/5 rounded-full blur-3xl"
            ></div>
            <img
              src="https://design.gemcoder.com/staticResource/echoAiSystemImages/88321adb74e9f61e9d0a2e47287a6eea.png"
              alt="开发者平台界面"
              class="w-full h-auto rounded-xl shadow-lg relative z-10"
            />
          </div>
        </div>
      </div>
    </section>
    <!-- 第三屏：集成能力 -->
    <section id="popular-apps" class="py-24 md:py-32 bg-white">
      <div class="container mx-auto px-6 md:px-12">
        <div
          class="flex flex-col md:flex-row md:items-end justify-between mb-16"
        >
          <div>
            <h2
              class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold text-dark mb-4"
            >
              集成能力
            </h2>
            <div class="w-20 h-1 bg-primary mb-6"></div>
            <p class="text-gray-600 max-w-2xl">
              轻如云开放平台深度集成多个开放平台，提供开箱即用的服务能力。
            </p>
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
          <!-- 集成微信生态 -->
          <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate transition-all duration-700 ease-out animated opacity-100 translate-y-0">
            <div class="h-80 overflow-hidden">
              <img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/84d1ae028f855f214d29f89ca4e6b5ed.png" alt="微信生态集成" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-6">
              <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                  <i class="fab fa-weixin text-green-500"> </i>
                </div>
                <h3 class="font-bold text-lg">集成微信生态</h3>
              </div>
              <p class="text-gray-600 text-sm">
                深度集成微信公众平台、微信开放平台、微信支付和企业微信能力
              </p>
            </div>
          </div>
          <!-- 阿里云生态 -->
          <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate transition-all duration-700 ease-out animated opacity-100 translate-y-0">
            <div class="h-80 overflow-hidden">
              <img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/c413684a10599dd3b4b5f8f9fb69afee.png" alt="阿里云服务套件" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-6">
              <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                  <i class="fas fa-cloud text-blue-500"> </i>
                </div>
                <h3 class="font-bold text-lg">集成阿里云、腾讯云服务</h3>
              </div>
              <p class="text-gray-600 text-sm">
                深度集成阿里云和腾讯云生态服务，包括存储、云计算、音视频、内容审核等全方位云服务
              </p>
            </div>
          </div>
          <!-- AI智能助手 -->
          <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate transition-all duration-700 ease-out animated opacity-100 translate-y-0">
            <div class="h-80 overflow-hidden">
              <img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/349ebccd3496c2b36b5733001d7d0948.png" alt="AI智能助手" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-6">
              <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                  <i class="fas fa-brain text-purple-500"> </i>
                </div>
                <h3 class="font-bold text-lg">AI智能助手</h3>
              </div>
              <p class="text-gray-600 text-sm">
                整合多家AI服务提供商，提供智能分析、自然语言处理等能力
              </p>
            </div>
          </div>
          <!-- 物联网集成平台 -->
          <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate transition-all duration-700 ease-out animated opacity-100 translate-y-0">
            <div class="h-80 overflow-hidden">
              <img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/9a54f2d82d20e23004582a8f54b2a732.png" alt="物联网集成平台" class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
            </div>
            <div class="p-6">
              <div class="flex items-center mb-3">
                <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-3">
                  <i class="fas fa-microchip text-teal-500"> </i>
                </div>
                <h3 class="font-bold text-lg">物联网集成平台</h3>
              </div>
              <p class="text-gray-600 text-sm">
                连接各类智能硬件设备，实现数据采集、分析与远程控制
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- 第四屏：平台机制 -->
    <section id="platform-mechanism" class="py-24 md:py-32 bg-dark text-white">
      <div class="container mx-auto px-6 md:px-12">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-4">
            平台机制
          </h2>
          <div class="w-20 h-1 bg-primary mx-auto mb-6"></div>
          <p class="text-gray-300 text-lg">
            轻如云开放平台通过先进的技术架构与开放机制，为开发者与用户提供卓越的服务体验。
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center mb-24">
          <div class="order-2 md:order-1">
            <h3 class="text-2xl md:text-3xl font-bold mb-6">多租户SaaS架构</h3>
            <p class="text-gray-300 mb-6">
              轻如云系统采用多租户架构设计，允许不同组织和用户在同一平台上拥有独立的应用环境，同时共享基础设施，降低成本并提高资源利用率。
            </p>
            <div class="space-y-4">
              <div class="flex items-start">
                <div
                  class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1 mr-4"
                >
                  <span class="text-primary font-bold"> 1 </span>
                </div>
                <div>
                  <h4 class="font-bold mb-1">数据隔离</h4>
                  <p class="text-gray-400 text-sm">
                    确保各租户数据安全隔离，保护隐私与商业机密
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div
                  class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1 mr-4"
                >
                  <span class="text-primary font-bold"> 2 </span>
                </div>
                <div>
                  <h4 class="font-bold mb-1">定制配置</h4>
                  <p class="text-gray-400 text-sm">
                    支持租户级别的个性化配置，满足不同业务需求
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div
                  class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1 mr-4"
                >
                  <span class="text-primary font-bold"> 3 </span>
                </div>
                <div>
                  <h4 class="font-bold mb-1">统一更新</h4>
                  <p class="text-gray-400 text-sm">
                    平台统一更新维护，租户无需担心技术升级
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="order-1 md:order-2 relative">
            <div
              class="absolute -top-10 -right-10 w-64 h-64 bg-primary/10 rounded-full blur-3xl"
            ></div>
            <img
              src="https://design.gemcoder.com/staticResource/echoAiSystemImages/8968e2e9afbfb3c8ae86d703e98fc494.png"
              alt="多租户SaaS架构"
              class="w-full h-auto rounded-xl shadow-xl relative z-10"
            />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
          <div class="relative">
            <div
              class="absolute -bottom-10 -left-10 w-64 h-64 bg-secondary/10 rounded-full blur-3xl"
            ></div>
            <img
              src="https://design.gemcoder.com/staticResource/echoAiSystemImages/3fc742c45cee64d07b3afe05bea61291.png"
              alt="模块化微服务"
              class="w-full h-auto rounded-xl shadow-xl relative z-10"
            />
          </div>
          <div>
            <h3 class="text-2xl md:text-3xl font-bold mb-6">模块化微服务</h3>
            <p class="text-gray-300 mb-6">
              将各类服务集成为即装即用的微服务，为平台内所有应用模块和租户提供标准化服务，同时对外提供开放接口。
            </p>
            <div class="space-y-4">
              <div class="flex items-start">
                <div
                  class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center flex-shrink-0 mt-1 mr-4"
                >
                  <span class="text-secondary font-bold"> 1 </span>
                </div>
                <div>
                  <h4 class="font-bold mb-1">即插即用</h4>
                  <p class="text-gray-400 text-sm">
                    服务模块一键安装，快速扩展平台功能
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div
                  class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center flex-shrink-0 mt-1 mr-4"
                >
                  <span class="text-secondary font-bold"> 2 </span>
                </div>
                <div>
                  <h4 class="font-bold mb-1">标准接口</h4>
                  <p class="text-gray-400 text-sm">
                    统一API设计，降低集成难度与学习成本
                  </p>
                </div>
              </div>
              <div class="flex items-start">
                <div
                  class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center flex-shrink-0 mt-1 mr-4"
                >
                  <span class="text-secondary font-bold"> 3 </span>
                </div>
                <div>
                  <h4 class="font-bold mb-1">独立扩展</h4>
                  <p class="text-gray-400 text-sm">
                    各服务模块独立扩展，保障系统稳定性
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- 第五屏：平台概述与核心优势 -->
    <section id="platform-overview" class="py-10 bg-white">
      <div class="container mx-auto px-6 md:px-12">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold text-dark mb-4">
            平台概述与核心优势
          </h2>
          <div class="w-20 h-1 bg-primary mx-auto mb-6"></div>
          <p class="text-gray-600 text-lg">
            轻如云开放平台提供强大的集成能力与开放生态，助力企业与开发者快速构建创新应用。
          </p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center mb-24">
          <div>
            <h3 class="text-2xl md:text-3xl font-bold mb-6">平台概述</h3>
            <p class="text-gray-600 mb-6">
              轻如云开放平台是一款多租户（SaaS）、模块化的WEB系统集成开放平台，基于主流跨平台开发框架Laravel开发。
            </p>
            <p class="text-gray-600 mb-8">
              凭借轻如云开放平台，运营者可以快速上线产品投入市场，省去大量开发时间和系统维护成本；开发者可以专注于业务逻辑研发，快速构建和迭代产品。
            </p>
            <div class="bg-light p-6 rounded-xl">
              <h4 class="font-bold mb-4 flex items-center">
                <i class="fas fa-check-circle text-primary mr-2"> </i>
                广泛的应用场景
              </h4>
              <div class="grid grid-cols-2 gap-4">
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-angle-right text-primary mr-2"> </i>
                  CRM系统
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-angle-right text-primary mr-2"> </i>
                  CMS系统
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-angle-right text-primary mr-2"> </i>
                  ERP系统
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-angle-right text-primary mr-2"> </i>
                  OA系统
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-angle-right text-primary mr-2"> </i>
                  电商平台
                </div>
                <div class="flex items-center text-gray-600">
                  <i class="fas fa-angle-right text-primary mr-2"> </i>
                  物联网平台
                </div>
              </div>
            </div>
          </div>
          <div class="relative">
            <div
              class="absolute -top-6 -right-6 w-64 h-64 bg-primary/5 rounded-full blur-3xl"
            ></div>
            <div
              class="absolute -bottom-6 -left-6 w-64 h-64 bg-secondary/5 rounded-full blur-3xl"
            ></div>
            <img
              src="https://design.gemcoder.com/api/searchImage?query=cloud%20platform%20dashboard%20with%20multiple%20service%20integrations,%20modern%20minimalist%20design,%20professional%20UI%20screenshot,%20white%20background&width=800&height=600"
              alt="轻如云平台控制台"
              class="w-full h-auto rounded-xl shadow-lg relative z-10"
            />
          </div>
        </div>
        <h3 class="text-2xl md:text-3xl font-bold mb-12 text-center">
          核心优势
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <!-- 优势类别1 -->
          <div>
            <div
              class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mb-6 mx-auto"
            >
              <i class="fas fa-exchange-alt text-primary text-2xl"> </i>
            </div>
            <h4 class="text-xl font-bold text-center mb-6">多种通讯协议</h4>
            <ul class="space-y-3">
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                HTTP
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                TCP
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                MQTT
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                WebSocket
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                BG28181
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                WebRTC
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                ModBUS
              </li>
            </ul>
          </div>
          <!-- 优势类别2 -->
          <div>
            <div
              class="w-16 h-16 bg-secondary/10 rounded-lg flex items-center justify-center mb-6 mx-auto"
            >
              <i class="fas fa-cloud text-secondary text-2xl"> </i>
            </div>
            <h4 class="text-xl font-bold text-center mb-6">开放平台集成</h4>
            <ul class="space-y-3">
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                微信生态
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                阿里云生态
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                腾讯云生态
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                Dcloud生态
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                AI大模型
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                支付系统
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-secondary mr-2"> </i>
                更多第三方服务
              </li>
            </ul>
          </div>
          <!-- 优势类别3 -->
          <div>
            <div
              class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mb-6 mx-auto"
            >
              <i class="fas fa-rocket text-primary text-2xl"> </i>
            </div>
            <h4 class="text-xl font-bold text-center mb-6">开放能力</h4>
            <ul class="space-y-3">
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                SaaS能力
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                应用服务拓展
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                硬件生态集成
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                开放API接口
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                模块化开发
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                多租户支持
              </li>
              <li class="flex items-center justify-center text-gray-600">
                <i class="fas fa-check text-primary mr-2"> </i>
                安全可靠
              </li>
            </ul>
          </div>
        </div>
        <div class="mt-20 text-center" id="download">
          <a
            href="/console"
            class="inline-flex items-center bg-primary hover:bg-primary/90 text-white font-medium py-3 px-8 rounded-md transition-all duration-300 transform hover:scale-105"
          >
            开始使用轻如云平台
            <i class="fas fa-arrow-right ml-2"> </i>
          </a>
        </div>
      </div>
    </section>
    <!-- [MODULE] c4e_下载页面:页面标题 -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 md:py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-4">
                    获取最新版系统
                </h1>
                <p class="text-lg md:text-xl text-blue-100 mb-8">
                    选择适合您的安装方式，快速部署并开始使用我们的系统
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
              <span
                      class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-700 text-blue-100"
              >
                <i class="fas fa-check-circle mr-1"> </i>
                最新版本: v1.9.20
              </span>
                    <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-700 text-blue-100"
                    >
                <i class="fas fa-calendar-alt mr-1"> </i>
                更新日期: 2026-03-10
              </span>
                </div>
            </div>
        </div>
    </div>
    <!-- [/MODULE] c4e_下载页面:页面标题 -- 包含页面标题、副标题和版本信息 -->
    <!-- [MODULE] d5f_下载页面:主要下载区域 -->
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="max-w-5xl mx-auto">
            <!-- 主要下载卡片 -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-12 transform transition-all duration-300 hover:shadow-xl">
                <div class="md:flex">
                    <!-- 左侧图片区域 -->
                    <div class="md:w-1/3 bg-gradient-to-br from-blue-50 to-indigo-50 flex items-center justify-center p-8">
                        <img src="{{ asset('static/icon200.jpg') }}" alt="系统安装包" class="h-40 w-40 object-contain rounded-xl"/>
                    </div>
                    <!-- 右侧内容区域 -->
                    <div class="md:w-2/3 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">
                            轻如云开放平台
                        </h2>
                        <p class="text-gray-600 mb-6">
                            一款多租户（SaaS）、模块化的WEB系统集成开放平台
                        </p>
                        <!-- 操作按钮 -->
                        <div class="flex flex-wrap gap-4">
                            <!-- 免费下载按钮 -->
                            <a href="https://swarelease.oss-cn-shenzhen.aliyuncs.com/r/master/qingframe-laster-full.zip" class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5" target="_blank">
                                <i class="fas fa-download mr-2"> </i>
                                免费下载
                            </a>
                            <!-- 安装手册按钮 -->
                            <a href="https://www.yuque.com/shenwa/qingru/ze9hby" class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-md shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200" target="_blank">
                                <i class="fas fa-file-alt mr-2"> </i>
                                安装手册
                            </a>
                        </div>
                        <!-- 说明文字 -->
                        <div class="mt-6 text-sm text-gray-500">
                            <p class="flex items-start">
                                <i class="fas fa-info-circle mt-1 mr-2 text-blue-500"> </i>
                                <span>
                      文件大小: 44.8 MB | 支持系统: Linux / Windows / Mac |
                      校验码:
                      <code class="bg-gray-100 px-1 py-0.5 rounded">
                        sha256:
                        bad67707dd3d0069d69301c248cf69c73b75843af04585744ec6b09be4880645
                      </code>
                    </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 其他安装方式推荐 -->
            <h2 class="text-2xl font-bold text-gray-900 mb-6">其他安装方式</h2>
            <div class="grid md:grid-cols-2 gap-8">
                <!-- 命令行安装 -->
                <div class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg">
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-terminal text-blue-600 text-xl"> </i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                命令行安装
                            </h3>
                            <p class="text-gray-600 mt-1">
                                适用于开发人员和服务器环境的快速安装方式
                            </p>
                        </div>
                    </div>
                    <div
                            class="bg-gray-50 rounded-lg p-4 font-mono text-sm overflow-x-auto"
                    >
                <pre>
#  进入站点根目录（具体以您的站点路径为准）
cd /www/wwwroot/yourdomain/
wget https://dl.gxswa.com/sh/install.sh && sh install.sh
</pre>
                    </div>
                    <div class="mt-4">
                        <a href="https://www.yuque.com/shenwa/qingru/ze9hby#MYRse" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center transition-colors duration-200">
                            查看完整安装文档
                            <i class="fas fa-arrow-right ml-1"> </i>
                        </a>
                    </div>
                </div>
                <!-- 宝塔面板一键部署 -->
                <div
                        class="bg-white rounded-xl shadow-md p-6 transition-all duration-300 hover:shadow-lg"
                >
                    <div class="flex items-start mb-4">
                        <div class="flex-shrink-0 bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-server text-green-600 text-xl"> </i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">
                                宝塔面板一键部署
                            </h3>
                            <p class="text-gray-600 mt-1">
                                通过宝塔面板快速部署到您的服务器
                            </p>
                        </div>
                    </div>
                    <ul class="space-y-2 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"> </i>
                            <span> 在宝塔面板软件商店【一键部署】搜索"轻如云开放平台" </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"> </i>
                            <span> 点击"一键安装"按钮 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-2"> </i>
                            <span> 按照引导完成配置 </span>
                        </li>
                    </ul>
                    <div class="mt-4">
                        <a href="https://www.yuque.com/shenwa/qingru/ze9hby#Oh98P" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center transition-colors duration-200">
                            查看宝塔部署教程
                            <i class="fas fa-arrow-right ml-1"> </i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [/MODULE] d5f_下载页面:主要下载区域 -- 包含桌面客户端下载区和其他安装方式推荐区，提供多种系统和安装方式选择 -->
    <!-- [MODULE] e6g_页面底部 -->
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">关于我们</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>
                            <a href="https://www.gxit.org/portal.php" target="_blank" class="hover:text-white transition-colors duration-200">公司官网</a>
                        </li>
                        <li>
                            <a href="https://www.gxit.org/portal.php" target="_blank" class="hover:text-white transition-colors duration-200">
                                团队成员
                            </a>
                        </li>
                        <li>
                            <a href="https://www.gxit.org/portal.php" target="_blank" class="hover:text-white transition-colors duration-200">
                                新闻动态
                            </a>
                        </li>
                        <li>
                            <a href="https://www.gxit.org/portal.php" target="_blank" class="hover:text-white transition-colors duration-200">
                                招贤纳士
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">帮助中心</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>
                            <a href="https://www.yuque.com/shenwa/qingru/gqea6plg3g4w99c5" target="_blank" class="hover:text-white transition-colors duration-200">
                                常见问题
                            </a>
                        </li>
                        <li>
                            <a href="https://www.yuque.com/shenwa/qingru/wq6gs0omqb3gb82h" target="_blank" class="hover:text-white transition-colors duration-200">
                                使用指南
                            </a>
                        </li>
                        <li>
                            <a href="https://www.yuque.com/shenwa/qingru/xsi1e1p9d59k5981" target="_blank" class="hover:text-white transition-colors duration-200">
                                开发指南
                            </a>
                        </li>
                        <li>
                            <a href="https://www.gxit.org/portal.php#aboutus" target="_blank" class="hover:text-white transition-colors duration-200">
                                联系支持
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">法律信息</h3>
                    <ul class="space-y-2 text-gray-400">
                        <li>
                            <a href="https://www.yuque.com/shenwa/qingru/cazasygc40bkq1qy" target="_blank" class="hover:text-white transition-colors duration-200">
                                服务条款
                            </a>
                        </li>
                        <li>
                            <a href="https://www.yuque.com/shenwa/qingru/cazasygc40bkq1qy#vP02U" target="_blank" class="hover:text-white transition-colors duration-200">
                                隐私政策
                            </a>
                        </li>
                        <li>
                            <a href="https://www.yuque.com/shenwa/qingru/hn3pu0zgzeflgghh" target="_blank" class="hover:text-white transition-colors duration-200">
                                版权声明
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="hover:text-white transition-colors duration-200">
                                许可协议
                            </a>
                        </li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">联系我们</h3>
                    <div class="flex space-x-4 mb-4">
                        <a href="javascript:void(0);" class="bg-gray-800 p-2 rounded-full text-gray-400 hover:bg-blue-600 hover:text-white transition-all duration-200">
                            <i class="fab fa-weixin"> </i>
                        </a>
                        <a href="javascript:void(0);" class="bg-gray-800 p-2 rounded-full text-gray-400 hover:bg-blue-400 hover:text-white transition-all duration-200">
                            <i class="fab fa-weibo"> </i>
                        </a>
                        <a href="javascript:void(0);" class="bg-gray-800 p-2 rounded-full text-gray-400 hover:bg-gray-700 hover:text-white transition-all duration-200">
                            <i class="fab fa-github"> </i>
                        </a>
                        <a href="javascript:void(0);" class="bg-gray-800 p-2 rounded-full text-gray-400 hover:bg-blue-700 hover:text-white transition-all duration-200">
                            <i class="fab fa-twitter"> </i>
                        </a>
                    </div>
                    <p class="text-gray-400 text-sm">手机号：18076579452</p>
                    <p class="text-gray-400 text-sm">与我们取得联系，获取产品最新动态和开发支持</p>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm">©2025 All rights reserved. 广西神蛙网络科技有限公司 保留所有权利.</p>
                <div class="mt-4 md:mt-0">
                </div>
            </div>
        </div>
    </footer>
    <!-- [/MODULE] e6g_页面底部 -- 包含网站导航、法律信息、社交媒体链接和订阅功能 -->
    <script>
      // 轮播图功能
      document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('.carousel-slide');
        var dots = document.querySelectorAll('.carousel-dot');
        var currentIndex = 0;
        var slideInterval;

        // 初始化轮播
        function initCarousel() {
          showSlide(currentIndex);
          startSlideInterval();

          // 点击指示器切换轮播
          dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
              currentIndex = parseInt(dot.dataset.index);
              showSlide(currentIndex);
              resetSlideInterval();
            });
          });
        }

        // 显示指定轮播
        function showSlide(index) {
          slides.forEach(function (slide, i) {
            slide.style.opacity = i === index ? '1' : '0';
          });
          dots.forEach(function (dot, i) {
            dot.style.opacity = i === index ? '1' : '0.5';
          });
        }

        // 开始轮播定时器
        function startSlideInterval() {
          slideInterval = setInterval(function () {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
          }, 5000);
        }

        // 重置轮播定时器
        function resetSlideInterval() {
          clearInterval(slideInterval);
          startSlideInterval();
        }

        // 初始化轮播
        initCarousel();
      });
      window.addEventListener('scroll', function (e) {
          const scrollTop =
              window.pageYOffset ||  // 现代浏览器
              document.documentElement.scrollTop ||  // IE/Edge
              document.body.scrollTop ||  // 旧版浏览器
              0;
          let header = jQuery('.sticky-nav'), hasClass = header.hasClass('fixed');

          if(scrollTop>0){
              if(!hasClass){
                  header.addClass('fixed').find('img.logo').addClass('h-10').removeClass('h-12');
              }
          }else{
              if(hasClass){
                  header.removeClass('fixed').find('img.logo').addClass('h-12').removeClass('h-10');
              }
          }
      });
    </script>
  </body>
</html>