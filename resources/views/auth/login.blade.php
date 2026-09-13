<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Login | Sistem CRUD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/signin.css') }}" rel="stylesheet">
    <style>
        .bd-placeholder-img { font-size: 1.125rem; text-anchor: middle; }
        @media (min-width: 768px) { .bd-placeholder-img-lg { font-size: 3.5rem; } }
    </style>
</head>
<body class="text-center">
<main class="form-signin">
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <img class="mb-4" src="{{ asset('assets/img/logo.png') }}" alt="" width="72" height="100">
        <h3 class="h3 mb-3 fw-normal">Login</h3>

        @if($errors->has('login'))
            <div class="alert alert-danger text-center"><b>{{ $errors->first('login') }}</b></div>
        @endif
        @if($errors->has('recaptcha'))
            <div class="alert alert-danger text-center"><b>{{ $errors->first('recaptcha') }}</b></div>
        @endif

        <div class="form-floating">
            <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" value="{{ old('username') }}">
            <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating">
            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
            <label for="floatingPassword">Password</label>
        </div>

        @if(config('services.recaptcha.site_key'))
        <div class="mb-3">
            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
        </div>
        @endif

        <button class="w-100 btn btn-lg btn-primary" type="submit">Login</button>
        <p class="mt-5 mb-3 text-muted">&copy; 2026</p>
    </form>
</main>

@if(config('services.recaptcha.site_key'))
<script src="https://www.google.com/recaptcha/api.js"></script>
@endif
</body>
</html>
