<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\Request;

class UtilController extends Controller
{
    //
    public function save(Request $request,$op='index'){
        global $_W;
        if ($op=='upload'){
            $type = $request->input('type', 'image');
            //dd($request->file('file')->get());
            $res = FileService::file_upload($_FILES['file'], $type);
            if (is_error($res)){
                return $this->message($res['message']);
            }
            return $this->message(array('url'=>tomedia($res['path']),'path'=>$res['path']),'','success');
        }
    }

}
