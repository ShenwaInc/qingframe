<?php

function ImagePicker($name,$value,$placeholder='请选择图片上传',$required=false){
    global $_W;
    $imagepicker = 'ip'.random(8,true);
    $options = array(
        'name'=>$name,
        'value'=>$value,
        'placeholder'=>$placeholder,
        'required'=>$required,
        'src'=>$value?tomedia($value):asset('static/images/nopic.jpg'),
        'picker'=>$imagepicker,
        'initimgupload'=>isset($_W['initimgupload']) ? true : false
    );
    $_W['initimgupload'] = true;
    return view('console.extra.uploadimg',$options);
}

function ImagesPicker($name,$value=array(),$placeholder='请选择图片（可多选）', $params=array()){
    global $_W;
    $imagepicker = 'ips'.random(8,true);
    $params['multiple'] = true;
    $params['direct'] = false;
    $params['fileSizeLimit'] = intval($_W['setting']['upload']['image']['limit']) * 1024;
    $params['dest_dir'] = '';
    $options = array(
        'name'=>$name,
        'value'=>$value,
        'placeholder'=>$placeholder,
        'picker'=>$imagepicker,
        'params'=>$params,
        'initmulti'=>isset($_W['initimgmulti']) ? true : false
    );
    $_W['initimgmulti'] = true;
    return view('console.extra.uploadimgs',$options);
}
