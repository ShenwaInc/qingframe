// 微信公众号微服务专用工具函数库
!function (window) {
    var util = {};

    // 消息提示
    util.message = function(message, redirect, type) {
        type = type || 'error';
        if (typeof message === 'string') {
            alert(message);
        } else if (message && message.message) {
            alert(message.message);
        }
        if (redirect && redirect !== '') {
            setTimeout(function() {
                window.location.href = redirect;
            }, 1500);
        }
    };

    // 确认对话框
    util.confirm = function(confirmCallback, cancelCallback, message) {
        if (confirm(message || '确认操作？')) {
            if (typeof confirmCallback === 'function') {
                confirmCallback();
            }
        } else {
            if (typeof cancelCallback === 'function') {
                cancelCallback();
            }
        }
    };

    // 对话框
    util.dialog = function(title, content, footer, options) {
        options = options || {};
        var containerName = options.containerName || 'dialog-container';
        
        $('#' + containerName).remove();
        
        var modalHtml = '<div class="modal fade" id="' + containerName + '" tabindex="-1" role="dialog">' +
            '<div class="modal-dialog" role="document">' +
            '<div class="modal-content">' +
            '<div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '<h4 class="modal-title">' + title + '</h4>' +
            '</div>' +
            '<div class="modal-body">' + content + '</div>' +
            '<div class="modal-footer">' + (footer || '') + '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
        
        $('body').append(modalHtml);
        var $modal = $('#' + containerName);
        $modal.modal('show');
        return $modal;
    };

    // 素材选择器
    util.material = function(callback, options) {
        // 检查是否在微服务环境中
        if (window.location.pathname.indexOf('/server/wechat/') !== -1) {
            // 如果wechatMaterialSelector函数存在，直接调用
            if (typeof wechatMaterialSelector === 'function') {
                return wechatMaterialSelector(callback, options);
            }
            // 否则显示错误信息
            util.message('素材选择器未初始化，请刷新页面重试');
            return false;
        }
        // 非微服务环境，显示错误信息
        util.message('素材选择器仅支持微服务环境');
        return false;
    };

    // 扩展jQuery
    $.extend({
        isFunction: function(obj) {
            return typeof obj === 'function';
        }
    });

    // 暴露到全局
    window.util = util;

}(window); 