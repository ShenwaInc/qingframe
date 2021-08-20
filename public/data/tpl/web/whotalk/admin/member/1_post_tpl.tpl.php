<?php defined('IN_IA') or exit('Access Denied');?><label class="layui-form-label"><?php  echo $field;?></label>
<div class="layui-input-block">
    <div class="dropdown layui-form-select">
        <input type="hidden" name="<?php  echo $name;?>" value="<?php  echo $value;?>" id="<?php  echo $memberpicker;?>">
        <input type="text" data-toggle="dropdown" id="<?php  echo $memberpicker;?>-i" name="memberpicker[]" data-mp="<?php  echo $memberpicker;?>" value="<?php  echo $nickname;?>" placeholder="<?php  echo $placeholder;?>" autocomplete="off" class="layui-input memberpicker"<?php  if($required) { ?> lay-verify="required"<?php  } ?> />
        <i class="layui-edge"></i>
        <i class="layui-icon layui-icon-close" layadmin-event="closedp" data-dp="#<?php  echo $memberpicker;?>"></i>
        <ul class="dropdown-menu <?php  echo $memberpicker;?>-menu memberpicker-menu layui-hide" style="width: 100%" data-mp="<?php  echo $memberpicker;?>" aria-labelledby="dLabel"></ul>
    </div>
</div>
<?php  if(!$_W['laymemberpicker']) { ?>
<script type="text/javascript">
jQuery(function () {
    jQuery('.memberpicker').keyup(function(){
        var keyword = $(this).val();
        var memberpicker = $(this).data('mp');
        var _this = this;
        if(keyword=='') return false;
        $.ajax({url:'<?php  echo weburl("util/memberpicker")?>',type:'POST',data:{_token:'<?php  echo $_W["token"];?>',keyword:keyword},dataType:'html',success:function (Html) {
            $('.'+memberpicker+'-menu').html(Html).removeClass('layui-hide');
            if($(_this).parent().hasClass('open')){
                $(_this).dropdown('toggle');
            }
            $(_this).dropdown('toggle');
        }})
    });
    jQuery('.memberpicker-menu').on('click','a[data-uid]',function () {
        var memberpicker = $(this).parent().parent().data('mp');
        var uid = $(this).data('uid');
        var nickname = $(this).text();
        $('#'+memberpicker).val(uid).next().val(nickname).dropdown('toggle');
        return false;
    });
});
</script>
<?php  } ?>
