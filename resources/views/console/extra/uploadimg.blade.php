<div class="layui-input-block">
    <div class="layui-input-inline" style="width: 70%">
        <input type="text" name="{{ $name }}" readonly{{ $required ? ' required lay-verify="required"' : '' }} value="{{ $value }}" placeholder="{{ $placeholder ? $placeholder : '请选择图片上传' }}" autocomplete="off" class="layui-input" />
    </div>
    <div class="layui-btn-group">
        <button type="button" class="layui-btn js-image-{{ $picker }}">上传图片</button>
    </div>
</div>
<div class="layui-input-block">
    <span class="layui-word-aux">
        <img alt="{{ $name }}" src="{{ $src }}" />
    </span>
</div>
<script type="text/javascript">
    util.image($('.js-image-{{ $picker }}'), function(url){
        $('.js-image-{{ $picker }}').prev().val(url.attachment);
        $('.js-image-{{ $picker }}').parent().next().find('img').attr('src',url.url);
    }, {
        crop : false,
        multiple : false
    });
</script>
