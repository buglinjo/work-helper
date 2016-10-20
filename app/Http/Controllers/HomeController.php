<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Buglinjo\HelloComposer\Hello;

class HomeController extends Controller
{
    public function index(){
        return Hello::world();
    }
}
