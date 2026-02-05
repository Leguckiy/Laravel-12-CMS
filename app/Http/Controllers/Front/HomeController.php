<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\FrontController;

class HomeController extends FrontController
{
    public function index()
    {
        return view('front.home');
    }
}
