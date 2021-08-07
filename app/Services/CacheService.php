<?php

namespace App\Services;

class CacheService
{

    static function system_key($cache_key) {
        $cache_key_all = cache_key_all();

        $params = array();
        $args = func_get_args();
        if (empty($args[1])) {
            $args[1] = '';
        }
        if (!is_array($args[1])) {
            $cache_key = $cache_key_all['caches'][$cache_key]['key'];
            preg_match_all('/\%([a-zA-Z\_\-0-9]+)/', $cache_key, $matches);
            for ($i = 0; $i < func_num_args() - 1; ++$i) {
                $cache_key = str_replace($matches[0][$i], $args[$i + 1], $cache_key);
            }

            return 'we7:' . $cache_key;
        } else {
            $params = $args[1];
        }

        if (empty($params)) {
            $res = preg_match_all('/([a-zA-Z\_\-0-9]+):/', $cache_key, $matches);
            if ($res) {
                $key = count($matches[1]) > 0 ? $matches[1][0] : $matches[1];
            } else {
                $key = $cache_key;
            }
            if (empty($cache_key_all['caches'][$key])) {
                return error(1, '缓存' . $key . ' 不存在!');
            } else {
                $cache_info_key = $cache_key_all['caches'][$key]['key'];
                preg_match_all('/\%([a-zA-Z\_\-0-9]+)/', $cache_info_key, $key_params);
                preg_match_all('/\:([a-zA-Z\_\-0-9]+)/', $cache_key, $val_params);

                if (count($key_params[1]) != count($val_params[1])) {
                    foreach ($key_params[1] as $key => $val) {
                        if (in_array($val, array_keys($cache_key_all['common_params']))) {
                            $cache_info_key = str_replace('%' . $val, $cache_key_all['common_params'][$val], $cache_info_key);
                            unset($key_params[1][$key]);
                        }
                    }

                    if (count($key_params[1]) == count($val_params[1])) {
                        $arr = array_combine($key_params[1], $val_params[1]);
                        foreach ($arr as $key => $val) {
                            if (preg_match('/\%' . $key . '/', $cache_info_key)) {
                                $cache_info_key = str_replace('%' . $key, $val, $cache_info_key);
                            }
                        }
                    }

                    if (strexists($cache_info_key, '%')) {
                        return error(1, '缺少缓存参数或参数不正确!');
                    } else {
                        return 'we7:' . $cache_info_key;
                    }
                } else {
                    return 'we7:' . $cache_key;
                }
            }
        }

        $cache_info = $cache_key_all['caches'][$cache_key];
        $cache_common_params = $cache_key_all['common_params'];

        if (empty($cache_info)) {
            return error(2, '缓存 ' . $cache_key . ' 不存在!');
        } else {
            $cache_key = $cache_info['key'];
        }

        foreach ($cache_common_params as $param_name => $param_val) {
            preg_match_all('/\%([a-zA-Z\_\-0-9]+)/', $cache_key, $matches);
            if (in_array($param_name, $matches[1]) && !in_array($param_name, array_keys($params))) {
                $params[$param_name] = $cache_common_params[$param_name];
            }
        }

        if (is_array($params) && !empty($params)) {
            foreach ($params as $key => $param) {
                $cache_key = str_replace('%' . $key, $param, $cache_key);
            }

            if (strexists($cache_key, '%')) {
                return error(1, '缺少缓存参数或参数不正确!');
            }
        }

        $cache_key = 'we7:' . $cache_key;
        if (strlen($cache_key) > CACHE_KEY_LENGTH) {
            trigger_error('Cache name is over the maximum length');
        }

        return $cache_key;
    }

}
