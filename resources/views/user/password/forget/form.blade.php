@extends('layout.base', ['hide_menu' => true])

@section('title', 'Lupa Password')

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Lupa Password</b></h3>
                </div>
                <form action="/insert_password_reset" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block">
                            <p>Masukkan email Anda untuk melanjutkan</p>
                        </div>
                        <div class="row form-group center-block" >
                            <label for="email"> Email: </label>
                            <input type="text" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="row form-group center-block">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default"> Lanjutkan </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
