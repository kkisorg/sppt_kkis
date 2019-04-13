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
                    @if (count($revisions) > 0)
                    <hr>
                    <div><h4><b> Daftar Revisi / Versi Sebelumnya: </b></h4></div>
                    <div class="panel-group" id="revision-list" role="tablist" aria-multiselectable="true">
                        @foreach($revisions as $revision)
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading{{ $revision->revision_no }}">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#revision-list" href="#collapse{{ $revision->revision_no }}" aria-expanded="false" aria-controls="collapse{{ $revision->revision_no }}">
                                        Versi {{ $revision->revision_no }} ({{ $revision->create_datetime }})
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $revision->revision_no }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $revision->revision_no }}">
                                <div class="panel-body">
                                    <div><h4><b>Isi Pengumuman: </b></h4></div>
                                    <div><h5><b> {{ $revision->organization_name}} - {{ $revision->title }} </b></h5></div>
                                    <div> {!! $revision->content !!} </div>
                                    <hr>
                                    <div><h4><b>Waktu: </b></h4></div>
                                    <div>{{ $revision->event_datetime }}</div>
                                    <hr>
                                    <div><h4><b> Media Distribusi: </b></h4></div>
                                    <div>{{ $revision->media }}</div>
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
