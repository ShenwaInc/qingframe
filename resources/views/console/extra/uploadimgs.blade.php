@if(!$initmulti)
<script type="text/javascript">
    function uploadMultiImage(elm) {
        var name = $(elm).next().val();
        util.image("", function(urls){
            console.log(urls);
            $.each(urls, function(idx, url){
                $(elm).parent().parent().next().append('<div class="multi-item"><img src="'+url.url+'" class="img-responsive img-thumbnail"><input type="hidden" name="'+name+'[]" value="'+url.attachment+'"><em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em></div>');
            });
        }, @php echo json_encode($params) @endphp);
    }
    function deleteMultiImage(elm){
        $(elm).parent().remove();
    }
</script>
@endif
<div class="layui-input-block">
    <div class="layui-input-inline" style="width:70%;">
        <input type="text" class="layui-input" readonly="readonly" value="" placeholder="{{ $placeholder }}" autocomplete="off">
    </div>
    <span class="layui-btn-group">
		<button class="layui-btn layui-btn-default" type="button" onclick="uploadMultiImage(this);">选择图片</button>
		<input type="hidden" value="{{ $name }}" />
	</span>
</div>
<div class="layui-input-block multi-img-details" style="overflow: hidden;">
@foreach($value as $pic)
    <div class="multi-item">
        <img src="{{ tomedia($pic) }}" class="img-responsive img-thumbnail">
        <input type="hidden" name="{{$name}}[]" value="{{$pic}}" >
        <em class="close" title="删除这张图片" onclick="deleteMultiImage(this)">×</em>
    </div>
@endforeach
</div>
