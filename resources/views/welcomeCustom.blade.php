<html lang="zh-CN">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>轻如云开放平台 - 应用分发平台</title>
    <script src="https://res.gemcoder.com/js/reload.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
            href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css"
            rel="stylesheet"
    />
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
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'San Francisco', 'Helvetica Neue', 'sans-serif']
                    },
                    height: {
                        'screen-90': '90vh'
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
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .transition-transform-opacity {
                transition-property: transform, opacity;
            }
        }
    </style>
</head>
<body class="font-sans bg-white text-dark overflow-x-hidden">
<!-- [MODULE] a1b_顶部导航栏 -->
<header
        id="navbar"
        class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 bg-transparent"
>
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 md:h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="javascript:void(0);" class="flex items-center">
                    <i class="fas fa-cloud text-primary text-2xl mr-2"> </i>
                    <span class="text-white font-bold text-xl tracking-tight">
                轻如云
              </span>
                </a>
            </div>
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8">
                <a
                        href="javascript:void(0);"
                        class="text-white font-medium hover:text-primary transition-colors border-b-2 border-primary py-5"
                >
                    首页
                </a>
                <a
                        href="javascript:void(0);"
                        class="text-white/80 font-medium hover:text-primary transition-colors py-5"
                >
                    应用市场
                </a>
                <a
                        href="javascript:void(0);"
                        class="text-white/80 font-medium hover:text-primary transition-colors py-5"
                >
                    服务市场
                </a>
                <a
                        href="javascript:void(0);"
                        class="text-white/80 font-medium hover:text-primary transition-colors py-5"
                >
                    免费下载
                </a>
                <a
                        href="javascript:void(0);"
                        class="text-white/80 font-medium hover:text-primary transition-colors py-5"
                >
                    精选推荐
                </a>
            </nav>
            <!-- User Profile -->
            <div class="hidden md:block">
                <a href="javascript:void(0);" class="flex items-center">
                    <div
                            class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center"
                    >
                        <i class="fas fa-user text-white text-sm"> </i>
                    </div>
                </a>
            </div>
            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button
                        id="mobile-menu-button"
                        class="text-white focus:outline-none"
                >
                    <i class="fas fa-bars text-xl"> </i>
                </button>
            </div>
        </div>
    </div>
    <!-- Mobile Navigation -->
    <div
            id="mobile-menu"
            class="hidden md:hidden bg-dark/95 backdrop-blur-md"
    >
        <div class="px-4 pt-2 pb-4 space-y-3">
            <a
                    href="javascript:void(0);"
                    class="block text-white font-medium hover:text-primary transition-colors py-3 border-l-4 border-primary pl-3"
            >
                首页
            </a>
            <a
                    href="javascript:void(0);"
                    class="block text-white/80 font-medium hover:text-primary transition-colors py-3 border-l-4 border-transparent hover:border-primary/50 pl-3"
            >
                应用市场
            </a>
            <a
                    href="javascript:void(0);"
                    class="block text-white/80 font-medium hover:text-primary transition-colors py-3 border-l-4 border-transparent hover:border-primary/50 pl-3"
            >
                服务市场
            </a>
            <a
                    href="javascript:void(0);"
                    class="block text-white/80 font-medium hover:text-primary transition-colors py-3 border-l-4 border-transparent hover:border-primary/50 pl-3"
            >
                免费下载
            </a>
            <a
                    href="javascript:void(0);"
                    class="block text-white/80 font-medium hover:text-primary transition-colors py-3 border-l-4 border-transparent hover:border-primary/50 pl-3"
            >
                精选推荐
            </a>
            <div class="pt-3 border-t border-white/10">
                <a
                        href="javascript:void(0);"
                        class="flex items-center text-white/80 py-2"
                >
                    <div
                            class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center mr-3"
                    >
                        <i class="fas fa-user text-white text-sm"> </i>
                    </div>
                    <span> 账户 </span>
                </a>
            </div>
        </div>
    </div>
</header>
<!-- [/MODULE] a1b_顶部导航栏 -- 包含logo、导航菜单和用户头像，具有吸顶效果和移动端适配 -->
<!-- [MODULE] c3d_主内容区域 -->
<main>
    <!-- [MODULE] e5f_首屏广告展示屏 -->
    <section id="hero" class="relative h-screen bg-dark overflow-hidden">
        <div class="absolute inset-0 z-0">
            <div class="carousel-container h-full">
                <!-- 轮播图1 -->
                <div
                        class="carousel-slide absolute inset-0 opacity-100 transition-opacity duration-1000 ease-in-out"
                >
                    <img
                            src="https://design.gemcoder.com/staticResource/echoAiSystemImages/fc265ec0ed3639d875802a6d30b725bf.png"
                            alt="轻如云开放平台"
                            class="w-full h-full object-cover opacity-50"
                    />
                    <div
                            class="absolute inset-0 bg-gradient-to-r from-dark/80 to-dark/40"
                    ></div>
                </div>
                <!-- 轮播图2 -->
                <div
                        class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out"
                >
                    <img
                            src="https://design.gemcoder.com/staticResource/echoAiSystemImages/8f5129cabeb55a48f22dc1c547cf12a5.png"
                            alt="丰富的应用生态"
                            class="w-full h-full object-cover opacity-50"
                    />
                    <div
                            class="absolute inset-0 bg-gradient-to-r from-dark/80 to-dark/40"
                    ></div>
                </div>
                <!-- 轮播图3 -->
                <div
                        class="carousel-slide absolute inset-0 opacity-0 transition-opacity duration-1000 ease-in-out"
                >
                    <img
                            src="https://design.gemcoder.com/staticResource/echoAiSystemImages/fc265ec0ed3639d875802a6d30b725bf.png"
                            alt="多平台集成"
                            class="w-full h-full object-cover opacity-50"
                    />
                    <div
                            class="absolute inset-0 bg-gradient-to-r from-dark/80 to-dark/40"
                    ></div>
                </div>
            </div>
        </div>
        <div class="relative z-10 h-full flex items-center">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <h1
                            class="text-[clamp(2.5rem,5vw,4rem)] font-bold text-white leading-tight mb-4 text-shadow"
                    >
                        轻如云开放平台
                    </h1>
                    <p
                            class="text-[clamp(1rem,2vw,1.25rem)] text-white/90 mb-8 max-w-2xl"
                    >
                        集成多种通讯协议与生态系统，打造丰富且专业的应用分发平台，为开发者提供强大的SaaS能力和拓展空间。
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a
                                href="javascript:void(0);"
                                class="px-8 py-3 bg-primary hover:bg-primary/90 text-white font-medium rounded-md transition-all transform hover:scale-105 shadow-lg hover:shadow-primary/30"
                        >
                            探索平台
                        </a>
                        <a
                                href="javascript:void(0);"
                                class="px-8 py-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 text-white font-medium rounded-md border border-white/30 transition-all"
                        >
                            开发者文档
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- 轮播控制按钮 -->
        <div
                class="absolute bottom-10 left-0 right-0 z-10 flex justify-center space-x-3"
        >
            <button
                    class="carousel-dot w-3 h-3 rounded-full bg-white opacity-100 transition-opacity"
            ></button>
            <button
                    class="carousel-dot w-3 h-3 rounded-full bg-white opacity-50 transition-opacity"
            ></button>
            <button
                    class="carousel-dot w-3 h-3 rounded-full bg-white opacity-50 transition-opacity"
            ></button>
        </div>
        <!-- 向下滚动指示 -->
        <div
                class="absolute bottom-10 left-1/2 transform -translate-x-1/2 z-10 animate-bounce"
        >
            <a
                    href="#platform"
                    class="text-white/70 hover:text-white transition-colors"
            >
                <i class="fas fa-chevron-down"> </i>
            </a>
        </div>
    </section>
    <!-- [/MODULE] e5f_首屏广告展示屏 -- 展示轮播广告图，包含平台介绍和主要行动按钮 -->
    <!-- [MODULE] g7h_第二屏展示平台定位 -->
    <section id="platform" class="h-screen bg-white flex items-center py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1 scroll-animate">
                    <h2
                            class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold text-dark mb-6"
                    >
                        多租户SaaS模块化
                        <br/>
                        <span class="text-primary"> 开放平台 </span>
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        轻如云开放平台是一款基于主流跨平台开发框架Laravel构建的多租户SaaS模块化WEB系统集成平台。我们致力于为开发者提供强大而灵活的工具，帮助您快速构建和分发应用。
                    </p>
                    <div class="space-y-6">
                        <!-- 特性1 -->
                        <div class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-lg mr-4">
                                <i class="fas fa-cubes text-primary text-xl"> </i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">微服务架构</h3>
                                <p class="text-gray-600">
                                    采用模块化设计，各功能组件独立运行，提高系统稳定性和扩展性
                                </p>
                            </div>
                        </div>
                        <!-- 特性2 -->
                        <div class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-lg mr-4">
                                <i class="fas fa-plug text-primary text-xl"> </i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">深度集成能力</h3>
                                <p class="text-gray-600">
                                    无缝对接各类第三方服务和生态系统，降低开发复杂度
                                </p>
                            </div>
                        </div>
                        <!-- 特性3 -->
                        <div class="flex items-start">
                            <div class="bg-primary/10 p-3 rounded-lg mr-4">
                                <i class="fas fa-code-branch text-primary text-xl"> </i>
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold mb-2">高度聚合</h3>
                                <p class="text-gray-600">
                                    整合多种服务和功能于一体，提供一站式解决方案
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="order-1 lg:order-2 scroll-animate">
                    <div class="relative">
                        <div
                                class="absolute -top-6 -left-6 w-64 h-64 bg-primary/5 rounded-full filter blur-3xl"
                        ></div>
                        <div
                                class="absolute -bottom-10 -right-10 w-80 h-80 bg-secondary/5 rounded-full filter blur-3xl"
                        ></div>
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/88321adb74e9f61e9d0a2e47287a6eea.png"
                                alt="轻如云平台界面"
                                class="relative z-10 rounded-xl shadow-2xl w-full max-w-lg mx-auto transform hover:scale-[1.02] transition-transform duration-500"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] g7h_第二屏展示平台定位 -- 介绍轻如云平台的定位和核心特性，包括微服务架构、深度集成能力和高度聚合 -->
    <!-- [MODULE] i9j_第三屏展示热门应用 -->
    <section id="popular-apps" class="h-screen bg-light py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-animate">
                <h2
                        class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold text-dark mb-4"
                >
                    热门应用
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    探索轻如云平台上最受欢迎的应用，提升您的工作效率和业务能力
                </p>
            </div>
            <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8"
            >
                <!-- 应用卡片1 -->
                <div
                        class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate"
                >
                    <div class="h-48 overflow-hidden">
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/84d1ae028f855f214d29f89ca4e6b5ed.png"
                                alt="微信生态集成"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div
                                    class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3"
                            >
                                <i class="fab fa-weixin text-green-500"> </i>
                            </div>
                            <h3 class="font-bold text-lg">微信生态集成</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            整合微信公众平台、微信支付和企业微信，实现全渠道连接
                        </p>
                        <div class="flex justify-between items-center">
                  <span class="text-xs text-gray-500">
                    4.8
                    <i class="fas fa-star text-yellow-400"> </i>
                  </span>
                            <a
                                    href="javascript:void(0);"
                                    class="text-primary text-sm font-medium hover:underline"
                            >
                                查看详情
                            </a>
                        </div>
                    </div>
                </div>
                <!-- 应用卡片2 -->
                <div
                        class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate"
                >
                    <div class="h-48 overflow-hidden">
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/c413684a10599dd3b4b5f8f9fb69afee.png"
                                alt="阿里云服务套件"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div
                                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3"
                            >
                                <i class="fas fa-cloud text-blue-500"> </i>
                            </div>
                            <h3 class="font-bold text-lg">阿里云服务套件</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            集成阿里云生态服务，包括存储、计算、AI等全方位云服务
                        </p>
                        <div class="flex justify-between items-center">
                  <span class="text-xs text-gray-500">
                    4.7
                    <i class="fas fa-star text-yellow-400"> </i>
                  </span>
                            <a
                                    href="javascript:void(0);"
                                    class="text-primary text-sm font-medium hover:underline"
                            >
                                查看详情
                            </a>
                        </div>
                    </div>
                </div>
                <!-- 应用卡片3 -->
                <div
                        class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate"
                >
                    <div class="h-48 overflow-hidden">
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/349ebccd3496c2b36b5733001d7d0948.png"
                                alt="AI智能助手"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div
                                    class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3"
                            >
                                <i class="fas fa-brain text-purple-500"> </i>
                            </div>
                            <h3 class="font-bold text-lg">AI智能助手</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            整合多家AI服务提供商，提供智能分析、自然语言处理等能力
                        </p>
                        <div class="flex justify-between items-center">
                  <span class="text-xs text-gray-500">
                    4.9
                    <i class="fas fa-star text-yellow-400"> </i>
                  </span>
                            <a
                                    href="javascript:void(0);"
                                    class="text-primary text-sm font-medium hover:underline"
                            >
                                查看详情
                            </a>
                        </div>
                    </div>
                </div>
                <!-- 应用卡片4 -->
                <div
                        class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow transform hover:-translate-y-1 duration-300 scroll-animate"
                >
                    <div class="h-48 overflow-hidden">
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/9a54f2d82d20e23004582a8f54b2a732.png"
                                alt="物联网集成平台"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500"
                        />
                    </div>
                    <div class="p-6">
                        <div class="flex items-center mb-3">
                            <div
                                    class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center mr-3"
                            >
                                <i class="fas fa-microchip text-teal-500"> </i>
                            </div>
                            <h3 class="font-bold text-lg">物联网集成平台</h3>
                        </div>
                        <p class="text-gray-600 text-sm mb-4">
                            连接各类智能硬件设备，实现数据采集、分析与远程控制
                        </p>
                        <div class="flex justify-between items-center">
                  <span class="text-xs text-gray-500">
                    4.6
                    <i class="fas fa-star text-yellow-400"> </i>
                  </span>
                            <a
                                    href="javascript:void(0);"
                                    class="text-primary text-sm font-medium hover:underline"
                            >
                                查看详情
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-12 scroll-animate">
                <a
                        href="javascript:void(0);"
                        class="inline-flex items-center px-6 py-3 border border-primary text-primary font-medium rounded-md hover:bg-primary hover:text-white transition-colors"
                >
                    查看更多应用
                    <i class="fas fa-arrow-right ml-2"> </i>
                </a>
            </div>
        </div>
    </section>
    <!-- [/MODULE] i9j_第三屏展示热门应用 -- 展示平台上的热门应用，包括微信生态集成、阿里云服务套件、AI智能助手和物联网集成平台等 -->
    <!-- [MODULE] k0l_第四屏展示平台机制 -->
    <section id="platform-mechanism" class="h-screen bg-white py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-animate">
                <h2
                        class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold text-dark mb-4"
                >
                    平台机制
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    轻如云平台通过多种机制保障服务的稳定运行和生态的健康发展
                </p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <!-- 机制1 -->
                <div
                        class="bg-light rounded-2xl p-8 hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6"
                    >
                        <i class="fas fa-shield-alt text-primary text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">安全可靠的SaaS架构</h3>
                    <p class="text-gray-600 mb-6">
                        多租户隔离设计，确保数据安全与隐私保护，提供企业级安全保障
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-2"> </i>
                            <span> 数据加密与访问控制 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-2"> </i>
                            <span> 多租户数据隔离 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-2"> </i>
                            <span> 完善的权限管理体系 </span>
                        </li>
                    </ul>
                </div>
                <!-- 机制2 -->
                <div
                        class="bg-light rounded-2xl p-8 hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-secondary/10 rounded-xl flex items-center justify-center mb-6"
                    >
                        <i class="fas fa-code-branch text-secondary text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">应用服务拓展能力</h3>
                    <p class="text-gray-600 mb-6">
                        灵活的插件机制和API接口，支持开发者快速扩展平台功能
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-2"> </i>
                            <span> 标准化API接口 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-2"> </i>
                            <span> 插件化开发框架 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-secondary mt-1 mr-2"> </i>
                            <span> 完善的开发者文档 </span>
                        </li>
                    </ul>
                </div>
                <!-- 机制3 -->
                <div
                        class="bg-light rounded-2xl p-8 hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-primary/10 rounded-xl flex items-center justify-center mb-6"
                    >
                        <i class="fas fa-microchip text-primary text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">硬件生态集成能力</h3>
                    <p class="text-gray-600 mb-6">
                        支持各类智能硬件接入，构建完整的物联网应用生态
                    </p>
                    <ul class="space-y-3 text-gray-600">
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-2"> </i>
                            <span> 多协议设备接入 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-2"> </i>
                            <span> 设备管理与监控 </span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-primary mt-1 mr-2"> </i>
                            <span> 实时数据处理 </span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="mt-20 bg-dark rounded-2xl overflow-hidden scroll-animate">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <div class="p-12 lg:p-16 flex flex-col justify-center">
                        <h3 class="text-2xl font-bold text-white mb-6">
                            集成多种通讯协议
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 mb-8">
                            <div class="bg-white/10 rounded-lg p-4 text-center">
                                <i class="fas fa-exchange-alt text-white text-xl mb-2"> </i>
                                <p class="text-white/80 text-sm">HTTP</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 text-center">
                                <i class="fas fa-plug text-white text-xl mb-2"> </i>
                                <p class="text-white/80 text-sm">TCP</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 text-center">
                                <i class="fas fa-wifi text-white text-xl mb-2"> </i>
                                <p class="text-white/80 text-sm">MQTT</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 text-center">
                                <i class="fas fa-sitemap text-white text-xl mb-2"> </i>
                                <p class="text-white/80 text-sm">SOCKET</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 text-center">
                                <i class="fas fa-video text-white text-xl mb-2"> </i>
                                <p class="text-white/80 text-sm">BG28181</p>
                            </div>
                            <div class="bg-white/10 rounded-lg p-4 text-center">
                                <i class="fas fa-comments text-white text-xl mb-2"> </i>
                                <p class="text-white/80 text-sm">WebRTC</p>
                            </div>
                        </div>
                        <a
                                href="javascript:void(0);"
                                class="text-primary hover:text-primary/80 font-medium inline-flex items-center self-start"
                        >
                            查看完整技术文档
                            <i class="fas fa-arrow-right ml-2"> </i>
                        </a>
                    </div>
                    <div class="relative h-64 lg:h-auto">
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/8968e2e9afbfb3c8ae86d703e98fc494.png"
                                alt="多种通讯协议集成"
                                class="absolute inset-0 w-full h-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] k0l_第四屏展示平台机制 -- 展示平台的SaaS能力、应用服务拓展能力和硬件生态集成能力，以及支持的多种通讯协议 -->
    <!-- [MODULE] m2n_第五屏展示平台 -->
    <section id="platform-showcase" class="h-screen bg-white py-20">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 scroll-animate">
                <h2
                        class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold text-dark mb-4"
                >
                    集成多个开放平台
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    轻如云平台整合多家行业领先服务商，打造全方位的应用生态系统
                </p>
            </div>
            <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-20"
            >
                <div class="scroll-animate">
                    <div class="relative">
                        <div
                                class="absolute -top-10 -right-10 w-64 h-64 bg-primary/5 rounded-full filter blur-3xl"
                        ></div>
                        <img
                                src="https://design.gemcoder.com/staticResource/echoAiSystemImages/3fc742c45cee64d07b3afe05bea61291.png"
                                alt="微信生态集成"
                                class="relative z-10 rounded-xl shadow-2xl w-full max-w-lg mx-auto"
                        />
                    </div>
                </div>
                <div class="scroll-animate">
                    <div
                            class="inline-block px-4 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium mb-6"
                    >
                        微信生态
                    </div>
                    <h3 class="text-2xl font-bold text-dark mb-6">
                        全方位微信生态整合
                    </h3>
                    <p class="text-gray-600 mb-8 leading-relaxed">
                        深度集成微信公众平台、微信开放平台、微信支付和企业微信，实现用户、消息、支付等全方位连接，帮助企业快速接入微信生态。
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div
                                    class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm mr-4"
                            >
                                <i class="fab fa-weixin text-green-500 text-xl"> </i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">公众号管理</h4>
                                <p class="text-gray-600 text-sm">
                                    统一管理多个公众号，实现消息推送、菜单管理等功能
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                    class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm mr-4"
                            >
                                <i class="fas fa-credit-card text-green-500 text-xl"> </i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">微信支付集成</h4>
                                <p class="text-gray-600 text-sm">
                                    快速接入微信支付，支持多种支付场景和营销工具
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div
                                    class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm mr-4"
                            >
                                <i class="fas fa-building text-green-500 text-xl"> </i>
                            </div>
                            <div>
                                <h4 class="font-semibold mb-1">企业微信对接</h4>
                                <p class="text-gray-600 text-sm">
                                    连接企业微信，实现组织架构同步、消息互通和应用集成
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- 阿里云生态 -->
                <div
                        class="bg-light rounded-xl p-8 text-center hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <i class="fas fa-cloud text-blue-500 text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">阿里云生态</h3>
                    <p class="text-gray-600 text-sm">
                        集成阿里云存储、计算、CDN等全方位云服务，构建稳定可靠的云端基础设施
                    </p>
                </div>
                <!-- 腾讯云生态 -->
                <div
                        class="bg-light rounded-xl p-8 text-center hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <i class="fab fa-qq text-blue-500 text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">腾讯云生态</h3>
                    <p class="text-gray-600 text-sm">
                        整合腾讯云AI能力、即时通讯、视频服务等，打造丰富的多媒体应用体验
                    </p>
                </div>
                <!-- Dcloud生态 -->
                <div
                        class="bg-light rounded-xl p-8 text-center hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <i class="fas fa-mobile-alt text-purple-500 text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Dcloud生态</h3>
                    <p class="text-gray-600 text-sm">
                        支持Dcloud生态应用快速集成，实现跨平台应用开发与分发
                    </p>
                </div>
                <!-- AI生态 -->
                <div
                        class="bg-light rounded-xl p-8 text-center hover:shadow-lg transition-shadow scroll-animate"
                >
                    <div
                            class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6"
                    >
                        <i class="fas fa-brain text-purple-500 text-2xl"> </i>
                    </div>
                    <h3 class="text-xl font-bold mb-3">AI生态</h3>
                    <p class="text-gray-600 text-sm">
                        集成多家AI服务提供商，提供自然语言处理、图像识别等智能能力
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] m2n_第五屏展示平台 -- 展示平台集成的多个开放平台，包括微信生态、阿里云生态、腾讯云生态、Dcloud生态和AI生态 -->
</main>
<!-- [/MODULE] c3d_主内容区域 -- 包含首屏广告展示屏、平台定位、热门应用、平台机制和平台展示等主要内容区块 -->
<!-- [MODULE] o4p_底部区域 -->
<footer class="bg-dark text-white pt-20 pb-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16"
        >
            <div>
                <div class="flex items-center mb-6">
                    <i class="fas fa-cloud text-primary text-2xl mr-2"> </i>
                    <span class="font-bold text-xl tracking-tight"> 轻如云 </span>
                </div>
                <p class="text-gray-400 mb-6">
                    多租户SaaS模块化WEB系统集成开放平台，为开发者提供强大的应用开发和分发能力。
                </p>
                <div class="flex space-x-4">
                    <a
                            href="javascript:void(0);"
                            class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="fab fa-github text-white"> </i>
                    </a>
                    <a
                            href="javascript:void(0);"
                            class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="fab fa-weixin text-white"> </i>
                    </a>
                    <a
                            href="javascript:void(0);"
                            class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-primary transition-colors"
                    >
                        <i class="fab fa-weibo text-white"> </i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-6">产品</h4>
                <ul class="space-y-4">
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            应用市场
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            服务市场
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            开发者中心
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            API文档
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            定价方案
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-6">资源</h4>
                <ul class="space-y-4">
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            帮助中心
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            教程文档
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            社区论坛
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            合作伙伴
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            案例研究
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-6">公司</h4>
                <ul class="space-y-4">
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            关于我们
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            联系方式
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            加入我们
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            隐私政策
                        </a>
                    </li>
                    <li>
                        <a
                                href="javascript:void(0);"
                                class="text-gray-400 hover:text-white transition-colors"
                        >
                            服务条款
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/10 pt-10">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-500 text-sm mb-4 md:mb-0">
                    © 2023 轻如云开放平台. 保留所有权利.
                </p>
                <div class="flex space-x-6">
                    <a
                            href="javascript:void(0);"
                            class="text-gray-500 hover:text-gray-300 text-sm"
                    >
                        隐私政策
                    </a>
                    <a
                            href="javascript:void(0);"
                            class="text-gray-500 hover:text-gray-300 text-sm"
                    >
                        服务条款
                    </a>
                    <a
                            href="javascript:void(0);"
                            class="text-gray-500 hover:text-gray-300 text-sm"
                    >
                        Cookie政策
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- [/MODULE] o4p_底部区域 -- 包含友情链接、版权信息、联系方式和隐私政策等常用链接 -->
<!-- [JSMOD] q6r_导航栏交互 -->
<script id="navbar-script">
    // 导航栏滚动效果
    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-dark/95', 'backdrop-blur-md', 'shadow-lg');
            navbar.classList.remove('bg-transparent');
        } else {
            navbar.classList.remove('bg-dark/95', 'backdrop-blur-md', 'shadow-lg');
            navbar.classList.add('bg-transparent');
        }
    });
    // 移动端菜单切换
    var mobileMenuButton = document.getElementById('mobile-menu-button');
    var mobileMenu = document.getElementById('mobile-menu');
    mobileMenuButton.addEventListener('click', function () {
        mobileMenu.classList.toggle('hidden');
        // 切换图标
        var icon = mobileMenuButton.querySelector('i');
        if (mobileMenu.classList.contains('hidden')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
    });
    // 平滑滚动
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
                // 关闭移动菜单
                if (!mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                    var icon = mobileMenuButton.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        });
    });
</script>
<!-- [/MODULE] q6r_导航栏交互 -- 实现导航栏滚动效果、移动端菜单切换和平滑滚动功能 -->
<!-- [JSMOD] s8t_轮播图交互 -->
<script id="carousel-script">
    // 轮播图功能
    document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('.carousel-slide');
        var dots = document.querySelectorAll('.carousel-dot');
        var currentSlide = 0;
        var slideInterval;

        // 初始化轮播
        function initCarousel() {
            showSlide(0);
            startSlideInterval();
            // 点击指示点切换轮播
            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () {
                    showSlide(index);
                    resetSlideInterval();
                });
            });
        }

        // 显示指定轮播
        function showSlide(index) {
            // 隐藏所有轮播
            slides.forEach(function (slide) {
                slide.classList.add('opacity-0');
                slide.classList.remove('opacity-100');
            });
            // 重置所有指示点
            dots.forEach(function (dot) {
                dot.classList.add('opacity-50');
                dot.classList.remove('opacity-100');
            });
            // 显示当前轮播和指示点
            slides[index].classList.add('opacity-100');
            slides[index].classList.remove('opacity-0');
            dots[index].classList.add('opacity-100');
            dots[index].classList.remove('opacity-50');
            currentSlide = index;
        }

        // 开始轮播定时器
        function startSlideInterval() {
            slideInterval = setInterval(function () {
                var nextSlide = (currentSlide + 1) % slides.length;
                showSlide(nextSlide);
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
</script>
<!-- [/MODULE] s8t_轮播图交互 -- 实现首屏广告轮播图的自动切换和手动切换功能 -->
<!-- [JSMOD] u0v_页面滚动动画 -->
<script id="scroll-animation-script">
    // 页面滚动动画效果
    document.addEventListener('DOMContentLoaded', function () {
        // 为需要动画的元素添加初始样式
        var animatedElements = document.querySelectorAll('.scroll-animate');
        animatedElements.forEach(function (element) {
            element.classList.add('transition-all', 'duration-700', 'ease-out', 'opacity-0', 'translate-y-10');
        });

        // 检测元素是否在视口中
        function isInViewport(element) {
            var rect = element.getBoundingClientRect();
            return rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 && rect.bottom >= 0;
        }

        // 处理滚动动画
        function handleScrollAnimation() {
            animatedElements.forEach(function (element) {
                if (isInViewport(element) && !element.classList.contains('animated')) {
                    element.classList.add('animated', 'opacity-100', 'translate-y-0');
                    element.classList.remove('opacity-0', 'translate-y-10');
                }
            });
        }

        // 初始检查和滚动监听
        handleScrollAnimation();
        window.addEventListener('scroll', handleScrollAnimation);
    });
</script>
<!-- [/MODULE] u0v_页面滚动动画 -- 实现页面元素在滚动到视口时的淡入和上移动画效果 -->
</body>
</html>