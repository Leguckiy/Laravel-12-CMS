<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta-title', 'My App')</title>
    @hasSection('meta-description')
        <meta name="description" content="@yield('meta-description')">
    @endif
    <link href="{{ asset('css/library/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/library/fontawesome.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    @yield('body')
    <script src="{{ asset('js/library/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/library/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
