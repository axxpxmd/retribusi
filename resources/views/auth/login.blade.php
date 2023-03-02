<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Title -->
    <link rel="icon" href="{{ asset('images/logo-png.png') }}" type="image/x-icon">
    <title>{{ config('app.name') }} | Form Login</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/myStyle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/util.css') }}">

    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body class="light">
    <div class="page parallel">
        <div class="d-flex row">
            <div class="col-md-9 height-full css-selector m-none d-flex align-content-center flex-wrap">
                <div class="col-md-6">
                    <div class="text-white p-l-80">
                        <p id="title" class="fs-50 font-weight-light animate__animated animate__backInLeft">SKRD</p>
                        <p class="mt-4 mb-1 fs-25 font-weight-lighter">Surat Ketetapan Retribusi Daerah.</p>
                        <p class="mt-0 fs-25 font-weight-lighter">Kota Tangerang Selatan.</p>
                        <hr class="mt-2 bg-white" width="200%">
                    </div>
                </div>
                <div class="absolute bottom-0 text-white p-l-85 mb-5">COPYRIGHT © {{ $year }}.</div>
            </div>
            <div class="col-md-3 white m-mt-login">
                <div class="pl-5 pt-5 pr-5 m-t-90 pb-0">
                    <img src="{{ asset('images/template/logo.png') }}" class="mx-auto d-block animate__animated animate__backInDown" width="150" alt=""/>
                </div>
                <div class="p-5">
                    @if (count($errors) > 0)
                    <div class="alert alert-danger" id="errorAlert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif 
                    <h3 class="font-weight-normal">Selamat Datang</h3>
                    <p>Silahkan masukan username dan password Anda.</p>
                    <form class="needs-validation" novalidate method="POST" action="{{ route('login') }}" autocomplete="off">
                        @csrf
                        <div class="form-group has-icon"><i class="icon icon-user"></i>
                            <input type="text" class="form-control form-control-lg @if ($errors->has('username')) is-invalid @endif" placeholder="username" name="username" autocomplete="off" value="{{ old('username') }}" required autofocus>
                            @if ($errors->has('username'))
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('username') }}</strong>
                            </div>
                            @endif
                        </div>
                        <div class="form-group has-icon"><i class="icon icon-user-secret"></i>
                            <input type="password" class="form-control form-control-lg @if ($errors->has('password')) is-invalid @endif" placeholder="Password" name="password" autocomplete="off" value="{{ old('password') }}" required>
                            @if ($errors->has('password'))
                            <div class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                            @endif
                        </div>
                        <button class="btn btn-primary btn-lg btn-block">Login<i class="icon-sign-in ml-2"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>