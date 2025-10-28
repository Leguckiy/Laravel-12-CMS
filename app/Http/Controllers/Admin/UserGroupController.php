<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserGroup;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserGroupController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'Home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'User groups',
            'route' => 'admin.user_group.index',
        ],
    ];

    protected string $title = 'User groups';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $userGroups = UserGroup::paginate(15);

        return view('admin.user_group.index', compact('userGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $adminRoutes = $this->getAdminRoutes();
        
        return view('admin.user_group.form', compact('adminRoutes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);
        
        $permission = $this->parsePermissions($request->input('permissions', []));
        
        UserGroup::create([
            'name' => $validated['name'],
            'permission' => $permission,
        ]);
        
        return redirect()->route('admin.user_group.index')->with('success', 'User group created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UserGroup $userGroup): View
    {
        return view('admin.user_group.show', compact('userGroup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserGroup $userGroup): View
    {
        $adminRoutes = $this->getAdminRoutes();
        
        return view('admin.user_group.form', compact('userGroup', 'adminRoutes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserGroup $userGroup): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
        ]);
        
        $permission = $this->parsePermissions($request->input('permissions', []));
        
        $userGroup->update([
            'name' => $validated['name'],
            'permission' => $permission,
        ]);
        
        return redirect()->route('admin.user_group.index')->with('success', 'User group updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserGroup $userGroup): RedirectResponse
    {
        $userGroup->delete();

        return redirect()->route('admin.user_group.index')->with('success', 'User group deleted successfully.');
    }

    /**
     * Get all admin routes from configuration
     * Transforms simple permissions_mapping to form-friendly format
     */
    protected function getAdminRoutes(): array
    {
        return config('admin.permissions_mapping', []);
    }

    /**
     * Parse permissions from form (comma-separated to array)
     */
    protected function parsePermissions(array $input): array
    {
        $parsed = [
            'view' => [],
            'edit' => [],
        ];
        
        // Process view permissions
        if (isset($input['view']) && is_array($input['view'])) {
            foreach ($input['view'] as $title) {
                $parsed['view'][] = $title;
            }
        }
        
        // Process edit permissions
        if (isset($input['edit']) && is_array($input['edit'])) {
            foreach ($input['edit'] as $title) {
                $parsed['edit'][] = $title;
            }
        }
        
        return $parsed;
    }
}
