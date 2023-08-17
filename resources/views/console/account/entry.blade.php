@include('common.header')

<div class="fui-content" style="min-height: 360px;">
    <form class="layui-form" method="POST" action="{{ wurl('account/entry',array('uniacid'=>$uniacid)) }}">
        @csrf
        <div class="layui-form-item must">
            <label class="layui-form-label">{{ __('chooseData', array('data'=>__('defaultEntry'))) }}</label>
            <div class="layui-input-block">
                @foreach($titles as $key=>$item)
                    <input type="radio" lay-filter="ctrls" data-target=".ctrls" value="{{ $key }}" name="ctrl" title="{{ $item }}"{{ $ctrl==$key ? ' checked' : '' }} />
                @endforeach
            </div>
        </div>
        @foreach($entrances as $index=>$entry)
        <div class="layui-form-item ctrls form-item{{$index}} must{{ $ctrl==$index ? '' : ' layui-hide' }}">
            <label class="layui-form-label">@lang('specificLocation')</label>
            <div class="layui-input-block">
                <select name="methods[{{ $index }}]">
                @foreach($entry as $key=>$item)
                    <option value="{{ $key }}"{{ $method==$key ? ' selected' : '' }}>{{ $item }}</option>
                @endforeach
                </select>
            </div>
        </div>
        @endforeach
        <div class="layui-form-item">
            <div class="layui-input-block">
                <button class="layui-btn layui-btn-normal" lay-submit type="submit" value="true" name="savedata">@lang('save')</button>
                <button type="reset" class="layui-btn layui-btn-primary">@lang('reset')</button>
            </div>
        </div>
    </form>
</div>

@include('common.footer')
