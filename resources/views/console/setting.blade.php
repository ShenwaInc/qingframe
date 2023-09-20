@include('common.header')
<div class="layui-fluid">
    <div class="main-content">

        <h2>@lang('systemManagement')</h2>

        <div class="layui-tab fui-tab margin-bottom-xl">
            <ul class="layui-tab-title title_tab">
                <li class="layui-this">
                    <a href="{{ url('console/setting') }}">@lang('siteInformation')</a>
                </li>
                <li>
                    <a href="{{ url('console/server') }}">@lang('microServers')</a>
                </li>
                <li>
                    <a href="{{ url('console/module') }}">@lang('applications')</a>
                </li>
            </ul>
        </div>

        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <a href="{{ url('console/setting/pageset') }}" class="fr text-blue ajaxshow" title="{{ __('modifyData', array('data'=>__('siteInformation'))) }}"><i
                        class="fa fa-edit"></i></a>
                <span class="title">@lang('siteInformation')</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">@lang('siteName')</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['title'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">LOGO</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ tomedia($_W['setting']['page']['logo']) }}" width="128"/>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('icon')</span></td>
                            <td class="soild-after">
                                <img class="radius" src="{{ tomedia($_W['setting']['page']['icon']) }}" width="48"/>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('SEOKeywords')</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['keywords'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('SEODescription')</span></td>
                            <td class="soild-after">{{ $_W['setting']['page']['description'] }}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('copyright')</span></td>
                            <td class="soild-after">{!! $_W['setting']['page']['copyright'] !!}</td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('bottomNavigation')</span></td>
                            <td class="soild-after text-blue">
                                {!! $_W['setting']['page']['links'] !!}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="fui-card layui-card">
            <div class="layui-card-header nobd">
                <span class="title">@lang('systemSettings')</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                            <col width="230"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">@lang('siteStatus')</span></td>
                            <td class="soild-after">{{ __($_W['setting']['close']['status']==1 ? 'close' : 'open') }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        @if($_W['setting']['close']['status']==1)
                            {
                            <tr>
                                <td><span class="fui-table-lable">@lang('reasonForClosing')</span></td>
                                <td class="soild-after">{{ $_W['setting']['close']['reson'] }}</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                        @endif
                        <tr>
                            <td><span class="fui-table-lable">@lang('debugMode')</span></td>
                            <td class="soild-after">{{ __(env('APP_DEBUG') ? 'open' : 'close') }}</td>
                            <td class="text-right soild-after">
                                @if(env('APP_DEBUG'))
                                    <a href="{{ url("console/setting/envdebug") }}?state=off" class="text-blue confirm" data-text="{{ __('debugModeConfirm', array('action'=>__('close'))) }}">@lang('close')</a>
                                @else
                                    <a href="{{ url("console/setting/envdebug") }}?state=on" class="text-blue confirm" data-text="{{ __('debugModeConfirm', array('action'=>__('open'))) }}？">@lang('open')</a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('securityEntrance')</span></td>
                            <td class="soild-after">
                                @if(!empty($appSecurityEntrance))
                                    {{ $_W['siteroot'].$appSecurityEntrance }}&nbsp;&nbsp;<span class="js-clip text-blue" data-url="{{ $_W['siteroot'].$appSecurityEntrance }}">@lang('copy')</span>
                                @else
                                    @lang('notConfigured')
                                @endif
                            <td class="text-right soild-after">
                                <a href="javascript:" class="text-blue js-SecurityEntrance">{{ __($appSecurityEntrance?'modify':'setup') }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('versionSystem')</span></td>
                            <td class="soild-after">
                                V{{ QingVersion }} Release{{ QingRelease }}
                                @if($cloudinfo['isnew'])
                                    &nbsp;&nbsp;<span class="layui-badge layui-bg-red" title="V{{ $cloudinfo['version'] }} Release{{ $cloudinfo['releasedate'] }}">{{ __($cloudinfo['releasedate']==QingRelease?'sourceCodeChanged':'versionNew') }}</span>
                                @endif
                            </td>
                            <td class="text-right soild-after" style="line-height: 28px">
                                @if($cloudinfo['isnew'])
                                    <a href="{{ wurl('setting/selfupgrade') }}" class="text-red js-terminal" data-text="@lang('upgradeConfirm')">@lang('upgradeNow')</a>&nbsp;&nbsp;
                                    <a href="{{ wurl('setting/updateLog') }}" class="text-blue ajaxshow">@lang('cloudComparison')</a><br/>
                                @endif
                                <a href="{{ wurl('setting/detection') }}" class="text-green ajaxshow">@lang('Check for updates')</a>&nbsp;&nbsp;
                                <a href="https://www.yuque.com/shenwa/qingru/bggtv6hgvtf4ieun" target="_blank" class="text-blue">@lang('updateLog')</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="fui-card layui-card @if(!$_W['isfounder']) layui-hide @endif ">
            <div class="layui-card-header nobd">
                <span class="title">@lang('System parameters')</span>
            </div>
            <div class="layui-card-body">
                <div class="un-padding">
                    <table class="layui-table fui-table lines" lay-skin="nob">
                        <colgroup>
                            <col width="120"/>
                            <col/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <td><span class="fui-table-lable">@lang('System name')</span></td>
                            <td class="soild-after">{{ $_W['config']['name'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('containerInformation')</span></td>
                            <td class="soild-after">{{ php_uname() }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('environment')</span></td>
                            <td class="soild-after">{{ $_SERVER['SERVER_SOFTWARE'] ?  : php_sapi_name() }}&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        <tr>
                            <td><span class="fui-table-lable">@lang('timezone')</span></td>
                            <td class="soild-after">{{ $_W['config']['setting']['timezone'] }}</td>
                            <td class="text-right soild-after"></td>
                        </tr>
                        @if($_W['isfounder'])
                            <tr>
                                <td><span class="fui-table-lable">@lang('cloud service')</span></td>
                                <td class="soild-after">
                                    @if($activeState['status']==1)
                                        {{$activeState['name']}}（ID:&nbsp;<span class="text-blue js-clip" data-url="{{ $activeState['siteid'] }}">{{ $activeState['siteid'] }}</span>）
                                    @else
                                        {{ $activeState['state'] }}
                                    @endif
                                </td>
                                <td class="text-right soild-after">
                                    <a href="{{ url('console/active') }}" class="text-blue">@lang('reset')</a>
                                </td>
                            </tr>
                            <tr>
                                <td><span class="fui-table-lable">@lang('systemKernel')</span></td>
                                <td class="soild-after">QingFrame (Based on <a href="https://laravel.com/" class="text-blue" target="_blank">Laravel</a> 6.2.0)</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                            <tr>
                                <td><span class="fui-table-lable">@lang('all rights reserved')</span></td>
                                <td class="soild-after">广西神蛙网络科技有限公司</td>
                                <td class="text-right soild-after"></td>
                            </tr>
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    $(function () {
        $('.js-SecurityEntrance').click(function () {
            SecurityEntrance('{{ $appSecurityEntrance }}');
            return false;
        });
    })
    function SecurityEntrance(code="") {
        if(code===""){
            code = Wrandom(8);
        }
        layer.prompt({title: '请输入安全入口（不能含/）', value:code, btn:['确定', '随机生成', '取消'], btn2:function (index) {
            layer.close(index);
            SecurityEntrance();
        }, yes:function (index, elem) {
            let value = $(elem).find('input.layui-layer-input').val();
            if(value.indexOf('/')>=0){
                layer.msg("安全入口不能包含字符/");
            }
            Core.post("{{ wurl("setting") }}", function (res) {
                Core.report(res);
            }, {
                'op':"appSecurity",
                'SecurityCode':value
            });
        }});
    }
</script>
<style>
    .text-blue a{color: #0081ff;}
</style>
@include('console.terminal')
@include('common.footer')
