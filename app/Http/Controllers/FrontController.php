<?php

namespace App\Http\Controllers;

use App\Support\FrontContext;
use Illuminate\Routing\Controller as BaseController;

abstract class FrontController extends BaseController
{
    protected FrontContext $context;

    public function __construct(FrontContext $context)
    {
        $this->context = $context;
    }
}
