@extends('layout.base')

@section('title', 'Ubah Pengumuman Dalam Distribusi')

@section('extra_js')
<script>
    $(document).ready(function() {
        ClassicEditor.create(document.querySelector('#header'), {
            simpleUpload: {uploadUrl: '{{ URL::to('/')}}/api/image_upload'}
        }).catch(error => {console.error(error);});
        ClassicEditor.create(document.querySelector('#content'), {
            simpleUpload: {uploadUrl: '{{ URL::to('/')}}/api/image_upload'}
        }).catch(error => {console.error(error);});
        ClassicEditor.create(document.querySelector('#footer'), {
            simpleUpload: {uploadUrl: '{{ URL::to('/')}}/api/image_upload'}
        }).catch(error => {console.error(error);});
    });
</script>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-6 col-sm-offset-3 col-md-6 col-md-offset-3 col-lg-6 col-lg-offset-3">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Ubah Pengumuman Dalam Distribusi</b></h3>
                </div>
                <form action="/offline_distribution/update_content" role="form" method="POST" class="form-vertical">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" id="id" value="{{ $offline_distribution->id }}">
                    <div class="panel-body">
                        <div><h4><b>{{ $offline_distribution->name }}</b></h4></div>
                        <div><h5><b>Waktu Distribusi: </b>{{ $offline_distribution->distribution_datetime }}</h5></div>
                        <div><h5><b>Batas Waktu (Deadline): </b>{{ $offline_distribution->deadline_datetime }}</h5></div>
                        <div><h5><b>Media: </b>{{ $offline_distribution->media_name }}</h5></div>
                        <hr>
                        <div><h5><b>Daftar Pengumuman (Untuk Referensi): </b></h5></div>
                        @foreach ($offline_distribution->announcement as $announcement)
                        <div>{!! $announcement->pivot->content !!}</div>
                        @endforeach
                        <hr>
                        <div class="row form-group center-block">
                            <label for="header"> Header: </label>
                            <textarea name="header" id="header" class="form-control" rows="5">{{ $offline_distribution->header }}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label for="footer"> Isi: </label>
                            <textarea name="content" id="content" class="form-control" rows="5">{{ $offline_distribution->content }}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <label for="footer"> Footer: </label>
                            <textarea name="footer" id="footer" class="form-control" rows="5">{{ $offline_distribution->footer }}</textarea>
                        </div>
                        <div class="row form-group center-block">
                            <button type="submit" class="btn btn-default"> Ubah </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
