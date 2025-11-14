<?php

namespace App\Http\View\Composers;

use App\Services\AdminMenuService;
use App\Support\AdminContext;
use Illuminate\View\View;

class AdminComposer
{
    public function __construct(
        protected AdminMenuService $menuService,
        protected AdminContext $context,
    ) {}

    public function compose(View $view): void
    {
        $request = request();
        $breadcrumbs = [];
        $title = '';

        if ($route = $request->route()) {
            $controller = $route->getController();

            if ($controller && method_exists($controller, 'getBreadcrumbs')) {
                $breadcrumbs = $controller->getBreadcrumbs();
            }

            if ($controller && method_exists($controller, 'getTitle')) {
                $title = $controller->getTitle();
            }
        }

        $view->with([
            'adminUser' => $this->context->user,
            'adminLanguage' => $this->context->language,
            'menuItems' => $this->menuService->getMenuItems(),
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
        ]);
    }
}
