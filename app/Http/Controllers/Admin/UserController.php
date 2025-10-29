<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\AdminUserRequest;
use App\Models\Language;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'users',
            'route' => 'admin.user.index',
        ],
    ];

    protected string $title = 'users';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with('userGroup')->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $userGroups = UserGroup::all();
        $userGroupsOptions = $userGroups->map(function($group) {
            return ['id' => $group->id, 'name' => $group->name];
        })->toArray();
        
        $languages = Language::where('status', true)->orderBy('sort_order')->get();
        $languagesOptions = $languages->map(function($language) {
            return ['id' => $language->id, 'name' => $language->name];
        })->toArray();
        
        return view('admin.user.form', compact('userGroups', 'userGroupsOptions', 'languagesOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AdminUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // Remove confirm field as it's not needed in database
        unset($validated['confirm']);
        
        User::create($validated);

        return redirect()->route('admin.user.index')->with('success', __('admin.created_successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load(['userGroup', 'language']);
        
        return view('admin.user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $userGroups = UserGroup::all();
        $userGroupsOptions = $userGroups->map(function($group) {
            return ['id' => $group->id, 'name' => $group->name];
        })->toArray();
        
        $languages = Language::where('status', true)->orderBy('sort_order')->get();
        $languagesOptions = $languages->map(function($language) {
            return ['id' => $language->id, 'name' => $language->name];
        })->toArray();
        
        return view('admin.user.form', compact('user', 'userGroupsOptions', 'languagesOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        
        // Remove password from validation if empty
        if (empty($validated['password'])) {
            unset($validated['password']);
        }
        
        // Remove confirm field as it's not needed in database
        unset($validated['confirm']);

        $user->update($validated);

        return redirect()->route('admin.user.index')->with('success', __('admin.updated_successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', __('admin.deleted_successfully'));
    }
}
