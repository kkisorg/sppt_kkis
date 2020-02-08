@extends('layout.base', ['hide_menu' => true])

@section('title', 'Login')

@section('extra_js')
<script>
    function validate_password() {
        if ($('#register-password').val() !== $('#register-password-confirmation').val()) {
            alert("Password tidak cocok.");
            $('#register-password').focus();
            return false;
        }
    }
</script>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Login</b></h3>
                </div>
                <div class="panel-body">
                    <form action="/authenticate" role="form" method="POST" class="form-vertical">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="login-email">Email:</label>
                            <input type="email" id="login-email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="login-password">Password:</label>
                            <input type="password" id="login-password" name="password" class="form-control" required>
                        </div>
                        <div class="check">
                            <label for="login-remember"><input type="checkbox" id="login-remember" name="remember"> Ingat saya</label>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Log In</button>
                        </div>
                    </form>
                    <div class="form-group">
                        <a class="btn btn-link" href="/forget_password">
                        Lupa password? Klik disini!
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Daftar</b></h3>
                </div>
                <div class="panel-body">
                    <form name="register-form" action="/register" role="form" method="POST" class="form-vertical" onsubmit="return validate_password()">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="register-name">Nama:</label>
                            <input type="text" id="register-name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="register-organization-name">Nama Ranting/Unit:</label>
                            <input type="text" id="register-organization-name" name="organization-name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="register-email">Nomor Telpon/HP:</label>
                            <input type="text" id="register-mobile-number" name="mobile-number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="register-email">Email:</label>
                            <input type="email" id="register-email" name="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="register-password">Password:</label>
                            <input type="password" id="register-password" name="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="register-password-confirmation">Ulangi Password:</label>
                            <input type="password" id="register-password-confirmation" name="password-confirmation" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <p>Dengan mengklik tombol daftar di bawah ini, Anda setuju dengan <a href="/privacy_policy.html" target="_blank">Kebijakan Privasi (Privacy Policy)</a>.</p>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-default">Daftar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
