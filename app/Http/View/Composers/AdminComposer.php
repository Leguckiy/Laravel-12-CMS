<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\AdminMenuService;

class AdminComposer
{
    protected $menuService;

    public function __construct(AdminMenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $adminUser = Auth::guard('admin')->user();
        
        $view->with([
            'adminUser' => $adminUser,
            'menuItems' => $this->menuService->getMenuItems(),
            'currentRoute' => request()->route() ? request()->route()->getName() : null
        ]);
    }
}
