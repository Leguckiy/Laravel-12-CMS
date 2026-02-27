<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AdminController;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Language;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\AdminImageUploader;
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
        $userGroupsOptions = UserGroup::getOptions();

        $languagesOptions = Language::getActiveOptions();

        return view('admin.user.form', compact('userGroups', 'userGroupsOptions', 'languagesOptions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Remove confirm field as it's not needed in database
        unset($validated['confirm'], $validated['image']);

        $user = User::create($validated);

        $image = $this->handleAvatarUpload($request, $user);
        if ($image !== $user->image) {
            $user->update(['image' => $image]);
        }

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
        $userGroupsOptions = UserGroup::getOptions();

        $languagesOptions = Language::getActiveOptions();

        return view('admin.user.form', compact('user', 'userGroupsOptions', 'languagesOptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        // Remove password from validation if empty
        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        // Remove confirm field as it's not needed in database
        unset($validated['confirm'], $validated['image']);

        $validated['image'] = $this->handleAvatarUpload($request, $user);

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

    private function handleAvatarUpload(UserRequest $request, User $user): ?string
    {
        $uploader = new AdminImageUploader;
        $currentFilename = $user->image;
        $currentPath = $user->image_path;

        if ($request->boolean('image_remove') && $currentPath) {
            $uploader->delete($currentPath);
            $currentFilename = null;
            $currentPath = null;
        }

        if (! $request->hasFile('image')) {
            return $currentFilename;
        }

        if ($currentPath) {
            $uploader->delete($currentPath);
        }

        $width = (int) config('image_sizes.small.width');
        $height = (int) config('image_sizes.small.height');

        return $uploader->uploadImage(
            'user_'.$user->id,
            User::IMAGE_DIRECTORY,
            $request->file('image'),
            $width,
            $height
        );
    }
}
