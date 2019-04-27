@extends('layout.base')

@section('title', 'Lihat Seluruh Pengumuman')

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Lihat Seluruh Pengumuman</b></h3>
                </div>
                <div class="panel-body">
                    @foreach($present_announcements as $announcement)
                    <div><h5><b> {{ $announcement->organization_name}} - {{ $announcement->title }} </b></h5></div>
                    <div> {!! $announcement->content !!} </div>
                    @if ($loop->iteration < count($present_announcements))
                    <hr>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
