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
        $request = request();
        $controller = $request->route()->getController();
        
        if ($controller) {
            if (method_exists($controller, 'getBreadcrumbs')) {
                $breadcrumbs = $controller->getBreadcrumbs();
            }
            
            if (method_exists($controller, 'getTitle')) {
                $title = $controller->getTitle();
            }
        }

        $adminUser = Auth::guard('admin')->user();

        $view->with([
            'adminUser' => $adminUser,
            'menuItems' => $this->menuService->getMenuItems(),
            'breadcrumbs' => $breadcrumbs ?? [],
            'title' => $title ?? ''
        ]);
    }
}
