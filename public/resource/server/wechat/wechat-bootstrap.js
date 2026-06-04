// 微信公众号微服务专用Bootstrap组件
(function() {
    // 扩展jQuery
    $.fn.modal = function(option) {
        return this.each(function() {
            var $this = $(this);
            if (typeof option == 'string') {
                if (option === 'show') {
                    // 添加模态框背景
                    if (!$('.modal-backdrop').length) {
                        $('body').append('<div class="modal-backdrop fade in"></div>');
                    }
                    $this.addClass('in').show();
                    $('body').addClass('modal-open');
                } else if (option === 'hide') {
                    $this.removeClass('in').hide();
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                }
            }
        });
    };
    
    $.fn.tab = function(option) {
        return this.each(function() {
            var $this = $(this);
            if (typeof option == 'string' && option === 'show') {
                var target = $this.attr('href') || $this.attr('data-target');
                var $target = $(target);
                $this.closest('ul').find('.active').removeClass('active');
                $this.parent().addClass('active');
                $target.siblings().removeClass('active in');
                $target.addClass('active in');
            }
        });
    };
    
    // Tooltip功能
    $.fn.tooltip = function(option) {
        return this.each(function() {
            var $this = $(this);
            var options = $.extend({}, {
                placement: 'top',
                trigger: 'hover',
                title: $this.attr('title') || $this.attr('data-original-title'),
                html: false
            }, option);
            
            // 创建tooltip元素
            var $tooltip = $('<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>');
            $tooltip.find('.tooltip-inner').text(options.title);
            
            // 显示tooltip
            function showTooltip() {
                if (!$tooltip.parent().length) {
                    $tooltip.appendTo('body');
                }
                
                var $element = $this;
                var elementPos = $element.offset();
                var elementWidth = $element.outerWidth();
                var elementHeight = $element.outerHeight();
                var tooltipWidth = $tooltip.outerWidth();
                var tooltipHeight = $tooltip.outerHeight();
                
                var top, left;
                
                switch (options.placement) {
                    case 'top':
                        top = elementPos.top - tooltipHeight - 10;
                        left = elementPos.left + (elementWidth / 2) - (tooltipWidth / 2);
                        break;
                    case 'bottom':
                        top = elementPos.top + elementHeight + 10;
                        left = elementPos.left + (elementWidth / 2) - (tooltipWidth / 2);
                        break;
                    case 'left':
                        top = elementPos.top + (elementHeight / 2) - (tooltipHeight / 2);
                        left = elementPos.left - tooltipWidth - 10;
                        break;
                    case 'right':
                        top = elementPos.top + (elementHeight / 2) - (tooltipHeight / 2);
                        left = elementPos.left + elementWidth + 10;
                        break;
                }
                
                $tooltip.css({
                    top: top + 'px',
                    left: left + 'px'
                }).addClass('in');
            }
            
            // 隐藏tooltip
            function hideTooltip() {
                $tooltip.removeClass('in');
            }
            
            // 绑定事件
            if (options.trigger === 'hover') {
                $this.on('mouseenter', showTooltip);
                $this.on('mouseleave', hideTooltip);
            } else if (options.trigger === 'click') {
                $this.on('click', function(e) {
                    e.preventDefault();
                    if ($tooltip.hasClass('in')) {
                        hideTooltip();
                    } else {
                        showTooltip();
                    }
                });
            }
            
            // 点击其他地方隐藏tooltip
            $(document).on('click', function(e) {
                if (!$(e.target).closest($this).length && !$(e.target).closest($tooltip).length) {
                    hideTooltip();
                }
            });
        });
    };
    
    // 自动初始化
    $(document).ready(function() {
        // 模态框
        $('[data-toggle="modal"]').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('data-target') || $(this).attr('href');
            $(target).modal('show');
        });
        
        // 关闭模态框 - 使用事件委托，确保动态添加的元素也能响应
        $(document).on('click', '.modal .close, .modal [data-dismiss="modal"]', function() {
            $(this).closest('.modal').modal('hide');
        });
        
        // 点击模态框背景关闭
        $(document).on('click', '.modal-backdrop', function() {
            $('.modal.in').modal('hide');
        });
        
        // ESC键关闭模态框
        $(document).on('keydown', function(e) {
            if (e.keyCode === 27 && $('.modal.in').length) {
                $('.modal.in').modal('hide');
            }
        });
        
        // 标签页
        $('[data-toggle="tab"]').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Tooltip自动初始化
        $('[data-toggle="tooltip"]').tooltip();
    });
    
})(); 