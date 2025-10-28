@include('common.header')
<div class="layui-fluid unpadding">
    <div class="main-content">

        @if(empty($_W['isajax']))
            <h2>{{ $title }}</h2>
        @endif

        <main class="py-4">
            @yield('content')
        </main>

    </div>
</div>

@include('common.footer')