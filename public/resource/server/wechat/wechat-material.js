// 微服务微信公众号素材选择器
(function() {
    // 保存原始的util.material函数
    var originalUtilMaterial = window.util.material;

    // 重写util.material函数
    window.util.material = function(callback, options) {
        var defaultOptions = {
            type: 'news',
            multiple: false,
            ignore: {
                'basic': false,
                'wxcard': true,
                'image': false,
                'music': false,
                'news': false,
                'video': false,
                'voice': false,
                'keyword': false,
                'module': false
            },
            others: {
                'basic': {
                    'typeVal': ''
                },
                'news': {
                    'showwx': true,
                    'showlocal': true
                }
            }
        };

        options = $.extend({}, defaultOptions, options);

        // 如果是微服务环境，使用自定义的素材选择器
        if (window.location.pathname.indexOf('/server/wechat/') !== -1) {
            return wechatMaterialSelector(callback, options);
        }

        // 否则使用原始函数
        return originalUtilMaterial(callback, options);
    };

        // 微服务素材选择器
    window.wechatMaterialSelector = function(callback, options) {
        var materialType = options.type || 'image';
        var currentPage = 1;

        // 创建模态框
        var modalHtml = buildMaterialModal(materialType);
        $('#wechat-material-modal').remove();
        $('body').append(modalHtml);

        var $modal = $('#wechat-material-modal');
        var $content = $modal.find('.material-content');

        // 根据类型加载不同的内容
        if (materialType === 'keyword') {
            // 加载关键字列表
            loadKeywordList($content);
        } else {
            // 加载素材列表，设定合理的每页数量
            loadMaterialList(materialType, currentPage, $content);
        }

        // 绑定事件
        bindMaterialEvents($modal, callback, materialType);

        // 显示模态框
        $modal.modal('show');

        return $modal;
    }

    // 构建素材选择模态框
    function buildMaterialModal(type) {
        var title = getMaterialTypeTitle(type);
        return `
            <div class="modal fade" id="wechat-material-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">选择${title}</h4>
                        </div>
                        <div class="modal-body">
                            <div class="material-content-wrapper">
                                <div class="material-content">
                                    <div class="text-center">
                                        <i class="fa fa-spinner fa-spin"></i> 加载中...
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-primary" id="select-material-btn">确定</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // 获取素材类型标题
    function getMaterialTypeTitle(type) {
        var titles = {
            'image': '图片',
            'voice': '语音',
            'video': '视频',
            'news': '图文'
        };
        return titles[type] || '素材';
    }

        // 渲染分页导航
    function renderPagination(currentPage, totalPages, type) {
        if (totalPages <= 1) {
            return '';
        }

        var html = '<div class="text-center"><ul class="pagination pagination-sm">';

        // 上一页
        if (currentPage > 1) {
            html += '<li><a href="#" page="' + (currentPage - 1) + '" data-type="' + type + '">&laquo;</a></li>';
        }

        // 页码
        var startPage = Math.max(1, currentPage - 2);
        var endPage = Math.min(totalPages, currentPage + 2);

        // 显示第一页
        if (startPage > 1) {
            html += '<li><a href="#" page="1" data-type="' + type + '">1</a></li>';
            if (startPage > 2) {
                html += '<li class="disabled"><span>...</span></li>';
            }
        }

        for (var i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += '<li class="active"><span>' + i + '</span></li>';
            } else {
                html += '<li><a href="#" page="' + i + '" data-type="' + type + '">' + i + '</a></li>';
            }
        }

        // 显示最后一页
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += '<li class="disabled"><span>...</span></li>';
            }
            html += '<li><a href="#" page="' + totalPages + '" data-type="' + type + '">' + totalPages + '</a></li>';
        }

        // 下一页
        if (currentPage < totalPages) {
            html += '<li><a href="#" page="' + (currentPage + 1) + '" data-type="' + type + '">&raquo;</a></li>';
        }

        html += '</ul></div>';
        return html;
    }

            // 加载关键字列表
    function loadKeywordList($content) {
        // 显示加载状态
        $content.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>');

        // 从URL中获取uniacid
        var urlParams = new URLSearchParams(window.location.search);
        var uniacid = urlParams.get('i') || '1';
        var url = '/server/wechat/util?do=keyword&i=' + uniacid;

        $.getJSON(url, function(data) {
            var keywords = [];

            // 解析关键字数据
            if (data.message && data.message.items && data.message.items.length > 0) {
                keywords = data.message.items;
            } else if (data.data && data.data.items && data.data.items.length > 0) {
                keywords = data.data.items;
            } else if (data.message && Array.isArray(data.message)) {
                keywords = data.message;
            }

            if (keywords.length > 0) {
                renderKeywordList(keywords, $content);
            } else {
                $content.html('<div class="text-center text-muted">暂无关键字</div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('请求关键字失败:', error);
            $content.html('<div class="text-center text-danger">加载失败: ' + error + '</div>');
        });
    }

        // 加载素材列表
    function loadMaterialList(type, page, $content) {
        // 显示加载状态
        $content.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> 加载中...</div>');

        // 从URL中获取uniacid
        var urlParams = new URLSearchParams(window.location.search);
        var uniacid = urlParams.get('i') || '1';
        // 设定合理的每页数量，避免内容过多
        var pageSize = 12; // 每页显示12个素材
        var url = '/server/wechat/util?do=' + type + '&page=' + page + '&page_size=' + pageSize + '&i=' + uniacid;

        $.getJSON(url, function(data) {
            // 修复数据解析逻辑，适配API返回的数据结构
            var items = [];
            if (data.message && data.message.items && data.message.items.length > 0) {
                items = data.message.items;
            } else if (data.message && data.message.list && data.message.list.length > 0) {
                items = data.message.list;
            } else if (data.message && Array.isArray(data.message)) {
                items = data.message;
            } else if (data.data && data.data.items && data.data.items.length > 0) {
                // 适配新的API返回格式
                items = data.data.items;
            } else if (data.data && data.data.list && data.data.list.length > 0) {
                items = data.data.list;
            }

            if (items.length > 0) {
                // 添加分页信息
                var total = data.total || data.message.total || data.message.total_count || 0;
                var currentPage = data.page || data.message.page || page || 1;
                var pageSize = data.page_size || data.message.page_size || 12;
                var totalPages = Math.ceil(total / pageSize);

                renderMaterialList(type, {
                    items: items,
                    total: total,
                    currentPage: currentPage,
                    pageSize: pageSize,
                    totalPages: totalPages
                }, $content);
            } else {
                $content.html('<div class="text-center text-muted">暂无素材</div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('请求失败:', error);
            $content.html('<div class="text-center text-danger">加载失败: ' + error + '</div>');
        });
    }

        // 渲染关键字列表
    function renderKeywordList(keywords, $content) {
        var html = '<div class="row">';
        keywords.forEach(function(keyword) {
            html += `
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="keyword-item" data-keyword="${keyword.content || keyword.name}" style="cursor: pointer; padding: 15px; border: 1px solid #ddd; margin: 5px; border-radius: 4px; text-align: center;">
                        <div class="keyword-info">
                            <div class="keyword-name" style="font-size: 14px; font-weight: bold;">${keyword.content || keyword.name}</div>
                            ${keyword.description ? '<div class="keyword-desc" style="font-size: 12px; color: #666; margin-top: 5px;">' + keyword.description + '</div>' : ''}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        $content.html(html);

        // 绑定关键字选择事件
        $content.find('.keyword-item').click(function() {
            $content.find('.keyword-item').removeClass('selected');
            $(this).addClass('selected');
        });
    }

    // 渲染素材列表
    function renderMaterialList(type, data, $content) {
        var html = '';

        if (type === 'image') {
            html = renderImageList(data.items);
        } else if (type === 'voice') {
            html = renderVoiceList(data.items);
        } else if (type === 'video') {
            html = renderVoiceList(data.items);
        }

        // 添加分页导航
        if (data.totalPages > 1) {
            html += renderPagination(data.currentPage, data.totalPages, type);
        }

        $content.html(html);

        // 绑定素材选择事件
        $content.find('.material-item').click(function() {
            $content.find('.material-item').removeClass('selected');
            $(this).addClass('selected');
        });
    }

    // 渲染图片列表
    function renderImageList(items) {
        var html = '<div class="row">';
        items.forEach(function(item) {
            // 使用安全的预览接口，避免防盗链问题
            var previewUrl = '/server/wechat/material/preview?type=image&mid=' + item.id;
            html += `
                <div class="col-md-3 col-sm-4 col-xs-6">
                    <div class="material-item" data-id="${item.id}" data-media-id="${item.media_id}" data-url="${item.attachment}">
                        <div class="material-thumb">
                            <img src="${previewUrl}" class="img-responsive" alt="${item.filename}" onerror="this.src='/static/images/nopic.jpg'">
                        </div>
                        <div class="material-info">
                            <div class="material-name">${item.filename}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        return html;
    }

    // 渲染语音列表
    function renderVoiceList(items) {
        var html = '<div class="row">';
        items.forEach(function(item) {
            html += `
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="material-item" data-id="${item.id}" data-media-id="${item.media_id}" data-url="${item.attachment}">
                        <div class="material-thumb">
                            <i class="fa fa-volume-up fa-3x text-muted"></i>
                        </div>
                        <div class="material-info">
                            <div class="material-name">${item.filename}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        return html;
    }

    // 渲染视频列表
    function renderVideoList(items) {
        var html = '<div class="row">';
        items.forEach(function(item) {
            html += `
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="material-item" data-id="${item.id}" data-media-id="${item.media_id}" data-url="${item.attachment}">
                        <div class="material-thumb">
                            <i class="fa fa-video-camera fa-3x text-muted"></i>
                        </div>
                        <div class="material-info">
                            <div class="material-name">${item.filename}</div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        return html;
    }

        // 绑定事件
    function bindMaterialEvents($modal, callback, type) {
        var $content = $modal.find('.material-content');

        // 确定按钮事件
        $modal.find('#select-material-btn').click(function() {
            if (type === 'keyword') {
                // 处理关键字选择
                var $selected = $content.find('.keyword-item.selected');
                if ($selected.length > 0) {
                    var keyword = $selected.data('keyword');
                    var material = {
                        content: keyword,
                        name: keyword,
                        type: 'keyword'
                    };

                    if (typeof callback === 'function') {
                        callback(material);
                    }

                    $modal.modal('hide');
                } else {
                    alert('请选择一个关键字');
                }
            } else {
                // 处理素材选择
                var $selected = $content.find('.material-item.selected');
                if ($selected.length > 0) {
                    var material = {
                        id: $selected.data('id'),
                        media_id: $selected.data('media-id'),
                        attachment: $selected.data('url'),
                        url: $selected.data('url'),
                        filename: $selected.find('.material-name').text(),
                        type: type,
                        // 添加appmsgSendedItem所需的字段
                        thumb_url: $selected.data('url'),
                        title: $selected.find('.material-name').text(),
                        description: $selected.find('.material-name').text()
                    };

                    if (typeof callback === 'function') {
                        callback(material);
                    }

                    $modal.modal('hide');
                } else {
                    alert('请选择一个素材');
                }
            }
        });

        // 手动绑定关闭按钮事件
        $modal.find('.close, [data-dismiss="modal"]').click(function() {
            $modal.modal('hide');
        });

        // 分页事件
        $content.on('click', '.pagination a', function(e) {
            e.preventDefault();
            var page = $(this).attr('page');
            var materialType = $(this).data('type') || type;
            if (page && !isNaN(parseInt(page))) {
                if (materialType === 'keyword') {
                    loadKeywordList($content);
                } else {
                    loadMaterialList(materialType, parseInt(page), $content);
                }
            }
        });
    }

    // 添加样式
    var style = `
        <style>
        .material-content-wrapper {
            max-height: 500px;
            overflow-y: auto;
            padding: 10px;
        }
        .material-content {
            min-height: 200px;
        }
        .material-item {
            border: 2px solid transparent;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .material-item:hover {
            border-color: #ddd;
        }
        .material-item.selected {
            border-color: #337ab7;
            background-color: #f5f5f5;
        }
        .keyword-item.selected {
            border-color: #337ab7;
            background-color: #f5f5f5;
        }
        .material-thumb {
            text-align: center;
            margin-bottom: 10px;
        }
        .material-thumb img {
            max-width: 100%;
            height: auto;
            max-height: 80px;
            object-fit: cover;
        }
        .material-info {
            text-align: center;
        }
        .material-name {
            font-size: 12px;
            color: #666;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pagination {
            margin: 20px 0 10px 0;
        }
        .pagination a {
            cursor: pointer;
        }
        .pagination .disabled span {
            color: #999;
            cursor: not-allowed;
        }
        .pagination .active span {
            background-color: #337ab7;
            border-color: #337ab7;
            color: #fff;
        }
        .pagination li {
            display: inline-block;
            margin: 0 2px;
        }
        .pagination li a,
        .pagination li span {
            padding: 6px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #337ab7;
        }
        .pagination li a:hover {
            background-color: #f5f5f5;
        }
        </style>
    `;

    if (!$('#wechat-material-style').length) {
        $('head').append(style);
    }

})();
