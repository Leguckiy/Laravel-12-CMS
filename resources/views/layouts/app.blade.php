<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('meta-title', 'My App')</title>
    <link href="{{ asset('css/library/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/library/fontawesome.min.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <script src="{{ asset('js/library/bootstrap.bundle.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
