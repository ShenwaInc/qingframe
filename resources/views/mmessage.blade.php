<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0,viewport-fit=cover">
    <title>温馨提示</title>
</head>
<style>
    body {
        font-family: -apple-system-font,Helvetica Neue,Helvetica,sans-serif;
        line-height: 1.6;
    }
    .container {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        overflow: hidden;
        color: rgba(0, 0, 0, 0.9);
    }
    .page {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        box-sizing: border-box;
        z-index: 1;
    }
    .weui-msg {
        padding: calc(100px + env(safe-area-inset-top)) env(safe-area-inset-right) env(safe-area-inset-bottom) env(safe-area-inset-left);
        text-align: center;
        line-height: 1.4;
        min-height: 100%;
        box-sizing: border-box;
        display: -webkit-box;
        display: -webkit-flex;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -webkit-flex-direction: column;
        flex-direction: column;
        background-color: #fff;
    }
    .weui-msg__icon-area {
        margin-bottom: 15px;
    }
    [class^="weui-icon-"], [class*=" weui-icon-"] {
        display: inline-block;
        vertical-align: middle;
        width: 24px;
        height: 24px;
        -webkit-mask-position: 50% 50%;
        mask-position: 50% 50%;
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-size: 100%;
        mask-size: 100%;
        background-color: currentColor;
    }
    .weui-icon_msg {
        width: 72px;
        height: 72px;
    }
    .weui-icon-error {
        -webkit-mask-image: url(data:image/svg+xml,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12%2022C6.477%2022%202%2017.523%202%2012S6.477%202%2012%202s10%204.477%2010%2010-4.477%2010-10%2010zm-.763-15.864l.11%207.596h1.305l.11-7.596h-1.525zm.759%2010.967c.512%200%20.902-.383.902-.882%200-.5-.39-.882-.902-.882a.878.878%200%2000-.896.882c0%20.499.396.882.896.882z%22%2F%3E%3C%2Fsvg%3E);
        mask-image: url(data:image/svg+xml,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12%2022C6.477%2022%202%2017.523%202%2012S6.477%202%2012%202s10%204.477%2010%2010-4.477%2010-10%2010zm-.763-15.864l.11%207.596h1.305l.11-7.596h-1.525zm.759%2010.967c.512%200%20.902-.383.902-.882%200-.5-.39-.882-.902-.882a.878.878%200%2000-.896.882c0%20.499.396.882.896.882z%22%2F%3E%3C%2Fsvg%3E);
        color: #fa5151;
    }
    .weui-icon-success {
        -webkit-mask-image: url(data:image/svg+xml,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12%2022C6.477%2022%202%2017.523%202%2012S6.477%202%2012%202s10%204.477%2010%2010-4.477%2010-10%2010zm-1.177-7.86l-2.765-2.767L7%2012.431l3.119%203.121a1%201%200%20001.414%200l5.952-5.95-1.062-1.062-5.6%205.6z%22%2F%3E%3C%2Fsvg%3E);
        mask-image: url(data:image/svg+xml,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M12%2022C6.477%2022%202%2017.523%202%2012S6.477%202%2012%202s10%204.477%2010%2010-4.477%2010-10%2010zm-1.177-7.86l-2.765-2.767L7%2012.431l3.119%203.121a1%201%200%20001.414%200l5.952-5.95-1.062-1.062-5.6%205.6z%22%2F%3E%3C%2Fsvg%3E);
        color: #07c160;
    }
    .weui-msg__text-area {
        margin-bottom: 15px;
        padding: 0 32px;
        -webkit-box-flex: 1;
        -webkit-flex: 1;
        flex: 1;
        line-height: 1.6;
        word-wrap: break-word;
        -webkit-hyphens: auto;
        hyphens: auto;
    }
    .weui-msg__title {
        margin-bottom: 16px;
        font-weight: 400;
        font-size: 22px;
        color: #191919;
        -webkit-text-stroke: 0.02em;
    }
    .weui-msg__opr-area {
        margin-bottom: 16px;
    }
    .weui-msg__opr-area .weui-btn-area {
        margin: 0;
    }
    .weui-btn {
        position: relative;
        display: block;
        width: 184px;
        margin-left: auto;
        margin-right: auto;
        padding: 8px 24px;
        box-sizing: border-box;
        font-weight: 700;
        font-size: 17px;
        text-align: center;
        text-decoration: none;
        color: #fff;
        line-height: 1.41176471;
        border-radius: 4px;
        overflow: hidden;
        -webkit-tap-highlight-color: rgba(0,0,0,0);
    }
    .weui-btn_primary {
        background-color: #07c160;
    }
    .weui-msg__tips-area {
        margin-bottom: 76px;
        padding: 0 40px;
        word-wrap: break-word;
        -webkit-hyphens: auto;
        hyphens: auto;
    }
    .weui-msg__opr-area+.weui-msg__tips-area {
        margin-bottom: 108px;
    }
    .weui-msg__tips {
        font-size: 12px;
        color: rgba(0,0,0,0.5);
    }
    .weui-msg__desc a, .weui-msg__desc-primary a, .weui-msg__tips a {
        color: #576b95;
        display: inline-block;
        vertical-align: baseline;
    }
</style>
<body>
    <div class="container">
        <div class="page">
            <div class="weui-msg">
                <div class="weui-msg__icon-area">
                    <i class="weui-icon-{{ $type }} weui-icon_msg"></i>
                </div>
                <div class="weui-msg__text-area">
                    <h2 class="weui-msg__title">{{ $message }}</h2>

                </div>
                <div class="weui-msg__opr-area">
                    <p class="weui-btn-area">
                        <a href="{{ $redirect=='' ? 'javascript:history.back();' : $redirect }}" class="weui-btn weui-btn_primary">确定</a>
                    </p>
                </div>
                <div class="weui-msg__tips-area">
                    @if($redirect!='')
                    <p class="weui-msg__tips"><a href="javascript:">即将自动跳转...</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@if($redirect!='')
    <script type="text/javascript">
        setTimeout(function (){
            window.location.href = '{{ $redirect }}';
        },1200);
    </script>
@endif
</body>
</html>
