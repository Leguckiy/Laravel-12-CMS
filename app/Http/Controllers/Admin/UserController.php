<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends AdminController
{
    protected array $breadcrumbs = [
        [
            'title' => 'Home',
            'route' => 'admin.dashboard',
        ],
        [
            'title' => 'Users',
            'route' => 'admin.user.index',
        ],
    ];

    protected string $title = 'Users';

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
        
        return view('admin.user.form', compact('userGroups', 'userGroupsOptions'));
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

        return redirect()->route('admin.user.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user->load('userGroup');
        
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
        
        return view('admin.user.form', compact('user', 'userGroups', 'userGroupsOptions'));
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

        return redirect()->route('admin.user.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');
    }
}
