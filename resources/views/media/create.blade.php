@extends('layout.base')

@section('title', 'Buat Media Baru')

@section('extra_css')
<style>
#is-active-ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-4 col-sm-offset-4 col-md-4 col-md-offset-4 col-lg-4 col-lg-offset-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Buat Media Baru</b></h3>
                </div>
                <form action="/media/insert" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <div class="panel-body">
                        <div class="row form-group center-block">
                            <label for="name"> Nama Media: </label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="text"> Teks: </label>
                            <select class="form-control" name="text" id="text">
                                <option value="REQUIRED"> WAJIB </option>
                                <option value="OPTIONAL"> OPSIONAL </option>
                                <option value="NOT_APPLICABLE"> TIDAK ADA </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image"> Gambar/Flyer: </label>
                            <select class="form-control" name="image" id="image">
                                <option value="REQUIRED"> WAJIB </option>
                                <option value="OPTIONAL"> OPSIONAL </option>
                                <option value="NOT_APPLICABLE"> TIDAK ADA </option>
                            </select>
                        </div>
                        <div class="row form-group center-block">
                            <label> Status: </label>
                            <ul id="is-active-ul">
                                <li class="form-group"><label for="is-active-yes" class="radio-inline"><input type="radio" name="is-active" id="is-active-yes" value="yes" checked> Aktif </label></li>
                                <li class="form-group"><label for="is-active-no" class="radio-inline"><input type="radio" name="is-active" id="is-active-no" value="no"> Tidak Aktif </label></li>
                            </ul>
                        </div>
                        <div class="row form-group center-block">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default"> Buat </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
