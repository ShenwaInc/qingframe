@include('common.header')

<div class="main-content">

    <h2 class="weui-desktop-page__title">@lang('accountManagement')</h2>

    <div class="layui-tab fui-tab margin-bottom-xl">
        <ul class="layui-tab-title title_tab">
            <li>
                <a href="{{ wurl('user/profile') }}">@lang('personalInformation')</a>
            </li>
            <li class="layui-this">
                <a href="javascript:;">@lang('subAccount')</a>
            </li>
        </ul>
    </div>

    <div class="fui-card layui-card">
        <div class="layui-card-header nobd">
            <a href="{{ wurl('user/create',array('uid'=>0),true) }}" class="fr layui-btn layui-btn-sm layui-btn-normal ajaxshow">{{ __('newData', array('data'=>__('subAccount'))) }}</a>
            <span class="title">{{ __('manageData', array('data'=>__('subAccount'))) }}</span>
        </div>
        <div class="layui-card-body">
            <div class="un-padding">
                <table class="layui-table fui-table lines" lay-even lay-skin="nob">
                    <colgroup>
                        <col width="120" />
                        <col />
                        <col width="120" />
                        <col width="120" />
                        <col width="200" />
                    </colgroup>
                    <thead>
                    <tr>
                        <th>UID</th>
                        <th>@lang('username')</th>
                        <th>@lang('addtime')</th>
                        <th style="text-align: center">@lang('expireDate')</th>
                        <th><div class="text-right">@lang('action')</div></th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(empty($users))
                        <tr>
                            <td colspan="5" class="text-center"><span class="text-gray">@lang('empty')</span></td>
                        </tr>
                    @endif
                    @foreach($users as $key=>$value)
                    <tr>
                        <td>{{ $value['uid'] }}</td>
                        <td>
                            {{ $value['username'] }}
                            @if(!empty($value['remark']))
                                <span class="text-gray">({{$value['remark']}})</span>
                            @endif
                        <td>{{ $value['createdate'] }}</td>
                        <td class="text-center{{ $value['expire'] ? ' text-red' : '' }}">{{ $value['expiredate'] }}</td>
                        <td class="text-right">
                            <a href="{{ wurl('user/checkout',array('uid'=>$value['uid']),true) }}" class="text-blue margin-right-sm confirm" data-text="@lang('subAccountSwitch')">@lang('switch')</a>
                            <a href="{{ wurl('user/create',array('uid'=>$value['uid']),true) }}" class="text-blue ajaxshow margin-right-sm">@lang('modify')</a>
                            <a href="{{ wurl('user/remove',array('uid'=>$value['uid'])) }}" class="text-red confirm" data-text="@lang('subAccountDelete')">@lang('delete')</a>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@include('common.footer')
