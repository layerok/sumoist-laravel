<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="{{ Asset::load('backend/css/main.css', true) }}" />
    <link rel="stylesheet" type="text/css" href="{{ Asset::load('backend/css/font-awesome/4.7.0/css/font-awesome.min.css', true) }}"/>
    <title>Login - {{ config('app.name') }}</title>
</head>
<body>
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>{{ config('app.name') }}</h1>
    </div>
    <div class="login-box">
        <form class="login-form" action="{{ route('admin.login.post') }}" method="POST" role="form">
            @csrf
            <h3 class="login-head"><i class="fa fa-lg fa-fw fa-user"></i>ВОЙТИ</h3>
            <div class="form-group">
                <label class="control-label" for="email">Логин</label>
                <input class="form-control" type="email" id="email" name="email" placeholder="Логин" autofocus value="{{ old('email') }}">
            </div>
            <div class="form-group">
                <label class="control-label" for="password">Пароль</label>
                <input class="form-control" type="password" id="password" name="password" placeholder="Пароль">
            </div>
            <div class="form-group">
                <div class="utility">
                    <div class="animated-checkbox">
                        <label>
                            <input type="checkbox" name="remember"><span class="label-text">Запомнить меня</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group btn-container">
                <button class="btn btn-primary btn-block" type="submit"><i class="fa fa-sign-in fa-lg fa-fw"></i>Войти</button>
            </div>
        </form>
    </div>
</section>
<script src="{{ Asset::load('backend/js/jquery-3.2.1.min.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/popper.min.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/bootstrap.min.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/main.js', true) }}"></script>
<script src="{{ Asset::load('backend/js/plugins/pace.min.js', true) }}"></script>
</body>
</html>
