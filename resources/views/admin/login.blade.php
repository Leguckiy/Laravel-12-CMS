@extends('layouts.app')

@section('content')
<div class="container">
    <br><br>
    <div class="row justify-content-sm-center">
        <div class="col-sm-10 col-md-8 col-lg-5">
            <div class="card">
                <div class="card-header">
                    <i class="fa-solid fa-lock"></i> {{ $text_login ?? 'Вход в админку' }}
                </div>
                <div class="card-body">
                    <form id="form-login" method="POST" action="{{ route('admin.login.submit') }}">
                        @csrf

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fa-solid fa-circle-exclamation"></i> 
                                @foreach($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="input-username" class="form-label">{{ $entry_username ?? 'Логин' }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" id="input-username" class="form-control" placeholder="{{ $entry_username ?? 'Логин' }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="input-password" class="form-label">{{ $entry_password ?? 'Password' }}</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" id="input-password" class="form-control" placeholder="{{ $entry_password ?? 'Password' }}" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-key"></i> {{ $button_login ?? 'Login' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
