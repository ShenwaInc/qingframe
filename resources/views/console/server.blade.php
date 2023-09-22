@include('common.header')
<div class="layui-fluid">
    <div class="main-content fui-content">
        <h2>@lang('systemManagement')</h2>
        <div class="layui-tab fui-tab margin-bottom-xl">
            <ul class="layui-tab-title title_tab">
                <li>
                    <a href="{{ url('console/setting') }}">@lang('siteInformation')</a>
                </li>
                <li class="layui-this">
                    <a href="{{ url('console/server') }}">@lang('microServers')</a>
                </li>
                <li>
                    <a href="{{ url('console/module') }}">@lang('applications')</a>
                </li>
            </ul>
        </div>

        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <span class="title">{{ $title }}</span>
                <div class="layui-tab fui-tab">
                    <ul class="layui-tab-title title_tab">
                        <li @if($op=='index')  class="layui-this" @endif>
                            <a href="{{ wurl('server') }}">@lang('installed')</a>
                        </li>
                        <li @if($op=='stop')  class="layui-this" @endif>
                            <a href="{{ wurl('server', array("op"=>"stop")) }}">@lang('terminated')</a>
                        </li>
                        <li @if($op=='local')  class="layui-this" @endif>
                            <a href="{{ wurl('server', array("op"=>"local")) }}">@lang('moreServices')</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="layui-card-body">
                <table class="layui-table" lay-skin="nob" lay-even>
                    <thead>
                    <tr>
                        <th>@lang('service')</th>
                        <th class="layui-hide-xs">@lang('version')</th>
                        <th class="layui-hide-xs">@lang('description')</th>
                        <th class="text-right">@lang('action')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($servers as $key=>$service)
                    <tr>
                        <td>
                            <div class="text-cut" style="max-width: 40vw;">
                                <img alt="{{ $service['name'] }}" class="layui-avatar" src="{{ asset($service['cover']) }}?v={{ QingRelease }}" height="36" />
                                @if($op=='index' && !empty($service['entry']))
                                    <a href="{{ $service['entry'] }}" target="_blank" class="color-default">{{ $service['name'] }}</a>
                                @else
                                    <span class="color-default">{{ $service['name'] }}</span>
                                @endif
                                <span id="update{{ $service['identity'] }}" class="layui-badge-dot{{ empty($service['upgrade']) ? ' layui-hide' : '' }}" lay-tips="@lang('versionNew')"></span>
                                @if($service['isdelete'])
                                    &nbsp;<span class="layui-badge layui-bg-cyan">@lang('deleted')</span>
                                @endif
                            </div>
                        </td>
                        <td class="layui-hide-xs">
                            V{{ $service['version'] }}
                        </td>
                        <td class="layui-hide-xs">{{ $service['summary'] }}</td>
                        <td class="text-right">
                            @if(empty($service['binded']))
                            <div class="layui-btn-group text-center">
                                {!! $service['actions'] !!}
                                @if($op!="local")
                                    @if($service['status']==1)
                                        <a class="layui-btn layui-btn-sm layui-btn-warm confirm" data-text="@lang('disableConfirm')" href="{{ wurl('server', array("op"=>"disable", "nid"=>$service['identity'])) }}">@lang('disable')</a>
                                    @else
                                        <a class="layui-btn layui-btn-sm layui-btn-normal confirm" data-text="@lang('restoreConfirm')" href="{{ wurl('server', array("op"=>"restore", "nid"=>$service['identity'])) }}">@lang('restore')</a>
                                    @endif
                                    <a class="layui-btn layui-btn-sm layui-btn-primary js-uninstall js-terminal" data-text="@lang('uninstallConfirm')" href="{{ wurl('server', array("op"=>"uninstall", "nid"=>$service['identity'])) }}">@lang('uninstall')</a>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($servers))
                    <tr><td colspan="4" class="text-muted text-center">@lang('empty')</td></tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@include('console.terminal')
<script type="text/javascript">
    $(function (){
        @if($op=='index')
        $('.js-upgrade').each(function (index, element) {
            let Elem = $(element);
            let identity = Elem.attr('data-nid');
            Core.get('console/server', function (res){
                if(res.type==='success'){
                    let tips = "{{ __('upgradeTo', array('data'=>__('service'))) }}V" + res.data.release.version + "Release" + res.data.release.releasedate;
                    Elem.removeClass('layui-hide').attr('lay-tips', tips);
                    $('#update' + identity).removeClass('layui-hide');
                }
            }, {
                nid:identity,
                op:'cloudChk'
            });
        });
        @endif
    })
</script>
@include('common.footer')
