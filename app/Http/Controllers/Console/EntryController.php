<?php

namespace App\Http\Controllers\Console;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EntryController extends Controller
{
    //
    function index(){
        $redirect = url('console');
        if (!empty($_SERVER['QUERY_STRING'])){
            $redirect .= '?' . $_SERVER['QUERY_STRING'];
        }
        header('Location: ' . $redirect);
        exit();
    }

}
