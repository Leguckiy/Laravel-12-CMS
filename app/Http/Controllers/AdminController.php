<?php

namespace App\Http\Controllers;

class AdminController extends Controller
{
    protected array $breadcrumbs;

    protected string $title;

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
