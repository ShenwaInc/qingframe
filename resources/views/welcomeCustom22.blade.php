<html lang="zh-CN">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>广西冰蓝科技有限责任公司 - 科技引领未来</title>
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
                        primary: '#0066FF',
                        secondary: '#00CCFF',
                        dark: '#121212',
                        'dark-light': '#1E1E1E',
                        'gray-custom': '#333333'
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'float': 'float 6s ease-in-out infinite'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            }
                        }
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
                text-shadow: 0 0 10px rgba(0, 102, 255, 0.5);
            }
            .text-gradient {
                background-clip: text;
                -webkit-background-clip: text;
                color: transparent;
                background-image: linear-gradient(90deg, #0066FF, #00CCFF);
            }
            .bg-glass {
                background: rgba(18, 18, 18, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            .border-glow {
                box-shadow: 0 0 15px rgba(0, 102, 255, 0.5);
            }
            .hover-scale {
                transition: transform 0.3s ease;
            }
            .hover-scale:hover {
                transform: scale(1.03);
            }
        }
        /* 自定义滚动条 */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #1E1E1E;
        }
        ::-webkit-scrollbar-thumb {
            background: #0066FF;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #00CCFF;
        }
        /* 基础样式 */
        body {
            background-color: #121212;
            color: #FFFFFF;
        }
    </style>
</head>
<body class="font-sans overflow-x-hidden">
<!-- [MODULE] a1b_主内容区域 -->
<div class="min-h-screen flex flex-col">
    <!-- [MODULE] c2d_导航栏 -->
    <header
            id="navbar"
            class="fixed w-full z-50 transition-all duration-300 bg-glass"
    >
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- 公司Logo -->
                <a href="javascript:void(0);" class="flex items-center space-x-2">
                    <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center"
                    >
                        <span class="text-white font-bold text-xl"> 冰蓝 </span>
                    </div>
                    <span class="text-xl font-bold text-white"> 科技 </span>
                </a>
                <!-- 桌面导航菜单 -->
                <nav class="hidden md:flex space-x-8">
                    <a
                            href="#home"
                            class="text-white hover:text-secondary transition-colors duration-300"
                    >
                        首页
                    </a>
                    <a
                            href="#about"
                            class="text-white hover:text-secondary transition-colors duration-300"
                    >
                        关于我们
                    </a>
                    <a
                            href="#services"
                            class="text-white hover:text-secondary transition-colors duration-300"
                    >
                        业务范围
                    </a>
                    <a
                            href="#cases"
                            class="text-white hover:text-secondary transition-colors duration-300"
                    >
                        项目案例
                    </a>
                    <a
                            href="#solutions"
                            class="text-white hover:text-secondary transition-colors duration-300"
                    >
                        解决方案
                    </a>
                    <a
                            href="#contact"
                            class="text-white hover:text-secondary transition-colors duration-300"
                    >
                        联系我们
                    </a>
                </nav>
                <!-- 联系按钮 -->
                <div class="hidden md:block">
                    <a
                            href="#contact"
                            class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-full transition-all duration-300 hover:border-glow"
                    >
                        联系我们
                    </a>
                </div>
                <!-- 移动端菜单按钮 -->
                <button id="menu-toggle" class="md:hidden text-white text-2xl">
                    <i class="fas fa-bars"> </i>
                </button>
            </div>
        </div>
        <!-- 移动端导航菜单 -->
        <div
                id="mobile-menu"
                class="md:hidden hidden bg-dark-light border-t border-gray-800"
        >
            <div class="container mx-auto px-4 py-3 space-y-3">
                <a
                        href="#home"
                        class="block text-white hover:text-secondary py-2 transition-colors duration-300"
                >
                    首页
                </a>
                <a
                        href="#about"
                        class="block text-white hover:text-secondary py-2 transition-colors duration-300"
                >
                    关于我们
                </a>
                <a
                        href="#services"
                        class="block text-white hover:text-secondary py-2 transition-colors duration-300"
                >
                    业务范围
                </a>
                <a
                        href="#cases"
                        class="block text-white hover:text-secondary py-2 transition-colors duration-300"
                >
                    项目案例
                </a>
                <a
                        href="#solutions"
                        class="block text-white hover:text-secondary py-2 transition-colors duration-300"
                >
                    解决方案
                </a>
                <a
                        href="#contact"
                        class="block text-white hover:text-secondary py-2 transition-colors duration-300"
                >
                    联系我们
                </a>
                <a
                        href="#contact"
                        class="block bg-primary hover:bg-primary/90 text-white text-center px-6 py-2 rounded-full transition-all duration-300"
                >
                    联系我们
                </a>
            </div>
        </div>
    </header>
    <!-- [/MODULE] c2d_导航栏 -->
    <main>
        <!-- [MODULE] e4f_首页:英雄区域 -->
        <section
                id="home"
                class="relative min-h-screen flex items-center pt-16 overflow-hidden"
        >
            <!-- 背景效果 -->
            <div class="absolute inset-0 z-0">
                <div
                        class="absolute inset-0 bg-gradient-to-b from-dark/80 to-dark"
                ></div>
                <div
                        class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary/20 via-dark to-dark"
                ></div>
                <div
                        class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary/20 rounded-full filter blur-3xl animate-pulse-slow"
                ></div>
                <div
                        class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-secondary/20 rounded-full filter blur-3xl animate-pulse-slow"
                        style="animation-delay: 1s;"
                ></div>
            </div>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="space-y-6">
                        <h1
                                class="text-[clamp(2.5rem,5vw,4rem)] font-bold leading-tight"
                        >
                            <span class="block"> 引领科技 </span>
                            <span class="text-gradient"> 创造未来 </span>
                        </h1>
                        <p class="text-gray-300 text-lg md:text-xl max-w-lg">
                            广西冰蓝科技有限责任公司创立于2011年，是一家在科技领域蓬勃发展的企业，专注于智能化系统集成与解决方案，为您构建安全、智能、高效的未来空间。
                        </p>
                        <div class="flex flex-wrap gap-4 pt-4">
                            <a
                                    href="#services"
                                    class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full transition-all duration-300 hover:border-glow flex items-center gap-2"
                            >
                                <span> 探索业务 </span>
                                <i class="fas fa-arrow-right"> </i>
                            </a>
                            <a
                                    href="#contact"
                                    class="bg-transparent border border-white/30 hover:border-secondary text-white px-8 py-3 rounded-full transition-all duration-300 hover:border-glow flex items-center gap-2"
                            >
                                <i class="fas fa-phone"> </i>
                                <span> 联系我们 </span>
                            </a>
                        </div>
                        <!-- 公司优势 -->
                        <div class="grid grid-cols-2 gap-4 pt-8">
                            <div class="flex items-start gap-3">
                                <div
                                        class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary"
                                >
                                    <i class="fas fa-shield-alt"> </i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">14年经验</h3>
                                    <p class="text-gray-400 text-sm">专业团队</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                        class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary"
                                >
                                    <i class="fas fa-certificate"> </i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">资质认证</h3>
                                    <p class="text-gray-400 text-sm">行业认可</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                        class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary"
                                >
                                    <i class="fas fa-users"> </i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">专业团队</h3>
                                    <p class="text-gray-400 text-sm">技术保障</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div
                                        class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary"
                                >
                                    <i class="fas fa-handshake"> </i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white">诚信服务</h3>
                                    <p class="text-gray-400 text-sm">客户至上</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div
                                class="relative z-10 rounded-2xl overflow-hidden border border-primary/30 hover-scale animate-float"
                        >
                            <img
                                    src="https://design.gemcoder.com/staticResource/echoAiSystemImages/2ad6ef7618a23c08c8229c00c41c7f6d.png"
                                    alt="冰蓝科技 - 智能科技解决方案"
                                    class="w-full h-auto object-cover"
                            />
                        </div>
                        <div
                                class="absolute -bottom-6 -left-6 w-32 h-32 border border-secondary/30 rounded-2xl -z-10"
                        ></div>
                        <div
                                class="absolute -top-6 -right-6 w-40 h-40 border border-primary/30 rounded-2xl -z-10"
                        ></div>
                    </div>
                </div>
            </div>
            <div
                    class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce"
            >
                <a
                        href="#about"
                        class="text-white/70 hover:text-white transition-colors duration-300"
                >
                    <i class="fas fa-chevron-down text-2xl"> </i>
                </a>
            </div>
        </section>
        <!-- [/MODULE] e4f_首页:英雄区域 -->

        <!-- [MODULE] f5g_关于我们 -->
        <section id="about" class="py-20 bg-dark-light">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">关于我们</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">广西冰蓝科技有限责任公司创立于2011年，注册资金壹仟万元人民币，专注于网络技术开发、电子与智能化工程、系统集成等领域。</p>
                </div>
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="relative">
                        <div class="relative z-10 rounded-2xl overflow-hidden hover-scale">
                            <img
                                    src="https://design.gemcoder.com/staticResource/echoAiSystemImages/ab08696988c4188b771fe9ab69f94acb.png"
                                    alt="冰蓝科技团队"
                                    class="w-full h-auto object-cover"
                            />
                        </div>
                        <div class="absolute -top-6 -left-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div>
                        <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div>
                    </div>
                    <div class="space-y-6">
                        <h3 class="text-2xl font-bold text-white">公司简介</h3>
                        <p class="text-gray-300 leading-relaxed">广西冰蓝科技有限责任公司创立于2011年，注册资金壹仟万元人民币，是一家在科技领域蓬勃发展的企业。在网络技术领域，公司专注于网络技术开发与技术转让，不断探索网络技术的前沿，致力于将最新的网络技术成果转化并推广。凭借自身的实力，公司在电子与智能化工程专业承包、城市及道路照明工程专业承包方面崭露头角，在严格遵守资质证及安全生产许可证相关规定并确保在有效期内经营的前提下，积极参与各类工程项目，为城市的智能化建设和道路照明优化贡献力量。</p>
                        <p class="text-gray-300 leading-relaxed">计算机系统集成和计算机网络综合布线也是公司的核心业务板块。公司拥有一支专业的技术团队，精心设计并构建高效、稳定的计算机系统集成方案，同时在计算机网络综合布线方面注重细节，确保网络线路布局合理、安全可靠，为各类企业和机构提供优质的网络基础设施建设服务。公司经营地址设在广西首府绿城南宁，下设行政部、业务部、工程部、技术部、财务部。现有职员20余名，其中国家认定二级项目经理3人，工程管理人员共18人。</p>
                        <div class="grid grid-cols-2 gap-6 pt-4">
                            <div class="bg-dark p-5 rounded-xl border border-gray-800">
                                <div class="text-3xl font-bold text-secondary mb-2">14+</div>
                                <div class="text-gray-400">年行业经验</div>
                            </div>
                            <div class="bg-dark p-5 rounded-xl border border-gray-800">
                                <div class="text-3xl font-bold text-secondary mb-2">20+</div>
                                <div class="text-gray-400">专业技术人员</div>
                            </div>
                            <div class="bg-dark p-5 rounded-xl border border-gray-800">
                                <div class="text-3xl font-bold text-secondary mb-2">3</div>
                                <div class="text-gray-400">国家二级项目经理</div>
                            </div>
                            <div class="bg-dark p-5 rounded-xl border border-gray-800">
                                <div class="text-3xl font-bold text-secondary mb-2">5</div>
                                <div class="text-gray-400">专业职能部门</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 企业精神 -->
                <div class="mt-20">
                    <h3 class="text-2xl font-bold text-center mb-10">企业精神</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                        <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center hover:border-primary/50 transition-colors duration-300">
                            <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-handshake text-2xl text-secondary"></i></div>
                            <h4 class="font-bold text-lg mb-2">诚信务实</h4>
                        </div>
                        <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center hover:border-primary/50 transition-colors duration-300">
                            <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-users text-2xl text-secondary"></i></div>
                            <h4 class="font-bold text-lg mb-2">团结拼搏</h4>
                        </div>
                        <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center hover:border-primary/50 transition-colors duration-300">
                            <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-lightbulb text-2xl text-secondary"></i></div>
                            <h4 class="font-bold text-lg mb-2">开拓创新</h4>
                        </div>
                        <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center hover:border-primary/50 transition-colors duration-300">
                            <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-road text-2xl text-secondary"></i></div>
                            <h4 class="font-bold text-lg mb-2">敦行致远</h4>
                        </div>
                        <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center hover:border-primary/50 transition-colors duration-300">
                            <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4"><i class="fas fa-gem text-2xl text-secondary"></i></div>
                            <h4 class="font-bold text-lg mb-2">一诺千金</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- [/MODULE] f5g_关于我们 -->

        <!-- [MODULE] g6h_业务范围 -->
        <section id="services" class="py-20 bg-dark">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">业务范围</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">我们提供全方位的智能化系统集成解决方案，满足不同行业客户的多样化需求。</p>
                </div>
                <!-- 业务分类标签 -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button class="service-tab active px-6 py-2 rounded-full bg-primary text-white" data-target="category1">基础网络与集成类</button>
                    <button class="service-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="category2">安全与安防类</button>
                    <button class="service-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="category3">信息与通讯类</button>
                    <button class="service-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="category4">建筑设备与能源管理类</button>
                    <button class="service-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="category5">专项场景与工程类</button>
                </div>
                <!-- 业务内容 -->
                <div class="service-content active" id="category1"><div class="grid md:grid-cols-3 gap-8"><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-cubes text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">智能化集成系统</h3><p class="text-gray-400">提供全面的智能化系统集成服务，实现各子系统的互联互通，打造高效智能的整体解决方案。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-network-wired text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">综合布线系统</h3><p class="text-gray-400">专业的综合布线解决方案，确保网络线路布局合理、安全可靠，为各类企业提供优质的网络基础设施。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-server text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">计算机网络系统</h3><p class="text-gray-400">设计构建高效、稳定的计算机网络系统，满足企业数据传输、资源共享和业务应用的需求。</p></div></div></div>
                <div class="service-content hidden" id="category2"><div class="grid md:grid-cols-3 gap-8"><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-id-card-alt text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">出入口控制 / 一卡通系统</h3><p class="text-gray-400">提供先进的出入口控制和一卡通系统，实现人员、车辆的智能化管理，提升安全性和管理效率。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-video text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">视频安防监控系统</h3><p class="text-gray-400">高清视频监控解决方案，实现全天候、全方位的安全监控，保障人员和财产安全。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-bell text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">入侵报警系统</h3><p class="text-gray-400">智能入侵报警系统，实时监测异常情况并及时报警，有效防范安全威胁。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-walking text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">电子巡查系统</h3><p class="text-gray-400">智能化电子巡查管理系统，规范巡查流程，提高巡查效率和质量。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-fire-alt text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">动火离人系统</h3><p class="text-gray-400">专业的动火作业安全监控系统，实时监测动火区域人员状态，确保作业安全。</p></div></div></div>
                <div class="service-content hidden" id="category3"><div class="grid md:grid-cols-3 gap-8"><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-bullhorn text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">信息发布系统</h3><p class="text-gray-400">高效的信息发布平台，实现各类信息的实时发布和管理，提升信息传播效率。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-walkie-talkie text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">无线对讲系统</h3><p class="text-gray-400">专业无线对讲解决方案，确保通讯畅通，满足各类场所的即时通讯需求。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-video-slash text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">远程会议系统</h3><p class="text-gray-400">高清远程会议解决方案，实现异地实时沟通，提升会议效率，降低沟通成本。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-microphone text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">会议扩声系统</h3><p class="text-gray-400">专业会议扩声系统，确保会议声音清晰、稳定，提升会议体验。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-tv text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">LED 大屏系统</h3><p class="text-gray-400">高清LED显示系统，适用于各类场所的信息展示和广告宣传，视觉效果震撼。</p></div></div></div>
                <div class="service-content hidden" id="category4"><div class="grid md:grid-cols-3 gap-8"><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-building text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">楼宇自控系统</h3><p class="text-gray-400">智能化楼宇自控系统，实现对建筑设备的集中监控和管理，提升建筑运行效率。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-lightbulb text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">智能照明系统</h3><p class="text-gray-400">智能照明解决方案，实现照明的自动化控制和节能管理，提升使用体验。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-bolt text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">能源管理系统</h3><p class="text-gray-400">能源管理解决方案，实现能源消耗的实时监测和优化管理，降低能源成本。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-plug text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">变配电 / UPS 系统</h3><p class="text-gray-400">专业的变配电和UPS系统解决方案，确保电力供应的稳定可靠，保障设备安全运行。</p></div></div></div>
                <div class="service-content hidden" id="category5"><div class="grid md:grid-cols-3 gap-8"><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-car text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">停车场管理及车位引导系统</h3><p class="text-gray-400">智能化停车场管理解决方案，实现车辆进出、车位引导的自动化管理，提升停车场运营效率。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-bolt text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">电子信息防雷接地系统</h3><p class="text-gray-400">专业的防雷接地解决方案，保护电子设备免受雷击损害，确保系统安全稳定运行。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-hospital text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">医护对讲系统</h3><p class="text-gray-400">医院专用对讲系统，实现医患之间的快速沟通，提升医疗服务效率和质量。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-satellite text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">卫星电视系统</h3><p class="text-gray-400">专业卫星电视接收系统，提供丰富的电视节目资源，满足不同场所的娱乐需求。</p></div><div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale"><div class="w-14 h-14 bg-primary/20 rounded-lg flex items-center justify-center mb-5"><i class="fas fa-server text-2xl text-secondary"></i></div><h3 class="text-xl font-bold mb-3">数据中心机房工程</h3><p class="text-gray-400">专业的数据中心机房建设解决方案，确保数据存储和处理的安全可靠。</p></div></div></div>
            </div>
        </section>
        <!-- [/MODULE] g6h_业务范围 -->

        <!-- [MODULE] h7i_合作品牌 -->
        <section class="py-20 bg-dark-light">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">合作品牌</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">我们与行业领先品牌建立了长期稳定的合作关系，共同为客户提供高品质的产品和服务。</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                    <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center">
                        <h3 class="text-lg font-semibold mb-4">布线基础类</h3>
                        <div class="space-y-4">
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">TCL-罗格朗</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">普天天纪</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">大唐电信</div>
                        </div>
                    </div>
                    <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center">
                        <h3 class="text-lg font-semibold mb-4">电气类</h3>
                        <div class="space-y-4">
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">西门子</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">施耐德</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">ABB</div>
                        </div>
                    </div>
                    <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center">
                        <h3 class="text-lg font-semibold mb-4">综合布线类</h3>
                        <div class="space-y-4">
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">康普</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">泛达</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">西蒙</div>
                        </div>
                    </div>
                    <div class="bg-dark p-6 rounded-xl border border-gray-800 text-center">
                        <h3 class="text-lg font-semibold mb-4">监控设备类</h3>
                        <div class="space-y-4">
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">海康威视</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">大华</div>
                            <div class="h-12 bg-gray-800 rounded flex items-center justify-center text-gray-200">宇视</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- [/MODULE] h7i_合作品牌 -->

        <!-- [MODULE] i8j_资质证书 -->
        <section class="py-20 bg-dark">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">资质证书</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">我们拥有行业认可的专业资质，确保为客户提供高质量的服务和解决方案。</p>
                </div>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale">
                        <div class="h-64 bg-gray-800 rounded-lg mb-4 flex items-center justify-center"><i class="fas fa-file-certificate text-6xl text-gray-600"></i></div>
                        <h3 class="text-xl font-bold mb-2">营业执照</h3>
                        <p class="text-gray-400">合法经营的基本凭证，证明公司具备独立法人资格和经营能力。公司注册资本壹仟万元，统一社会信用代码等信息齐全。</p>
                    </div>
                    <div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale">
                        <div class="h-64 bg-gray-800 rounded-lg mb-4 flex items-center justify-center"><i class="fas fa-award text-6xl text-gray-600"></i></div>
                        <h3 class="text-xl font-bold mb-2">电子与智能化工程专业承包二级资质</h3>
                        <p class="text-gray-400">证明公司在电子与智能化工程领域具备专业承包能力和技术实力，可承担各类智能化工程的施工。</p>
                    </div>
                    <div class="bg-dark-light p-6 rounded-xl border border-gray-800 hover:border-primary/50 transition-all duration-300 hover-scale">
                        <div class="h-64 bg-gray-800 rounded-lg mb-4 flex items-center justify-center"><i class="fas fa-shield-alt text-6xl text-gray-600"></i></div>
                        <h3 class="text-xl font-bold mb-2">安全生产许可证</h3>
                        <p class="text-gray-400">证明公司具备安全生产条件，能够确保工程施工过程中的安全管理，保障施工人员及周边环境安全。</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- [/MODULE] i8j_资质证书 -->

        <!-- [MODULE] j9k_项目案例 -->
        <section id="cases" class="py-20 bg-dark-light">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">项目案例</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">我们完成了众多成功案例，为不同行业客户提供了专业的智能化解决方案。</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] border-collapse">
                        <thead>
                        <tr class="border-b border-gray-700">
                            <th class="py-4 px-6 text-left text-gray-400 font-semibold">序号</th>
                            <th class="py-4 px-6 text-left text-gray-400 font-semibold">项目名称</th>
                            <th class="py-4 px-6 text-left text-gray-400 font-semibold">业主/甲方</th>
                            <th class="py-4 px-6 text-left text-gray-400 font-semibold">项目时间</th>
                            <th class="py-4 px-6 text-left text-gray-400 font-semibold">项目类型</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">01</td><td class="py-4 px-6 text-white font-medium">广西南宁市五象新都（互联网智慧民生小区）建新花园项目智能化工程</td><td class="py-4 px-6 text-gray-300">广西祥嘉投资有限公司</td><td class="py-4 px-6 text-gray-300">2018.11-2021.11</td><td class="py-4 px-6 text-secondary">智慧社区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">02</td><td class="py-4 px-6 text-white font-medium">广西南宁市金玖世家营销中心项目智能化系统供应及安装工程</td><td class="py-4 px-6 text-gray-300">南宁市耀鑫房地产开发有限公司</td><td class="py-4 px-6 text-gray-300">2019.01-2019.04</td><td class="py-4 px-6 text-secondary">智慧社区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">03</td><td class="py-4 px-6 text-white font-medium">广西南宁市五象新都（互联网智慧民生小区）新华花园项目智能化工程</td><td class="py-4 px-6 text-gray-300">广西祥嘉投资有限公司</td><td class="py-4 px-6 text-gray-300">2019.06-2022.07</td><td class="py-4 px-6 text-secondary">智慧社区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">04</td><td class="py-4 px-6 text-white font-medium">路桥·锦绣新城智能化项目</td><td class="py-4 px-6 text-gray-300">广西祥嘉投资有限公司</td><td class="py-4 px-6 text-gray-300">2020.06-2023.2</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">05</td><td class="py-4 px-6 text-white font-medium">广西水电工程局南方15楼会议室改造项目</td><td class="py-4 px-6 text-gray-300">北京电力自动化设备有限公司</td><td class="py-4 px-6 text-gray-300">2020.12-2021.03</td><td class="py-4 px-6 text-secondary">智慧办公室</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">06</td><td class="py-4 px-6 text-white font-medium">广西长长路桥建设有限公司装修项目办公楼智能化工程施工</td><td class="py-4 px-6 text-gray-300">广西长长路桥建设有限公司</td><td class="py-4 px-6 text-gray-300">2022.01-2022.03</td><td class="py-4 px-6 text-secondary">智慧办公室</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">07</td><td class="py-4 px-6 text-white font-medium">北海万达广场2022年消控室物料采购</td><td class="py-4 px-6 text-gray-300">北海万达广场商业管理有限公司</td><td class="py-4 px-6 text-gray-300">2022.05-07</td><td class="py-4 px-6 text-secondary">智慧商场</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">08</td><td class="py-4 px-6 text-white font-medium">路桥·观江府项目智能化工程</td><td class="py-4 px-6 text-gray-300">广西北投观江置业有限公司</td><td class="py-4 px-6 text-gray-300">2022.11-2023.10</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">09</td><td class="py-4 px-6 text-white font-medium">北投领上项目售楼部智能化系统工程</td><td class="py-4 px-6 text-gray-300">广西北投兴东置业有限公司</td><td class="py-4 px-6 text-gray-300">2023.10-2023.11</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">10</td><td class="py-4 px-6 text-white font-medium">百东·馨园项目Ι标智能化工程</td><td class="py-4 px-6 text-gray-300">广西百色试验区发展集团有限公司</td><td class="py-4 px-6 text-gray-300">2023.10-2024.02</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">11</td><td class="py-4 px-6 text-white font-medium">路桥.锦绣嘉园智能化项目</td><td class="py-4 px-6 text-gray-300">广西祥嘉投资有限公司</td><td class="py-4 px-6 text-gray-300">2021.12-2024.05</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">12</td><td class="py-4 px-6 text-white font-medium">鼎华城江语华庭建筑智能化工程</td><td class="py-4 px-6 text-gray-300">中铁二十五局集团第四工程有限公司</td><td class="py-4 px-6 text-gray-300">2023.01-</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">13</td><td class="py-4 px-6 text-white font-medium">鼎华城龙昇柳岸建筑智能化工程</td><td class="py-4 px-6 text-gray-300">中铁二十五局集团第四工程有限公司</td><td class="py-4 px-6 text-gray-300">2023.01-</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">14</td><td class="py-4 px-6 text-white font-medium">民爆仓库技术防范设施安装</td><td class="py-4 px-6 text-gray-300">广西工程技术研究院有限公司</td><td class="py-4 px-6 text-gray-300">2023.12-</td><td class="py-4 px-6 text-secondary">民爆仓库技术防范</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">15</td><td class="py-4 px-6 text-white font-medium">2024年STN网络建设系统集成采购项目</td><td class="py-4 px-6 text-gray-300">广西壮族自治区通信产业服务有限公司技术服务分公司</td><td class="py-4 px-6 text-gray-300">2024.09-</td><td class="py-4 px-6 text-secondary">系统集成</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">16</td><td class="py-4 px-6 text-white font-medium">路桥·青云府EPC工程智能化工程</td><td class="py-4 px-6 text-gray-300">广西路桥工程集团有限公司</td><td class="py-4 px-6 text-gray-300">2025.03-</td><td class="py-4 px-6 text-secondary">智慧小区</td></tr>
                        <tr class="border-b border-gray-800 hover:bg-dark transition-colors duration-300"><td class="py-4 px-6 text-gray-300">17</td><td class="py-4 px-6 text-white font-medium">2025年中国电信广西公司IP城域网扩容系统集成服务项目</td><td class="py-4 px-6 text-gray-300">广西壮族自治区通信产业服务有限公司技术服务分公司</td><td class="py-4 px-6 text-gray-300">2025.03-</td><td class="py-4 px-6 text-secondary">系统集成</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center mt-10">
                    <a href="javascript:void(0);" class="inline-flex items-center gap-2 text-secondary hover:text-white transition-colors duration-300"><span>查看更多案例</span><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </section>
        <!-- [/MODULE] j9k_项目案例 -->

        <!-- [MODULE] k1l_解决方案 -->
        <section id="solutions" class="py-20 bg-dark">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">解决方案</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">我们为不同行业提供定制化的智能化解决方案，满足客户的多样化需求。</p>
                </div>
                <!-- 解决方案标签 -->
                <div class="flex flex-wrap justify-center gap-4 mb-12">
                    <button class="solution-tab active px-6 py-2 rounded-full bg-primary text-white" data-target="solution1">智慧社区</button>
                    <button class="solution-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="solution2">智慧停车场</button>
                    <button class="solution-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="solution3">智能家居</button>
                    <button class="solution-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="solution4">智慧景区</button>
                    <button class="solution-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="solution5">智慧校园</button>
                    <button class="solution-tab px-6 py-2 rounded-full bg-dark-light text-white hover:bg-primary/20 transition-colors duration-300" data-target="solution6">建筑综合体楼宇自控</button>
                </div>
                <!-- 智慧社区方案 -->
                <div class="solution-content active" id="solution1">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div><h3 class="text-2xl font-bold mb-6">智慧社区方案</h3><div class="space-y-4"><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><span class="text-secondary font-semibold">1</span></div><div><h4 class="font-semibold text-white">可视化楼宇房产管理</h4><p class="text-gray-400">一键生成楼宇房产（支持EXCEL导入）</p></div></div><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><span class="text-secondary font-semibold">2</span></div><div><h4 class="font-semibold text-white">住户管理</h4><p class="text-gray-400">业主、成员、租户管理，严格、宽松和自由三种注册方式</p></div></div><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><span class="text-secondary font-semibold">3</span></div><div><h4 class="font-semibold text-white">报修和投诉建议处理</h4><p class="text-gray-400">完整处理流程（派单与抢单）</p></div></div><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><span class="text-secondary font-semibold">4</span></div><div><h4 class="font-semibold text-white">智能门禁</h4><p class="text-gray-400">微信开门、定位防骚扰、开门日志</p></div></div><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><span class="text-secondary font-semibold">5</span></div><div><h4 class="font-semibold text-white">商铺和车位管理</h4><p class="text-gray-400">一键生成或EXCEL导入</p></div></div><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><span class="text-secondary font-semibold">6</span></div><div><h4 class="font-semibold text-white">多收费项目管理</h4><p class="text-gray-400">批量生成账单，前后台收银，可视化管理</p></div></div></div><div class="mt-8"><a href="javascript:void(0);" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full transition-all duration-300 hover:border-glow"><span>了解更多</span><i class="fas fa-arrow-right"></i></a></div></div>
                        <div class="relative"><div class="relative z-10 rounded-2xl overflow-hidden hover-scale"><img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/a52bd10c3fa9e7921476650985497a40.png" alt="智慧社区解决方案" class="w-full h-auto object-cover"/></div><div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div><div class="absolute -bottom-6 -left-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div></div>
                    </div>
                </div>
                <!-- 智慧停车场方案 -->
                <div class="solution-content hidden" id="solution2">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div><h3 class="text-2xl font-bold mb-6">智慧停车场方案</h3><h4 class="text-xl font-semibold mb-4 text-secondary">无感车辆系统</h4><p class="text-gray-300 mb-6">在现有设备的基础上升级（7-14天）；公众号、小程序二维码收款(可全程无人化)</p><h5 class="font-semibold text-white mb-3">方式流程：</h5><div class="space-y-4 mb-8"><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-arrow-right text-secondary"></i></div><div><h6 class="font-medium text-white">车辆入场</h6><p class="text-gray-400">车辆识别进入</p></div></div><div class="flex gap-4"><div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-arrow-right text-secondary"></i></div><div><h6 class="font-medium text-white">车辆离场</h6><ul class="text-gray-400 list-disc list-inside space-y-1"><li>月卡制车辆直接识别抬杆离场</li><li>临时车扫码付款抬杆离场</li><li>消费型车主在商家处领取电子券离场</li><li>设置呼叫操作台：电子可视操作屏</li></ul></div></div></div><h4 class="text-xl font-semibold mb-4 text-secondary">非机动车智能管理系统</h4><p class="text-gray-300">车辆扫码识别进入，临时车扫码付款抬杆离场，支持应急呼叫处理。</p><div class="mt-8"><a href="javascript:void(0);" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full transition-all duration-300 hover:border-glow"><span>了解更多</span><i class="fas fa-arrow-right"></i></a></div></div>
                        <div class="relative"><div class="relative z-10 rounded-2xl overflow-hidden hover-scale"><img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/a678be0a2ab855044680e794cf9f9813.png" alt="智慧停车场解决方案" class="w-full h-auto object-cover"/></div><div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div><div class="absolute -bottom-6 -left-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div></div>
                    </div>
                </div>
                <!-- 智能家居方案 -->
                <div class="solution-content hidden" id="solution3">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div><h3 class="text-2xl font-bold mb-6">智能家居方案</h3><p class="text-gray-300 mb-6">智能家居系统将家中的各种设备连接到一起，提供家电控制、照明控制、窗帘控制、电话远程控制、室内外遥控、防盗报警、环境监测、暖通控制、红外转发以及可编程定时控制等多种功能和手段。</p><div class="space-y-4"><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-lightbulb text-secondary"></i></div><div><h4 class="font-semibold text-white">智能照明</h4><p class="text-gray-400">远程控制灯光开关、亮度调节、场景模式设置</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-thermometer-half text-secondary"></i></div><div><h4 class="font-semibold text-white">智能温控</h4><p class="text-gray-400">远程控制空调、地暖，智能调节室内温度</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-lock text-secondary"></i></div><div><h4 class="font-semibold text-white">智能安防</h4><p class="text-gray-400">门窗传感器、人体感应器、智能门锁，实时监控报警</p></div></div></div><div class="mt-8"><a href="javascript:void(0);" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full transition-all duration-300 hover:border-glow"><span>了解更多</span><i class="fas fa-arrow-right"></i></a></div></div>
                        <div class="relative"><div class="relative z-10 rounded-2xl overflow-hidden hover-scale"><img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/524c230197d73a0061ee91409fe560dc.png" alt="智能家居解决方案" class="w-full h-auto object-cover"/></div><div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div><div class="absolute -bottom-6 -left-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div></div>
                    </div>
                </div>
                <!-- 智慧景区方案 -->
                <div class="solution-content hidden" id="solution4">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div><h3 class="text-2xl font-bold mb-6">智慧景区方案</h3><p class="text-gray-300 mb-6">智慧景区解决方案通过物联网、大数据、云计算等技术，实现景区的智能化管理和服务，提升游客体验和景区管理效率。</p><div class="space-y-4"><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-ticket-alt text-secondary"></i></div><div><h4 class="font-semibold text-white">电子票务系统</h4><p class="text-gray-400">在线预订、扫码入园、智能验票，减少排队时间</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-map-marked-alt text-secondary"></i></div><div><h4 class="font-semibold text-white">智能导览</h4><p class="text-gray-400">电子地图、语音导览、AR导览，提升游览体验</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-users text-secondary"></i></div><div><h4 class="font-semibold text-white">客流监控</h4><p class="text-gray-400">实时监测客流密度，智能分流，保障游览安全</p></div></div></div><div class="mt-8"><a href="javascript:void(0);" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full transition-all duration-300 hover:border-glow"><span>了解更多</span><i class="fas fa-arrow-right"></i></a></div></div>
                        <div class="relative"><div class="relative z-10 rounded-2xl overflow-hidden hover-scale"><img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/db07c9299f55e2db5862eedd02a2f876.png" alt="智慧景区解决方案" class="w-full h-auto object-cover"/></div><div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div><div class="absolute -bottom-6 -left-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div></div>
                    </div>
                </div>
                <!-- 智慧校园方案 -->
                <div class="solution-content hidden" id="solution5">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div><h3 class="text-2xl font-bold mb-6">智慧校园方案</h3><p class="text-gray-300 mb-6">智慧校园解决方案通过信息化技术，实现教学、管理、服务等方面的智能化，提升校园管理效率和教学质量。</p><div class="space-y-4"><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-graduation-cap text-secondary"></i></div><div><h4 class="font-semibold text-white">智能教学</h4><p class="text-gray-400">智慧教室、在线教学平台、互动教学系统</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-id-card-alt text-secondary"></i></div><div><h4 class="font-semibold text-white">校园一卡通</h4><p class="text-gray-400">身份识别、门禁、消费、图书借阅等一体化管理</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-shield-alt text-secondary"></i></div><div><h4 class="font-semibold text-white">校园安防</h4><p class="text-gray-400">视频监控、入侵报警、电子巡更、应急指挥系统</p></div></div></div><div class="mt-8"><a href="javascript:void(0);" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full transition-all duration-300 hover:border-glow"><span>了解更多</span><i class="fas fa-arrow-right"></i></a></div></div>
                        <div class="relative"><div class="relative z-10 rounded-2xl overflow-hidden hover-scale"><img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/ba57fe55fe19139ef5f886f32b4e9e62.png" alt="智慧校园解决方案" class="w-full h-auto object-cover"/></div><div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div><div class="absolute -bottom-6 -left-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div></div>
                    </div>
                </div>
                <!-- 建筑综合体楼宇自控方案 -->
                <div class="solution-content hidden" id="solution6">
                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div><h3 class="text-2xl font-bold mb-6">建筑综合体楼宇自控方案</h3><p class="text-gray-300 mb-6">IBMS是Intelligent Building Management System的简称，即是智能建筑集成管理系统，又称智能化系统集成管理平台软件。它是智能建筑集成管理核心，协同互联是资深的IBMS系统集成软件开发公司。</p><p class="text-gray-300 mb-6">主要解决弱电子系统和工控领域子系统的集成，实现子系统的互联互通,集中集成和监控各个子系统，实现子系统的联动，IBMS软件平台与云计算PASS平台实现数据通讯。</p><div class="space-y-4"><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-sitemap text-secondary"></i></div><div><h4 class="font-semibold text-white">系统集成</h4><p class="text-gray-400">实现各子系统的互联互通，集中监控和管理</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-cloud text-secondary"></i></div><div><h4 class="font-semibold text-white">云平台对接</h4><p class="text-gray-400">与云计算PASS平台实现数据通讯，数据存储和管理</p></div></div><div class="flex items-start gap-4"><div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0 mt-1"><i class="fas fa-mobile-alt text-secondary"></i></div><div><h4 class="font-semibold text-white">移动管理</h4><p class="text-gray-400">在pc端或者移动设备实时管理报警信息、维修信息等</p></div></div></div><div class="mt-8"><a href="javascript:void(0);" class="inline-flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-full transition-all duration-300 hover:border-glow"><span>了解更多</span><i class="fas fa-arrow-right"></i></a></div></div>
                        <div class="relative"><div class="relative z-10 rounded-2xl overflow-hidden hover-scale"><img src="https://design.gemcoder.com/staticResource/echoAiSystemImages/d98734a07098a06dbfcc3c0255a2639b.png" alt="建筑综合体楼宇自控方案" class="w-full h-auto object-cover"/></div><div class="absolute -top-6 -right-6 w-32 h-32 bg-primary/10 rounded-2xl -z-10"></div><div class="absolute -bottom-6 -left-6 w-40 h-40 bg-secondary/10 rounded-2xl -z-10"></div></div>
                    </div>
                </div>
            </div>
        </section>
        <!-- [/MODULE] k1l_解决方案 -->

        <!-- [MODULE] l2m_联系我们 (已修改：移除发送消息表单，联系方式占满全宽并内分左右两列，右侧地图占位，移动端自适应) -->
        <section id="contact" class="py-20 bg-dark-light relative overflow-hidden">
            <!-- 背景效果保持不变 -->
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary/10 via-dark-light to-dark-light"></div>
                <div class="absolute top-1/4 right-1/4 w-64 h-64 bg-primary/10 rounded-full filter blur-3xl animate-pulse-slow"></div>
                <div class="absolute bottom-1/3 left-1/3 w-96 h-96 bg-secondary/10 rounded-full filter blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
            </div>
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <h2 class="text-[clamp(1.8rem,3vw,2.5rem)] font-bold mb-4">联系我们</h2>
                    <div class="w-20 h-1 bg-gradient-to-r from-primary to-secondary mx-auto mb-6"></div>
                    <p class="text-gray-400">无论您有任何问题或需求，我们都将竭诚为您提供专业的服务和支持。</p>
                </div>
                <!-- 联系方式卡片：宽度100%，内部左侧联系信息，右侧地图占位 -->
                <div class="bg-dark p-6 md:p-8 rounded-2xl border border-gray-800 w-full">
                    <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                        <!-- 左侧：联系信息 -->
                        <div class="flex-1 space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-secondary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white mb-1">公司地址</h4>
                                    <p class="text-gray-400">南宁市高科路28号2号楼3层302室</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-secondary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white mb-1">联系电话</h4>
                                    <p class="text-gray-400">0771-3153864</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-secondary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white mb-1">电子邮箱</h4>
                                    <p class="text-gray-400">info@binglan-tech.com</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-clock text-secondary text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-white mb-1">工作时间</h4>
                                    <p class="text-gray-400">周一至周五: 9:00 - 18:00</p>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-white mb-4">关注我们</h4>
                                <div class="flex gap-4">
                                    <a href="javascript:void(0);" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary hover:bg-primary hover:text-white transition-all duration-300"><i class="fab fa-weixin"></i></a>
                                    <a href="javascript:void(0);" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary hover:bg-primary hover:text-white transition-all duration-300"><i class="fab fa-weibo"></i></a>
                                    <a href="javascript:void(0);" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary hover:bg-primary hover:text-white transition-all duration-300"><i class="fab fa-linkedin"></i></a>
                                    <a href="javascript:void(0);" class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center text-secondary hover:bg-primary hover:text-white transition-all duration-300"><i class="fab fa-qq"></i></a>
                                </div>
                            </div>
                        </div>
                        <!-- 右侧：地图占位 (示意图片) -->
                        <div class="flex-1 min-h-[280px] md:min-h-[320px] bg-gray-800 rounded-xl overflow-hidden shadow-lg flex items-center justify-center border border-gray-700">
                            <!-- 示意地图占位，可使用占位图或图标，实际项目中替换为真实地图组件 -->
                            <div class="text-center p-4">
                                <i class="fas fa-map-marked-alt text-6xl text-primary/60 mb-3"></i>
                                <p class="text-gray-400 text-sm">地图位置示意</p>
                                <p class="text-gray-500 text-xs mt-2">南宁市高科路28号2号楼3层302室</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- [/MODULE] l2m_联系我们 -->
    </main>

    <!-- [MODULE] m3n_页脚 (联系方式已同步更新地址电话) -->
    <footer class="bg-dark border-t border-gray-800 py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-10">
                <div><div class="flex items-center space-x-2 mb-6"><div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center"><span class="text-white font-bold text-xl">冰蓝</span></div><span class="text-xl font-bold text-white">科技</span></div><p class="text-gray-400 mb-6">广西冰蓝科技有限责任公司创立于2011年，是一家在科技领域蓬勃发展的企业，专注于智能化系统集成与解决方案。</p><div class="flex gap-4"><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300"><i class="fab fa-weixin text-xl"></i></a><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300"><i class="fab fa-weibo text-xl"></i></a><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300"><i class="fab fa-linkedin text-xl"></i></a><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300"><i class="fab fa-qq text-xl"></i></a></div></div>
                <div><h4 class="text-lg font-semibold text-white mb-6">快速链接</h4><ul class="space-y-3"><li><a href="#home" class="text-gray-400 hover:text-secondary transition-colors duration-300">首页</a></li><li><a href="#about" class="text-gray-400 hover:text-secondary transition-colors duration-300">关于我们</a></li><li><a href="#services" class="text-gray-400 hover:text-secondary transition-colors duration-300">业务范围</a></li><li><a href="#cases" class="text-gray-400 hover:text-secondary transition-colors duration-300">项目案例</a></li><li><a href="#solutions" class="text-gray-400 hover:text-secondary transition-colors duration-300">解决方案</a></li><li><a href="#contact" class="text-gray-400 hover:text-secondary transition-colors duration-300">联系我们</a></li></ul></div>
                <div><h4 class="text-lg font-semibold text-white mb-6">业务范围</h4><ul class="space-y-3"><li><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300">智能化集成系统</a></li><li><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300">综合布线系统</a></li><li><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300">视频安防监控系统</a></li><li><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300">楼宇自控系统</a></li><li><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300">停车场管理系统</a></li><li><a href="javascript:void(0);" class="text-gray-400 hover:text-secondary transition-colors duration-300">智慧社区解决方案</a></li></ul></div>
                <div><h4 class="text-lg font-semibold text-white mb-6">联系我们</h4><ul class="space-y-3"><li class="flex items-start gap-3"><i class="fas fa-map-marker-alt text-secondary mt-1"></i><span class="text-gray-400">南宁市高科路28号2号楼3层302室</span></li><li class="flex items-center gap-3"><i class="fas fa-phone text-secondary"></i><span class="text-gray-400">0771-3153864</span></li><li class="flex items-center gap-3"><i class="fas fa-envelope text-secondary"></i><span class="text-gray-400">info@binglan-tech.com</span></li><li class="flex items-center gap-3"><i class="fas fa-clock text-secondary"></i><span class="text-gray-400">周一至周五: 9:00 - 18:00</span></li></ul></div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center"><p class="text-gray-500 text-sm mb-4 md:mb-0">© 2023 广西冰蓝科技有限责任公司. 保留所有权利.</p><div class="flex gap-6"><a href="javascript:void(0);" class="text-gray-500 hover:text-gray-400 text-sm transition-colors duration-300">隐私政策</a><a href="javascript:void(0);" class="text-gray-500 hover:text-gray-400 text-sm transition-colors duration-300">使用条款</a><a href="javascript:void(0);" class="text-gray-500 hover:text-gray-400 text-sm transition-colors duration-300">网站地图</a></div></div>
        </div>
    </footer>
    <!-- [/MODULE] m3n_页脚 -->
</div>

<!-- 脚本：导航栏交互、业务范围标签切换、解决方案标签切换（已移除contact-form-script） -->
<script id="navbar-script">
    var navbar = document.getElementById('navbar');
    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            navbar.classList.add('bg-dark', 'shadow-lg');
            navbar.classList.remove('bg-glass');
        } else {
            navbar.classList.remove('bg-dark', 'shadow-lg');
            navbar.classList.add('bg-glass');
        }
    });
    var menuToggle = document.getElementById('menu-toggle');
    var mobileMenu = document.getElementById('mobile-menu');
    menuToggle.addEventListener('click', function () {
        mobileMenu.classList.toggle('hidden');
        var icon = menuToggle.querySelector('i');
        if (mobileMenu.classList.contains('hidden')) {
            icon.classList.remove('fa-times');
            icon.classList.add('fa-bars');
        } else {
            icon.classList.remove('fa-bars');
            icon.classList.add('fa-times');
        }
    });
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            if (!mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                var icon = menuToggle.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
            var targetId = this.getAttribute('href');
            if (targetId === '#') return;
            var targetElement = document.querySelector(targetId);
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 80,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
<script id="services-tab-script">
    var serviceTabs = document.querySelectorAll('.service-tab');
    var serviceContents = document.querySelectorAll('.service-content');
    serviceTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            serviceTabs.forEach(function (t) {
                t.classList.remove('active', 'bg-primary');
                t.classList.add('bg-dark-light', 'hover:bg-primary/20');
            });
            tab.classList.add('active', 'bg-primary');
            tab.classList.remove('bg-dark-light', 'hover:bg-primary/20');
            serviceContents.forEach(function (content) {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            var target = tab.getAttribute('data-target');
            var activeContent = document.getElementById(target);
            activeContent.classList.remove('hidden');
            activeContent.classList.add('active');
        });
    });
</script>
<script id="solutions-tab-script">
    var solutionTabs = document.querySelectorAll('.solution-tab');
    var solutionContents = document.querySelectorAll('.solution-content');
    solutionTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            solutionTabs.forEach(function (t) {
                t.classList.remove('active', 'bg-primary');
                t.classList.add('bg-dark-light', 'hover:bg-primary/20');
            });
            tab.classList.add('active', 'bg-primary');
            tab.classList.remove('bg-dark-light', 'hover:bg-primary/20');
            solutionContents.forEach(function (content) {
                content.classList.add('hidden');
                content.classList.remove('active');
            });
            var target = tab.getAttribute('data-target');
            var activeContent = document.getElementById(target);
            activeContent.classList.remove('hidden');
            activeContent.classList.add('active');
        });
    });
</script>
</body>
</html>