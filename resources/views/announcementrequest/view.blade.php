@extends('layout.base')

@section('title', 'Lihat Pengumuman')

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Lihat Pengumuman</b></h3>
                </div>
                <div class="panel-body">
                    <div><h4><b>Isi Pengumuman: </b></h4></div>
                    <div><h5><b> {{ $announcement_request->organization_name}} - {{ $announcement_request->title }} </b></h5></div>
                    <div> {!! $announcement_request->content !!} </div>
                    <hr>
                    <div><h4><b>Waktu: </b></h4></div>
                    <div>{{ $announcement_request->event_datetime }}</div>
                    <hr>
                    <div><h4><b> Media Distribusi: </b></h4></div>
                    <div>{{ $announcement_request->media }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
