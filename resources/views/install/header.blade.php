<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/favicon.ico" />
    <title>安装向导</title>
    <link rel="stylesheet" href="{{ asset('/static/layui/css/layui.css') }}?v={{ QingRelease }}" />
    <link rel="stylesheet" href="{{ asset('/static/fului/fului-for-lay.css') }}?v={{ QingRelease }}" />
    <link rel="stylesheet" href="{{ asset('/static/installer/style.css') }}?v={{ QingRelease }}" />
    <script type="text/javascript" src="{{ asset('/static/layui/layui.js') }}?v={{ QingRelease }}"></script>
    <script type="text/javascript" src="{{ asset('/static/js/jquery-1.11.1.min.js') }}?v={{ QingRelease }}"></script>
    <script type="text/javascript" src="{{ asset('/static/js/core.jquery.js') }}?v={{ QingRelease }}"></script>
</head>

<body class="layui-layout-admin">
    <div class="layui-body" style="left: 0; top: 0">
        <div class="main-panel">
