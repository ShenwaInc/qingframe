@include('common.header')
<style>
    .message-card{width: 90%; max-width: 1600px; margin: 0 auto 0;}
    .message-main{box-shadow: 0 1px 5px 0 rgb(0 0 0 / 5%);  min-height: 450px; background: #FFFFFF;}
    .message-card .layui-card{border: none !important; padding-top: 150px; box-shadow: none;}
    .message-icon{line-height: 1.5; color: #47b449;}
    .message-icon.message-error{color: #FF5722;}
    .message-icon.message-info{color: #FFB800;}
    .message-icon .layui-icon{font-size: 72px;}
    .message-text{color: #353535; font-size: 16px; font-weight: 400; padding-bottom: 6px}
    .message-light{color: #01AAED;}
    .message-redirect{font-size: 11px;}
</style>

<div class="message-card">
    <div class="message-main text-center">
        <div class="layui-card">
            <div class="layui-card-body">
                <div class="message-icon message-{{$type}}">
                    <i class="layui-icon layui-icon-{{$type=='success'?'ok-circle':'about'}}"></i>
                </div>
                <div class="message-text">
                    {{ $message }}
                </div>
                @if(!empty($redirect))
                <div class="message-redirect">
                    <a href="{!! $redirect !!}" class="message-light">{{ __('aboutToJump') }}</a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!empty($redirect))
    <script type="text/javascript">
        $(function (){
            setTimeout(function (){
                window.location.href = "{!! $redirect !!}";
            },3*1e3);
        });
    </script>
@endif

@include('common.footer')
