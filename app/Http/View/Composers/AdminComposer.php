<?php

namespace App\Http\View\Composers;

use App\Models\Language;
use App\Models\Setting;
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

        $viewsWithoutBreadcrumbsAndTitle = ['admin.not_found', 'admin.login'];
        if (! in_array($view->name(), $viewsWithoutBreadcrumbsAndTitle, true)) {
            if ($route = $request->route()) {
                $controller = $route->getController();

                if ($controller && method_exists($controller, 'getBreadcrumbs')) {
                    $breadcrumbs = $controller->getBreadcrumbs();
                }

                if ($controller && method_exists($controller, 'getTitle')) {
                    $title = $controller->getTitle();
                }
            }
        }

        $storeLanguage = Language::find((int) Setting::get('config_language_id'));
        $storeUrl = route('front.home', ['lang' => $storeLanguage->code]);

        $view->with([
            'adminUser' => $this->context->user,
            'adminLanguage' => $this->context->language,
            'storeUrl' => $storeUrl,
            'menuItems' => $this->menuService->getMenuItems(),
            'breadcrumbs' => $breadcrumbs,
            'title' => $title,
        ]);
    }
}
