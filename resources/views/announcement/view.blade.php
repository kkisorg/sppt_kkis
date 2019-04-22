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
                    <div><h5><b> {{ $announcement->organization_name}} - {{ $announcement->title }} </b></h5></div>
                    <div> {!! $announcement->content !!} </div>
                    @if (count($announcement->media_content) > 0)
                    <hr>
                    <div><h4><b> Isi Pengumuman Tiap Media: </b></h4></div>
                    <div class="panel-group" id="content-list" role="tablist" aria-multiselectable="true">
                        @foreach($announcement->media_content as $media_name => $media_content)
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading{{ $loop->iteration }}">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#content-list" href="#collapse{{ $loop->iteration }}" aria-expanded="false" aria-controls="collapse{{ $loop->iteration }}">
                                        {{ $media_name }}
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $loop->iteration }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $loop->iteration }}">
                                <div class="panel-body">
                                    <div> {!! $media_content !!} </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
