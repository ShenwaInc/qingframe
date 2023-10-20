<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;

class EntryController extends Controller
{
    //
    function index(){
        $redirect = wurl('');
        if (!empty($_SERVER['QUERY_STRING'])){
            $redirect .= '?' . $_SERVER['QUERY_STRING'];
        }
        header('Location: ' . $redirect);
        exit();
    }

}
