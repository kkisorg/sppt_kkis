@extends('layout.base')

@section('title', 'Lihat Jadwal Pengiriman Email')

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3><b>Lihat Jadwal Publikasi Media Online</b></h3>
                </div>
                <div class="panel-body">
                    <div><h4><b>Judul Pengumuman: </b></h4></div>
                    <div><h5> {{ $schedule->title }} </h5></div>
                    <div><h4><b>Isi Pengumuman: </b></h4></div>
                    <div>{!! $schedule->content !!} </div>
                    <hr>
                    <div><h4><b>Waktu: </b></h4></div>
                    <div>{{ $schedule->publish_datetime }}</div>
                    <div><h4><b>Kelola: </b></h4></div>
                    <a class="btn btn-success" href="/announcement_online_media_publish_schedule/manual_invoke/{{ $schedule->id }}" onclick="return confirm('Apakah Anda yakin mempublikasikan pengumuman ini secara manual??');"> Publikasikan secara manual </a>
                    @if (count($records) > 0)
                    <hr>
                    <div><h4><b> Rekor Publikasi Online Media: </b></h4></div>
                    <div class="panel-group" id="record-list" role="tablist" aria-multiselectable="true">
                        @foreach($records as $record)
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="heading{{ $loop->iteration }}">
                                <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#record-list" href="#collapse{{ $loop->iteration }}" aria-expanded="false" aria-controls="collapse{{ $loop->iteration }}">
                                        Percobaan Kirim {{ $loop->iteration }} ({{ $record->create_datetime }})
                                        @if ($record->is_manual) (Dijalankan manual oleh {{ $record->creator->name }}) @endif
                                        @if (@$record->status === 'FAILED')
                                        <span class="label label-danger">
                                        @elseif (@$record->status === 'SUCCESS')
                                        <span class="label label-success">
                                        @else
                                        <span class="label label-warning">
                                        @endif
                                            {{ $record->status }}
                                        </span>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{ $loop->iteration }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $loop->iteration }}">
                                <div class="panel-body">
                                    <div><h4><b>Request Parameter: </b></h4></div>
                                    <div><pre>{{ $record->request_parameter }} </pre></div>
                                    <div><h4><b>Response Content: </b></h4></div>
                                    <div><pre>@if ($record->response_content === null) null @else {{ $record->response_content }} @endif</pre></div>
                                    <div><h4><b>Error: </b></h4></div>
                                    <div><pre>@if ($record->error === null) null @else {{ $record->error }} @endif</pre></div>
                                    <hr>
                                    <div><h4><b>Waktu: </b></h4></div>
                                    <div>{{ $record->create_datetime }}</div>
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
