@extends('layout.base')

@section('title', 'Ubah Media')

@section('extra_css')
<style>
#is-active-ul, #is-online-ul {
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
                    <h3><b>Ubah Media</b></h3>
                </div>
                <form action="/media/update" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="{{ $media->id }}">
                    <div class="panel-body">
                        <div class="row form-group center-block">
                            <label for="name"> Nama Media: </label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ $media->name }}" required>
                        </div>
                        <div class="row form-group center-block">
                            <label> Jenis: </label>
                            <ul id="is-online-ul">
                                <li class="form-group"><label for="is-online-yes" class="radio-inline"><input type="radio" name="is-online" id="is-online-yes" value="yes" @if ($media->is_online) checked @endif> Online </label></li>
                                <li class="form-group"><label for="is-online-no" class="radio-inline"><input type="radio" name="is-online" id="is-online-no" value="no" @if (!$media->is_online) checked @endif> Offline </label></li>
                            </ul>
                        </div>
                        <div class="form-group">
                            <label for="text"> Teks: </label>
                            <select class="form-control" name="text" id="text">
                                <option value="REQUIRED" @if ($media->text === 'REQUIRED') selected @endif> WAJIB </option>
                                <option value="OPTIONAL" @if ($media->text === 'OPTIONAL') selected @endif> OPSIONAL </option>
                                <option value="NOT_APPLICABLE" @if ($media->text === 'NOT_APPLICABLE') selected @endif> TIDAK ADA </option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image"> Gambar/Flyer: </label>
                            <select class="form-control" name="image" id="image">
                                <option value="REQUIRED" @if ($media->image === 'REQUIRED') selected @endif> WAJIB </option>
                                <option value="OPTIONAL" @if ($media->image === 'OPTIONAL') selected @endif> OPSIONAL </option>
                                <option value="NOT_APPLICABLE" @if ($media->image === 'NOT_APPLICABLE') selected @endif> TIDAK ADA </option>
                            </select>
                        </div>
                        <div class="row form-group center-block">
                            <label> Status: </label>
                            <ul id="is-active-ul">
                                <li class="form-group"><label for="is-active-yes" class="radio-inline"><input type="radio" name="is-active" id="is-active-yes" value="yes" @if ($media->is_active) checked @endif> Aktif </label></li>
                                <li class="form-group"><label for="is-active-no" class="radio-inline"><input type="radio" name="is-active" id="is-active-no" value="no" @if (!$media->is_active) checked @endif> Tidak Aktif </label></li>
                            </ul>
                        </div>
                        <div class="row form-group center-block">
                            <div class="form-group">
                                <button type="submit" class="btn btn-default"> Ubah </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
