<html lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>桂民投总部基地 - 智慧小区展示</title>
    <script src="https://res.gemcoder.com/js/reload.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.bootcdn.net/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#0F3460',
                        secondary: '#E94560',
                        accent: '#16C2D5',
                        dark: '#1A1A2E',
                        light: '#F5F5F5'
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    },
                    spacing: {
                        'screen': '100vh'
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
                text-shadow: 0 2px 4px rgba(0,0,0,0.5);
            }
            .text-shadow-lg {
                text-shadow: 0 4px 8px rgba(0,0,0,0.7);
            }
            .bg-blur {
                backdrop-filter: blur(8px);
            }
        }
    </style>
</head>
<body class="font-sans text-gray-800 overflow-x-hidden bg-light">
<!-- [MODULE] a1b_主内容区域 -->
<div class="relative h-screen overflow-hidden">
    <!-- [MODULE] c2d_导航指示器 -->
    <div class="fixed right-8 top-1/2 transform -translate-y-1/2 z-50 flex flex-col space-y-4">
        <button class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-primary/80 text-white rounded-l-full opacity-100 group" data-index="0">
            <span class="mr-3 text-xl font-semibold tracking-wide"> 首页 </span>
            <i class="fas fa-home"> </i>
            <span
                    class="absolute right-0 w-1 h-10 bg-secondary transition-all duration-300"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="1"
        >
          <span class="mr-3 text-xl font-semibold tracking-wide">
            项目简介
          </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="2"
        >
          <span class="mr-3 text-xl font-semibold tracking-wide">
            项目图示
          </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="3"
        >
            <span class="mr-3 text-xl font-semibold tracking-wide"> 1号楼 </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="4"
        >
            <span class="mr-3 text-xl font-semibold tracking-wide"> 2号楼 </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="5"
        >
            <span class="mr-3 text-xl font-semibold tracking-wide"> 3号楼 </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="6"
        >
            <span class="mr-3 text-xl font-semibold tracking-wide"> 4号楼·酒店 </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="7"
        >
          <span class="mr-3 text-xl font-semibold tracking-wide">
            户型鉴赏
          </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
        <button
                class="nav-item flex items-center justify-end transition-all duration-300 py-2 px-4 bg-black/20 text-white/70 hover:text-white rounded-l-full opacity-100 group"
                data-index="8"
        >
          <span class="mr-3 text-xl font-semibold tracking-wide">
            集团简介
          </span>
            <span
                    class="absolute right-0 w-0 h-10 bg-secondary transition-all duration-300 group-hover:w-1"
            >
          </span>
        </button>
    </div>
    <!-- [/MODULE] c2d_导航指示器 -- 右侧垂直排列的导航点，点击可跳转到对应屏幕，当前屏幕导航点高亮显示 -->
    <!-- [MODULE] e3f_轮播图区域 -->
    <section class="h-screen relative overflow-hidden" id="section-0">
        <div class="carousel-container h-full w-full">
            <!-- 轮播图1 -->
            <div
                    class="carousel-slide absolute inset-0 transition-opacity duration-1000 opacity-100"
            >
                <img
                        alt="五象东江景CBD"
                        class="w-full h-full object-cover"
                        src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/m0TRqUKRiKy1kWVSfhvajEPaVqR3vxzO.png"
                />
                <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent flex flex-col justify-end p-16"
                >
                    <h2
                            class="text-[clamp(2rem,5vw,4rem)] font-bold text-white text-shadow-lg mb-4"
                    >
                        五象东江景CBD
                    </h2>
                    <p
                            class="text-[clamp(1.2rem,3vw,2rem)] text-white text-shadow max-w-3xl"
                    >
                        广西知名民企总部聚集区
                    </p>
                </div>
            </div>
            <!-- 轮播图2 -->
            <div
                    class="carousel-slide absolute inset-0 transition-opacity duration-1000 opacity-0"
            >
                <img
                        alt="广西3.0版中央商务区"
                        class="w-full h-full object-cover"
                        src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/2WVUxrP0CZ65fMrail9RZTEE82bSqldA.png"
                />
                <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent flex flex-col justify-end p-16"
                >
                    <h2
                            class="text-[clamp(2rem,5vw,4rem)] font-bold text-white text-shadow-lg mb-4"
                    >
                        广西3.0版中央商务区
                    </h2>
                    <p
                            class="text-[clamp(1.2rem,3vw,2rem)] text-white text-shadow max-w-3xl"
                    >
                        超级总部基地
                    </p>
                </div>
            </div>
            <!-- 轮播图3 -->
            <div
                    class="carousel-slide absolute inset-0 transition-opacity duration-1000 opacity-0"
            >
                <img
                        alt="桂民投总部基地智慧办公App下载"
                        class="w-full h-full object-cover"
                        src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/j8cBSNO1OSVAF1n1eBjMiesaBc4ahdIJ.png"
                />
                <div
                        class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent flex flex-col justify-end p-16"
                >
                    <h2
                            class="text-[clamp(2rem,5vw,4rem)] font-bold text-white text-shadow-lg mb-4"
                    >
                        桂民投总部基地
                    </h2>
                    <p
                            class="text-[clamp(1.2rem,3vw,2rem)] text-white text-shadow max-w-3xl mb-8"
                    >
                        智慧办公App下载
                    </p>
                    <div class="flex items-center space-x-6">
                        <button onclick="downloadApp()" class="bg-white text-primary px-8 py-4 rounded-full text-lg font-semibold flex items-center space-x-2 transform transition hover:scale-105">
                            <i class="fab fa-android text-2xl"> </i>
                            <span> 立即下载 </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- 轮播控制按钮 -->
        <button
                class="carousel-prev absolute left-8 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all z-10"
        >
            <i class="fas fa-chevron-left text-xl"> </i>
        </button>
        <button
                class="carousel-next absolute right-8 top-1/2 transform -translate-y-1/2 bg-white/30 hover:bg-white/50 text-white w-12 h-12 rounded-full flex items-center justify-center transition-all z-10"
        >
            <i class="fas fa-chevron-right text-xl"> </i>
        </button>
        <!-- 轮播指示器 -->
        <div
                class="absolute bottom-16 left-1/2 transform -translate-x-1/2 flex space-x-3 z-10"
        >
            <button
                    class="carousel-dot w-3 h-3 rounded-full bg-white opacity-100 transition-all"
                    data-index="0"
            ></button>
            <button
                    class="carousel-dot w-3 h-3 rounded-full bg-white opacity-50 transition-all"
                    data-index="1"
            ></button>
            <button
                    class="carousel-dot w-3 h-3 rounded-full bg-white opacity-50 transition-all"
                    data-index="2"
            ></button>
        </div>
        <!-- 向下滚动提示 -->
        <div
                class="absolute bottom-10 left-1/2 transform -translate-x-1/2 text-white animate-bounce z-10"
        >
            <i class="fas fa-chevron-down text-2xl"> </i>
        </div>
    </section>
    <!-- [/MODULE] e3f_轮播图区域 -- 首屏轮播图，包含三张轮播图片，分别展示五象东江景CBD、广西3.0版中央商务区和智慧办公App下载信息，配有轮播控制按钮和指示器 -->
    <!-- [MODULE] g4h_项目简介区域 -->
    <section class="h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center relative overflow-hidden" id="section-1">
        <div class="absolute inset-0 opacity-10">
            <img alt="Background pattern" class="w-full h-full object-cover" src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png" />
        </div>
        <div class="container mx-auto px-8 py-16 relative z-10">
            <h2 class="text-[clamp(2rem,5vw,3.5rem)] font-bold text-primary mb-12 text-center leading-tight tracking-wide">
                项目简介
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="order-2 md:order-1">
                    <img alt="项目效果图" class="w-full h-auto rounded-lg shadow-xl" src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/12/tkpHPnglhd5UFHDuPykd5sP7PoRRVWCc.jpg"
                    />
                </div>
                <div class="order-1 md:order-2 space-y-6">
                    <div class="flex items-start">
                        <div class="bg-primary text-white p-3 rounded-full mr-4">
                            <i class="fas fa-map-marker-alt text-xl"> </i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">
                                项目地址
                            </h3>
                            <p
                                    class="text-gray-600 text-lg leading-relaxed tracking-wide"
                            >
                                南宁市五象新区五象大道218号（五象大道和龙岗大道交汇处）
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-primary text-white p-3 rounded-full mr-4">
                            <i class="fas fa-building text-xl"> </i>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-1">
                                开发商
                            </h3>
                            <p class="text-gray-600">广西桂民投新材料有限公司</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="flex items-start">
                            <div class="bg-primary text-white p-2 rounded-full mr-3">
                                <i class="fas fa-ruler-combined text-lg"> </i>
                            </div>
                            <div>
                                <h3
                                        class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                >
                                    占地面积
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    70.35亩
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary text-white p-2 rounded-full mr-3">
                                <i class="fas fa-cubes text-lg"> </i>
                            </div>
                            <div>
                                <h3
                                        class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                >
                                    建筑面积
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    约368263㎡
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary text-white p-2 rounded-full mr-3">
                                <i class="fas fa-sort-amount-up text-lg"> </i>
                            </div>
                            <div>
                                <h3
                                        class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                >
                                    容积率
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    5.86
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary text-white p-2 rounded-full mr-3">
                                <i class="fas fa-tree text-lg"> </i>
                            </div>
                            <div>
                                <h3
                                        class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                >
                                    绿地率
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    25.82%
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-primary text-white p-2 rounded-full mr-3">
                            <i class="fas fa-home text-lg"> </i>
                        </div>
                        <div>
                            <h3
                                    class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                            >
                                物业类型
                            </h3>
                            <p
                                    class="text-gray-600 text-lg leading-relaxed tracking-wide"
                            >
                                写字楼、公寓
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-primary text-white p-2 rounded-full mr-3">
                            <i class="fas fa-user-friends text-lg"> </i>
                        </div>
                        <div>
                            <h3
                                    class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                            >
                                总户数
                            </h3>
                            <p
                                    class="text-gray-600 text-lg leading-relaxed tracking-wide"
                            >
                                写字楼528套，公寓1552套，商铺150套
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="bg-primary text-white p-2 rounded-full mr-3">
                            <i class="fas fa-car text-lg"> </i>
                        </div>
                        <div>
                            <h3
                                    class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                            >
                                车位
                            </h3>
                            <p
                                    class="text-gray-600 text-lg leading-relaxed tracking-wide"
                            >
                                机动车停车位 2815个
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] g4h_项目简介区域 -- 展示项目的基本信息，包括地址、开发商、占地面积、建筑面积、容积率、绿地率、物业类型、总户数和车位等详细信息 -->
    <!-- [MODULE] i5j_项目图示区域 -->
    <section class="h-screen bg-gray-50 py-16" id="section-2">
        <div class="container mx-auto px-8">
            <h2
                    class="text-[clamp(1.8rem,4vw,3rem)] font-bold text-primary mb-12 text-center"
            >
                项目图示
            </h2>
            <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-8 h-[calc(100vh-12rem)]"
            >
                <!-- 项目区位图 -->
                <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col h-full transform transition hover:shadow-xl"
                >
                    <div class="bg-primary text-white p-4">
                        <h3 class="text-xl font-semibold">项目区位图</h3>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <img alt="项目区位图" class="w-full h-full object-cover transition-transform duration-700 hover:scale-110" src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/p9ZhkpnlRfFxnU4vcav2gUAadPtHqulW.png" />
                    </div>
                </div>
                <!-- 项目鸟瞰图 -->
                <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col h-full transform transition hover:shadow-xl"
                >
                    <div class="bg-primary text-white p-4">
                        <h3 class="text-xl font-semibold">项目鸟瞰图</h3>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <img
                                alt="项目鸟瞰图"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-110"
                                src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/eT7zmLlNGvy0eWaZq3AiwGDdgG3kJUTI.png"
                        />
                    </div>
                </div>
                <!-- 项目总平图 -->
                <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col h-full transform transition hover:shadow-xl"
                >
                    <div class="bg-primary text-white p-4">
                        <h3 class="text-xl font-semibold">2#/3#楼低区平面图</h3>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <img
                                alt="2#/3#楼低区平面图"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-110"
                                src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/GW2lzlccEZtLduY3p4VzshnVP5ZbHSbs.png"
                        />
                    </div>
                </div>
                <!-- 户型平面图 -->
                <div
                        class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col h-full transform transition hover:shadow-xl"
                >
                    <div class="bg-primary text-white p-4">
                        <h3 class="text-xl font-semibold">2#/3#楼中区平面图</h3>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <img
                                alt="2#/3#楼中区平面图"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-110"
                                src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/Y52FDye3aOELqjjxjl5BPgLiBqwFTcxF.png"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] i5j_项目图示区域 -- 展示项目的区位图、鸟瞰图、总平图和户型平面图，每张图片配有标题，鼠标悬停时有缩放效果 -->
    <!-- [MODULE] k6l_1#楼介绍区域 -->
    <section class="h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center relative overflow-hidden" id="section-3">
        <div class="absolute inset-0 opacity-10">
            <img alt="Background pattern" class="w-full h-full object-cover" src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png" />
        </div>
        <div class="container mx-auto px-8 py-16 relative z-10">
            <h2
                    class="text-[clamp(2rem,5vw,3.5rem)] font-bold text-primary mb-12 text-center leading-tight tracking-wide"
            >
                1#楼 · 甲级商务写字楼
            </h2>
            <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center h-[calc(100vh-16rem)]"
            >
                <div class="order-2 lg:order-1">
                    <img
                            alt="1#楼外观图"
                            class="w-full h-full object-cover rounded-lg shadow-xl"
                            src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/12/bvdA4GPCgl4XOepwJ0ALhT2mrDMxrccX.png"
                    />
                </div>
                <div class="order-1 lg:order-2 space-y-6">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3
                                        class="text-2xl font-bold text-primary mb-2 tracking-wide"
                                >
                                    1#楼
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    共计39层，建筑高度195米
                                </p>
                            </div>
                            <div
                                    class="bg-primary text-white px-4 py-2 rounded-full text-lg font-semibold"
                            >
                                写字楼
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="flex items-start">
                                <div
                                        class="bg-primary/10 text-primary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-ruler-combined text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        单层面积
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        1997㎡
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                        class="bg-primary/10 text-primary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-building text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        单层套数
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        16套
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                        class="bg-primary/10 text-primary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-home text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        总户数
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        写字楼528套
                                        <br />
                                        商铺46套
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                        class="bg-primary/10 text-primary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-arrows-alt-h text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        户型面积
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        约84、130、169㎡
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-primary/10 text-primary p-2 rounded-full mr-3">
                                <i class="fas fa-arrows-alt-v text-lg"> </i>
                            </div>
                            <div>
                                <h4
                                        class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                >
                                    层高
                                </h4>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    5.09米
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] k6l_1#楼介绍区域 -- 展示1#楼的基本信息，包括层数、高度、面积、套数和户型等详细数据 -->
    <!-- [MODULE] l7m_2#楼介绍区域 -->
    <section
            class="h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center relative overflow-hidden"
            id="section-4"
    >
        <div class="absolute inset-0 opacity-10">
            <img
                    alt="Background pattern"
                    class="w-full h-full object-cover"
                    src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png"
            />
        </div>
        <div class="container mx-auto px-8 py-16 relative z-10">
            <h2
                    class="text-[clamp(2rem,5vw,3.5rem)] font-bold text-primary mb-12 text-center leading-tight tracking-wide"
            >
                2#楼 · 商务公寓
            </h2>
            <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center h-[calc(100vh-16rem)]"
            >
                <div class="order-2 lg:order-1">
                    <img
                            alt="2#楼外观图"
                            class="w-full h-full object-cover rounded-lg shadow-xl"
                            src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/12/vMKT7S9ffNWLQF3ge3jwm3uMFywR2mNi.png"
                    />
                </div>
                <div class="order-1 lg:order-2 space-y-6">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3
                                        class="text-2xl font-bold text-primary mb-2 tracking-wide"
                                >
                                    2#楼
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    共计35层，建筑高度170米
                                </p>
                            </div>
                            <div
                                    class="bg-secondary text-white px-4 py-2 rounded-full text-lg font-semibold"
                            >
                                公寓
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="flex items-start">
                                <div
                                        class="bg-secondary/10 text-secondary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-ruler-combined text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        单层面积
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        1932㎡
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                        class="bg-secondary/10 text-secondary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-home text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        总户数
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        公寓882套
                                        <br />
                                        商铺65套
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                        class="bg-secondary/10 text-secondary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-arrows-alt-h text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        户型面积
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        约42、63、124㎡
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div
                                        class="bg-secondary/10 text-secondary p-2 rounded-full mr-3"
                                >
                                    <i class="fas fa-arrows-alt-v text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        层高
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        5.09米
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                                class="bg-secondary/5 p-4 rounded-lg border-l-4 border-secondary"
                        >
                            <p
                                    class="text-gray-700 text-lg leading-relaxed tracking-wide"
                            >
                                灵活多变的空间设计，满足商务人士多样化需求，打造舒适办公与生活环境。
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] l7m_2#楼介绍区域 -- 展示2#楼的基本信息，包括层数、高度、面积、套数和户型等详细数据 -->
    <!-- [MODULE] n9o_3#楼介绍区域 -->
    <section
            class="h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center relative overflow-hidden"
            id="section-5"
    >
        <div class="absolute inset-0 opacity-10">
            <img
                    alt="Background pattern"
                    class="w-full h-full object-cover"
                    src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png"
            />
        </div>
        <div class="container mx-auto px-8 py-16 relative z-10">
            <h2
                    class="text-[clamp(2rem,5vw,3.5rem)] font-bold text-primary mb-12 text-center leading-tight tracking-wide"
            >
                3#楼 · 创客公寓
            </h2>
            <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center h-[calc(100vh-16rem)]"
            >
                <div class="order-2 lg:order-1">
                    <img
                            alt="3#楼外观图"
                            class="w-full h-full object-cover rounded-lg shadow-xl"
                            src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/12/nEDuAMGCLmrs6dBfbvbMha7uItqYUH28.png"
                    />
                </div>
                <div class="order-1 lg:order-2 space-y-6">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3
                                        class="text-2xl font-bold text-primary mb-2 tracking-wide"
                                >
                                    3#楼
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    共计25层，建筑高度125米
                                </p>
                            </div>
                            <div
                                    class="bg-accent text-white px-4 py-2 rounded-full text-lg font-semibold"
                            >
                                创客空间
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 mb-6">
                            <div class="flex items-start">
                                <div class="bg-accent/10 text-accent p-2 rounded-full mr-3">
                                    <i class="fas fa-ruler-combined text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        单层面积
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        1932㎡
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-accent/10 text-accent p-2 rounded-full mr-3">
                                    <i class="fas fa-home text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        总户数
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        公寓670套
                                        <br />
                                        商铺39套
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-accent/10 text-accent p-2 rounded-full mr-3">
                                    <i class="fas fa-arrows-alt-h text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        户型面积
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        约42、63、124㎡
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="bg-accent/10 text-accent p-2 rounded-full mr-3">
                                    <i class="fas fa-arrows-alt-v text-lg"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-lg font-semibold text-gray-800 mb-1 tracking-wide"
                                    >
                                        层高
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        5.09米
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div
                                class="bg-accent/5 p-4 rounded-lg border-l-4 border-accent"
                        >
                            <p
                                    class="text-gray-700 text-lg leading-relaxed tracking-wide"
                            >
                                专为创业者和创意人士打造的灵活空间，提供高效办公环境和交流平台。
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] n9o_3#楼介绍区域 -- 展示3#楼的基本信息，包括层数、高度、面积、套数和户型等详细数据 -->
    <!-- [MODULE] p0q_4#楼介绍区域 -->
    <section
            class="h-screen bg-gradient-to-br from-gray-50 to-gray-100 flex items-center relative overflow-hidden"
            id="section-6"
    >
        <div class="absolute inset-0 opacity-10">
            <img
                    alt="Background pattern"
                    class="w-full h-full object-cover"
                    src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png"
            />
        </div>
        <div class="container mx-auto px-8 py-16 relative z-10">
            <h2
                    class="text-[clamp(2rem,5vw,3.5rem)] font-bold text-primary mb-12 text-center leading-tight tracking-wide"
            >
                4#楼 · 五星级酒店
            </h2>
            <div
                    class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center h-[calc(100vh-16rem)]"
            >
                <div class="order-2 lg:order-1">
                    <img
                            alt="4#楼五星级酒店外观"
                            class="w-full h-full object-cover rounded-lg shadow-xl"
                            src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/12/4GRvFpevoCb2K6StjtJ39evkYpQAm0Kc.png"
                    />
                </div>
                <div class="order-1 lg:order-2 space-y-6">
                    <div class="bg-white p-8 rounded-lg shadow-lg">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3
                                        class="text-2xl font-bold text-primary mb-2 tracking-wide"
                                >
                                    4#楼
                                </h3>
                                <p
                                        class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                >
                                    五星级酒店，自持运营，34层，160米
                                </p>
                            </div>
                            <div
                                    class="bg-primary text-white px-4 py-2 rounded-full text-lg font-semibold"
                            >
                                酒店
                            </div>
                        </div>
                        <div class="mb-6">
                            <div class="flex items-start mb-6">
                                <div
                                        class="bg-primary/10 text-primary p-3 rounded-full mr-4"
                                >
                                    <i class="fas fa-utensils text-xl"> </i>
                                </div>
                                <div>
                                    <h4
                                            class="text-xl font-semibold text-gray-800 mb-2 tracking-wide"
                                    >
                                        广西最大宴会厅
                                    </h4>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide mb-2"
                                    >
                                        容纳200桌，共2000人
                                    </p>
                                    <p
                                            class="text-gray-600 text-lg leading-relaxed tracking-wide"
                                    >
                                        满足大型商务会议、婚宴及各类活动需求
                                    </p>
                                </div>
                            </div>
                            <div class="bg-primary/5 p-4 rounded-lg">
                                <p
                                        class="text-gray-700 text-lg leading-relaxed tracking-wide"
                                >
                                    五星级酒店是地标商务中心的标配，相关的资产、租金超越没有配备酒店的项目，为整个社区提供高端商务配套服务。
                                </p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-concierge-bell text-primary mr-3 text-xl">
                                </i>
                                <span
                                        class="text-gray-700 text-lg leading-relaxed tracking-wide"
                                >
                      高端礼宾服务
                    </span>
                            </div>
                            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-clipboard-check text-primary mr-3 text-xl">
                                </i>
                                <span
                                        class="text-gray-700 text-lg leading-relaxed tracking-wide"
                                >
                      商务会议中心
                    </span>
                            </div>
                            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-utensils text-primary mr-3 text-xl"> </i>
                                <span
                                        class="text-gray-700 text-lg leading-relaxed tracking-wide"
                                >
                      米其林餐厅
                    </span>
                            </div>
                            <div class="flex items-center bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-spa text-primary mr-3 text-xl"> </i>
                                <span
                                        class="text-gray-700 text-lg leading-relaxed tracking-wide"
                                >
                      豪华健身中心
                    </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] p0q_4#楼介绍区域 -- 展示4#楼五星级酒店的基本信息，包括层数、高度、宴会厅容量及配套设施等详细数据 -->
    <!-- [MODULE] m7n_户型鉴赏区域 -->
    <section
            class="h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-16 relative overflow-hidden"
            id="section-7"
    >
        <div class="absolute inset-0 opacity-10">
            <img
                    alt="Background pattern"
                    class="w-full h-full object-cover"
                    src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png"
            />
        </div>
        <div class="container mx-auto px-8 relative z-10">
            <h2 class="text-[clamp(2rem,5vw,3.5rem)] font-bold text-primary mb-12 text-center leading-tight tracking-wide">
                户型鉴赏
            </h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 h-[calc(100vh-12rem)]">
                <!-- 办公室 -->
                <div
                        class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col h-full"
                >
                    <div class="bg-primary text-white p-4">
                        <h3 class="text-2xl font-semibold tracking-wide">办公室</h3>
                        <p class="text-white/80 text-lg leading-relaxed tracking-wide">
                            办公样板间实景图
                        </p>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <img
                                alt="办公室样板间"
                                class="w-full h-full object-cover"
                                src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/hhUgiPVlsRFLEIjId1cqGlTi36yPQlmV.png"
                        />
                    </div>
                    <div class="p-6">
                        <h4
                                class="text-xl font-semibold mb-4 text-gray-800 tracking-wide"
                        >
                            户型优势
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ①
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      8.4米大开间，工作之余纵览八尺江畔。
                    </span>
                            </li>
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ②
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      方正、高拓展户型，空间灵活多变。
                    </span>
                            </li>
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ③
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      5.09米层高，双层空间，办公生活一室无忧。
                    </span>
                            </li>
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ④
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      独立总裁办公室，商务会客从容掌握。
                    </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- loft创客空间 -->
                <div
                        class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col h-full"
                >
                    <div class="bg-primary text-white p-4">
                        <h3 class="text-2xl font-semibold tracking-wide">
                            loft创客空间
                        </h3>
                        <p class="text-white/80 text-lg leading-relaxed tracking-wide">
                            5.09米loft创客空间样板间实景图
                        </p>
                    </div>
                    <div class="flex-grow overflow-hidden">
                        <img
                                alt="loft创客空间样板间"
                                class="w-full h-full object-cover"
                                src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/qEaFvDaQO7fQqY88sGyRYkmUQ0MVgzqu.png"
                        />
                    </div>
                    <div class="p-6">
                        <h4
                                class="text-xl font-semibold mb-4 text-gray-800 tracking-wide"
                        >
                            户型优势
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ①
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      高拓展面积，尽享两房待遇。
                    </span>
                            </li>
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ②
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      5.09米层高，轻享双层生活。
                    </span>
                            </li>
                            板
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ③
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      动静分离，奢享墅级体验。
                    </span>
                            </li>
                            <li class="flex items-start">
                    <span
                            class="bg-primary text-white rounded-full w-6 h-6 flex items-center justify-center mr-3 flex-shrink-0 mt-0.5"
                    >
                      ④
                    </span>
                                <span class="text-lg leading-relaxed tracking-wide">
                      低总价、低月供，地铁物业潜力。
                    </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] m7n_户型鉴赏区域 -- 展示两种户型：办公室和loft创客空间，每种户型包含实景图和户型优势列表 -->
    <!-- [MODULE] o8p_集团简介区域 -->
    <section class="h-screen bg-primary text-white py-16 relative overflow-hidden" id="section-8">
        <div class="absolute inset-0 opacity-10">
            <img
                    src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/FPmfatB8hc1C84G0kmAakDQktD64hR0j.png"
                    alt="Background pattern"
                    class="w-full h-full object-cover"
            />
        </div>
        <div class="container mx-auto px-8 relative z-10">
            <h2 class="text-[clamp(2rem,5vw,3.5rem)] font-bold mb-12 text-center leading-tight tracking-wide">
                集团简介
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-6 gap-8 h-[calc(100vh-12rem)]">
                <div class="md:col-span-3 flex flex-col justify-center">
                    <div class="bg-white/10 backdrop-blur-sm p-8 rounded-lg h-full flex flex-col justify-center">
                        <h3 class="text-2xl font-bold mb-6 text-white tracking-wide">
                            广西桂民投新材料科技有限公司
                        </h3>
                        <p class="text-white/90 mb-6 text-lg leading-relaxed tracking-wide">
                            （简称：桂民投集团）在自治区党委、自治区人民政府的大力支持下，在自治区党委统战部、自治区非公办的指导下，由广西非公经济"两个健康"促进会作为主发起，广西20家知名民营企业组建而成。
                        </p>
                        <p class="text-white/90 mb-6 text-lg leading-relaxed tracking-wide">
                            业务涉及资产管理、建筑施工、房地产开发投资、仓储物流、机械制造等多个领域。
                        </p>
                        <ul class="space-y-3 text-white/90">
                            <li class="flex items-start text-lg leading-relaxed tracking-wide">
                                <i class="fas fa-check-circle text-secondary mr-3 mt-1">
                                </i>
                                <span>
                      南宁空港扶绥经济区桂民投产业园及山圩木业桂民投产业园
                    </span>
                            </li>
                            <li class="flex items-start text-lg leading-relaxed tracking-wide">
                                <i class="fas fa-check-circle text-secondary mr-3 mt-1">
                                </i>
                                <span> 藤县新材料产业园 </span>
                            </li>
                            <li class="flex items-start text-lg leading-relaxed tracking-wide">
                                <i class="fas fa-check-circle text-secondary mr-3 mt-1">
                                </i>
                                <span> 桂民投总部基地 </span>
                            </li>
                            <li class="flex items-start text-lg leading-relaxed tracking-wide">
                                <i class="fas fa-check-circle text-secondary mr-3 mt-1">
                                </i>
                                <span> 玉林桂民投智慧物流园 </span>
                            </li>
                            <li class="flex items-start text-lg leading-relaxed tracking-wide">
                                <i class="fas fa-check-circle text-secondary mr-3 mt-1">
                                </i>
                                <span> 桂民投碳酸钙全产业链精深加工等项目 </span>
                            </li>
                            <li class="flex items-start text-lg leading-relaxed tracking-wide">
                                <i class="fas fa-check-circle text-secondary mr-3 mt-1">
                                </i>
                                <span> 累计签约投资额超400亿元 </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="md:col-span-3 flex flex-col">
                    <div class="bg-white/10 backdrop-blur-sm p-8 rounded-lg flex-grow mb-8">
                        <p class="text-white/90 mb-6 text-lg leading-relaxed tracking-wide">
                            桂民投集团全方位参与广西产业园区、广西民营银行、千亿元碳酸钙和大健康产业等重点产业的投资、开发、建设、运营，投资建设项目遍布南宁、梧州、玉林、崇左等多个市县。
                        </p>
                        <img alt="桂民投总部基地" class="w-auto rounded-lg shadow-xl" src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/Wm84l2lDWvXw3UUUD85olbrD67qJ36u6.png" height="8em" />
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm p-5 rounded-lg">
                        <!-- [MODULE] qr1_联系信息区域 -->
                        <div class="rounded-lg">
                            <div class="flex items-center justify-between gap-8">
                                <div class="bg-white p-3 rounded-lg">
                                    <img src="https://guimintou-1253887929.cos.ap-guangzhou.myqcloud.com/images/1/2025/11/oASUO3aTRFwaubLVGVcwNJiQTSMrRl5Z.png" alt="WeChat QR code" class="object-contain" />
                                </div>
                            </div>
                        </div>
                        <!-- [/MODULE] qr1_联系信息区域 -- 展示联系电话和微信公众号二维码 -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- [/MODULE] o8p_集团简介区域 -- 展示桂民投集团的基本信息、业务范围和投资规模，背景为深蓝色，文字为白色，包含集团标志和联系方式 -->
</div>
<!-- [/MODULE] a1b_主内容区域 -- 包含整个智慧小区展示页面的所有内容模块 -->
<!-- [JSMOD] q9r_轮播图控制脚本 -->
<script id="carousel-script">
    // 轮播图功能
    document.addEventListener('DOMContentLoaded', function () {
        var slides = document.querySelectorAll('.carousel-slide');
        var dots = document.querySelectorAll('.carousel-dot');
        var prevBtn = document.querySelector('.carousel-prev');
        var nextBtn = document.querySelector('.carousel-next');
        var currentIndex = 0;
        var slideInterval;
        // 初始化轮播
        function initCarousel() {
            showSlide(currentIndex);
            startSlideInterval();
            // 事件监听
            prevBtn.addEventListener('click', function () {
                prevSlide();
                resetSlideInterval();
            });
            nextBtn.addEventListener('click', function () {
                nextSlide();
                resetSlideInterval();
            });
            dots.forEach(function (dot) {
                dot.addEventListener('click', function () {
                    currentIndex = parseInt(dot.dataset.index);
                    showSlide(currentIndex);
                    resetSlideInterval();
                });
            });
        }
        // 显示指定索引的幻灯片
        function showSlide(index) {
            // 隐藏所有幻灯片
            slides.forEach(function (slide) {
                slide.classList.add('opacity-0');
                slide.classList.remove('opacity-100');
            });
            // 重置所有指示器
            dots.forEach(function (dot) {
                dot.classList.remove('opacity-100');
                dot.classList.add('opacity-50');
            });
            // 显示当前幻灯片和指示器
            slides[index].classList.add('opacity-100');
            slides[index].classList.remove('opacity-0');
            dots[index].classList.add('opacity-100');
            dots[index].classList.remove('opacity-50');
        }
        // 上一张幻灯片
        function prevSlide() {
            currentIndex = (currentIndex - 1 + slides.length) % slides.length;
            showSlide(currentIndex);
        }
        // 下一张幻灯片
        function nextSlide() {
            currentIndex = (currentIndex + 1) % slides.length;
            showSlide(currentIndex);
        }
        // 开始自动轮播
        function startSlideInterval() {
            slideInterval = setInterval(nextSlide, 5000);
        }
        // 重置自动轮播计时器
        function resetSlideInterval() {
            clearInterval(slideInterval);
            startSlideInterval();
        }
        // 初始化轮播
        initCarousel();
    });
</script>
<!-- [/JSMOD] q9r_轮播图控制脚本 -- 控制首屏轮播图的自动播放和手动切换功能，包括上一张、下一张按钮和轮播指示器的交互 -->
<!-- 备用方案：最简脚本 -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 最简化的滚动控制
        const sections = document.querySelectorAll('section[id^="section-"]');
        const navButtons = document.querySelectorAll('.nav-item');
        let isScrolling = false, currentIndex = 0;

        // 导航按钮点击
        navButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const index = parseInt(this.getAttribute('data-index'));
                scrollTo(index);
            });
        });

        // 鼠标滚轮
        window.addEventListener('wheel', function(e) {
            if (isScrolling) return;

            //const current = Math.round(window.scrollY / window.innerHeight);
            console.log(e.deltaY);

            if (e.deltaY > 50) {
                scrollTo(currentIndex + 1);
            } else if (e.deltaY < -50) {
                scrollTo(currentIndex - 1);
            }
        }, { passive: false });

        // 触屏滑动
        let touchStart = 0;
        window.addEventListener('touchstart', e => touchStart = e.touches[0].clientY);
        window.addEventListener('touchend', e => {
            if (isScrolling) return;

            const touchEnd = e.changedTouches[0].clientY;
            const diff = touchStart - touchEnd;

            if (diff > 50) {
                scrollTo(currentIndex + 1);
            } else if (diff < -50) {
                scrollTo(currentIndex - 1);
            }
        });

        // 核心滚动函数
        function scrollTo(index) {
            if (index < 0 || index >= sections.length || isScrolling) return;

            isScrolling = true;

            // 更新导航
            navButtons.forEach((btn, i) => {
                const indicator = btn.querySelector('span:last-child');
                if (i === index) {
                    btn.classList.add('bg-primary/80', 'text-white');
                    btn.classList.remove('bg-black/20', 'text-white/70');
                    if (indicator) {
                        indicator.classList.add('w-1');
                        indicator.classList.remove('w-0');
                    }
                } else {
                    btn.classList.add('bg-black/20', 'text-white/70');
                    btn.classList.remove('bg-primary/80', 'text-white');
                    if (indicator && i !== 0) {
                        indicator.classList.add('w-0');
                        indicator.classList.remove('w-1');
                    }
                }
            });

            // 滚动
            sections[index].scrollIntoView({ behavior: 'smooth' });

            currentIndex = index;

            // 重置滚动状态
            setTimeout(() => isScrolling = false, 200);
        }
    });
</script>
<script type="text/javascript">
    function downloadApp() {
        window.open("http://111.59.18.164:10773/app/m/swa_service/client/download?appid=1&i=1", "_blank");
    }
</script>
</body>
</html>