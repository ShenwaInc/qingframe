<div class="layui-input-block">
    <div class="layui-input-inline" style="width: 70%">
        <input type="text" name="{{ $name }}" readonly{{ $required ? ' required lay-verify="required"' : '' }} value="{{ $value }}" placeholder="{{ $placeholder ? $placeholder : '请选择图片上传' }}" autocomplete="off" class="layui-input" />
    </div>
    <div class="layui-btn-group">
        <button type="button" class="layui-btn" onclick="showImageDialog(this);">上传图片</button>
    </div>
</div>
<div class="layui-input-block input-group" style="margin-top:.5em;">
    <img alt="{{ $name }}" class="img-responsive img-thumbnail" src="{{ $src }}" width="150" />
    <em class="close" style="position:absolute;" title="删除这张图片" onclick="deleteImage(this)">×</em>
</div>
@if(!$initimgupload)
<script type="text/javascript">
    function showImageDialog(elm, opts, options) {
        require(["util"], function(util){
            var btn = $(elm);
            var ipt = btn.parent().prev().find("input");
            var val = ipt.val();
            var img = ipt.parent().parent().next().children();
            options = @php echo json_encode($options); @endphp;
            util.image(val, function(url){
                if(url.url){
                    if(img.length > 0){
                        img.get(0).src = url.url;
                    }
                    ipt.val(url.attachment);
                    ipt.attr("filename",url.filename);
                    ipt.attr("url",url.url);
                }
                if(url.media_id){
                    if(img.length > 0){
                        img.get(0).src = url.url;
                    }
                    ipt.val(url.media_id);
                }
            }, options);
        });
    }
    function deleteImage(elm){
        $(elm).prev().attr("src", "/web/resource/images/nopic.jpg");
        $(elm).parent().prev().find("input").val("");
    }
</script>
@endif
