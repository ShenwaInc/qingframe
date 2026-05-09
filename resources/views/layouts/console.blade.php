@include('common.header')
<div class="layui-fluid unpadding">
    <div class="{{ $_W['isajax'] ? '' : 'main-content' }}">

        @if(empty($_W['isajax']))
            <h2>
                {{ $title }}
                @yield('titleExtra')
            </h2>
        @endif

        @yield('content')

    </div>
</div>

@include('common.footer')