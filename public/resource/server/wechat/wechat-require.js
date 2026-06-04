// 微信公众号微服务专用require函数
(function() {
    // 简化版的require函数
    window.require = function(dependencies, callback) {
        console.log('wechat-require.js: Loading dependencies:', dependencies);

        // 检查依赖是否已加载
        var allLoaded = true;
        var loadedModules = {};

        dependencies.forEach(function(dep) {
            if (dep === 'underscore') {
                // 如果依赖underscore，我们提供一个简化版
                if (typeof _ === 'undefined') {
                    console.log('Creating simplified underscore module');
                    window._ = {
                        // 添加一些常用的underscore函数
                        each: function(obj, iterator) {
                            if (obj && obj.length) {
                                for (var i = 0; i < obj.length; i++) {
                                    iterator(obj[i], i, obj);
                                }
                            } else if (obj) {
                                for (var key in obj) {
                                    if (obj.hasOwnProperty(key)) {
                                        iterator(obj[key], key, obj);
                                    }
                                }
                            }
                        },
                        map: function(obj, iterator) {
                            var results = [];
                            if (obj && obj.length) {
                                for (var i = 0; i < obj.length; i++) {
                                    results.push(iterator(obj[i], i, obj));
                                }
                            } else if (obj) {
                                for (var key in obj) {
                                    if (obj.hasOwnProperty(key)) {
                                        results.push(iterator(obj[key], key, obj));
                                    }
                                }
                            }
                            return results;
                        },
                        extend: function(obj) {
                            for (var i = 1; i < arguments.length; i++) {
                                var source = arguments[i];
                                for (var key in source) {
                                    if (source.hasOwnProperty(key)) {
                                        obj[key] = source[key];
                                    }
                                }
                            }
                            return obj;
                        },
                        isArray: function(obj) {
                            return Object.prototype.toString.call(obj) === '[object Array]';
                        },
                        isObject: function(obj) {
                            return obj === Object(obj);
                        },
                        isFunction: function(obj) {
                            return typeof obj === 'function';
                        },
                        isString: function(obj) {
                            return typeof obj === 'string';
                        },
                        isNumber: function(obj) {
                            return typeof obj === 'number';
                        },
                        isBoolean: function(obj) {
                            return obj === true || obj === false;
                        },
                        isEmpty: function(obj) {
                            if (obj == null) return true;
                            if (obj.length > 0) return false;
                            if (obj.length === 0) return true;
                            for (var key in obj) {
                                if (obj.hasOwnProperty(key)) return false;
                            }
                            return true;
                        }
                    };
                }
                loadedModules[dep] = window._;
            } else if (dep === 'jquery') {
                loadedModules[dep] = window.jQuery || window.$;
            } else {
                console.warn('Unknown dependency:', dep);
                loadedModules[dep] = null;
            }
        });

        // 执行回调函数
        if (typeof callback === 'function') {
            try {
                // 检查所有依赖是否可用
                var allDepsAvailable = dependencies.every(function(dep) {
                    return loadedModules[dep] !== null && loadedModules[dep] !== undefined;
                });

                if (allDepsAvailable) {
                    callback.apply(null, dependencies.map(function(dep) {
                        return loadedModules[dep];
                    }));
                } else {
                    console.warn('Some dependencies are not available:', dependencies.filter(function(dep) {
                        return loadedModules[dep] === null || loadedModules[dep] === undefined;
                    }));
                    // 尝试用可用的依赖执行回调
                    var availableDeps = dependencies.map(function(dep) {
                        return loadedModules[dep] || null;
                    });
                    callback.apply(null, availableDeps);
                }
            } catch (e) {
                console.error('Error executing require callback:', e);
                // 提供更详细的错误信息
                console.error('Dependencies:', dependencies);
                console.error('Loaded modules:', loadedModules);
            }
        }
    };

    // 添加require.config方法
    window.require.config = function(config) {
        console.log('wechat-require.js: Configuring require with:', config);
        // 简化版的config，只记录配置，不实际使用
        window.requireConfig = config;
    };

    // 简化版的define函数
    window.define = function(name, dependencies, factory) {
        if (typeof dependencies === 'function') {
            factory = dependencies;
            dependencies = [];
        }

        console.log('wechat-require.js: Defining module:', name);

        // 执行工厂函数
        if (typeof factory === 'function') {
            try {
                var module = factory.apply(null, dependencies.map(function(dep) {
                    return window[dep] || null;
                }));

                // 将模块存储到全局变量
                if (name) {
                    window[name] = module;
                }
            } catch (e) {
                console.error('Error defining module:', name, e);
            }
        }
    };

    console.log('wechat-require.js loaded');

})();
