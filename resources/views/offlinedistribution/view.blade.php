@extends('layout.base')

@section('title', 'Lihat Distribusi')

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Lihat Distribusi</b></h3>
                </div>
                <div class="panel-body">
                    <div><h4><b>Isi Pengumuman: </b></h4></div>
                    <div><h5><b> {{ $offline_distribution->name}} </b></h5></div>
                    <div> {!! $offline_distribution->header !!} </div>
                    <div> {!! $offline_distribution->content !!} </div>
                    <div> {!! $offline_distribution->footer !!} </div>
                    <hr>
                    <div><h4><b>Waktu Distribusi: </b></h4></div>
                    <div>{{ $offline_distribution->distribution_datetime }}</div>
                    <hr>
                    <div><h4><b>Batas Waktu (Deadline): </b></h4></div>
                    <div>{{ $offline_distribution->deadline_datetime }}</div>
                    <hr>
                    <div><h4><b> Media Distribusi: </b></h4></div>
                    <div>{{ $offline_distribution->media_name }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection
