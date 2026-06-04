// 微信公众号微服务专用AngularJS控制器
(function() {
    // 等待AngularJS加载完成
    function waitForAngular() {
        if (typeof angular === 'undefined') {
            setTimeout(waitForAngular, 50);
            return;
        }

        createModules();
    }

    function createModules() {
        try {
            // 创建we7app模块（简化版）
            angular.module("we7app", ["ngSanitize"]);

            // 创建menuApp模块
            angular.module("menuApp", ["we7app"]);

            // 创建materialApp模块
            angular.module("materialApp", ["we7app"]);

            // 创建replyFormApp模块
            angular.module("replyFormApp", ["we7app"]);

            console.log('所有AngularJS模块创建成功');

            // 条件菜单设计器控制器
            angular.module("menuApp").controller("conditionMenuDesigner", ["$scope", "config", "$http", function ($scope, config, $http) {

                // 初始化菜单数据
                $scope.context = {};
                $scope.context.group = config.group || {
                    title: "默认菜单",
                    type: 1,
                    button: [{
                        name: "菜单名称",
                        type: "click",
                        url: "",
                        key: "",
                        media_id: "",
                        appid: "",
                        pagepath: "",
                        sub_button: []
                    }],
                    matchrule: {
                        sex: 0,
                        client_platform_type: 0,
                        group_id: -1,
                        country: "",
                        province: "",
                        city: "",
                        language: ""
                    }
                };

                // 初始化活动项
                $scope.context.activeIndex = 0;
                $scope.context.activeBut = $scope.context.group.button[$scope.context.activeIndex];
                $scope.context.activeItem = $scope.context.activeBut;
                $scope.context.activeType = 1;

                // 添加表情选择功能
                $scope.context.selectEmoji = function() {
                    if (typeof util !== 'undefined' && util.emojiBrowser) {
                        util.emojiBrowser(function(emoji) {
                            var emojiCode = emoji.find("span").text();
                            var currentName = $scope.context.activeItem.name || '';
                            $scope.context.activeItem.name = currentName + '[U+' + emojiCode + ']';
                            $scope.$apply();
                        });
                    } else {
                        console.error('util.emojiBrowser not available');
                        // 备用表情选择器
                        showSimpleEmojiSelector();
                    }
                };

                // 备用表情选择器
                function showSimpleEmojiSelector() {
                    var emojis = ['😀', '😃', '😄', '😁', '😆', '😅', '😂', '🤣', '😊', '😇', '🙂', '🙃', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '😋', '😛', '😝', '😜', '🤪', '🤨', '🧐', '🤓', '😎', '🤩', '🥳', '😏', '😒', '😞', '😔', '😟', '😕', '🙁', '☹️', '😣', '😖', '😫', '😩', '🥺', '😢', '😭', '😤', '😠', '😡', '🤬', '🤯', '😳', '🥵', '🥶', '😱', '😨', '😰', '😥', '😓', '🤗', '🤔', '🤭', '🤫', '🤥', '😶', '😐', '😑', '😯', '😦', '😧', '😮', '😲', '🥱', '😴', '🤤', '😪', '😵', '🤐', '🥴', '🤢', '🤮', '🤧', '😷', '🤒', '🤕'];

                    var modalHtml = `
                        <div class="modal fade" id="emoji-modal" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                        <h4 class="modal-title">选择表情</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="emoji-grid">
                                            ${emojis.map(function(emoji, index) {
                                                return `<span class="emoji-item" data-emoji="${emoji}" style="cursor: pointer; font-size: 24px; margin: 5px; display: inline-block;">${emoji}</span>`;
                                            }).join('')}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#emoji-modal').remove();
                    $('body').append(modalHtml);

                    var $modal = $('#emoji-modal');

                    // 绑定表情点击事件
                    $modal.on('click', '.emoji-item', function() {
                        var selectedEmoji = $(this).data('emoji');
                        var currentName = $scope.context.activeItem.name || '';
                        $scope.context.activeItem.name = currentName + selectedEmoji;
                        $scope.$apply();
                        $modal.modal('hide');
                    });

                    $modal.modal('show');
                }

                // 提交菜单
                $scope.context.submit = function(submitType) {
                    var group = $scope.context.group;

                    if (!group.title || group.title.trim() === '') {
                        group.title = '默认菜单';
                    }

                    if (group.button.length < 1) {
                        util.message("没有设置菜单", "", "error");
                        return false;
                    }

                    $("#btn-submit").attr("disabled", true);
                    $http.post(window.location.href, {
                        group: group,
                        method: "post",
                        submit_type: submitType
                    }).success(function(response) {
                        if (response.message.errno !== 0) {
                            $("#btn-submit").attr("disabled", false);
                            util.message(response.message.message, "", "error");
                        } else {
                            util.message("创建菜单成功", response.redirect, "success");
                        }
                    }).error(function() {
                        $("#btn-submit").attr("disabled", false);
                        util.message("网络错误，请稍候重试", "", "error");
                    });
                };

                // 触发活动按钮
                $scope.context.triggerActiveBut = function(button) {
                    var index = $scope.context.group.button.indexOf(button);
                    if (index === -1) return false;

                    $scope.context.activeIndex = index;
                    $scope.context.activeBut = $scope.context.group.button[$scope.context.activeIndex];
                    $scope.context.activeItem = $scope.context.activeBut;
                    $scope.context.activeType = 1;
                    $scope.context.activeItem.forceHide = 0;
                };

                // 编辑按钮
                $scope.context.editBut = function(subButton, button, id) {
                    $scope.context.triggerActiveBut(button);

                    if (subButton) {
                        $scope.context.activeItem = subButton;
                        $scope.context.activeType = 2;
                    } else {
                        $scope.context.activeItem = button;
                        $scope.context.activeType = 1;
                    }

                    if ($scope.context.activeType === 1 && $scope.context.activeItem.sub_button.length > 0) {
                        $scope.context.activeItem.forceHide = 1;
                    } else {
                        $scope.context.activeItem.forceHide = 0;
                    }
                };

                // 添加按钮
                $scope.context.addBut = function() {
                    if ($scope.context.group.button.length >= 3) return;

                    $scope.context.group.button.push({
                        name: "菜单名称",
                        type: "click",
                        url: "",
                        key: "",
                        media_id: "",
                        appid: "",
                        pagepath: "",
                        sub_button: []
                    });

                    var newButton = $scope.context.group.button[$scope.context.group.button.length - 1];
                    $scope.context.triggerActiveBut(newButton);
                };

                // 移除按钮
                $scope.context.removeBut = function(item, type) {
                    if (type === 1) {
                        util.confirm(function() {
                            var index = $scope.context.group.button.indexOf(item);
                            $scope.context.group.button.splice(index, 1);
                            $scope.context.triggerActiveBut($scope.context.group.button[0]);
                        }, function() {
                            return false;
                        }, "确认删除吗?");
                    } else {
                        var index = $scope.context.activeBut.sub_button.indexOf(item);
                        $scope.context.activeBut.sub_button.splice(index, 1);
                        $scope.context.triggerActiveBut($scope.context.activeBut);
                    }

                    if ($scope.context.activeItem.sub_button.length > 0) {
                        $scope.context.activeItem.forceHide = 1;
                    } else {
                        $scope.context.activeItem.forceHide = 0;
                    }
                };

                // 添加子按钮
                $scope.context.addSubBut = function(button) {
                    if ($scope.context.group.disabled === 1) return false;

                    $scope.context.triggerActiveBut(button);

                    if ($scope.context.activeBut.sub_button.length >= 5) return;

                    $scope.context.activeBut.sub_button.push({
                        name: "子菜单名称",
                        type: "click",
                        url: "",
                        key: "",
                        appid: "",
                        pagepath: "",
                        media_id: ""
                    });

                    $scope.context.activeItem = $scope.context.activeBut.sub_button[$scope.context.activeBut.sub_button.length - 1];
                    $scope.context.activeType = 2;
                    $scope.context.activeItem.forceHide = 0;
                };

                // 选择素材
                $scope.context.select_mediaid = function(type, param) {
                    var options = {
                        type: type,
                        isWechat: true,
                        needType: 3
                    };

                    util.material(function(material) {
                        $scope.context.activeItem.key = "";
                        $scope.context.activeItem.media_id = material.media_id;
                        $scope.context.activeItem.material = [];

                        if (type === "keyword") {
                            $scope.context.activeItem.material.push(material);
                            $scope.context.activeItem.material[0].type = "keyword";
                            $scope.context.activeItem.key = "keyword:" + material.content;
                            $scope.context.activeItem.media_id = "";

                            if (param === "1") {
                                $scope.context.activeItem.material[0].etype = "click";
                                $scope.context.activeItem.material[0].name = material.name;
                                $scope.context.activeItem.material[0].content = material.content;
                            }
                        } else if (type === "image" || type === "news" || type === "voice" || type === "video") {
                            $scope.context.activeItem.material.push(material);

                            // 设置appmsgSendedItem所需的字段
                            if (type === "image") {
                                $scope.context.activeItem.appmsgSendedItem = {
                                    thumb_url: material.attachment,
                                    title: material.filename,
                                    description: material.filename
                                };
                            } else if (type === "news") {
                                $scope.context.activeItem.appmsgSendedItem = {
                                    thumb_url: material.attachment,
                                    title: material.filename,
                                    description: material.filename
                                };
                            }
                        } else if (type === "module") {
                            $scope.context.activeItem.key = "module:" + material.name;
                            $scope.context.activeItem.material.push(material);
                            $scope.context.activeItem.material[0].module_type = $scope.context.activeItem.material[0].type;
                            $scope.context.activeItem.material[0].type = "module";
                            $scope.context.activeItem.material[0].etype = "module";
                        }

                        $scope.$digest();
                    }, options);
                };

                // 选择素材
                $scope.context.select_mediaid = function(type, param) {
                    var options = {
                        type: type,
                        isWechat: true,
                        needType: 3
                    };

                    // 检查是否有自定义的素材选择器
                    if (typeof wechatMaterialSelector !== 'undefined') {
                        wechatMaterialSelector(type, function(material) {
                            $scope.context.activeItem.key = "";
                            $scope.context.activeItem.media_id = material.media_id;
                            $scope.context.activeItem.material = [];

                            if (type === "keyword") {
                                $scope.context.activeItem.material.push(material);
                                $scope.context.activeItem.material[0].type = "keyword";
                                $scope.context.activeItem.key = "keyword:" + material.content;
                                $scope.context.activeItem.media_id = "";

                                if (param === "1") {
                                    $scope.context.activeItem.material[0].etype = "click";
                                    $scope.context.activeItem.material[0].name = material.name;
                                    $scope.context.activeItem.material[0].content = material.content;
                                }
                            } else if (type === "image" || type === "news" || type === "voice" || type === "video") {
                                $scope.context.activeItem.material.push(material);

                                // 设置appmsgSendedItem所需的字段
                                if (type === "image") {
                                    $scope.context.activeItem.appmsgSendedItem = {
                                        thumb_url: material.thumb_url || material.attachment,
                                        title: material.title || material.filename,
                                        description: material.description || material.filename
                                    };
                                } else if (type === "news") {
                                    $scope.context.activeItem.appmsgSendedItem = {
                                        thumb_url: material.thumb_url || material.attachment,
                                        title: material.title || material.filename,
                                        description: material.description || material.filename
                                    };
                                }
                            } else if (type === "module") {
                                $scope.context.activeItem.key = "module:" + material.name;
                                $scope.context.activeItem.material.push(material);
                                $scope.context.activeItem.material[0].module_type = $scope.context.activeItem.material[0].type;
                                $scope.context.activeItem.material[0].type = "module";
                                $scope.context.activeItem.material[0].etype = "module";
                            }

                            $scope.$apply();
                        }, options);
                    } else {
                        console.error('wechatMaterialSelector not available');
                    }
                };

                // 初始化
                $scope.context.editBut("", $scope.context.group.button[0], $scope.context.group.id);
            }]);

            // KeywordReply控制器
            angular.module("replyFormApp").controller("KeywordReply", ["$scope", "$http", "config", function ($scope, $http, config) {

                $scope.reply = {
                    status: 1,
                    showAdvance: false,
                    entry: {
                        keywords: [],
                        istop: 0
                    }
                };

                $scope.newKeyword = {
                    content: '',
                    type: 1
                };

                // 切换高级设置显示
                $scope.changeShowAdvance = function() {
                    $scope.reply.showAdvance = !$scope.reply.showAdvance;
                };

                // 切换状态
                $scope.changeStatus = function() {
                    $scope.reply.status = $scope.reply.status ? 0 : 1;
                };

                // 显示添加关键字模态框
                $scope.showAddkeywordModal = function() {
                    $('#addkeywordModal').modal('show');
                };

                // 添加新关键字
                $scope.addNewKeyword = function() {
                    if (!$scope.newKeyword.content.trim()) {
                        util.message('请输入关键字', '', 'error');
                        return;
                    }

                    $scope.reply.entry.keywords.push({
                        content: $scope.newKeyword.content,
                        type: $scope.newKeyword.type
                    });

                    $scope.newKeyword.content = '';
                    $scope.newKeyword.type = 1;

                    $('#addkeywordModal').modal('hide');
                };

                // 删除关键字
                $scope.delKeyword = function(keyword) {
                    var index = $scope.reply.entry.keywords.indexOf(keyword);
                    if (index > -1) {
                        $scope.reply.entry.keywords.splice(index, 1);
                    }
                };

                // 提交表单
                $scope.submitForm = function() {
                    // 更新隐藏字段
                    $('input[name="keywords"]').val(JSON.stringify($scope.reply.entry.keywords));
                    $('input[name="status"]').val($scope.reply.status);

                    // 提交表单
                    $('#reply-form').submit();
                };

                // 初始化数据
                if (config && config.replydata) {
                    $scope.reply = _.extend($scope.reply, config.replydata);
                }
            }]);

            // MaterialApp控制器
            angular.module("materialApp").controller("MaterialController", ["$scope", "$http", "config", function ($scope, $http, config) {

                // 初始化素材数据
                $scope.materials = [];
                $scope.currentPage = 1;
                $scope.pageSize = 24;
                $scope.total = 0;
                $scope.loading = false;

                // 删除素材
                $scope.deleteMaterial = function(materialId, type) {
                    if (confirm('确定要删除这个素材吗？')) {
                        $http.post(config.del_url, {
                            material_id: materialId,
                            type: type
                        }).then(function(response) {
                            if (response.data.type === 'success') {
                                // 重新加载素材列表
                                $scope.loadMaterials();
                            } else {
                                alert('删除失败：' + response.data.message);
                            }
                        }).catch(function(error) {
                            console.error('删除素材失败:', error);
                            alert('删除失败，请重试');
                        });
                    }
                };

                // 同步素材
                $scope.syncMaterials = function() {
                    $scope.loading = true;
                    $http.post(config.sync_url).then(function(response) {
                        if (response.data.type === 'success') {
                            alert('同步成功');
                            $scope.loadMaterials();
                        } else {
                            alert('同步失败：' + response.data.message);
                        }
                    }).catch(function(error) {
                        console.error('同步素材失败:', error);
                        alert('同步失败，请重试');
                    }).finally(function() {
                        $scope.loading = false;
                    });
                };

                // 加载素材列表
                $scope.loadMaterials = function() {
                    // 这里可以根据需要实现素材列表加载逻辑
                    console.log('加载素材列表');
                };

                // 群发素材
                $scope.checkGroup = function(type, materialId) {
                    console.log('群发素材:', type, materialId);
                    // 这里可以实现群发逻辑
                    alert('群发功能待实现');
                };

                // 转换为微信文章
                $scope.newsToWechat = function(materialId) {
                    console.log('转换为微信文章:', materialId);
                    // 这里可以实现转换逻辑
                    alert('转换功能待实现');
                };

                // 删除素材
                $scope.del_material = function(type, materialId, server) {
                    console.log('删除素材:', type, materialId, server);
                    if (confirm('确定要删除这个素材吗？')) {
                        $http.post(config.del_url, {
                            material_id: materialId,
                            type: type,
                            server: server
                        }).then(function(response) {
                            if (response.data.type === 'success') {
                                alert('删除成功');
                                // 重新加载页面
                                window.location.reload();
                            } else {
                                alert('删除失败：' + response.data.message);
                            }
                        }).catch(function(error) {
                            console.error('删除素材失败:', error);
                            alert('删除失败，请重试');
                        });
                    }
                };

                // 初始化
                $scope.loadMaterials();
            }]);

            // WelcomeDisplay控制器
            angular.module("replyFormApp").controller("WelcomeDisplay", ["$scope", function ($scope) {
                // 欢迎信息显示控制器
            }]);

            // DefaultDisplay控制器
            angular.module("replyFormApp").controller("DefaultDisplay", ["$scope", function ($scope) {
                // 默认回复显示控制器
            }]);

        } catch (e) {
            console.error('Error creating AngularJS modules:', e);
        }
    }

    // 开始等待AngularJS
    waitForAngular();

})();
