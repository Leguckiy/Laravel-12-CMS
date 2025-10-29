<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected array $breadcrumbs;

    protected string $title;

    public function getBreadcrumbs(): array
    {
        return array_map(function($item) {
            $item['title'] = __("admin.{$item['title']}");
            return $item;
        }, $this->breadcrumbs);
    }

    public function getTitle(): string
    {
        return __("admin.{$this->title}");
    }
}
