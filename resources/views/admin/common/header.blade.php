<header id="header" class="navbar navbar-expand navbar-light bg-light">
    <div class="container-fluid">
        <a href="{{ route('admin.dashboard') }}" class="navbar-brand d-none d-lg-block">
            <img src="{{ asset('images/admin/logo.svg') }}" alt="{{ config('app.name') }}" title="{{ config('app.name') }}" height="40"/>
        </a>
        <button type="button" id="button-menu" class="btn btn-link d-inline-block d-lg-none">
            <i class="fas fa-bars"></i>
        </button>
        <ul class="nav navbar-nav">
            <li id="nav-profile" class="nav-item dropdown">
                <a href="#" data-bs-toggle="dropdown" class="nav-link">
                    @if ($adminUser->image_url)
                        <img
                            src="{{ $adminUser->image_url }}"
                            alt="{{ $adminUser->fullname }}"
                            class="rounded-circle me-2"
                            width="36"
                            height="36"
                        >
                    @else
                        <div class="avatar-sm bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <span class="d-none d-md-inline d-lg-inline">
                        <span>{{ $adminUser->fullname }}</span>
                        <i class="fa-solid fa-caret-down fa-fw"></i>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a href="{{ route('admin.user.edit', $adminUser->id) }}" class="dropdown-item">
                            <i class="fas fa-user-circle fa-fw"></i>
                            {{ __('admin.your_profile') }}
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a href="{{ url('/') }}" target="_blank" class="dropdown-item">
                            <i class="fas fa-external-link-alt fa-fw"></i>
                            {{ __('admin.go_to_website') }}
                        </a>
                    </li>
                </ul>
            </li>

            <li id="nav-logout" class="nav-item d-flex align-items-center">
                <a href="{{ route('admin.logout') }}" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="d-none d-md-inline">{{ __('admin.logout') }}</span>
                </a>
            </li>
        </ul>
    </div>
</header>
