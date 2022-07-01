@include('common.header')

<div class="main-content fui-content">

    <h2>平台管理</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('account/profile',array('uniacid'=>$uniacid)) }}">基础信息</a>
            </li>
            <li class="layui-this">
                <a href="{{ wurl('account/functions',array('uniacid'=>$uniacid)) }}">应用与服务</a>
            </li>
            @if(in_array($role,['founder','owner']) || $_W['isfounder'])
                <li>
                    <a href="{{ wurl('account/role',array('uniacid'=>$uniacid)) }}">操作权限</a>
                </li>
            @endif
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            @if($_W['isfounder'])
                <a href="{{ wurl('account/modules',array('uniacid'=>$uniacid), true) }}" class="fr text-blue ajaxshow layui-hide" title="平台模块管理">管理</a>
            @endif
            <span class="title">应用模块</span>
        </div>
        <div class="layui-card-body">
            @if(empty($components))
                <div class="fui-empty text-center" style="line-height: 150px;">
                    <span class="text-gray" style="font-size: 16px;">暂无可用应用</span>
                </div>
            @else
            <div class="layui-row layui-col-space15 fui-list card">
                @foreach($components as $item)
                    <div class="layui-col-md3 layui-col-sm4 layui-col-xs6 fui-item arrow">
                        <a target="_blank" href="{{ wurl("m/".$item['identity']) }}" class="fui-content">
                            <div class="fui-info">
                                <img alt="{{ $item['name'] }}" class="radius" src="{{ tomedia($item['logo']) }}" />
                                <strong class="card-name">{{ $item['name'] }}</strong>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <span class="title">功能服务</span>
        </div>
        <div class="layui-card-body">
            <div class="layui-row layui-col-space15 fui-list card">
                @foreach($servers as $value)
                    <div class="layui-col-md3 layui-col-sm4 layui-col-xs6 fui-item fui-item-sm arrow">
                        <a target="_blank" href="{{ serv($value['name'])->url($value['entry']) }}" title="{{ $value['summary'] }}" class="fui-content">
                            <div class="fui-info">
                                <img alt="{{ $value['title'] }}" class="radius" src="{{ asset($value['cover']) }}" />
                                <strong class="card-name">{{ $value['title'] }}</strong>
                                <p class="text-cut">{{ $value['summary'] }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
<style>
.fui-item-sm .fui-content{padding: 10px 15px}
.fui-list.card .fui-item-sm .card-name{line-height: 26px;}
.fui-item.arrow .fui-content{padding-right: 28px; position: relative;}
.fui-item.arrow .fui-content:after{content: "\e602"; font-family: layui-icon !important; font-size: 28px; position: absolute; right: 5px; top: 50%; margin-top: -14px; color: #bebebe;}
</style>
@include('common.footer')
