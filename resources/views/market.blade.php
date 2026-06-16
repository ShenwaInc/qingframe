<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $title }}</title>
    <script src="{{ asset('static/tailwind/tailwind3.4.17.js') }}?v={{ QingRelease }}"></script>
    <script src="{{ asset('static/vue/vue.global.js') }}?v={{ QingRelease }}"></script>
    <link rel="stylesheet" href="{{ asset('static/tailwind/css/font-awesome.all.css') }}?v={{ QingRelease }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        secondary: '#0f172a',
                        accent: '#3b82f6',
                        neutral: '#f8fafc',
                        red: '#ef4444'
                    },
                    spacing: {
                        '128': '32rem'
                    },
                    boxShadow: {
                        'neo': '8px 8px 16px #d1d9e6, -8px -8px 16px #ffffff',
                        'neo-inset': 'inset 4px 4px 8px #d1d9e6, inset -4px -4px 8px #ffffff',
                        'neo-sm': '4px 4px 8px #d1d9e6, -4px -4px 8px #ffffff'
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
            .neo {
                box-shadow: 8px 8px 16px #d1d9e6, -8px -8px 16px #ffffff;
            }
            .neo-inset {
                box-shadow: inset 4px 4px 8px #d1d9e6, inset -4px -4px 8px #ffffff;
            }
            .neo-sm {
                box-shadow: 4px 4px 8px #d1d9e6, -4px -4px 8px #ffffff;
            }
            .bg-neo {
                background-color: #ebf0f5;
            }
            .text-gradient {
                background-clip: text;
                -webkit-background-clip: text;
                color: transparent;
                background-image: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            }
        }
        .sticky-nav + .container{padding-top: 100px;}
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 12px 12px 24px #d1d9e6, -12px -12px 24px #ffffff;
        }
        .btn-hover {
            transition: all 0.2s ease;
        }
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 6px 6px 12px #d1d9e6, -6px -6px 12px #ffffff;
        }
        .btn-hover:active {
            transform: translateY(0);
            box-shadow: inset 4px 4px 8px #d1d9e6, inset -4px -4px 8px #ffffff;
        }
        /* 模态框样式 */
        .modal-overlay {
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
        .modal-content {
            max-height: 80vh;
            overflow-y: auto;
        }
        /* 左侧浮动工具栏 */
        .float-toolbar {
            position: fixed;
            right: 50%;
            top: 50%;
            margin-right: -40%;
            transform: translateY(-50%);
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .float-toolbar .tool-btn {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #ebf0f5;
            box-shadow: 6px 6px 12px #d1d9e6, -6px -6px 12px #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            font-size: 20px;
            transition: all 0.2s;
            cursor: pointer;
            text-decoration: none;
        }
        .float-toolbar .tool-btn:hover {
            transform: scale(1.05);
            box-shadow: 8px 8px 16px #d1d9e6, -8px -8px 16px #ffffff;
            color: #2563eb;
        }
        .float-toolbar .tool-btn:active {
            transform: scale(0.95);
            box-shadow: inset 4px 4px 8px #d1d9e6, inset -4px -4px 8px #ffffff;
        }
        @media (max-width: 1140px) {
            .float-toolbar {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-neo text-slate-700 min-h-screen">
@include('sticky-nav')
<!-- [MODULE] 73k_整体页面容器 -->
<div class="container mx-auto px-4 py-6 max-w-7xl" id="app">
    <!-- [MODULE] a2f_导航栏模块 -->
    <div class="mb-12 rounded-2xl p-6 neo bg-neo">
        <div class="flex items-center justify-start flex-wrap gap-4">
            <div class="flex-1">
                <div class="relative rounded-full neo-inset overflow-hidden">
                    <input type="text" v-model="searchKeyword" v-type="search" @confirm="doSearch()" placeholder="搜索云应用、AI工具、SaaS服务..." class="w-full bg-transparent px-6 py-3 !pl-12 focus:outline-none" />
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <button @click="doSearch()" type="button" class="px-6 py-2 rounded-full bg-primary text-white absolute right-2 top-1">
                        搜索
                    </button>
                </div>
            </div>
        </div>
        <!-- [MODULE] c8b_导航菜单 -->
        <nav class="mt-6 pt-6 border-t border-slate-200">
            <ul class="flex flex-wrap gap-2">
                <li>
                    <a href="/market/#" class="px-6 py-2 rounded-full bg-primary text-white block">
                        全部
                    </a>
                </li>
                @foreach($category as $item)
                    <li>
                        <a href="/market/#{{ $item['id'] }}" class="px-6 py-2 rounded-full neo-sm btn-hover bg-neo block transition-colors hover:text-primary">
                            {{ $item['title'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
        <!-- [/MODULE] c8b_导航菜单 -- 应用分类导航菜单 -->
    </div>
    <!-- [/MODULE] a2f_导航栏模块 -- 包含logo、搜索框和导航菜单 -->
    <!-- [MODULE] 4k2_主内容区域 -->
    <main>
        <!-- [MODULE] 91x_轮播banner模块 -->
        <section>
            <div class="rounded-3xl overflow-hidden neo relative">
                <div class="relative h-[500px] bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
                    <img
                            src="{{ asset('static/images/market_banner1.png') }}"
                            alt="云服务banner"
                            class="w-full h-full object-cover"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-primary/60 to-transparent"></div>
                    <div class="absolute top-1/2 left-12 -translate-y-1/2 max-w-xl text-white">
                        <h2 class="text-[clamp(2rem,5vw,3.5rem)] font-bold leading-tight mb-4">
                            探索未来
                            <br />
                            云应用生态
                        </h2>
                        <p class="text-xl opacity-90 mb-8">
                            精选全球优质SaaS应用，一键部署，即开即用
                        </p>
                        <div class="flex gap-4">
                            <button class="px-8 py-4 bg-white text-primary rounded-full font-semibold text-lg neo-sm btn-hover">
                                立即探索
                                <i class="fas fa-arrow-right ml-2"> </i>
                            </button>
                            <button class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-full font-semibold text-lg neo-sm btn-hover">
                                入驻成为开发者
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- [/MODULE] 91x_轮播banner模块 -- 大型宣传轮播展示 -->
        <!-- [MODULE] p5s_推荐应用模块 -->
        <div class="pt-20" id="featured"></div>
        <section>
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-secondary">精选推荐</h2>
                <!-- 精选推荐的【查看更多】绑定 keyword='' featured=1 -->
                <a href="/market/#featured"
                   @click.prevent="showMoreApp('', 1)"
                   class="text-primary flex items-center gap-2 hover:underline">
                    查看更多
                    <i class="fas fa-chevron-right"> </i>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- 应用卡片 -->
                @foreach($plugins as $value)
                    <a href="{{ $value['website'] ?: 'javascript:void(0);' }}" {{ $value['website'] ? 'target="_blank"' : '' }}>
                        <div class="rounded-2xl p-5 neo bg-neo card-hover content-auto relative">
                            <div class="w-full aspect-square rounded-xl overflow-hidden mb-4 absolute top-0 left-0">
                                <img src="{{ $value['cover'] ?? $value['icon'] }}" alt="{{ $value['name'] }}" class="w-full h-full object-cover" />
                            </div>
                            <div class="block w-full aspect-square mb-10"></div>
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-lg neo-sm flex items-center justify-center from-blue-500 to-purple-600 text-white overflow-hidden">
                                    <img src="{{ $value['icon'] }}" alt="{{ $value['name'] }}" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg text-secondary line-clamp-1" title="{{ $value['name'] }}">
                                        {{ $value['name'] }}
                                    </h3>
                                    <p class="text-sm text-slate-500">{{ $value['author'] }}</p>
                                </div>
                            </div>
                            <p class="text-slate-600 mb-4 text-sm line-clamp-2 h-11">
                                {{ $value['summary'] }}
                            </p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-yellow-500 text-sm">
                                    <i class="fas fa-star"> </i>
                                    <i class="fas fa-star"> </i>
                                    <i class="fas fa-star"> </i>
                                    <i class="fas fa-star"> </i>
                                    <i class="fas fa-star"> </i>
                                    <span class="ml-1 text-slate-600"> {{ $value['rating'] ?? '5.0' }} </span>
                                </div>
                                <span class="text-primary font-semibold"> 查看详情 </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
        <!-- [/MODULE] p5s_推荐应用模块 -- 展示精选推荐应用卡片 -->
        <!-- [MODULE] x9z_分类展示模块 -->
        <section class="mb-16">
            <!-- 应用分类 -->
            @foreach($category as $value)
                <div class="pt-20" id="{{ $value['id'] }}"></div>
                <div class="category-item">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-secondary">
                            <i class="text-primary mr-2 {{ $value['icon'] }}"> </i>
                            {{ $value['title'] }}
                        </h2>
                        <!-- 分类的【查看更多】绑定 keyword=分类标题 featured=null（不传） -->
                        <a href="/market/#{{ $value['id'] }}"
                           @click.prevent="showMoreApp('{{ addslashes($value['title']) }}', null)"
                           class="text-primary flex items-center gap-2 hover:underline">
                            查看更多
                            <i class="fas fa-chevron-right"> </i>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- 应用大卡片 -->
                        @foreach($value['list'] as $plugin)
                            <div class="rounded-2xl overflow-hidden neo bg-white card-hover">
                                <div class="h-52 relative">
                                    <img
                                            src="{{ $plugin['cover_large'] ?? ($plugin['cover'] ?? $plugin['icon']) }}"
                                            class="w-full h-full object-cover"
                                            alt="{{ $plugin['name'] }}"
                                    />
                                    @if(!empty($plugin['featured']))
                                        <div class="absolute top-4 right-4 bg-red/90 text-white px-3 py-1 rounded-full text-sm">
                                            热门
                                        </div>
                                    @endif
                                </div>
                                <div class="p-6">
                                    <h3 class="text-xl font-semibold mb-2">{{ $plugin['name'] }}</h3>
                                    <p class="text-slate-600 mb-4">
                                        {{ $plugin['summary'] }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400 text-sm">
                                                <i class="fas fa-star"> </i>
                                                <i class="fas fa-star"> </i>
                                                <i class="fas fa-star"> </i>
                                                <i class="fas fa-star"> </i>
                                                <i class="fas fa-star"> </i>
                                            </div>
                                            <span class="ml-2 text-sm text-slate-500">
                                        {{ $plugin['rating'] ?? '5.0' }} ({{ $plugin['downloads'] ?? '100+' }})
                      </span>
                                        </div>
                                        <a class="px-4 py-2 bg-primary text-white rounded-xl neo-sm btn-hover" target="_blank" href="{{ $plugin['website'] }}">
                                            查看详情
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            <!-- 微服务 -->
            <div class="mb-12 mt-20">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-secondary">
                        <i class="fas fa-code text-primary mr-2"> </i>
                        微服务
                    </h2>
                    <a href="{{ wurl('server', ['op'=>'local']) }}" target="_blank" class="text-primary flex items-center gap-2 hover:underline">
                        更多服务
                        <i class="fas fa-chevron-right"> </i>
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($servers as $value)
                        <div class="rounded-xl p-5 neo bg-neo card-hover">
                            <div class="flex gap-4 items-center mb-3">
                                <div class="w-14 h-14 rounded-xl neo-sm flex items-center justify-center bg-gradient-to-br from-cyan-500 to-blue-500 text-white text-2xl overflow-hidden">
                                    <img src="{{ $value['cover'] }}" alt="{{ $value['name'] }}" />
                                </div>
                                <div>
                                    <h3 class="font-semibold text-lg">{{ $value['name'] }}</h3>
                                    <div class="flex text-yellow-500 text-sm mt-1">
                                        <i class="fas fa-star"> </i>
                                        <span class="ml-1 text-slate-500"> 5.0 </span>
                                    </div>
                                </div>
                            </div>
                            <p class="text-slate-600 text-sm mb-3">
                                {{ $value['summary'] }}
                            </p>
                            <div class="flex justify-between items-center">
                                <span class="text-primary font-medium"> 系统自带 </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        <!-- [/MODULE] x9z_分类展示模块 -- 按分类展示不同应用 -->
        <!-- [MODULE] b7d_热门榜单模块 -->
        <section class="mb-16">
            <div class="rounded-2xl p-8 neo bg-neo">
                <h2 class="text-2xl font-bold text-secondary mb-6">
                    <i class="fas fa-fire text-orange-500 mr-2"> </i>
                    下载排行榜
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <tbody>
                        <tr class="border-b border-slate-200">
                            <td class="py-4 pl-2">
                      <span
                              class="inline-block w-8 h-8 rounded-full bg-orange-500 text-white text-center leading-8 font-bold"
                      >
                        1
                      </span>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                            class="w-12 h-12 rounded-lg neo-sm bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white"
                                    >
                                        <i class="fas fa-robot"> </i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-secondary">
                                            智联AI助手
                                        </h4>
                                        <p class="text-sm text-slate-500">
                                            人工智能 / 100k+ 安装
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <button
                                        class="px-5 py-2 bg-primary text-white rounded-xl neo-sm btn-hover"
                                >
                                    立即使用
                                </button>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-4 pl-2">
                      <span
                              class="inline-block w-8 h-8 rounded-full bg-orange-300 text-white text-center leading-8 font-bold"
                      >
                        2
                      </span>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                            class="w-12 h-12 rounded-lg neo-sm bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white"
                                    >
                                        <i class="fas fa-chart-pie"> </i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-secondary">数据洞察</h4>
                                        <p class="text-sm text-slate-500">
                                            大数据分析 / 85k+ 安装
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <button
                                        class="px-5 py-2 bg-primary text-white rounded-xl neo-sm btn-hover"
                                >
                                    立即使用
                                </button>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-4 pl-2">
                      <span
                              class="inline-block w-8 h-8 rounded-full bg-orange-200 text-white text-center leading-8 font-bold"
                      >
                        3
                      </span>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                            class="w-12 h-12 rounded-lg neo-sm bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white"
                                    >
                                        <i class="fas fa-paint-brush"> </i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-secondary">
                                            创意AI作画
                                        </h4>
                                        <p class="text-sm text-slate-500">
                                            AI生成 / 78k+ 安装
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <button
                                        class="px-5 py-2 bg-primary text-white rounded-xl neo-sm btn-hover"
                                >
                                    立即使用
                                </button>
                            </td>
                        </tr>
                        <tr class="border-b border-slate-200">
                            <td class="py-4 pl-2">
                      <span
                              class="inline-block w-8 h-8 rounded-full bg-slate-200 text-slate-600 text-center leading-8 font-bold"
                      >
                        4
                      </span>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                            class="w-12 h-12 rounded-lg neo-sm bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white"
                                    >
                                        <i class="fas fa-shield-alt"> </i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-secondary">云盾安全</h4>
                                        <p class="text-sm text-slate-500">
                                            云安全 / 62k+ 安装
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <button
                                        class="px-5 py-2 bg-primary text-white rounded-xl neo-sm btn-hover"
                                >
                                    立即使用
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 pl-2">
                      <span
                              class="inline-block w-8 h-8 rounded-full bg-slate-200 text-slate-600 text-center leading-8 font-bold"
                      >
                        5
                      </span>
                            </td>
                            <td class="py-4">
                                <div class="flex items-center gap-4">
                                    <div
                                            class="w-12 h-12 rounded-lg neo-sm bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center text-white"
                                    >
                                        <i class="fas fa-microphone"> </i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-secondary">
                                            语音转文字Pro
                                        </h4>
                                        <p class="text-sm text-slate-500">
                                            AI工具 / 55k+ 安装
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-right">
                                <button
                                        class="px-5 py-2 bg-primary text-white rounded-xl neo-sm btn-hover"
                                >
                                    立即使用
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <!-- [/MODULE] b7d_热门榜单模块 -- 应用下载排行榜展示 -->
        <!-- [MODULE] q4w_开发者入驻模块 -->
        <section class="mb-16">
            <div
                    class="rounded-3xl p-12 neo bg-gradient-to-r from-primary to-indigo-600 text-white text-center relative overflow-hidden"
            >
                <div class="relative z-10 max-w-3xl mx-auto">
                    <h2 class="text-[clamp(1.5rem,3vw,2.5rem)] font-bold mb-4">
                        成为开发者
                    </h2>
                    <p class="text-xl opacity-90 mb-8">
                        将你的应用发布到应用市场，触达千万企业客户，轻松获取收益
                    </p>
                    <div class="flex flex-wrap gap-4 justify-center">
                        <a
                                class="px-8 py-4 bg-white text-primary rounded-full font-semibold text-lg neo-sm btn-hover"
                                href="https://www.yuque.com/shenwa/qingru/xsi1e1p9d59k5981#ZGk8B"
                                target="_blank"
                        >
                            立即入驻
                            <i class="fas fa-rocket ml-2"> </i>
                        </a>
                        <a
                                class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-full font-semibold text-lg neo-sm btn-hover"
                                href="https://www.yuque.com/shenwa/qingru/xsi1e1p9d59k5981"
                                target="_blank"
                        >
                            查看开发者文档
                        </a>
                    </div>
                </div>
                <!-- 背景装饰 -->
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <i class="fas fa-code text-[15rem] absolute -top-10 -left-10">
                    </i>
                    <i class="fas fa-cloud text-[12rem] absolute bottom-0 right-10">
                    </i>
                </div>
            </div>
        </section>
        <!-- [/MODULE] q4w_开发者入驻模块 -- 开发者入驻宣传区块 -->
    </main>
    <!-- [/MODULE] 4k2_主内容区域 -- 包含所有展示内容 -->

    <!-- ========== 新增模态框 ========== -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center modal-overlay" @click.self="closeModal">
        <div class="bg-neo rounded-3xl neo p-6 w-11/12 max-w-4xl modal-content relative">
            <!-- 关闭按钮 -->
            <button @click="closeModal" class="absolute top-4 right-4 text-slate-500 hover:text-primary text-2xl">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="text-2xl font-bold text-secondary mb-4 flex items-center gap-2">
                <span v-if="modalFeatured !== null && modalFeatured !== undefined">精选推荐</span>
                <span v-else>@{{ modalKeyword }}</span>
            </h3>
            <div v-if="loading" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-primary"></i>
                <p class="mt-2 text-slate-500">加载中...</p>
            </div>
            <div v-else-if="error" class="text-center py-12 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl"></i>
                <p class="mt-2">@{{ error }}</p>
            </div>
            <div v-else>
                <div v-if="modalApps.length === 0" class="text-center py-12 text-slate-500">
                    <i class="fas fa-inbox text-5xl opacity-30"></i>
                    <p class="mt-2">暂无相关应用</p>
                </div>
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="app in modalApps" :key="app.id" class="rounded-xl p-4 neo bg-white card-hover flex flex-col">
                        <div class="flex items-center gap-3 mb-2">
                            <img :src="app.icon || app.cover" :alt="app.name" class="w-12 h-12 rounded-lg object-cover neo-sm" />
                            <div class="flex-1">
                                <h4 class="font-semibold text-secondary">@{{ app.name }}</h4>
                                <p class="text-sm text-slate-500">@{{ app.author }}</p>
                            </div>
                        </div>
                        <p class="text-slate-600 text-sm flex-1">@{{ app.summary }}</p>
                        <div class="flex justify-between items-center mt-3">
                            <div class="flex text-yellow-500 text-sm">
                                <i class="fas fa-star"></i>
                                <span class="ml-1 text-slate-600">@{{ app.rating || '5.0' }}</span>
                            </div>
                            <a :href="app.website || 'javascript:void(0)'" target="_blank" class="px-4 py-1 bg-primary text-white rounded-xl neo-sm btn-hover text-sm">
                                查看详情
                            </a>
                        </div>
                    </div>
                </div>
                <!-- 分页 -->
                <div v-if="modalTotal > 0" class="flex justify-between items-center mt-6 pt-4 border-t border-slate-200">
                    <span class="text-sm text-slate-500">共 @{{ modalTotal }} 条，第 @{{ modalPage }}/@{{ Math.ceil(modalTotal/modalPageSize) }} 页</span>
                    <div class="flex gap-2">
                        <button @click="prevPage" :disabled="modalPage <= 1" class="px-4 py-2 neo-sm rounded-xl btn-hover disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-chevron-left"></i> 上一页
                        </button>
                        <button @click="nextPage" :disabled="modalPage * modalPageSize >= modalTotal" class="px-4 py-2 neo-sm rounded-xl btn-hover disabled:opacity-50 disabled:cursor-not-allowed">
                            下一页 <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ========== 模态框结束 ========== -->

    <!-- ========== 右侧浮动工具栏 ========== -->
    <div class="float-toolbar">
        <!-- 返回顶部按钮 -->
        <button class="tool-btn" @click="scrollToTop()" title="返回顶部">
            <i class="fas fa-arrow-up"></i>
        </button>
        <!-- 联系客服按钮，跳转企业微信客服 -->
        <a class="tool-btn" href="https://work.weixin.qq.com/kfid/kfc54a08558b8eecbb3" target="_blank" title="联系客服">
            <i class="fas fa-headset"></i>
        </a>
    </div>
    <!-- ========== 工具栏结束 ========== -->

    <!-- [MODULE] f3a_页脚模块 -->
    <footer class="rounded-2xl p-8 neo bg-neo">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div
                            class="w-10 h-10 rounded-xl neo-sm flex items-center justify-center bg-primary text-white overflow-hidden"
                    >
                        <img alt="{{ $title }}" src="{{ asset('static/images/qingframe.png') }}?v={{ QingRelease }}" />
                    </div>
                    <h3 class="text-xl font-bold text-gradient">{{ $title }}</h3>
                </div>
                <p class="text-slate-600 text-sm">
                    发现优质云应用，连接供需，共创云原生新生态
                </p>
                <div class="flex gap-3 mt-4">
                    <a href="https://work.weixin.qq.com/kfid/kfc54a08558b8eecbb3" target="_blank" class="w-10 h-10 rounded-full neo-sm flex items-center justify-center hover:text-primary transition-colors">
                        <i class="fab fa-weixin"> </i>
                    </a>
                    <a href="https://github.com/ShenwaInc/qingwork.git" target="_blank" class="w-10 h-10 rounded-full neo-sm flex items-center justify-center hover:text-primary transition-colors">
                        <i class="fab fa-github"> </i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="font-semibold text-lg mb-4 text-secondary">关于我们</h4>
                <ul class="space-y-2 text-slate-600">
                    <li>
                        <a href="https://www.yuque.com/shenwa/qingru/py9q43" target="_blank" class="hover:text-primary">
                            平台介绍
                        </a>
                    </li>
                    <li>
                        <a href="https://www.qingruyun.com/#" target="_blank" class="hover:text-primary">
                            官方网站
                        </a>
                    </li>
                    <li>
                        <a href="mailto:shenwa@gxit.org" class="hover:text-primary">
                            合作联系
                        </a>
                    </li>
                    <li>
                        <a href="https://www.yuque.com/shenwa/qingru/xsi1e1p9d59k5981#ZGk8B" target="_blank" class="hover:text-primary">
                            加入我们
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-lg mb-4 text-secondary">帮助中心</h4>
                <ul class="space-y-2 text-slate-600">
                    <li>
                        <a href="https://www.yuque.com/shenwa/qingru/mxblmispgdn2ixg7" target="_blank" class="hover:text-primary">
                            用户指南
                        </a>
                    </li>
                    <li>
                        <a href="https://www.yuque.com/shenwa/qingru/xsi1e1p9d59k5981" target="_blank" class="hover:text-primary">
                            开发者文档
                        </a>
                    </li>
                    <li>
                        <a href="https://www.yuque.com/shenwa/qingru/gqea6plg3g4w99c5" target="_blank" class="hover:text-primary">
                            常见问题
                        </a>
                    </li>
                    <li>
                        <a href="https://www.yuque.com/shenwa/qingru/cazasygc40bkq1qy#vP02U" target="_blank" class="hover:text-primary">
                            隐私政策
                        </a>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-semibold text-lg mb-4 text-secondary">联系我们</h4>
                <ul class="space-y-2 text-slate-600">
                    <li class="flex items-center gap-2">
                        <i class="fas fa-envelope"> </i>
                        shenwa@gxit.org
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-phone"> </i>
                        18076579452（微信同号）
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="fas fa-map-marker-alt"> </i>
                        南宁市高新区创新路23号二号楼B座二层211
                    </li>
                </ul>
            </div>
        </div>
        <div
                class="border-t border-slate-200 mt-8 pt-8 text-center text-slate-500 text-sm"
        >
            <p>© 2026 轻如云计算（南宁）有限公司. All rights reserved.</p>
        </div>
    </footer>
    <!-- [/MODULE] f3a_页脚模块 -- 网站页脚信息 -->
</div>
<script type="text/javascript">
    const { createApp, ref } = Vue;

    createApp({
        setup() {
            // 模态框状态
            const showModal = ref(false);
            const modalApps = ref([]);
            const modalTotal = ref(0);
            const modalPage = ref(1);
            const modalPageSize = ref(10);
            const modalKeyword = ref('');
            const modalFeatured = ref(null);
            const loading = ref(false);
            const error = ref('');
            const searchKeyword = ref('');

            // 请求数据
            const fetchApps = async () => {
                loading.value = true;
                error.value = '';
                try {
                    const params = new URLSearchParams({
                        keyword: modalKeyword.value,
                        page: modalPage.value,
                        pageSize: modalPageSize.value,
                        inajax: 1
                    });
                    // 仅当 featured 有值（非 null/undefined）才追加
                    if (modalFeatured.value !== null && modalFeatured.value !== undefined) {
                        params.append('featured', modalFeatured.value);
                    }
                    const response = await fetch('/market/search?' + params.toString());
                    const data = await response.json();
                    // 根据后端返回结构，假设成功时 data.type === "success"
                    if (data.type === "success") {
                        modalApps.value = data.data.plugins || [];
                        modalTotal.value = data.data.total || 0;
                        modalPage.value = data.data.page || 1;
                        modalPageSize.value = data.data.pageSize || 10;
                    } else {
                        error.value = data.message || '请求失败';
                    }
                } catch (e) {
                    error.value = '网络错误，请稍后重试';
                } finally {
                    loading.value = false;
                }
            };

            // 显示更多（供模板调用）
            const showMoreApp = (keyword = '', featured = null) => {
                modalKeyword.value = keyword;
                modalFeatured.value = featured;
                modalPage.value = 1;          // 重置页码
                showModal.value = true;
                fetchApps();
            };

            const closeModal = () => {
                showModal.value = false;
            };

            const prevPage = () => {
                if (modalPage.value > 1) {
                    modalPage.value--;
                    fetchApps();
                }
            };

            const nextPage = () => {
                if (modalPage.value * modalPageSize.value < modalTotal.value) {
                    modalPage.value++;
                    fetchApps();
                }
            };

            // 返回顶部方法
            const scrollToTop = () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            const  doSearch = () => {
                if(!searchKeyword.value || searchKeyword.value===''){
                    return;
                }
                return showMoreApp(searchKeyword.value);
            };

            return {
                showModal,
                modalApps,
                modalTotal,
                modalPage,
                modalPageSize,
                modalKeyword,
                modalFeatured,
                loading,
                error,
                searchKeyword,
                showMoreApp,
                closeModal,
                prevPage,
                nextPage,
                scrollToTop,
                doSearch
            };
        }
    }).mount('#app')
</script>
<!-- [/MODULE] 73k_整体页面容器 -- 页面整体容器包裹所有内容 -->
</body>
</html>